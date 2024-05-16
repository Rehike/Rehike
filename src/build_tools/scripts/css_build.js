const { BuildTask } = require("./build_task");

const sassBackend = require("sass");
const gulpSassBackend = require("gulp-sass");
const GulpSass = gulpSassBackend(sassBackend);
const through2 = require("through2");

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

exports.CSSBuildTask = CSSBuildTask;