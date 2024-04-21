/**
 * @fileoverview Parsing utilities for rhbuild files.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const through2 = require("through2");
const RehikeBuild = require("./rehikebuild_main");

/**
 * Gulp task for setting up .rhbuild files.
 */
function GulpSetupRhBuildTask()
{
    return gulp.src("**/.rhbuild", { cwd: RehikeBuild.BASE_SRC_DIR })
        .pipe(gulpParseRhBuild());
}

/**
 * Gulp object wrapper for parsing .rhbuild files.
 */
function gulpParseRhBuild()
{
    return through2.obj(function (file, encoding, callback)
    {
        let fileContents = file.contents.toString();
        doParse(file.path, fileContents);
        callback();
    });
}

/**
 * Sets up the parsing environment and parses a .rhbuild file.
 * 
 * @param {string} scriptContents Contents of an .rhbuild file.
 */
function doParse(filePath, scriptContents)
{
    let JS_BUILD_FILES = null;
    let CSS_BUILD_FILES = null;
    let PROTOBUF_BUILD_FILES = null;
    
    function runInParserContext()
    {
        eval(scriptContents);
    }
    
    runInParserContext();
    
    RehikeBuild.pushSourceFiles({
        baseName: filePath,
        jsBuildFiles: JS_BUILD_FILES,
        cssBuildFiles: CSS_BUILD_FILES,
        protobufBuildFiles: PROTOBUF_BUILD_FILES,
    });
}

exports.doParse = doParse;
exports.gulpParseRhBuild = gulpParseRhBuild;
exports.GulpSetupRhBuildTask = GulpSetupRhBuildTask;