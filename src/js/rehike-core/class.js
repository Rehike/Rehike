/**
 * @fileoverview CSS class utilities for Rehike Core JS.
 * 
 * @author The Rehike Maintainers
 */

rehike.class = {};

/**
 * Determine if an element has a class.
 * 
 * @param {Element} element 
 * @param {string} className 
 * @return {boolean}
 */
rehike.class.has = function(element, className)
{
    if ("" == className) return;

    if (element.classList)
    {
        return element.classList.contains(className) ? true : false;
    }
    else
    {
        return element.getAttribute("class").indexOf(className) > -1;
    }
};

/**
 * Add a class to an element.
 * 
 * @param {Element} element 
 * @param {string} className 
 */
rehike.class.add = function(element, className)
{
    if ("" == className) return;

    if (element.classList)
    {
        element.classList.add(className);
    }
    else
    {
        element.setAttribute("class", element.getAttribute("class") + " " + className);
    }
};

/**
 * Remove a class from an element.
 * 
 * @param {Element} element 
 * @param {string} className 
 */
rehike.class.remove = function(element, className)
{
    if ("" == className) return;

    if (element.classList)
    {
        element.classList.remove(className);
    }
    else
    {
        element.setAttribute("class", element.getAttribute("class").replace(className, ""));
    }
};