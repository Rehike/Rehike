const { BuildTask, g_buildTaskRegistry } = require("./build_task");

const sassBackend = require("sass");
const gulpSassBackend = require("gulp-sass");
const GulpSass = gulpSassBackend(sassBackend);
const through2 = require("through2");
const path = require("path");
const fs = require("fs");
const imageSize = require("image-size");
const RehikeBuild = require("./rehikebuild_main");

class CSSBuildTask extends BuildTask
{
    /**
     * Should we do a 2x resource build too?
     * 
     * @type {boolean}
     */
    do2xBuild = false;
    
    /**
     * A reference to the original descriptor given to the constructor.
     * 
     * This is cloned and modified for preparing the 2x build task.
     */
    descriptor = null;
    
    /**
     * Are we currently doing a 2x resource build?
     * 
     * @type {boolean}
     */
    is2xBuild = false;
    
    constructor(descriptor, inputFileNames, outputFileName)
    {
        super(descriptor, inputFileNames, outputFileName);
        
        this.descriptor = descriptor;
        
        if (descriptor.css2xBuild && !descriptor.cssIsCurrently2xBuildTask)
        {
            this.do2xBuild = true;
        }
        
        if (descriptor.cssIsCurrently2xBuildTask)
        {
            this.is2xBuild = true;
        }
    }
    
    /** @inheritdoc @override */
    _buildGulpTask()
    {
        console.log("CSSBuildTask._buildGulpTask");
        const task = this._prepareGulpBackend();
        let currentBuildTask = this;
        let result = task
            .pipe(through2.obj(function(file, encoding, callback) {
                file.contents = Buffer.from(currentBuildTask._doRehikeSpriteTransform(file.contents.toString()));
                this.push(file);
                callback();
            }))
            .pipe(GulpSass.sync({ outputStyle: "compressed" }).on("error", GulpSass.logError))
            .pipe(through2.obj(function(file, encoding, callback) {
                // Temporary testing code: 
                if (process.argv.includes("--test-branched-build-task"))
                {
                    let test = new BuildTask({taskName: "Fake test task."}, "fake", "fake");
                    g_buildTaskRegistry.push(test);
                }
                
                if (currentBuildTask.do2xBuild)
                {
                    let descriptor2x = JSON.parse(JSON.stringify(currentBuildTask.descriptor));
                    descriptor2x.cssIsCurrently2xBuildTask = true;
                    
                    console.log("aaaa: " + currentBuildTask._determine2xPath(currentBuildTask.outputFileName));
                    
                    let buildTask2x = new CSSBuildTask(
                        descriptor2x, 
                        currentBuildTask.inputFileNames[0],
                        currentBuildTask._determine2xPath(currentBuildTask.outputFileName)
                    );
                    
                    buildTask2x.displayName += "@2x";
                    
                    g_buildTaskRegistry.push(buildTask2x);
                }
                
                this.push(file);
                callback();
            }));
        return result;
    }
    
    _determine2xPath(originalPath)
    {
        let extension = path.extname(originalPath);
        let base = originalPath.split(extension)[0];
        return base + "-2x" + extension;
    }
    
    /**
     * 
     * @param {} originalContent 
     */
    _doRehikeSpriteTransform(originalContent)
    {
        try
        {
            let rehikeSpriteCalls = originalContent.matchAll(/\@include\s+rehike\.sprite\s*\((?<arguments>.*?)\)\s*;/g);
            let result = originalContent;
            
            for (let fnCall of rehikeSpriteCalls)
            {
                //console.log(fnCall);
                
                let args = fnCall.groups.arguments;
                let parts = args.split(",");
                
                if (parts.length != 5)
                {
                    throw new Error("epic fail!");
                }
                
                for (let part of parts)
                {
                    part = part.trim();
                }
                
                for (let i = 0; i < parts.length; i++)
                    parts[i] = parts[i].trim();
                
                // Scale multiplier
                let scale = 1;
                let originalWidthFor2x = 0;
                let originalHeightFor2x = 0;
                
                if (this.is2xBuild)
                {
                    let originalDimensions = imageSize(parts[0].replace("/rehike/static", RehikeBuild.REHIKE_ROOT_DIR + "/static").replace(/"/g, ""));
                    
                    originalWidthFor2x = originalDimensions.width || 0;
                    originalHeightFor2x = originalDimensions.height || 0;
                    
                    parts[0] = this._determine2xPath(parts[0]);
                    scale = 2;
                }
                
                let newText =
                    `background: no-repeat url(${parts[0].replace(/\"/g, "")}) -${parts[1]}px -${parts[2]}px;\n` +
                    (this.is2xBuild
                        ? `background-size: ${originalWidthFor2x}px ${originalHeightFor2x}px;`
                        : ``
                    ) +
                    `width: ${parts[3]}px;\n` +
                    `height: ${parts[4]}px;`;
                    
                result = result.replace(fnCall[0], newText);
            }
            
            console.log(result);
            
            return result;
        }
        catch (e)
        {
            console.error(e);
            return "";
        }
    }
    
    // /**
    //  * Transforms URLs in the final CSS to 
    //  * 
    //  * @param {string} originalContent 
    //  */
    // _do2xTransform(originalContent)
    // {
    //     try
    //     {
    //         //let filePaths = [];
    //         let result = originalContent;
            
    //         let filePaths = originalContent.matchAll(/url\s*\(\s*.*?(?<rehikeStaticUrl>\/rehike\/static\/.*?\.\w+)/g);
            
    //         for (let path of filePaths)
    //         {
    //             //console.log(path);
                
    //             if (path.groups)
    //             {
    //                 let url = path.groups.rehikeStaticUrl;
                    
    //                 if (this._validate2xUrl(url))
    //                 {
    //                     result = result.replace(url, this._determine2xPath(url));
    //                 }
    //             }
    //         }
    //         return result;
    //     }
    //     catch (e)
    //     {
    //         console.error(e);
    //         return "";
    //     }
    // }
    
    /**
     * @param {string} path
     * @return {boolean}
     */
    _validate2xUrl(path)
    {
        if (path.startsWith("rehike/static/"))
        {
            if (fs.existsSync(RehikeBuild.REHIKE_ROOT_DIR + path.substring("rehike/".length)))
            {
                return true;
            }
        }
        
        return false;
    }
}

exports.CSSBuildTask = CSSBuildTask;