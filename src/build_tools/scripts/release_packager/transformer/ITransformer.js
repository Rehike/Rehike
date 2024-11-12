/**
 * @fileoverview Interface for transformers.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

/**
 * A transform class transforms the contents of a file for the release build.
 * 
 * @interface
 */
class ITransformer
{
    /**
     * Retrieves the transformed content of the file.
     * 
     * @param {string} originalContent The original content of the file.
     * 
     * @return {string} The modified content
     */
    transform(originalContent) {}
}

exports.ITransformer = ITransformer;