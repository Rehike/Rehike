/**
 * @fileoverview Responsible for creating the VFL mapping.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const crypto = require("crypto");
const path = require("path");
const fs = require("fs/promises");

const RehikeBuild = require("./rehikebuild_main");

/**
 * The output destination at which to store the VFL cache.
 */
const VFL_OUTPUT_DESTINATION = "includes/static_version_map.json";

/**
 * Stores the VFL cache map.
 * 
 * This maps the original file name to its versioned file name, which allows for
 * easy lookup during runtime.
 * 
 * This is exported to the file specified in {@link VFL_OUTPUT_DESTINATION} in
 * JSON format.
 */
const g_vflMap = {};

/**
 * Generates a VFL mapping from a finished build task.
 * 
 * @param {BuildTask} buildTask Finished build task with the output data.
 */
function generateVflMapping(buildTask)
{
    if (
        buildTask.status != RehikeBuild.BuildTask.Status.FINISHING &&
        buildTask.status != RehikeBuild.BuildTask.Status.FINISHED
    )
    {
        throw new Error("Attempted to generate VFL mapping from unfinished build task");
    }
    
    let fileContents = buildTask._data.contents;
    
    // This is actually the same exact hashing algorithm as YouTube's VFL tool itself uses:
    let vflHash = crypto
        .createHash("md5")
        .update(fileContents)
        .digest("base64")
        .substring(0, 6);
        
    let origPath = buildTask.outputFileName;
        
    let basename = path.basename(origPath, path.extname(origPath));
    let newFileName = basename + "-vfl" + vflHash + path.extname(origPath);
    let newPath = RehikeBuild.unwindows(path.join(path.dirname(origPath), newFileName));
    
    console.log(newPath);
    
    g_vflMap[RehikeBuild.unwindows(origPath)] = newPath;
}

/**
 * Replaces the VFL cache file with new contents from the build results.
 */
async function generateNewCache()
{
    let fh = await fs.open(
        path.join(RehikeBuild.REHIKE_ROOT_DIR, VFL_OUTPUT_DESTINATION),
        "w"
    );
    
    console.log("Wrote VFL cache.");
    await fh.write(Buffer.from(JSON.stringify(g_vflMap, null, 4)));
    
    await fh.close();
}

exports.generateVflMapping = generateVflMapping;
exports.generateNewCache = generateNewCache;