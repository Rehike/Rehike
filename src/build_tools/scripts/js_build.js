const { BuildTask } = require("./build_task");
const RehikeBuild = require("./rehikebuild_main");

const gulp = require("gulp");
const path = require("path");
const closureCompilerBackend = require("google-closure-compiler");
const GulpClosureCompiler = closureCompilerBackend.gulp();
const GulpPreprocess = require("gulp-preprocess");

class JSBuildTask extends BuildTask
{
    /** @inheritdoc @override */
    _buildGulpTask()
    {
        const task = this._prepareGulpBackend();
        let result = task
            .pipe(GulpPreprocess({
                includeBase: path.dirname(this.inputFileNames[0])
            }))
            .pipe(GulpClosureCompiler({
                compilation_level: "SIMPLE_OPTIMIZATIONS",
                //process_closure_primitives: true,
                language_out: "ECMASCRIPT3",
                output_wrapper: "(function(){%output%})();"
            }));
        return result;
    }
    
    /** @inheritdoc @override */
    _prepareGulpBackend()
    {
        const buildFiles = this.inputFileNames.slice(0); // .slice(0) to clone the array
        
        // Commented out because we won't use Closure Compiler's bundling method. It just seems
        // too unstable.
        // // Requirements for Closure Compiler:
        // buildFiles.push(
        //     "build_tools/node_modules/google-closure-library/closure/goog/base.js"
        // );
        
        return gulp.src(buildFiles, RehikeBuild.commonBuildCfg);
    }
}

exports.JSBuildTask = JSBuildTask;