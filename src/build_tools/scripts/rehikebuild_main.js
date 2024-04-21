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
 */
const buildFiles = {
    js: {},
    css: {},
    protobuf: {}
};

/**
 * Stores rename handles for build files.
 * 
 * @see See {@link getRenameHandle} for more information
 */
const renameHandles = {};

/**
 * Gets all known build files for a given language name.
 * 
 * @param {string} languageName 
 * @returns {string[]}
 */
function getBuildFilesForLanguage(languageName)
{
    if (!(languageName in buildFiles))
    {
        console.warn("Attempted to read build files for non-existent language: " + languageName);
        return [];
    }
    
    return Object.keys(buildFiles[languageName]);
}

/**
 * Gets the output path related to a given build file path.
 * 
 * @param {string} buildFilePath 
 * @returns {string?}
 */
function getOutputPathForBuildFile(buildFilePath)
{
    const flattened = Utils.flattenObject(buildFiles);
    
    let normalizedPath = buildFilePath.replace(new RegExp("\\" + path.sep, "g"), "/");
    
    let outputPath = flattened[normalizedPath];
    
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
    function decorateAndPush(srcEntry, dest)
    {
        assert(typeof srcEntry == "object");
        
        for (let entryKey in srcEntry)
        {
            // Resolve the full path of the file (relative from the src/ directory).
            // This is necessary because the keys for these maps in .rhbuild files
            // are relative to the .rhbuild file path.
            let fullEntryPath = path.resolve(basePath, entryKey)
                .replace(new RegExp("\\" + path.sep, "g"), "/");
            //fullEntryPath = path.relative(BASE_SRC_DIR, fullEntryPath);
            
            // Create a property on the destination object that has the same value
            // as the source entry. The source path is always relative to the Rehike
            // root directory.
            dest[fullEntryPath] = srcEntry[entryKey]
                .replace(new RegExp("\\" + path.sep, "g"), "/");
        }
    }
    
    if (descriptor.cssBuildFiles != null)
        decorateAndPush(descriptor.cssBuildFiles, buildFiles.css);
    
    if (descriptor.jsBuildFiles != null)
        decorateAndPush(descriptor.jsBuildFiles, buildFiles.js);

    if (descriptor.protobufBuildFiles != null)
        decorateAndPush(descriptor.protobufBuildFiles, buildFiles.protobuf);
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
    
    throw new Exception(`Rename handle ${handle} does not exist.`);
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

exports.getBuildFilesForLanguage = getBuildFilesForLanguage;
exports.getOutputPathForBuildFile = getOutputPathForBuildFile;
exports.pushSourceFiles = pushSourceFiles;
exports.GulpInitRehikeBuildTask = GulpInitRehikeBuildTask;
exports.GulpFinalizePathsTask = GulpFinalizePathsTask;
exports.BASE_SRC_DIR = BASE_SRC_DIR;
exports.REHIKE_ROOT_DIR = REHIKE_ROOT_DIR;
exports.Parser = require("./parse_rhbuild");