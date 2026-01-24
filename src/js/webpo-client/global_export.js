/**
 * @fileoverview Implementation of the global export for the PO token generator.
 * 
 * On the standard WEB client, the YouTube player shares the PO token generator with the Kevlar
 * main script via a globally-exported interface. This script creates an object with the same
 * necessary interface in order to report our own PO tokens to the player.
 * 
 * In order for the YouTube player to request PO tokens from this source, the experiment flags
 * "bg_st_hr" and "html5_use_shared_owl_instance" must be set.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

// The below are just interface definitions and not compiled.
// @ifdef INTERFACE
/**
 * Interface for the arguments parameter passed to the "mint PO token" function,
 * mws().
 * 
 * @interface
 */
class IMintPoTokenArgs
{
    /**
     * The identifier of the content being minted.
     * 
     * For example, in the case of a video, this will be the encrypted video ID,
     * as found in the URL.
     * 
     * @type {string}
     */
    c;
    
    /**
     * Currently unknown. If I had to guess, this would be for session-based POTs.
     * 
     * @type {string|null}
     */
    e;
    
    /**
     * Currently unknown. If I had to guess, this is a boolean denoting that content-backed
     * POTs are used.
     * 
     * @type {boolean}
     */
    mc;
    
    /**
     * Currently unknown. If I had to guess, this is a boolean denoting that session-backed
     * POTs are used. The experiments can overlap, so it's theoretically possible that both
     * booleans can be set without this one taking effect.
     * 
     * @type {boolean}
     */
    me;
}

/**
 * Interface for the WebPO client retrieved from the havuokmhhs-0 wpc()
 * callback.
 * 
 * @interface
 */
class IWebPoClient
{
    /**
     * The purpose of this function is currently unknown. It is not necessary to
     * functionality, but it is exported by the Kevlar interface.
     */
    c(obj) { return false; }
    
    /**
     * The purpose of this function is currently unknown. It is not necessary to
     * functionality, but it is exported by the Kevlar interface.
     */
    f() { return null; }
    
    /**
     * The purpose of this function is currently unknown. It is not necessary to
     * functionality, but it is exported by the Kevlar interface.
     */
    m() { return new Uint8Array(); }
    
    /**
     * Mints a new PO token for the given content.
     * 
     * @param {IMintPoTokenArgs} args
     * @returns {string} The final PO token.
     */
    mws(args) { return ""; }
}

/** @interface */
class IBevasrs
{
    /**
     * Retrieves a reference to the WebPO client.
     * 
     * @returns {Promise<IWebPoClient>}
     */
    wpc() {}
}

/**
 * Interface for the top-level object which exports the WebPO client (and probably other
 * attestation things) from Kevlar JS.
 *  
 * @interface
 */
class IHavuokmhhs
{
    /**
     * @type {IBevasrs}
     */
    bevasrs;
}
// @endif // INTERFACE

/** @constant */
var c_resolvedPromise = Promise.resolve();

/**
 * Implements the Rehike WebPO client used for interfacing with the YouTube
 * player.
 * 
 * @class
 * @implements {IWebPoClient}
 */
module.WebPoClient = function()
{
    this.minter = new module.PoTokenMinter();
};

/**
 * Initialises the WebPO client.
 * 
 * @returns {Promise}
 */
module.WebPoClient.prototype.initialize = function()
{
    return this.minter.initialize();
};

/**
 * @type {module.PoTokenMinter}
 */
module.WebPoClient.prototype.minter = null;

module.WebPoClient.prototype.c = function(obj)
{
    // TODO(kawapure): Figure out what Kevlar does for this exactly. It seems
    // to be fine with always being true, though.
    return true;
};

module.WebPoClient.prototype.f = function(obj)
{
    // TODO(kawapure): I believe this causes a race condition.
    // I guess as to the actual behaviour is that this promise is contingent
    // upon prerequistes for the WebPO client (i.e. BotGuard) being available,
    // and it returns a pending promise for that case.
    return c_resolvedPromise;
};

module.WebPoClient.prototype.m = function()
{
    if (args.mc)
    {
        return this.minter.mintAsU8Array(args.c);
    }
    
    throw new Error("Unhandled case.");
};

module.WebPoClient.prototype.mws = function(args)
{
    if (args.mc)
    {
        return this.minter.mint(args.c);
    }
    
    throw new Error("Unhandled case.");
};

/** @static */
module.s_webpoClient = new module.WebPoClient();

// Initialise the havuokmhhs interface:
/** @constant */
var c_havuokmhhsName = "havuokmhhs-0";

if (!(c_havuokmhhsName in window))
{
    /** @implements {IHavuokmhhs}  */
    window[c_havuokmhhsName] = {
        bevasrs: {
            wpc: function()
            {
                // Defer resolution until all required state is ready:
                return Promise.all([
                    // Wait for the global WebPO client to be initialised:
                    new Promise(function(resolve, reject)
                    {
                        if (module.s_webpoClient.initialized)
                        {
                            resolve();
                        }
                        else
                        {
                            module.s_webpoClient.initialize().then(resolve);
                        }
                    }),
                ]).then(function() { return module.s_webpoClient; });
            }
        }
    };
}