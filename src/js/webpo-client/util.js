/**
 * @fileoverview Utilities for the PO token generator.
 * 
 * Implementation lifted from Reprety.
 * 
 * @author The Rehike Maintainers
 */

/** @constant */
var c_base64urlCharRegex = /[-_.]/g;

/** @constant */
var c_base64urlToBase64Map = {
    "-": "+",
    "_": "/",
    ".": "="
};

function isBase64url(input) {
    return c_base64urlCharRegex.test(input);
}

function base64ToU8(base64) {
    var base64Mod = isBase64url(base64)
        ? base64.replace(c_base64urlCharRegex, function(m) { return c_base64urlToBase64Map[m]; })
        : base64;
    base64Mod = atob(base64Mod);
    var arr = new Uint8Array(base64Mod.length);
    for (var i = 0; i < base64Mod.length; i++) {
        arr[i] = base64Mod.charCodeAt(i);
    }
    return arr;
}

function u8ToBase64(u8, base64url) {
    if (base64url === void 0) base64url = false;
    var str = '';
    for (var i = 0; i < u8.length; i++) {
        str += String.fromCharCode(u8[i]);
    }
    var result = btoa(str);
    if (base64url) {
        return result.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }
    return result;
}