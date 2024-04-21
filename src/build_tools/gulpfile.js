/**
 * @fileoverview Rehike Build System
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const RehikeBuild = require("./scripts/rehikebuild_main");

// CSS compiler includes
const sassBackend = require("sass");
const gulpSassBackend = require("gulp-sass");
const GulpSass = gulpSassBackend(sassBackend);

/**
 * Common build configuration options.
 */
const commonBuildCfg = {
    base: RehikeBuild.BASE_SRC_DIR,
    root: RehikeBuild.BASE_SRC_DIR,
    cwd: RehikeBuild.BASE_SRC_DIR,
};

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

/**
 * Runs CSS build tasks.
 */
function BuildCss()
{
    return gulp.src(RehikeBuild.getBuildFilesForLanguage("css"), commonBuildCfg)
        // Initialize the build task so that RehikeBuild actions can be applied to it:
        .pipe(RehikeBuild.GulpInitRehikeBuildTask())
        // Send the file contents off to the SASS compiler:
        .pipe(GulpSass.sync({ outputStyle: "compressed" }).on("error", GulpSass.logError))
        // Finalize the build task:
        .pipe(RehikeBuild.GulpFinalizePathsTask())
        .pipe(gulp.dest(RehikeBuild.BASE_SRC_DIR));
}

// Main build action
exports.build = gulp.series(
    CommonStartupTask,
    gulp.parallel(
        BuildCss
    )
);

// Build everything if no arguments are provided:
exports.default = exports.build;