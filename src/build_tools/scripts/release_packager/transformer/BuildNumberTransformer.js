/**
 * @fileoverview Transformer for Rehike\Version\BuildNumber
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const { ITransformer } = require("./ITransformer");

/**
 * @implements {ITransformer}
 */
class BuildNumberTransformer
{
    /**
     * The build number to hardcode.
     * 
     * @type {string} String encoding a integer number literal.
     */
    buildNum = "0";
    
    constructor(buildNum)
    {
        this.buildNum = buildNum;
    }
    
    transform(originalContent)
    {
        return originalContent.replace(
            /public static function getBuildNumber\(\): int\s*\{.*return.*?;\s*\}/s,
            `public static function getBuildNumber(): int { return ${this.buildNum}; }`
        );
    }
}

exports.BuildNumberTransformer = BuildNumberTransformer;