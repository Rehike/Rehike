/**
 * @fileoverview Debugging helpers for the Rehike WebPO client.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

function logAndThrow()
{
    logError.apply(this, arguments);
    throw Error.apply(this, arguments);
}

function logAndReject()
{
    logError.apply(this, arguments);
    return Promise.reject(arguments[0]);
}

function logError(message)
{
	var args = [].slice.call(arguments);
	args = ["[Rehike.WebPoClient]"].concat(args);
    console.error.apply(this, args);
}

function log(message)
{
	var args = [].slice.call(arguments);
	args = ["[Rehike.WebPoClient]"].concat(args);
    console.log.apply(this, args);
}