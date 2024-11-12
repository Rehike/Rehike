/**
 * @fileoverview Release packing for final release build.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const RehikeBuild = require("./rehikebuild_main");
const path = require("path");
const fs = require("fs/promises");
const ChildProcess = require("child_process");
const chalk = require("chalk");

const { ITransformer } = require("./release_packager/transformer/ITransformer");
const { BuildNumberTransformer } = require("./release_packager/transformer/BuildNumberTransformer");
const { ConstantsTransformer } = require("./release_packager/transformer/ConstantsTransformer");

const RELEASE_BUILD_FOLDER = "release_build";

const RELEASE_BUILD_EXCLUDED_PATHS = [
    // Can't copy a folder to itself:
    RELEASE_BUILD_FOLDER,
    // Developer-specific files:
    ".git",
    ".git-hooks",
    ".github",
    ".gitattributes",
    ".gitignore",
    "docs",
    "CONTRIBUTING.md",
    "src",
    "composer.lock",
    // May contain developer private information; will be recreated:
    "cache",
    // Development logs; will be recreated with no files:
    "logs",
    // Recreated:
    ".version"
];

const MESSY_JSON_PARSER_PROXY_PHP_FILE = path.join(
    RehikeBuild.REHIKEBUILD_DIR,
    "scripts/release_packager/messy_json_parser_proxy.php"
);

const GET_BUILD_NUMBER_PHP_FILE = path.join(
    RehikeBuild.REHIKEBUILD_DIR,
    "scripts/release_packager/get_build_number.php"
);

class ReleasePackager
{
    static s_instance = null;
    
    /**
     * Builds and packs a Rehike release build.
     */
    static async buildAndPack()
    {
        this.s_instance = new ReleasePackager();
        return await this.s_instance.buildAndPackWorker();
    }
    
    async buildAndPackWorker()
    {
        try
        {
            await this.ensurePhp();
        }
        catch (e)
        {
            console.error(
                "PHP is not available on PATH or exposed to Node.js. Please double check " +
                "your configuration and try again."
            );
            return;
        }
        
        let buildNumber = await this.getRehikeBuildNumber();
        
        console.log(`Building Rehike release build ${buildNumber}...`);
        
        // Read the version information from the current .version and mark
        // it as release. This approach may be abandoned in the future.
        let versionInfo = await this.readVersionFile();
        versionInfo.isRelease = true;
        
        // Remove the build working folder to make sure that we won't have
        // any conflicts on rebuilding.
        try
        {
            await fs.rm(this.getBuildWorkingFolder(), {recursive: true});
        }
        catch (e) { /* ignore */ }
        
        await this.ensureBuildFolder();
        await this.copyRehikeFiles();
        await this.writeNewVersionFile(versionInfo);
        
        // Remake the default cache and logs folders:
        await fs.mkdir(path.join(this.getBuildWorkingFolder(), "cache"));
        await fs.mkdir(path.join(this.getBuildWorkingFolder(), "logs"));
        
        await this.transformFile(
            path.join(this.getBuildWorkingFolder(), "modules/Rehike/Version/BuildNumber.php"),
            new BuildNumberTransformer(buildNumber)
        );
        
        const constantsTransformerProps = {};
        
        // Use "Release Test" versioning for all test releases.
        // Since we're still testing the system, this is the default.
        if (true)
        {
            constantsTransformerProps.versionDisplayName = "Release Test";
        }
        
        await this.transformFile(
            path.join(this.getBuildWorkingFolder(), "includes/constants.php"),
            new ConstantsTransformer(constantsTransformerProps)
        );
        
        /*
         * TODO(izzy): Precache Twig templates and CoffeeTranslation language files?
         *
         * It would likely be useful to do this, but I have concerns about the cache
         * becoming invalid when transferred between systems due to the invalidation
         * depending on file-modified time.
         */
        
        console.log("Generated release build files successfully.");
        console.log(chalk.cyan(
            "Note that we don't currently zip the contents automatically. " +
            "You will have to zip with 7-Zip on your own."
        ));
    }
    
    /**
     * Gets the working folder to store files in.
     * 
     * @returns {string}
     */
    getBuildWorkingFolder()
    {
        return path.join(RehikeBuild.REHIKE_ROOT_DIR, RELEASE_BUILD_FOLDER);
    }
    
    /**
     * Creates the build artifacts folder.
     * 
     * @see {RELEASE_BUILD_FOLDER}
     * 
     * @returns {Promise<void>}
     */
    ensureBuildFolder()
    {
        let self = this; // I love JS
        
        return new Promise(function(resolve, reject) {
            let folderPath = self.getBuildWorkingFolder();
            
            fs.mkdir(folderPath).then(() => resolve()).catch(function(e) {
                if (e && e.code == "EEXIST")
                {
                    resolve();
                }
                
                reject();
            });
        });
    }
    
    /**
     * Copy Rehike files to the build directory.
     * 
     * @return {Promise<void>}
     */
    async copyRehikeFiles()
    {
        let dir = await fs.readdir(RehikeBuild.REHIKE_ROOT_DIR);
        
        for (let file of dir)
        {
            let absoluteFilePath = path.join(RehikeBuild.REHIKE_ROOT_DIR, file);
            
            if ((await fs.stat(absoluteFilePath)).isDirectory() && file == ".git")
            {
                // .git optimisation since it's a huge folder.
                continue;
            }
            
            if (RELEASE_BUILD_EXCLUDED_PATHS.includes(file))
            {
                // TODO(izzy): Re-enable when verbosity added:
                //console.log(`Skipping excluded path ${file}.`);
                continue;
            }
            
            let destinationName = path.join(this.getBuildWorkingFolder(), file);
            
            await fs.cp(absoluteFilePath, destinationName, { recursive: true });
        }
    }
    
    /**
     * Ensures that the PHP interpreter is available to us over here in Node land.
     * 
     * @returns {Promise<void>}
     */
    ensurePhp()
    {
        return new Promise(function(resolve, reject) {
            ChildProcess.exec("php --version", function (error, stdout, stderr) {
                if (error)
                {
                    reject();
                }
                
                if (stdout.indexOf("PHP") > -1)
                {
                    resolve();
                }
                
                reject();
            });
        });
    }
    
    /**
     * Gets the Rehike build number.
     * 
     * @returns {Promise<string>}
     */
    async getRehikeBuildNumber()
    {
        return await executePhp(GET_BUILD_NUMBER_PHP_FILE);
    }
    
    /**
     * Reads the version file.
     * 
     * For consistency, we call into the messy JSON parser from PHP. This ensures that
     * RehikeBuild will get the same result as Rehike itself.
     * 
     * @returns {Promise<object>}
     */
    async readVersionFile()
    {
        return JSON.parse(await executePhp(MESSY_JSON_PARSER_PROXY_PHP_FILE));
    }
    
    /**
     * Writes a new version file with the updated version information for the release
     * build.
     * 
     * @param {object} versionInfo 
     * @returns {Promise<void>}
     */
    async writeNewVersionFile(versionInfo)
    {
        fs.writeFile(path.resolve(this.getBuildWorkingFolder(), ".version"), JSON.stringify(versionInfo));
    }
    
    /**
     * Applies a simple transformation to a Rehike source code file.
     * 
     * @param {string} filePath
     * @param {ITransformer} transformer
     */
    async transformFile(filePath, transformer)
    {
        let originalContent = await fs.readFile(filePath);
        await fs.writeFile(filePath, transformer.transform(originalContent.toString()));
    }
}

/**
 * Runs a PHP script and gets the result from it.
 * 
 * @throws {string} PHP stderr
 * 
 * @param {string} phpScript Path to the PHP script
 * @returns {Promise<string>} PHP stdout
 */
function executePhp(phpScript, options = {})
{
    return new Promise(function(resolve, reject) {
        let process = ChildProcess.exec(`php ${phpScript}`, {
            cwd: RehikeBuild.REHIKE_ROOT_DIR
        }, function(error, stdout, stderr) {
            if (error || process.exitCode != 0)
            {
                reject(stderr);
                return;
            }
            
            if (options.printStderr !== false && stderr.trim() != "")
            {
                console.log(stderr.trim());
            }
            
            resolve(stdout);
        });
    });
}

exports.ReleasePackager = ReleasePackager;