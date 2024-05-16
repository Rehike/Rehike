/**
 * @fileoverview Rehike Build System
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const RehikeBuild = require("./scripts/rehikebuild_main");
const VflGenerator = require("./scripts/vfl_gen");

/**
 * Wraps a Node.js stream for consumption alongside promises.
 * 
 * @param {Stream} stream
 * @returns {Promise<void>}
 */
function promiseWrapStream(stream)
{
    return new Promise((resolve, reject) => {
        stream.on("finish", resolve);
        stream.on("end", resolve);
        stream.on("error", reject);
    });
}

/**
 * All common startup tasks for the build environment.
 * 
 * @return {Promise<void>}
 */
function CommonStartupTask()
{
    const stream = promiseWrapStream;
    
    return Promise.all([
        stream(RehikeBuild.Parser.GulpSetupRhBuildTask())
    ]);
}

CommonStartupTask.displayName = "RehikeBuild :: Initialization";

async function BuildAll()
{
    const iterator = RehikeBuild.BuildTask.getAllBuildTasks();
    
    const tasks = [];
    
    /*
     * The waiting architecture here is pretty complicated in order to work with
     * Gulp.
     */
    while (iterator.hasNewItems())
    {
        // We continuously get slices in a loop while they're made. This is done
        // in order to dynamically add more Gulp build tasks during the build.
        const slice = iterator.getNext();
        
        tasks.push(...slice.tasks);
        
        await new Promise((resolve, reject) => {
            gulp.parallel( slice.gulpWrappers )( () => resolve() );
        });
    }
    
    // Wait for all RehikeBuild tasks to finish, which may take longer than Gulp:
    await Promise.all(tasks.map(task => task.resolutionPromise));
    
    await VflGenerator.generateNewCache();
    console.log("All builds complete.");
}

BuildAll.displayName = "RehikeBuild :: Main build task";

console.log("Welcome to RehikeBuild!");

// Main build action
exports.build = gulp.series(
    CommonStartupTask,
    BuildAll
);

// Build everything if no arguments are provided:
exports.default = exports.build;