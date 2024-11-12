/**
 * @fileoverview Command-line arguments parsing for RehikeBuild.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const SHORT_ALIASES = {
    "p": "package",
    "v": "verbose",
};

let g_argCache = null;

/**
 * Gets the command-line arguments passed to RehikeBuild.
 * 
 * @returns {object} Key/value map of the arguments
 */
function getArgs()
{
    if (g_argCache)
        return g_argCache;
    
    const args = process.argv;
    
    let result = {};
    let currentKey = null;
    
    for (const arg of args)
    {
        if (arg.substring(0, 2) == "--")
        {
            currentKey = arg.substring(2);
            result[currentKey] = [];
        }
        else if (arg[0] == "-" && arg[1] != "-")
        {
            currentKey = SHORT_ALIASES[arg.substring(1)] || arg.substring(1);
            result[currentKey] = [];
        }
        else
        {
            if (result[currentKey])
                result[currentKey].push(arg);
        }
    }
    
    for (const key in result)
    {
        if (result[key].length == 0)
        {
            result[key] = true;
        }
        else if (result[key].length == 1 && ["false", "true"].includes(result[key][0].toLowerCase()))
        {
            result[key] = "true" == result[key][0].toLowerCase() ? true : false;
        }
    }
    
    g_argCache = result;
    return result;
}

exports.getArgs = getArgs;