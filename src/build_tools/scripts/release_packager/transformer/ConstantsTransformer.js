/**
 * @fileoverview Transformer for Rehike\Constants namespace.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

const { ITransformer } = require("./ITransformer");

/**
 * @implements {ITransformer}
 */
class ConstantsTransformer
{
    transform(originalContent)
    {
        return originalContent.replace(
            "const IS_RELEASE = false;",
            "const IS_RELEASE = true;"
        );
    }
}

exports.ConstantsTransformer = ConstantsTransformer;