/**
 * @fileoverview Manages Gulp use in RehikeBuild.
 * 
 * @author Isabella <kawapure@gmail.com>
 */

const gulp = require("gulp");
const chalk = require("chalk");

/**
 * Sets up logging.
 * 
 * @param {gulp.Gulp} gulp 
 */
function setupLogging(gulp)
{
    gulp.on("start", function(event)
    {
        let info = parseLogCommand(event.name);
        
        if (!info.noLog)
        {
            let logMsg = "";
            
            if (info.isPackage)
            {
                logMsg = `Starting build for package "${chalk.cyan(info.baseName)}"...`;
            }
            else
            {
                logMsg = info.baseName;
            }
            
            console.log(logMsg);
        }
    });
    
    gulp.on("stop", function(event)
    {
        let info = parseLogCommand(event.name);
        
        if (!info.noLog)
        {
            let logMsg = "";
            
            if (info.isPackage)
            {
                logMsg = `Finished build for package "${chalk.cyan(info.baseName)}" in ${chalk.magenta(formatHrTime(event.duration))}`;
            }
            else
            {
                logMsg = info.baseName;
            }
            
            console.log(logMsg);
        }
    });
    
    gulp.on("error", function(event)
    {
        // This error logging code sucks. Consider cleaning up when errors become prominent.
        let info = parseLogCommand(event.name);
        
        console.log(`${chalk.red("Error in " + event.name + ": ")} ${JSON.stringify(event)}`);
    });
}

setupLogging(gulp);

/**
 * Runs a Gulp task.
 * 
 * @param {gulp.TaskFunction} task 
 * @param {function()} cb 
 */
function runGulpTask(task, cb = null)
{
    // https://github.com/gulpjs/gulp-cli/blob/master/lib/versioned/%5E4.0.0/index.js#L74
    task(function(err)
    {
        if (err)
        {
            console.error(err);
        }
        
        if (cb)
        {
            cb();
        }
    });
}

/**
 * Parses a log command.
 * 
 * @param {string} logCommand 
 */
function parseLogCommand(logCommand)
{
    let out = {
        baseName: logCommand,
        noLog: false,
        isPackage: false
    };
    
    if (logCommand.startsWith("@RHBUILD::"))
    {
        let command = logCommand.substring("@RHBUILD::".length);
        
        if (command == "NOLOG")
        {
            out.baseName = "";
            out.noLog = true;
        }
        else if (command.startsWith("PACKAGE::"))
        {
            let packageName = command.substring("PACKAGE::".length);
            
            out.baseName = packageName;
            out.isPackage = true;
        }
    }
    
    return out;
}

// Code taken from Gulp.
var units = [
    ['h', 3600e9],
    ['min', 60e9],
    ['s', 1e9],
    ['ms', 1e6],
    ['μs', 1e3],
];
  
function formatHrTime(hrtime)
{
    if (!Array.isArray(hrtime) || hrtime.length !== 2)
    {
        return '';
    }
    if (typeof hrtime[0] !== 'number' || typeof hrtime[1] !== 'number')
    {
        return '';
    }

    var nano = hrtime[0] * 1e9 + hrtime[1];

    for (var i = 0; i < units.length; i++)
    {
        if (nano < units[i][1])
        {
            continue;
        }

        if (nano >= units[i][1] * 10)
        {
            return Math.round(nano / units[i][1]) + ' ' + units[i][0];
        }

        var s = String(Math.round(nano * 1e2 / units[i][1]));
        if (s.slice(-2) === '00')
        {
            s = s.slice(0, -2);
        } else if (s.slice(-1) === '0')
        {
            s = s.slice(0, -2) + '.' + s.slice(-2, -1);
        } else
        {
            s = s.slice(0, -2) + '.' + s.slice(-2);
        }
        return s + ' ' + units[i][0];
    }

    if (nano > 0)
    {
        return nano + ' ns';
    }

    return '';
}
// end gulp code

exports.runGulpTask = runGulpTask;