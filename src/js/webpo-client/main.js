/**
 * @fileoverview Rehike WebPO token generation implementation.
 * 
 * This module is responsible for generating proof-of-origin tokens (PO tokens) for the YouTube
 * player WEB client, which uses Google's BotGuard.
 * 
 * Starting in January 2026, WebPO tokens cannot be generated via the legacy Waa/Create endpoint
 * that they were using, which means that they must use InnerTube's att/get endpoint to create the
 * BotGuard client. However, the YouTube player does not come with any built-in functionality to
 * use this endpoint over the legacy endpoint, which means that it can no longer mint valid PO
 * tokens on its own.
 * 
 * As a result, Rehike must mint its own PO tokens and hack the player to report these custom PO
 * tokens to it.
 * 
 * Special thanks to Reprety for tremendous amounts of help.
 * Special thanks to LuanRT for other reference material.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

/* @include ../shared/delayload_stub.js */(function(){

rehike.webpo = (window.rehike && window.rehike.webpo) || {};

/** Module quick idenitifer. @constant */
var module = rehike.webpo;

// @include debug.js
// @include util.js
// @include botguard.js

// IMPORTANT: global_export.js is included at the end of this file as it relies on
// PoTokenMinter, which is defined within this file.

/** @class */
module.PoTokenMinter = function() {};

/**
 * Is the WebPO token minter initialised?
 * 
 * @type {boolean}
 */
module.PoTokenMinter.prototype.initialized = false;

/**
 * Integrity token.
 * 
 * @type {string}
 */
module.PoTokenMinter.prototype.integrityToken = null;

/**
 * Array of post-process functions.
 * 
 * @type {Array<(string) => Uint8Array>}
 */
module.PoTokenMinter.prototype.postProcessFunctions = null;

/**
 * Function from BotGuard which effectively acquires the PO token for a given identity.
 */
module.PoTokenMinter.prototype.acquirePo = null;

/**
 * Initialises the WebPO token minter.
 * 
 * @returns {Promise<void>}
 */
module.PoTokenMinter.prototype.initialize = function()
{
    var self = this;
    return module.botguard.getClient()
        .then(function(bgClient)
        {
            return module.botguard.getIntegrityTokenContext();
        })
        .then(function(itContext)
        {
            self.integrityToken = itContext.integrityTokenResponse[0];
            self.postProcessFunctions = itContext.webPoSignalOutput;
            
            if (!self.postProcessFunctions || !self.postProcessFunctions[0])
            {
                logAndThrow("No postprocessing function found in webPoSignalOutput.");
            }
            
            var processFunc = self.postProcessFunctions[0];
            var u8Token = base64ToU8(self.integrityToken);
            var acquirePo = processFunc(u8Token);
            
            if (typeof acquirePo !== "function")
            {
                logAndThrow("acquirePo is not a function.");
            }
            
            self.acquirePo = acquirePo;
            self.initialized = true;
        });
};

/**
 * Mints a WebPO token and returns a Uint8Array.
 * 
 * @param {string} identity  The identity for which to generate the PO token. In the case of
 *                            content-based PO tokens, for videos, this will be the ID of the
 *                            video in question. In the case of session-based PO tokens, this
 *                            will be the user's GAIA ID or visitor ID.
 * @param {boolean} opt_noLog  Do not log the result.
 * 
 * @return {Uint8Array}  Raw bytes of the PO token.
 */
module.PoTokenMinter.prototype.mintAsU8Array = function(identity, opt_noLog)
{
    if (!opt_noLog)
        opt_noLog = false;
    
    if (!this.initialized)
    {
        throw new Error("The WebPO token minter is not yet initialised.");
    }
    
    var identityU8 = (new TextEncoder()).encode(identity);
    var poBuffer = this.acquirePo(identityU8);
    if (!opt_noLog)
        log("Generated POT (as U8 array):", u8ToBase64(poBuffer, true));
    return poBuffer;
};

/**
 * Mints a WebPO token.
 * 
 * @param {string} identity  The identity for which to generate the PO token. In the case of
 *                            content-based PO tokens, for videos, this will be the ID of the
 *                            video in question. In the case of session-based PO tokens, this
 *                            will be the user's GAIA ID or visitor ID.
 * 
 * @return {string}  Base64-encoded PO token string.
 */
module.PoTokenMinter.prototype.mint = function(identity)
{
    var poToken = u8ToBase64(this.mintAsU8Array(identity, true), true);
    log("Generated POT:", poToken);
    return poToken;
};

// @include global_export.js

}); // intentionally not called for the delayload stub