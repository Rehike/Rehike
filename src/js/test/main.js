/**
 * @fileoverview Test JS project for RehikekBuild.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

goog.provide("test");

function unused()
{
    console.log("this is never called");
}

/**
 * An unexported private function.
 * 
 * @returns {void}
 */
function privateTest()
{
    console.log("Hello world");
}

/**
 * An example function.
 * 
 * @returns {void}
 */
test.test = function()
{
    return privateTest();
};

test();