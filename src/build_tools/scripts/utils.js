/**
 * @fileoverview Utilities for RehikeBuild.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

/**
 * Flattens a multi-dimensional object to a single-dimensional object.
 * 
 * This returns basically a copy of the input object with all properties
 * of all child objects pushed out to the root.
 * 
 * @param {object} obj 
 * @returns {object}
 */
function flattenObject(obj)
{
    var flattened = {};
    
    Object.keys(obj).forEach(key => {
        const value = obj[key];
        
        if (typeof value == "object" && value != null && !Array.isArray(value))
        {
            Object.assign(flattened, flattenObject(value));
        }
        else
        {
            flattened[key] = value;
        }
    });
    
    return flattened;
}

exports.flattenObject = flattenObject;