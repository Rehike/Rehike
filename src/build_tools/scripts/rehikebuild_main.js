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
const { Transform } = require("stream");

// Includes should be relative to the src/ directory, and this script resides in
// src/build_tools/scripts, so we need to up two directories.
const BASE_SRC_DIR = path.resolve(__dirname, "../..");

// Rehike root directory is three directories up from here.
const REHIKE_ROOT_DIR = path.resolve(__dirname, "../../..");

// CSS compiler includes
const sassBackend = require("sass");
const gulpSassBackend = require("gulp-sass");
const GulpSass = gulpSassBackend(sassBackend);

// JS compiler includes:
const closureCompilerBackend = require("google-closure-compiler");
const GulpClosureCompiler = closureCompilerBackend.gulp();

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
    
    static Status = {
        PENDING: 0,
        FINISHED: 1,
        ERRORED: 2,
    };
    
    _gulpTask = null;
    _status = BuildTask.Status.PENDING;
    
    _data = null;
    
    _resolutionPromise = {
        resolve: null,
        reject: null,
        promise: null
    };
    
    get resolutionPromise()
    {
        return this._resolutionPromise.promise;
    }
    
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
        
        this._resolutionPromise.promise = new Promise((resolve, reject) => {
            this._resolutionPromise.resolve = resolve;
            this._resolutionPromise.reject = reject;
        });
    }
    
    get gulpTask()
    {
        this._ensureGulpTask();
        
        return this._gulpTask;
    }
    
    get isPending()
    {
        return this._status == BuildTask.Status.PENDING;
    }
    
    get status()
    {
        return this._status;
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
            const parent = this;
            console.log("Creating gulp task");
            this._gulpTask = this._buildGulpTask();
            
            this._gulpTask = this._gulpTask.pipe(this._getDataFromStream(this));
            
            this._gulpTask.on("finish", function() {
                parent._status = BuildTask.Status.FINISHED;
                parent._resolutionPromise.resolve(parent._data);
                
                console.dir(parent._data.contents.toString());
            });
            
            this._gulpTask.on("error", function(e) {
                parent._status = BuildTask.Status.ERRORED;
                parent._resolutionPromise.reject(e);
            });
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
    
    /**
     * Gets the data from the Gulp transform stream.
     * 
     * @param {BuildTask} targetObj 
     * @returns {Transform}
     */
    _getDataFromStream(targetObj)
    {
        return through2.obj(function(file, encoding, callback) {
            targetObj._data = file;
            
            // This should always be the last step, but just in case, we actually don't
            // push the file in any case.
            callback();
        });
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

class JSBuildTask extends BuildTask
{
    /** @inheritdoc @override */
    _buildGulpTask()
    {
        console.log("JSBuildTask._buildGulpTask");
        const task = this._prepareGulpBackend();
        let result = task.pipe(GulpClosureCompiler({
                compilation_level: "ADVANCED_OPTIMIZATIONS",
                process_closure_primitives: true,
                language_out: "ECMASCRIPT3",
                output_wrapper: "(function(){%output%})();"
            }));
        return result;
    }
    
    /** @inheritdoc @override */
    _prepareGulpBackend()
    {
        const buildFiles = this.inputFileNames.slice(0); // .slice(0) to clone the array
        
        // Requirements for Closure Compiler:
        buildFiles.push(
            "build_tools/node_modules/google-closure-library/closure/goog/base.js"
        );
        
        return gulp.src(buildFiles, commonBuildCfg);
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
    function buildSourceToSource(descriptor, srcEntry, languageName)
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
                case "js":
                    buildTask = new JSBuildTask(descriptor, fullEntryPath, normalizedDestPath);
                    break;
            }
            
            if (buildTask)
            {
                g_buildTaskRegistry.push(buildTask);
            }
        }
    }
    
    function buildManyToOne(descriptor, srcEntries, outputBundle, languageName)
    {
        assert(typeof srcEntries == "object");
        assert(typeof outputBundle == "string");
        
        // Resolutions of the full paths of the files (relative from the src/ directory).
        const fullEntryPaths = [];
        
        // The destination path is always relative to the Rehike root directory.
        const normalizedDestPath = outputBundle.replace(new RegExp("\\" + path.sep, "g"), "/");
        
        for (let entry of srcEntries)
        {
            fullEntryPaths.push(
                path.resolve(basePath, entry).replace(new RegExp("\\" + path.sep, "g"), "/")
            );
        }
        
        let buildTask = null;
        
        switch (languageName)
        {
            case "js":
                buildTask = new JSBuildTask(descriptor, fullEntryPaths, normalizedDestPath);
                break;
        }
        
        if (buildTask)
        {
            g_buildTaskRegistry.push(buildTask);
        }
    }
    
    if (descriptor.cssBuildFiles != null)
        buildSourceToSource(descriptor, descriptor.cssBuildFiles, "css");
    
    if (descriptor.jsBuildFiles != null)
        buildManyToOne(descriptor, descriptor.jsBuildFiles, descriptor.jsOutputBundle, "js");

    if (descriptor.protobufBuildFiles != null)
        buildSourceToSource(descriptor, descriptor.protobufBuildFiles, "protobuf");
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