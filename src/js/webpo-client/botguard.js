/**
 * @fileoverview Manages BotGuard and integrity tokens generation.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */

/** @constant */
var c_generateItUrl = "https://jnn-pa.googleapis.com/$rpc/google.internal.waa.v1.Waa/GenerateIT";
/** @constant */
var c_wpoRequestKey = "O43z0dpjhgX20SCx4KAo";

module.botguard = {};

/**
 * Is the BotGuard client initialised?
 * 
 * @type {boolean}
 */
module.botguard.initialized = false;

/**
 * Reference to the wrapped BotGuard virtual machine API.
 * 
 * @type {object}
 */
module.botguard.api = null;

/**
 * The integrity token that will be used throughout the application session.
 * 
 * @type {object|null}
 */
module.botguard.integrityToken = null;

/**
 * Determines if the cached state is sufficient.
 * 
 * @returns {boolean}
 */
module.botguard.isCacheSufficient = function()
{
    return module.botguard.initialized &&
        module.botguard.integrityToken;
};

/**
 * Gets a reference to the BotGuard client. If the BotGuard client is not yet loaded,
 * then it will be loaded.
 * 
 * @returns {Promise<object>}
 */
module.botguard.getClient = function()
{
    if (module.botguard.api)
    {
        return Promise.resolve(module.botguard.api);
    }
    
    if (!yt || !yt.config_)
    {
        return logAndReject("Called too early.");
    }
    
    if (!("REHIKE_ATT" in yt.config_))
    {
        return logAndReject("Unable to create the botguard client. Missing yt.config_.REHIKE_ATT.");
    }
    
    var att = yt.config_.REHIKE_ATT;
    
    return module.botguard.runBotguardScript_(att.bgChallenge)
        .then(function(vm)
        {
            var result = module.botguard.wrapBotguardInterface_(vm, att);
            module.botguard.api = result;
            rehike.pubsub.publish("webpo-botguard-vm-api-available", result);
            return result;
        });
};

/**
 * Gets the integrity token, or generates one if one is unavailable.
 * 
 * @returns {Promise<object>}
 */
module.botguard.getIntegrityTokenContext = function()
{
    if (module.botguard.integrityToken)
    {
        return Promise.resolve(module.botguard.integrityToken);
    }
    
    return module.botguard.generateIntegrityTokenContext();
};

/**
 * Generates an integrity token for verifying the integrity of the runtime environment.
 * 
 * @returns {Promise<object>}
 */
module.botguard.generateIntegrityTokenContext = function()
{
    var webPoSignalOutput = [];
    return module.botguard.getClient()
        .then(function(bgClient)
        {
            return new Promise(function(resolve)
            {
                bgClient.vmFunctions.asyncSnapshotFunction(function(bgResponse)
                {
                    resolve({
                        botguardResponse: bgResponse,
                        webPoSignalOutput: webPoSignalOutput
                    });
                }, [, , webPoSignalOutput]);
            });
        })
        .then(function(snapshotResponse)
        {
            var payload = [
                c_wpoRequestKey,
                snapshotResponse.botguardResponse
            ];
            
            return fetch(c_generateItUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json+protobuf",
                    "x-goog-api-key": "AIzaSyDyT5W0Jh49F30Pqqtyfdf7pDLFKLJoAnw",
                    "x-user-agent": "grpc-web-javascript/0.1",
                    "Accept": "*/*"
                },
                body: JSON.stringify(payload),
                credentials: "omit"
            });
        })
        .then(function(response)
        {
            if (!response.ok)
            {
                logAndThrow("Failed GenerateIT request.");
            }
            return response.json();
        })
        .then(function(response)
        {
            var result = {
                integrityTokenResponse: response,
                webPoSignalOutput: webPoSignalOutput
            };
            module.botguard.integrityToken = result;
            rehike.pubsub.publish("webpo-integrity-token-generated", result);
            return result;
        });
};

/**
 * Creates and runs the BotGuard script.
 * 
 * @private
 * 
 * @param {object} bgChallenge 
 * @returns {Promise<object>}
 */
module.botguard.runBotguardScript_ = function(bgChallenge)
{
    if (!("globalName" in window))
    {
        var scriptEl = document.createElement("script");
        scriptEl.src = bgChallenge.interpreterUrl.privateDoNotAccessOrElseTrustedResourceUrlWrappedValue;
        scriptEl.id = bgChallenge.interpreterHash;
        
        return new Promise(function(resolve, reject)
        {
            scriptEl.onload = function()
            {
                setTimeout(function()
                {
                    scriptEl.remove();
                }, 0);
                
                return module.botguard.checkClientExists_(bgChallenge.globalName, resolve);
            };
            document.head.appendChild(scriptEl);
        });
    }
    
    return Promise.resolve(window[bgChallenge.globalName]);
};

/**
 * Checks that the BotGuard client is loaded and ready to be used.
 * 
 * @private
 * 
 * @param {string} globalName Checked in window.
 * @param {(value: object) => void} resolveCb
 * 
 * @returns {void}
 */
module.botguard.checkClientExists_ = function(globalName, resolveCb)
{
    if (globalName in window)
    {
        log("BotGuard client exists:", globalName, window[globalName]);
        module.botguard.initialized = true;
        rehike.pubsub.publish("webpo-botguard-initialized");
        return resolveCb(window[globalName]);
    }
    
    setTimeout(function()
    {
        module.botguard.checkClientExists_(globalName, resolveCb);
    }, 1200);
};

/**
 * Wraps the BotGuard interface for consumption by the rest of the scripts.
 * 
 * @private
 * 
 * @param {object} vm  The BotGuard virtual machine interface.
 * @param {object} att  Attestation session configuration.
 */
module.botguard.wrapBotguardInterface_ = function(vm, att)
{
    var program = att.bgChallenge.program;
    var challenge = att.challenge;
    
    var vmFunctions = {};
    var syncSnapshotFunction = vm.a(program, function(a, b, c, d)
    {
        Object.assign(vmFunctions, {
            asyncSnapshotFunction: a,
            shutdownFunction: b,
            passEventFunction: c,
            checkCameraFunction: d
        });
    }, true, undefined, function() { /** no-op */ }, [ [], [] ])[0];
    
    var webPoSignalOutput = [];
    var bgResponse = syncSnapshotFunction([, , webPoSignalOutput]);
    if (bgResponse && bgResponse.indexOf("$"))
    {
        bgResponse = null;
    }
    
    return {
        notBoundResponse: bgResponse,
        challenge: challenge,
        webPoSignalOutput: webPoSignalOutput,
        syncSnapshotFunction: syncSnapshotFunction,
        vmFunctions: vmFunctions
    };
};