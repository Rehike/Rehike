/*
 * This is a stub to delay the script load until Rehike core JS is ready.
 */
    
(function(delayLoadedScript) { 
    if (window.rehike)
    {
        delayLoadedScript();
    }
    else
    {
        var dlmPath = "_rehikeCoreDelayLoadModules";
        window[dlmPath] = window[dlmPath] || [];
        window[dlmPath].push(function delayLoadHandler() {
            if (window.rehike)
            {
                delayLoadedScript();
            }
        });
    } 
})