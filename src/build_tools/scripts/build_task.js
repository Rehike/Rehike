/**
 * @fileoverview Provides the base class for Gulp builds tasks.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const through2 = require("through2");
const path = require("path");
const { Transform } = require("stream");
const fs = require("fs/promises");

const RehikeBuild = require("./rehikebuild_main");
const VflGenerator = require("./vfl_gen");

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
        FINISHING: 1,
        FINISHED: 2,
        ERRORED: 3,
        UP_TO_DATE: 4,
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
            
            this._gulpTask.on("finish", async function() {
                // Perform post-task events:
                parent._status = BuildTask.Status.FINISHING;
                await parent._onAllTasksCompleted();
                
                // We're done building, so signal to any outside subscribers:
                parent._status = BuildTask.Status.FINISHED;
                parent._resolutionPromise.resolve(parent._data);
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
        return gulp.src(this.inputFileNames, RehikeBuild.commonBuildCfg);
    }
    
    /**
     * Runs when all Gulp tasks are done running.
     * 
     * @protected
     * @virtual
     */
    async _onAllTasksCompleted()
    {
        // Ensure that the directories to the file path exist when attempting to load it:
        let fullOutputPath = path.join(RehikeBuild.REHIKE_ROOT_DIR, this.outputFileName);
        
        const dirName = path.dirname(fullOutputPath);
        
        try
        {
            if (!((await fs.stat(dirName)).isDirectory()))
            {
                await fs.mkdir(dirName, { recursive: true });
            }
        }
        catch (e)
        {
            await fs.mkdir(dirName, { recursive: true });
        }
        
        // Write the file:
        const fd = await fs.open(fullOutputPath, "w");
        
        await fd.write(this._data.contents);
        
        console.log(`Wrote out file "${fullOutputPath}"`);
        
        await fd.close();
        
        // Generate VFL mapping:
        VflGenerator.generateVflMapping(this);
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
     * @returns {IIteratorResponse}
     */
    getNext()
    {
        const chunk = g_buildTaskRegistry.slice(this._latestKnownItemPosition);
        
        this._latestKnownItemPosition = g_buildTaskRegistry.length;
        
        let gulpWrappers = [];
        let tasks = [];
        
        for (const buildTask of chunk)
        {
            const wrapper = function() {
                return buildTask.gulpTask;
            };
            
            // Inherit the Gulp task name from the wrapped task so that the
            // console logs work correctly:
            wrapper.displayName = buildTask.displayName;
            
            gulpWrappers.push(wrapper);
            tasks.push(buildTask);
        }
        
        return {
            gulpWrappers: gulpWrappers,
            tasks: tasks
        };
    }
}

/**
 * @interface
 */
class IIteratorResponse
{
    /**
     * Wrapped tasks for Gulp's Undertaker module.
     * 
     * @type {callback[]}
     */
    gulpWrappers;
    
    /**
     * All source build tasks for the chunk.
     * 
     * @type {BuildTask[]}
     */
    tasks;
}

exports.g_buildTaskRegistry = g_buildTaskRegistry;
exports.BuildTask = BuildTask;