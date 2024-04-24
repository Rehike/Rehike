/**
 * @fileoverview Main implementations for RehikeBuild and .rhbuild files.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const through2 = require("through2");
const Utils = require("./utils");
const path = require("path");
const assert = require("assert/strict");
const crypto = require("crypto");

// Includes should be relative to the src/ directory, and this script resides in
// src/build_tools/scripts, so we need to up two directories.
const BASE_SRC_DIR = path.resolve(__dirname, "../..");

// Rehike root directory is three directories up from here.
const REHIKE_ROOT_DIR = path.resolve(__dirname, "../../..");

/**
 * Stores a map of all known build files.
 * 
 * The mapping is stored: source path -> output destination
 */
const buildFiles = {
    /**
     * All build files.
     */
    _all: [],
    
    /**
     * Packages determine all source file that compose a single destination file.
     * 
     * @type {Map<string, WeakRef<BuildFile>[]>}
     */
    _packages: new Map(),
    
    js: [],
    css: [],
    protobuf: [],
    
    /**
     * Push a build file to the build map.
     * 
     * @param {BuildFile} buildFile
     */
    push(buildFile)
    {
        this._all.push(buildFile);
        
        // Per-language name cache:
        if (!this[buildFile.languageName])
        {
            this[buildFile.languageName] = [];
        }
        
        this[buildFile.languageName].push(new WeakRef(buildFile));
        
        // Package name cache:
        if (!this._packages.has(buildFile.destinationPath))
        {
            this._packages.set(buildFile.destinationPath, []);
        }
        
        this._packages.get(buildFile.destinationPath).push(new WeakRef(buildFile));
    },
};

/**
 * Stores rename handles for build files.
 * 
 * @see See {@link getRenameHandle} for more information
 */
const renameHandles = {};

/**
 * A build file.
 */
class BuildFile
{
    languageName;
    sourcePath;
    destinationPath;
    
    /**
     * @param {object} info 
     */
    constructor(info)
    {
       assert(info.languageName && info.sourcePath && info.destinationPath);
       
       this.languageName = info.languageName;
       this.sourcePath = info.sourcePath;
       this.destinationPath = info.destinationPath;
    }
}

/**
 * API for retrieving build files for a language.
 */
class LanguageBuildFilesAPI
{
    /**
     * The name of the language.
     * 
     * @readonly
     * @private
     */
    _languageName = "";
    
    constructor(languageName)
    {
        this._languageName = languageName;
    }
    
    /**
     * Gets all build files (unsorted) for a language.
     * 
     * @returns {string[]}
     */
    getAllSourceNames()
    {
        let files = buildFiles[this._languageName]
            .map(item => item.deref())
            .filter(item => null != item)
            .map(item => item.sourcePath);
        return files;
    }
    
    /**
     * Gets the package API for a language.
     * 
     * @returns {Map<string, BuildFile[]>}
     */
    getPackages()
    {
        let result = new Map();
        
        for (const mapping of buildFiles._packages)
        {
            const destPath = mapping[0];
            const packages = mapping[1];
            
            for (const pPackage of packages)
            {
                const pkg = pPackage.deref();
                
                if (!pkg || pkg.languageName != this._languageName)
                    continue;
                
                if (!result.get(destPath))
                    result.set(destPath, []);
                
                result.get(destPath).push(pkg);
            }
        }
        
        return result;
    }
}

/**
 * Gets all known build files for a given language name.
 * 
 * @param {string} languageName 
 * @returns {LanguageBuildFilesAPI}
 */
function getBuildFilesForLanguage(languageName)
{
    if (!(languageName in buildFiles))
    {
        console.warn("Attempted to read build files for non-existent language: " + languageName);
        return [];
    }
    
    return new LanguageBuildFilesAPI(languageName);
}

/**
 * Gets the output path related to a given build file path.
 * 
 * @param {string} buildFilePath 
 * @returns {string?}
 */
function getOutputPathForBuildFile(buildFilePath)
{
    let normalizedPath = buildFilePath.replace(new RegExp("\\" + path.sep, "g"), "/");
    
    let outputPath = null;
    for (const buildFile of buildFiles._all)
    {
        if (buildFile.sourcePath == normalizedPath)
        {
            outputPath = buildFile.destinationPath;
            break;
        }
    }
    
    if (outputPath)
    {
        return path.resolve(REHIKE_ROOT_DIR, outputPath);
    }
    
    console.error(`Build file ${buildFilePath} does not exist`);
}

/**
 * Pushes a list of source files from a .rhbuild file to the global list.
 */
