/**
 * @fileoverview Main implementations for RehikeBuild
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const path = require("path");
const assert = require("assert/strict");

const { 
    BuildTask, 
    g_buildTaskRegistry: buildTaskRegistry 
} = require("./build_task");

// Includes should be relative to the src/ directory, and this script resides in
// src/build_tools/scripts, so we need to up two directories.
const BASE_SRC_DIR = path.resolve(__dirname, "../..");

// Rehike root directory is three directories up from here.
const REHIKE_ROOT_DIR = path.resolve(__dirname, "../../..");

// Build task backends:
const { CSSBuildTask } = require("./css_build");
const { JSBuildTask } = require("./js_build");

/**
 * Common build configuration options.
 */
const commonBuildCfg = {
    base: BASE_SRC_DIR,
    root: BASE_SRC_DIR,
    cwd: BASE_SRC_DIR,
};

/**
 * Pushes a list of source files from a .rhbuild file to the global list.
 */
function pushSourceFiles(descriptor)
{
    assert(typeof descriptor.baseName == "string", JSON.stringify(descriptor));
    
    const basePath = path.dirname(descriptor.baseName);
    
    // Common function to decorate and push entries for all languages, assuming they
    // exist.
    function buildSourceToSource(descriptor, srcEntry, languageName)
    {
        assert(typeof srcEntry == "object");

        for (let entryKey in srcEntry)
        {
            // Resolve the full path of the file (relative from the src/ directory).
            // This is necessary because the keys for these maps in .rhbuild files
            // are relative to the .rhbuild file path.
            let fullEntryPath = unwindows(path.resolve(basePath, entryKey));
                
            // The destination path is always relative to the Rehike root directory.
            const normalizedDestPath = unwindows(srcEntry[entryKey]);
            
            let buildTask = null;
            
            switch (languageName)
            {
                case "css":
                    buildTask = new CSSBuildTask(descriptor, fullEntryPath, normalizedDestPath);
                    break;
            }
            
            if (buildTask)
            {
                buildTaskRegistry.push(buildTask);
            }
        }
    }
    
    function buildManyToOne(descriptor, srcEntries, outputBundle, languageName)
    {
        assert(typeof srcEntries == "object");
        assert(typeof outputBundle == "string");
        
        // Resolutions of the full paths of the files (relative from the src/ directory).
        const fullEntryPaths = [];
        
        // The destination path is always relative to the Rehike root directory.
        const normalizedDestPath = unwindows(outputBundle);
        
        for (let entry of srcEntries)
        {
            fullEntryPaths.push(
                unwindows(path.resolve(basePath, entry))
            );
        }
        
        let buildTask = null;
        
        switch (languageName)
        {
            case "js":
                buildTask = new JSBuildTask(descriptor, fullEntryPaths, normalizedDestPath);
                break;
        }
        
        if (buildTask)
        {
            buildTaskRegistry.push(buildTask);
        }
    }
    
    if (descriptor.cssBuildFiles != null)
        buildSourceToSource(descriptor, descriptor.cssBuildFiles, "css");
    
    if (descriptor.jsBuildFiles != null)
        buildManyToOne(descriptor, descriptor.jsBuildFiles, descriptor.jsOutputBundle, "js");

    if (descriptor.protobufBuildFiles != null)
        buildSourceToSource(descriptor, descriptor.protobufBuildFiles, "protobuf");
}

/**
 * Converts a path using Windows separators (\) to Unix ones (/).
 * 
 * @param {string} pathToModify
 * @return {string}
 */
function unwindows(pathToModify)
{
    return pathToModify.replace(new RegExp("\\" + path.sep, "g"), "/");
}

// Exported constants:
exports.BASE_SRC_DIR = BASE_SRC_DIR;
exports.REHIKE_ROOT_DIR = REHIKE_ROOT_DIR;

// Exported classes:
exports.BuildTask = BuildTask; // re-exported

// Exported functions:
exports.pushSourceFiles = pushSourceFiles;
exports.unwindows = unwindows;

// Namespace aliases:
exports.Parser = require("./parse_rhbuild");