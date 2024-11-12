/**
 * @fileoverview Transformer for Rehike\Constants namespace.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const { ITransformer } = require("./ITransformer");

/**
 * @implements {ITransformer}
 */
class ConstantsTransformer
{
    /**
     * @type {?string}
     */
    _versionDisplayName = null;
    
    constructor(opts = {})
    {
        if (opts.versionDisplayName)
            this._versionDisplayName = opts.versionDisplayName;
    }
    
    transform(originalContent)
    {
        let content = originalContent;
        
        if (this._versionDisplayName)
        {
            content = content.replace(
                /const\s+VERSION\s*\=\s*\".*?\";/s,
                "const VERSION = " + JSON.stringify(this._versionDisplayName) + ";"
            );
        }
        
        return content.replace(
            "const IS_RELEASE = false;",
            "const IS_RELEASE = true;"
        );
    }
}

exports.ConstantsTransformer = ConstantsTransformer;