function pushSourceFiles(descriptor)
{
    assert(typeof descriptor.baseName == "string", JSON.stringify(descriptor));
    
    const basePath = path.dirname(descriptor.baseName);
    
    // Common function to decorate and push entries for all languages, assuming they
    // exist.
    function decorateAndPush(srcEntry, languageName)
    {
        assert(typeof srcEntry == "object");

        for (let entryKey in srcEntry)
        {
            // Resolve the full path of the file (relative from the src/ directory).
            // This is necessary because the keys for these maps in .rhbuild files
            // are relative to the .rhbuild file path.
            let fullEntryPath = path.resolve(basePath, entryKey)
                .replace(new RegExp("\\" + path.sep, "g"), "/");
                
            // The destination path is always relative to the Rehike root directory.
            const normalizedDestPath = srcEntry[entryKey]
                .replace(new RegExp("\\" + path.sep, "g"), "/");
                
            const buildFile = new BuildFile({
                languageName: languageName,
                sourcePath: fullEntryPath,
                destinationPath: normalizedDestPath
            });
            
            buildFiles.push(buildFile);
        }
    }
    
    if (descriptor.cssBuildFiles != null)
        decorateAndPush(descriptor.cssBuildFiles, "css");
    
    if (descriptor.jsBuildFiles != null)
        decorateAndPush(descriptor.jsBuildFiles, "js");

    if (descriptor.protobufBuildFiles != null)
        decorateAndPush(descriptor.protobufBuildFiles, "protobuf");
}

/**
 * Gets a rename handle for tracking files between renames.
 * 
 * Rename handles are required because Gulp tasks can rename files subtly (such
 * as changing the extensions) and we want to track file names easily on our
 * own; RehikeBuild definitions define their own output paths.
 * 
 * @param {string} resolutionName
 */
function getRenameHandle(resolutionName)
{
    let id;
    
    do 
    {
        id = "rhbuild_" + crypto.randomBytes(20).toString("hex");
    }
    while (Object.keys(renameHandles).includes(id));
    
    renameHandles[id] = resolutionName;
    
    return id;
}

/**
 * Resolves a rename handle.
 * 
 * @see See {@link getRenameHandle} for more information.
 * 
 * @param {string} handle 
 */
function resolveRenameHandle(handle)
{
    // Limit the handle file name to just the basename without any extension,
    // so it matches the actual handle name used internally:
    let normalizedHandle = path.basename(handle, path.extname(handle));
    
    if (renameHandles[normalizedHandle])
    {
        return renameHandles[normalizedHandle];
    }
    
    throw new Error(`Rename handle ${handle} does not exist.`);
}

/**
 * Gulp task for initializing RehikeBuild tasks.
 */
function GulpInitRehikeBuildTask()
{
    return through2.obj(function(file, encoding, callback)
    {
        const handle = getRenameHandle(getOutputPathForBuildFile(file.path));
        
        file.basename = handle + file.extname;
        
        this.push(file);
        callback();
    });
}

/**
 * Gulp task for finalizing RehikeBuild tasks.
 */
function GulpFinalizePathsTask()
{
    return through2.obj(function(file, encoding, callback)
    {
        const originalPath = file.path;
        let newPath = resolveRenameHandle(originalPath);
        
        if (newPath)
        {
            file.path = newPath;
        }
        else
        {
            console.error("Failed to get new path, debug info: ", newPath);
            callback();
            return;
        }
        
        if (file.sourceMap)
        {
            file.sourceMap.file = file.relative;
        }
        
        this.push(file);
        
        callback(null, file);
    });
}

/**
 * Gulp task for getting the RehikeBuild handle name.
 */
function GulpHackGetRehikeBuildHandleTask(outputObj, outputName)
{
    return through2.obj(function(file, encoding, callback)
    {
        // Limit the handle file name to just the basename without any extension,
        // so it matches the actual handle name used internally:
        let normalizedHandle = path.basename(file.path, path.extname(file.path));
        
        outputObj[outputName] = normalizedHandle;
        
        this.push(file);
        callback();
    });
}

exports.getBuildFilesForLanguage = getBuildFilesForLanguage;
exports.getOutputPathForBuildFile = getOutputPathForBuildFile;
exports.pushSourceFiles = pushSourceFiles;
exports.GulpInitRehikeBuildTask = GulpInitRehikeBuildTask;
exports.GulpFinalizePathsTask = GulpFinalizePathsTask;
exports.GulpHackGetRehikeBuildHandleTask = GulpHackGetRehikeBuildHandleTask;
exports.BASE_SRC_DIR = BASE_SRC_DIR;
exports.REHIKE_ROOT_DIR = REHIKE_ROOT_DIR;
exports.Parser = require("./parse_rhbuild");