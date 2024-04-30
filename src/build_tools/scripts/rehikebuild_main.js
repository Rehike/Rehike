/**
 * @fileoverview Main implementations for RehikeBuild
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const through2 = require("through2");
const Utils = require("./utils");
const path = require("path");
const assert = require("assert/strict");
const crypto = require("crypto");
const Transform = require("streamx").Transform;

// Includes should be relative to the src/ directory, and this script resides in
// src/build_tools/scripts, so we need to up two directories.
const BASE_SRC_DIR = path.resolve(__dirname, "../..");

// Rehike root directory is three directories up from here.
const REHIKE_ROOT_DIR = path.resolve(__dirname, "../../..");

// CSS compiler includes
const sassBackend = require("sass");
const gulpSassBackend = require("gulp-sass");
const GulpSass = gulpSassBackend(sassBackend);

/**
 * Common build configuration options.
 */
const commonBuildCfg = {
    base: BASE_SRC_DIR,
    root: BASE_SRC_DIR,
    cwd: BASE_SRC_DIR,
};

/**
 * Stores all registered build tasks.
 * 
 * @type {BuildTask[]}
 */
const g_buildTaskRegistry = [];

/**
 * Base class for Gulp build tasks.
 * 
 * @abstract
 */
class BuildTask
{
    inputFileNames = [];
    outputFileName = "";
    displayName = "";
    
    _gulpTask = null;
    _isPending = true;
    
    constructor(descriptor, inputFileNames, outputFileName)
    {
        if (typeof inputFileNames == "string")
        {
            this.inputFileNames = [inputFileNames];
        }
        else
        {
            this.inputFileNames = inputFileNames;
        }
        
        this.displayName = descriptor.taskName;
        
        this.outputFileName = outputFileName;
        
        console.log(`Created new BuildTask(${JSON.stringify(inputFileNames)}, ${outputFileName})`);
    }
    
    get gulpTask()
    {
        this._ensureGulpTask();
        
        return this._gulpTask;
    }
    
    get isPending()
    {
        return this._isPending;
    }
    
    static getAllBuildTasks()
    {
        // console.log(JSON.stringify(g_buildTaskRegistry));
        let out = [];
        
        for (const wrapper of g_buildTaskRegistry)
        {
            const fn = function() {
                return wrapper.gulpTask;
            };
            
            fn.displayName = "[RehikeBuild] " + wrapper.displayName;
            
            out.push(fn);
        }
        
        return out;
        //return g_buildTaskRegistry.map(wrapper => wrapper.gulpTask);
    }
    
    /**
     * Ensures that the Gulp task exists, and creates it if it doesn't.
     */
    _ensureGulpTask()
    {
        if (!this._gulpTask)
        {
            console.log("Creating gulp task");
            this._gulpTask = this._buildGulpTask();
            this._gulpTask.on("end", function() {
                console.log("Task ended:");
                console.dir(arguments);
            });
            this._gulpTask.on("finish", function() {
                console.log("Task finished:");
                console.dir(arguments);
                
                //console.dir(this.gulpTask);
            }.bind(this));
        }
    }
    
    /**
     * Builds a Gulp task for the file.
     * 
     * @abstract
     * @virtual
     * @protected
     */
    _buildGulpTask()
    {
        const gulp = this._prepareGulpBackend();
        return gulp;
    }
    
    /**
     * Sets up the Gulp backend for building the task.
     * 
     * @protected
     */
    _prepareGulpBackend()
    {
        return gulp.src(this.inputFileNames, commonBuildCfg);
    }
}

class CSSBuildTask extends BuildTask
{
    /** @inheritdoc @override */
    _buildGulpTask()
    {
        console.log("CSSBuildTask._buildGulpTask");
        const task = this._prepareGulpBackend();
        let result = task.pipe(GulpSass.sync({ outputStyle: "compressed" }).on("error", GulpSass.logError))
            .pipe(through2.obj(function(file, encoding, callback) {
                console.log("hi!!!!");
                this.push(file);
                
                let test = new BuildTask({}, "fake", "fake");
                g_buildTaskRegistry.push(test);
                
                callback();
            }));
        return result;
    }
}

/**
 * Opens a file as a Vinyl file.
 * 
 * This is similar to gulp.src, except that it is designed to work with full file paths,
 * which is what RehikeBuild is designed to use.
 * 
 * @param {string} fullFilePath 
 */
function openVinylFile(fullFilePath)
{
    
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
    function decorateAndPush(descriptor, srcEntry, languageName)
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
                
            // const buildTask = new BuildFile({
            //     languageName: languageName,
            //     sourcePath: fullEntryPath,
            //     destinationPath: normalizedDestPath
            // });
            
            let buildTask = null;
            
            switch (languageName)
            {
                case "css":
                    buildTask = new CSSBuildTask(descriptor, fullEntryPath, normalizedDestPath);
                    break;
            }
            
            if (buildTask)
            {
                g_buildTaskRegistry.push(buildTask);
            }
        }
    }
    
    if (descriptor.cssBuildFiles != null)
        decorateAndPush(descriptor, descriptor.cssBuildFiles, "css");
    
    if (descriptor.jsBuildFiles != null)
        decorateAndPush(descriptor, descriptor.jsBuildFiles, "js");

    if (descriptor.protobufBuildFiles != null)
        decorateAndPush(descriptor, descriptor.protobufBuildFiles, "protobuf");
}

// module.exports = {
//     // Exported constants:
//     BASE_SRC_DIR: BASE_SRC_DIR,
//     REHIKE_ROOT_DIR: REHIKE_ROOT_DIR,
    
//     // Exported classes:
//     BuildTask: BuildTask,
//     CSSBuildTask: CSSBuildTask,
    
//     // Exported functions:
//     pushSourceFiles: pushSourceFiles,
    
//     // Namespace aliases:
//     Parser: require("./parse_rhbuild"),
// };

exports.BASE_SRC_DIR = BASE_SRC_DIR;
exports.REHIKE_ROOT_DIR = REHIKE_ROOT_DIR;

exports.BuildTask = BuildTask;
exports.CSSBuildTask = CSSBuildTask;

exports.pushSourceFiles = pushSourceFiles;

exports.Parser = require("./parse_rhbuild");