/**
 * @fileoverview Parsing utilities for rhbuild files.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const gulp = require("gulp");
const path = require("path");
const through2 = require("through2");
const RehikeBuild = require("./rehikebuild_main");
const assert = require("assert/strict");

/**
 * Gulp task for setting up .rhbuild files.
 */
function GulpSetupRhBuildTask(buildProjects = [])
{
    let inputSource = "**/.rhbuild";
    
    // If we're given a list of build packages to parse, then we want to only specify
    // those in the build sources. Since the package names correspond to the file-system
    // layout, we just build a static list.
    if (buildProjects.length > 0)
    {
        inputSource = buildProjects.map(item => `${item}/.rhbuild`);
    }
    
    return gulp.src(inputSource, { cwd: RehikeBuild.BASE_SRC_DIR })
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
    let TASK_NAME = null;
    let JS_BUILD_FILES = null;
    let JS_OUTPUT_BUNDLE = null;
    let CSS_BUILD_FILES = null;
    let PROTOBUF_BUILD_FILES = null;
    
    function runInParserContext()
    {
        eval(scriptContents);
    }
    
    runInParserContext();
    
    let gulpTaskName;
    if (TASK_NAME)
    {
        gulpTaskName = TASK_NAME;
    }
    else
    {
        let temp = path.dirname(filePath).split(path.sep);
        
        for (let i = 0, len = temp.length; i < len; i++)
        {
            if (temp[i] == "src")
            {
                temp = temp.slice(i + 1);
                break;
            }
        }
        
        gulpTaskName = "@RHBUILD::PACKAGE::" + temp.join("/");
    }
    
    RehikeBuild.pushSourceFiles({
        baseName: filePath,
        taskName: gulpTaskName,
        jsBuildFiles: JS_BUILD_FILES,
        jsOutputBundle: JS_OUTPUT_BUNDLE,
        cssBuildFiles: CSS_BUILD_FILES,
        protobufBuildFiles: PROTOBUF_BUILD_FILES,
    });
}

exports.doParse = doParse;
exports.gulpParseRhBuild = gulpParseRhBuild;
exports.GulpSetupRhBuildTask = GulpSetupRhBuildTask;