/**
 * @fileoverview Rehike Build System
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const through2 = require("through2");
const RehikeBuild = require("./scripts/rehikebuild_main");

// Miscellaneous includes:
const path = require("path");

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

/**
 * Runs JS build tasks.
 */
function BuildJs()
{
    const packages = RehikeBuild.getBuildFilesForLanguage("js").getPackages();
    
    const buildEvents = [];
    
    packages.forEach((fileDefs, destinationName) => {
        const buildFiles = fileDefs.map(file => file.sourcePath);
        console.log("BuildJs:packages.forEach", JSON.stringify(buildFiles));
        
        // Requirements for Closure Compiler:
        buildFiles.push(
            "build_tools/node_modules/google-closure-library/closure/goog/base.js"
        );
        
        //let existingFileName = {};
        
        buildEvents.push(promiseWrapStream(
            gulp.src(buildFiles, commonBuildCfg)
            // Initialize the build task so that RehikeBuild actions can be applied to it:
            .pipe(RehikeBuild.GulpInitRehikeBuildTask())
            //.pipe(RehikeBuild.GulpHackGetRehikeBuildHandleTask(existingFileName, "efn"))
            // Send the file contents off to the JS compiler:
            .pipe(GulpClosureCompiler({
                compilation_level: "ADVANCED_OPTIMIZATIONS",
                process_closure_primitives: true,
                language_out: "ECMASCRIPT3",
                output_wrapper: "(function(){%output%})();",
                js_output_file: "NAME_DOESNT_FUCKING_WORK.js"
            }))
            // Finalize the build task:
            .pipe(RehikeBuild.GulpFinalizePathsTask())
            .pipe(gulp.dest(RehikeBuild.BASE_SRC_DIR))
        ));
    });
    
    const BuildEvents = () => Promise.all(buildEvents);
    
    return gulp.parallel(BuildEvents);
}

async function BuildAll()
{
    const iterator = RehikeBuild.BuildTask.getAllBuildTasks();
    
    while (iterator.hasNewItems())
    {
        await new Promise((resolve, reject) => {
            gulp.parallel( iterator.getNext() )( () => resolve() );
        });
    }
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