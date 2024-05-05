/**
 * @fileoverview Main implementations for RehikeBuild
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const through2 = require("through2");
const path = require("path");
const assert = require("assert/strict");

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
 * Note that new tasks should always be appended to the end of this array in
 * order for the build system to function correctly. Inserting an item in the
 * middle will mess things up.
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
    
    /**
     * Gets an iterator for all build tasks in the registry.
     * 
     * @returns {BuildTaskRegistryIterator}
     */
    static getAllBuildTasks()
    {
        return new BuildTaskRegistryIterator();
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
                this.push(file);
                
                // Temporary testing code: 
                if (process.argv.includes("--test-branched-build-task"))
                {
                    let test = new BuildTask({taskName: "Fake test task."}, "fake", "fake");
                    g_buildTaskRegistry.push(test);
                }
                
                callback();
            }));
        return result;
    }
}

/**
 * Iterates the build task registry.
 * 
 * This design exists to allow tasks to be added dynamically during the build process.
 */
class BuildTaskRegistryIterator
{
    /**
     * The latest known item position in the build task registry.
     * 
     * @private
     */
    _latestKnownItemPosition = 0;
    
    /**
     * Check if new items were added to the registry since the last time we checked.
     * 
     * @returns {boolean}
     */
    hasNewItems()
    {
        return this._latestKnownItemPosition < g_buildTaskRegistry.length;
    }
    
    /**
     * Gets the latest unread chunk of build tasks from the registry.
     * 
     * This function is also responsible for the decoration process so that they
     * work with Gulp.
     * 
     * @returns {callback[]} Wrapped tasks for Gulp's Undertaker module.
     */
    getNext()
    {
        const chunk = g_buildTaskRegistry.slice(this._latestKnownItemPosition);
        
        this._latestKnownItemPosition = g_buildTaskRegistry.length;
        
        let out = [];
        
        for (const wrapper of chunk)
        {
            const fn = function() {
                return wrapper.gulpTask;
            };
            
            fn.displayName = "[RehikeBuild] " + wrapper.displayName;
            
            out.push(fn);
        }
        
        return out;
    }
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

// Exported constants:
exports.BASE_SRC_DIR = BASE_SRC_DIR;
exports.REHIKE_ROOT_DIR = REHIKE_ROOT_DIR;

// Exported classes:
exports.BuildTask = BuildTask;
exports.CSSBuildTask = CSSBuildTask;

// Exported functions:
exports.pushSourceFiles = pushSourceFiles;

// Namespace aliases:
exports.Parser = require("./parse_rhbuild");