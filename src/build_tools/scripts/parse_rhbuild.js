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
    
    let jsOutput = null;
    
    if (JS_BUILD_FILES || JS_OUTPUT_BUNDLE)
    {
        assert(JS_BUILD_FILES && JS_OUTPUT_BUNDLE);
        jsOutput = buildJsBuildMap(JS_BUILD_FILES, JS_OUTPUT_BUNDLE);
    }
    
    let taskName;
    if (TASK_NAME)
    {
        taskName = TASK_NAME;
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
        
        taskName = "Building package \"" + temp.join("/") + "\"";
    }
    
    RehikeBuild.pushSourceFiles({
        baseName: filePath,
        taskName: taskName,
        jsBuildFiles: jsOutput,
        cssBuildFiles: CSS_BUILD_FILES,
        protobufBuildFiles: PROTOBUF_BUILD_FILES,
    });
}

/**
 * Gets a JS build map from the output source.
 * 
 * @param {string[]} buildFiles
 * @param {string} outputBundleName
 * 
 * @return {object} Build sources to destination bundle name.
 */
function buildJsBuildMap(buildFiles, outputBundleName)
{
    let result = {};
    
    for (let file of buildFiles)
    {
        result[file] = outputBundleName;
    }
    
    return result;
}

exports.doParse = doParse;
exports.gulpParseRhBuild = gulpParseRhBuild;
exports.GulpSetupRhBuildTask = GulpSetupRhBuildTask;