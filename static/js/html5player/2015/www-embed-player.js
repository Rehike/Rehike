(function() {
    var g, aa = aa || {}, m = this;
    function p(a) {
        return void 0 !== a
    }
    function q(a, b, c) {
        a = a.split(".");
        c = c || m;
        a[0]in c || !c.execScript || c.execScript("var " + a[0]);
        for (var d; a.length && (d = a.shift()); )
            !a.length && p(b) ? c[d] = b : c[d] ? c = c[d] : c = c[d] = {}
    }
    function r(a, b) {
        for (var c = a.split("."), d = b || m, e; e = c.shift(); )
            if (null != d[e])
                d = d[e];
            else
                return null;
        return d
    }
    function t() {}
    function ba(a) {
        a.getInstance = function() {
            return a.R ? a.R : a.R = new a
        }
    }
    function ca(a) {
        var b = typeof a;
        if ("object" == b)
            if (a) {
                if (a instanceof Array)
                    return "array";
                if (a instanceof Object)
                    return b;
                var c = Object.prototype.toString.call(a);
                if ("[object Window]" == c)
                    return "object";
                if ("[object Array]" == c || "number" == typeof a.length && "undefined" != typeof a.splice && "undefined" != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable("splice"))
                    return "array";
                if ("[object Function]" == c || "undefined" != typeof a.call && "undefined" != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable("call"))
                    return "function"
            } else
                return "null";
        else if ("function" == b && "undefined" == typeof a.call)
            return "object";
        return b
    }
    function da(a) {
        return "array" == ca(a)
    }
    function ea(a) {
        var b = ca(a);
        return "array" == b || "object" == b && "number" == typeof a.length
    }
    function u(a) {
        return "string" == typeof a
    }
    function fa(a) {
        return "number" == typeof a
    }
    function ga(a) {
        return "function" == ca(a)
    }
    function ha(a) {
        var b = typeof a;
        return "object" == b && null != a || "function" == b
    }
    function ia(a) {
        return a[ja] || (a[ja] = ++ka)
    }
    var ja = "closure_uid_" + (1E9 * Math.random() >>> 0)
      , ka = 0;
    function ma(a, b, c) {
        return a.call.apply(a.bind, arguments)
    }
    function na(a, b, c) {
        if (!a)
            throw Error();
        if (2 < arguments.length) {
            var d = Array.prototype.slice.call(arguments, 2);
            return function() {
                var c = Array.prototype.slice.call(arguments);
                Array.prototype.unshift.apply(c, d);
                return a.apply(b, c)
            }
        }
        return function() {
            return a.apply(b, arguments)
        }
    }
    function v(a, b, c) {
        v = Function.prototype.bind && -1 != Function.prototype.bind.toString().indexOf("native code") ? ma : na;
        return v.apply(null, arguments)
    }
    function oa(a, b) {
        var c = Array.prototype.slice.call(arguments, 1);
        return function() {
            var b = c.slice();
            b.push.apply(b, arguments);
            return a.apply(this, b)
        }
    }
    var w = Date.now || function() {
        return +new Date
    }
    ;
    function y(a, b) {
        function c() {}
        c.prototype = b.prototype;
        a.G = b.prototype;
        a.prototype = new c;
        a.prototype.constructor = a;
        a.base = function(a, c, f) {
            for (var h = Array(arguments.length - 2), k = 2; k < arguments.length; k++)
                h[k - 2] = arguments[k];
            return b.prototype[c].apply(a, h)
        }
    }
    Function.prototype.bind = Function.prototype.bind || function(a, b) {
        if (1 < arguments.length) {
            var c = Array.prototype.slice.call(arguments, 1);
            c.unshift(this, a);
            return v.apply(null, c)
        }
        return v(this, a)
    }
    ;
    function pa(a) {
        if (Error.captureStackTrace)
            Error.captureStackTrace(this, pa);
        else {
            var b = Error().stack;
            b && (this.stack = b)
        }
        a && (this.message = String(a))
    }
    y(pa, Error);
    pa.prototype.name = "CustomError";
    var qa;
    function ra(a) {
        var b = a.length - 5;
        return 0 <= b && a.indexOf("_html", b) == b
    }
    var sa = String.prototype.trim ? function(a) {
        return a.trim()
    }
    : function(a) {
        return a.replace(/^[\s\xa0]+|[\s\xa0]+$/g, "")
    }
    ;
    function ta(a) {
        return decodeURIComponent(a.replace(/\+/g, " "))
    }
    function ua(a) {
        var b = {
            "&amp;": "&",
            "&lt;": "<",
            "&gt;": ">",
            "&quot;": '"'
        }, c;
        c = m.document.createElement("div");
        return a.replace(va, function(a, e) {
            var f = b[a];
            if (f)
                return f;
            if ("#" == e.charAt(0)) {
                var h = Number("0" + e.substr(1));
                isNaN(h) || (f = String.fromCharCode(h))
            }
            f || (c.innerHTML = a + " ",
            f = c.firstChild.nodeValue.slice(0, -1));
            return b[a] = f
        })
    }
    function wa(a) {
        return a.replace(/&([^;]+);/g, function(a, c) {
            switch (c) {
            case "amp":
                return "&";
            case "lt":
                return "<";
            case "gt":
                return ">";
            case "quot":
                return '"';
            default:
                if ("#" == c.charAt(0)) {
                    var d = Number("0" + c.substr(1));
                    if (!isNaN(d))
                        return String.fromCharCode(d)
                }
                return a
            }
        })
    }
    var va = /&([^;\s<&]+);?/g;
    function xa(a, b) {
        for (var c = 0, d = sa(String(a)).split("."), e = sa(String(b)).split("."), f = Math.max(d.length, e.length), h = 0; 0 == c && h < f; h++) {
            var k = d[h] || ""
              , l = e[h] || ""
              , n = RegExp("(\\d*)(\\D*)", "g")
              , x = RegExp("(\\d*)(\\D*)", "g");
            do {
                var Z = n.exec(k) || ["", "", ""]
                  , la = x.exec(l) || ["", "", ""];
                if (0 == Z[0].length && 0 == la[0].length)
                    break;
                c = ya(0 == Z[1].length ? 0 : parseInt(Z[1], 10), 0 == la[1].length ? 0 : parseInt(la[1], 10)) || ya(0 == Z[2].length, 0 == la[2].length) || ya(Z[2], la[2])
            } while (0 == c)
        }
        return c
    }
    function ya(a, b) {
        return a < b ? -1 : a > b ? 1 : 0
    }
    function za(a) {
        for (var b = 0, c = 0; c < a.length; ++c)
            b = 31 * b + a.charCodeAt(c),
            b %= 4294967296;
        return b
    }
    ;function Aa() {}
    ;var z = Array.prototype
      , Ba = z.indexOf ? function(a, b, c) {
        return z.indexOf.call(a, b, c)
    }
    : function(a, b, c) {
        c = null == c ? 0 : 0 > c ? Math.max(0, a.length + c) : c;
        if (u(a))
            return u(b) && 1 == b.length ? a.indexOf(b, c) : -1;
        for (; c < a.length; c++)
            if (c in a && a[c] === b)
                return c;
        return -1
    }
      , A = z.forEach ? function(a, b, c) {
        z.forEach.call(a, b, c)
    }
    : function(a, b, c) {
        for (var d = a.length, e = u(a) ? a.split("") : a, f = 0; f < d; f++)
            f in e && b.call(c, e[f], f, a)
    }
      , Ca = z.filter ? function(a, b, c) {
        return z.filter.call(a, b, c)
    }
    : function(a, b, c) {
        for (var d = a.length, e = [], f = 0, h = u(a) ? a.split("") : a, k = 0; k < d; k++)
            if (k in h) {
                var l = h[k];
                b.call(c, l, k, a) && (e[f++] = l)
            }
        return e
    }
      , B = z.map ? function(a, b, c) {
        return z.map.call(a, b, c)
    }
    : function(a, b, c) {
        for (var d = a.length, e = Array(d), f = u(a) ? a.split("") : a, h = 0; h < d; h++)
            h in f && (e[h] = b.call(c, f[h], h, a));
        return e
    }
      , Da = z.some ? function(a, b, c) {
        return z.some.call(a, b, c)
    }
    : function(a, b, c) {
        for (var d = a.length, e = u(a) ? a.split("") : a, f = 0; f < d; f++)
            if (f in e && b.call(c, e[f], f, a))
                return !0;
        return !1
    }
      , Ea = z.every ? function(a, b, c) {
        return z.every.call(a, b, c)
    }
    : function(a, b, c) {
        for (var d = a.length, e = u(a) ? a.split("") : a, f = 0; f < d; f++)
            if (f in e && !b.call(c, e[f], f, a))
                return !1;
        return !0
    }
    ;
    function Fa(a, b, c) {
        b = Ga(a, b, c);
        return 0 > b ? null : u(a) ? a.charAt(b) : a[b]
    }
    function Ga(a, b, c) {
        for (var d = a.length, e = u(a) ? a.split("") : a, f = 0; f < d; f++)
            if (f in e && b.call(c, e[f], f, a))
                return f;
        return -1
    }
    function Ha(a, b) {
        return 0 <= Ba(a, b)
    }
    function Ia() {
        var a = Ja;
        if (!da(a))
            for (var b = a.length - 1; 0 <= b; b--)
                delete a[b];
        a.length = 0
    }
    function Ka(a, b) {
        Ha(a, b) || a.push(b)
    }
    function La(a, b) {
        var c = Ba(a, b), d;
        (d = 0 <= c) && z.splice.call(a, c, 1);
        return d
    }
    function Ma(a, b) {
        var c = Ga(a, b, void 0);
        0 <= c && z.splice.call(a, c, 1)
    }
    function Na(a) {
        return z.concat.apply(z, arguments)
    }
    function Oa(a) {
        var b = a.length;
        if (0 < b) {
            for (var c = Array(b), d = 0; d < b; d++)
                c[d] = a[d];
            return c
        }
        return []
    }
    function Pa(a, b) {
        for (var c = 1; c < arguments.length; c++) {
            var d = arguments[c];
            if (ea(d)) {
                var e = a.length || 0
                  , f = d.length || 0;
                a.length = e + f;
                for (var h = 0; h < f; h++)
                    a[e + h] = d[h]
            } else
                a.push(d)
        }
    }
    function Qa(a, b, c, d) {
        return z.splice.apply(a, Ra(arguments, 1))
    }
    function Ra(a, b, c) {
        return 2 >= arguments.length ? z.slice.call(a, b) : z.slice.call(a, b, c)
    }
    function Sa(a, b, c) {
        if (!ea(a) || !ea(b) || a.length != b.length)
            return !1;
        var d = a.length;
        c = c || Ta;
        for (var e = 0; e < d; e++)
            if (!c(a[e], b[e]))
                return !1;
        return !0
    }
    function Ua(a, b) {
        return a > b ? 1 : a < b ? -1 : 0
    }
    function Ta(a, b) {
        return a === b
    }
    ;function Va(a, b) {
        for (var c in a)
            b.call(void 0, a[c], c, a)
    }
    function Wa(a, b, c) {
        var d = {}, e;
        for (e in a)
            b.call(c, a[e], e, a) && (d[e] = a[e]);
        return d
    }
    function Xa(a) {
        var b = 0, c;
        for (c in a)
            b++;
        return b
    }
    function Ya(a, b) {
        return Za(a, b)
    }
    function $a(a) {
        var b = [], c = 0, d;
        for (d in a)
            b[c++] = a[d];
        return b
    }
    function ab(a) {
        var b = [], c = 0, d;
        for (d in a)
            b[c++] = d;
        return b
    }
    function Za(a, b) {
        for (var c in a)
            if (a[c] == b)
                return !0;
        return !1
    }
    function bb(a) {
        var b = cb, c;
        for (c in b)
            if (a.call(void 0, b[c], c, b))
                return c
    }
    function db(a) {
        for (var b in a)
            return !1;
        return !0
    }
    function eb(a, b) {
        if (b in a)
            throw Error('The object already contains the key "' + b + '"');
        a[b] = !0
    }
    function fb(a) {
        var b = {}, c;
        for (c in a)
            b[c] = a[c];
        return b
    }
    function gb(a) {
        var b = ca(a);
        if ("object" == b || "array" == b) {
            if (a.clone)
                return a.clone();
            var b = "array" == b ? [] : {}, c;
            for (c in a)
                b[c] = gb(a[c]);
            return b
        }
        return a
    }
    var hb = "constructor hasOwnProperty isPrototypeOf propertyIsEnumerable toLocaleString toString valueOf".split(" ");
    function ib(a, b) {
        for (var c, d, e = 1; e < arguments.length; e++) {
            d = arguments[e];
            for (c in d)
                a[c] = d[c];
            for (var f = 0; f < hb.length; f++)
                c = hb[f],
                Object.prototype.hasOwnProperty.call(d, c) && (a[c] = d[c])
        }
    }
    function jb(a) {
        var b = arguments.length;
        if (1 == b && da(arguments[0]))
            return jb.apply(null, arguments[0]);
        for (var c = {}, d = 0; d < b; d++)
            c[arguments[d]] = !0;
        return c
    }
    ;jb("area base br col command embed hr img input keygen link meta param source track wbr".split(" "));
    function kb() {
        this.e = ""
    }
    kb.prototype.$b = !0;
    kb.prototype.Vb = function() {
        return this.e
    }
    ;
    kb.prototype.toString = function() {
        return "Const{" + this.e + "}"
    }
    ;
    function lb() {
        var a = new kb;
        a.e = "HTML that is escaped and sanitized server-side and passed through yt.net.ajax";
        return a
    }
    ;function mb() {
        this.e = "";
        this.f = nb
    }
    mb.prototype.$b = !0;
    mb.prototype.Vb = function() {
        return this.e
    }
    ;
    function ob(a) {
        return a instanceof mb && a.constructor === mb && a.f === nb ? a.e : "type_error:SafeUrl"
    }
    var pb = /^(?:(?:https?|mailto|ftp):|[^&:/?#]*(?:[/?#]|$))/i;
    function qb(a) {
        if (a instanceof mb)
            return a;
        a = a.$b ? a.Vb() : String(a);
        a = pb.test(a) ? rb(a) : "about:invalid#zClosurez";
        var b = new mb;
        b.e = a;
        return b
    }
    function rb(a) {
        try {
            var b = encodeURI(a)
        } catch (c) {
            return "about:invalid#zClosurez"
        }
        return b.replace(sb, function(a) {
            return tb[a]
        })
    }
    var sb = /[()']|%5B|%5D|%25/g
      , tb = {
        "'": "%27",
        "(": "%28",
        ")": "%29",
        "%5B": "[",
        "%5D": "]",
        "%25": "%"
    }
      , nb = {};
    function ub() {
        this.e = "";
        this.f = null
    }
    ub.prototype.$b = !0;
    ub.prototype.Vb = function() {
        return this.e
    }
    ;
    jb("action", "cite", "data", "formaction", "href", "manifest", "poster", "src");
    jb("EMBED", "IFRAME", "LINK", "OBJECT", "SCRIPT", "STYLE", "TEMPLATE");
    function vb(a, b) {
        var c = new ub;
        c.e = a;
        c.f = b;
        return c
    }
    vb("", 0);
    function wb(a, b) {
        var c;
        c = b instanceof mb ? b : qb(b);
        a.href = ob(c)
    }
    ;function xb(a, b, c) {
        a && (a.dataset ? a.dataset[yb(b)] = c : a.setAttribute("data-" + b, c))
    }
    function C(a, b) {
        return a ? a.dataset ? a.dataset[yb(b)] : a.getAttribute("data-" + b) : null
    }
    var zb = {};
    function yb(a) {
        return zb[a] || (zb[a] = String(a).replace(/\-([a-z])/g, function(a, c) {
            return c.toUpperCase()
        }))
    }
    ;function D() {
        this.Pa = this.Pa;
        this.la = this.la
    }
    D.prototype.Pa = !1;
    D.prototype.F = function() {
        return this.Pa
    }
    ;
    D.prototype.dispose = function() {
        this.Pa || (this.Pa = !0,
        this.B())
    }
    ;
    function Ab(a, b) {
        a.Pa ? b.call(void 0) : (a.la || (a.la = []),
        a.la.push(p(void 0) ? v(b, void 0) : b))
    }
    D.prototype.B = function() {
        if (this.la)
            for (; this.la.length; )
                this.la.shift()()
    }
    ;
    function Bb(a) {
        a && "function" == typeof a.dispose && a.dispose()
    }
    function Cb(a) {
        for (var b = 0, c = arguments.length; b < c; ++b) {
            var d = arguments[b];
            ea(d) ? Cb.apply(null, d) : Bb(d)
        }
    }
    ;function E() {
        D.call(this);
        this.e = [];
        this.aa = {}
    }
    y(E, D);
    g = E.prototype;
    g.Bc = 1;
    g.Cb = 0;
    g.subscribe = function(a, b, c) {
        var d = this.aa[a];
        d || (d = this.aa[a] = []);
        var e = this.Bc;
        this.e[e] = a;
        this.e[e + 1] = b;
        this.e[e + 2] = c;
        this.Bc = e + 3;
        d.push(e);
        return e
    }
    ;
    g.Na = function(a, b, c) {
        if (a = this.aa[a]) {
            var d = this.e;
            if (a = Fa(a, function(a) {
                return d[a + 1] == b && d[a + 2] == c
            }))
                return this.ka(a)
        }
        return !1
    }
    ;
    g.ka = function(a) {
        if (0 != this.Cb)
            return this.f || (this.f = []),
            this.f.push(a),
            !1;
        var b = this.e[a];
        if (b) {
            var c = this.aa[b];
            c && La(c, a);
            delete this.e[a];
            delete this.e[a + 1];
            delete this.e[a + 2]
        }
        return !!b
    }
    ;
    g.A = function(a, b) {
        var c = this.aa[a];
        if (c) {
            this.Cb++;
            for (var d = Array(arguments.length - 1), e = 1, f = arguments.length; e < f; e++)
                d[e - 1] = arguments[e];
            e = 0;
            for (f = c.length; e < f; e++) {
                var h = c[e];
                this.e[h + 1].apply(this.e[h + 2], d)
            }
            this.Cb--;
            if (this.f && 0 == this.Cb)
                for (; c = this.f.pop(); )
                    this.ka(c);
            return 0 != e
        }
        return !1
    }
    ;
    g.clear = function(a) {
        if (a) {
            var b = this.aa[a];
            b && (A(b, this.ka, this),
            delete this.aa[a])
        } else
            this.e.length = 0,
            this.aa = {}
    }
    ;
    g.W = function(a) {
        if (a) {
            var b = this.aa[a];
            return b ? b.length : 0
        }
        a = 0;
        for (b in this.aa)
            a += this.W(b);
        return a
    }
    ;
    g.B = function() {
        E.G.B.call(this);
        delete this.e;
        delete this.aa;
        delete this.f
    }
    ;
    var Db = window.yt && window.yt.config_ || window.ytcfg && window.ytcfg.data_ || {};
    q("yt.config_", Db, void 0);
    q("yt.tokens_", window.yt && window.yt.tokens_ || {}, void 0);
    var Eb = window.yt && window.yt.msgs_ || {};
    q("yt.msgs_", Eb, void 0);
    function Fb(a) {
        Gb(Db, arguments)
    }
    function F(a, b) {
        return a in Db ? Db[a] : b
    }
    function G(a, b) {
        ga(a) && (a = Hb(a));
        return window.setTimeout(a, b)
    }
    function Ib(a, b) {
        ga(a) && (a = Hb(a));
        window.setInterval(a, b)
    }
    function H(a) {
        window.clearTimeout(a)
    }
    function Hb(a) {
        return a && window.yterr ? function() {
            try {
                return a.apply(this, arguments)
            } catch (b) {
                throw Jb(b),
                b;
            }
        }
        : a
    }
    function Jb(a, b) {
        var c = r("yt.www.errors.log");
        c ? c(a, b) : (c = F("ERRORS") || [],
        c.push([a, b]),
        Fb("ERRORS", c))
    }
    function Kb() {
        var a = {}
          , b = "FLASH_UPGRADE"in Eb ? Eb.FLASH_UPGRADE : 'You need to upgrade your Adobe Flash Player to watchthis video. <br> <a href="http://get.adobe.com/flashplayer/">Download it from Adobe.</a>';
        if (b)
            for (var c in a)
                b = b.replace(new RegExp("\\$" + c,"gi"), function() {
                    return a[c]
                });
        return b
    }
    function Gb(a, b) {
        if (1 < b.length) {
            var c = b[0];
            a[c] = b[1]
        } else {
            var d = b[0];
            for (c in d)
                a[c] = d[c]
        }
    }
    var Lb = "Microsoft Internet Explorer" == navigator.appName;
    var Mb = r("yt.pubsub.instance_") || new E;
    E.prototype.subscribe = E.prototype.subscribe;
    E.prototype.unsubscribeByKey = E.prototype.ka;
    E.prototype.publish = E.prototype.A;
    E.prototype.clear = E.prototype.clear;
    q("yt.pubsub.instance_", Mb, void 0);
    var Nb = r("yt.pubsub.subscribedKeys_") || {};
    q("yt.pubsub.subscribedKeys_", Nb, void 0);
    var Ob = r("yt.pubsub.topicToKeys_") || {};
    q("yt.pubsub.topicToKeys_", Ob, void 0);
    var Pb = r("yt.pubsub.isSynchronous_") || {};
    q("yt.pubsub.isSynchronous_", Pb, void 0);
    var Qb = r("yt.pubsub.skipSubId_") || null;
    q("yt.pubsub.skipSubId_", Qb, void 0);
    function Rb(a, b, c) {
        var d = Sb();
        if (d) {
            var e = d.subscribe(a, function() {
                if (!Qb || Qb != e) {
                    var d = arguments
                      , h = function() {
                        Nb[e] && b.apply(c || window, d)
                    };
                    try {
                        Pb[a] ? h() : G(h, 0)
                    } catch (k) {
                        Jb(k)
                    }
                }
            }, c);
            Nb[e] = !0;
            Ob[a] || (Ob[a] = []);
            Ob[a].push(e);
            return e
        }
        return 0
    }
    function Tb(a) {
        var b = Sb();
        b && ("number" == typeof a ? a = [a] : "string" == typeof a && (a = [parseInt(a, 10)]),
        A(a, function(a) {
            b.unsubscribeByKey(a);
            delete Nb[a]
        }))
    }
    function I(a, b) {
        var c = Sb();
        return c ? c.publish.apply(c, arguments) : !1
    }
    function Ub(a, b) {
        Pb[a] = !0;
        var c = Sb();
        c && c.publish.apply(c, arguments);
        Pb[a] = !1
    }
    function Vb(a) {
        Ob[a] && (a = Ob[a],
        A(a, function(a) {
            Nb[a] && delete Nb[a]
        }),
        a.length = 0)
    }
    function Wb(a) {
        var b = Sb();
        if (b)
            if (b.clear(a),
            a)
                Vb(a);
            else
                for (var c in Ob)
                    Vb(c)
    }
    function Sb() {
        return r("yt.pubsub.instance_")
    }
    ;function Xb(a, b) {
        if (window.spf) {
            var c = "";
            if (a) {
                var d = a.indexOf("jsbin/")
                  , e = a.lastIndexOf(".js")
                  , f = d + 6;
                -1 < d && -1 < e && e > f && (c = a.substring(f, e),
                c = c.replace(Yb, ""),
                c = c.replace(Zb, ""),
                c = c.replace("debug-", ""),
                c = c.replace("tracing-", ""))
            }
            spf.script.load(a, c, b)
        } else
            $b(a, b)
    }
    function $b(a, b) {
        var c = ac(a)
          , d = document.getElementById(c)
          , e = d && C(d, "loaded")
          , f = d && !e;
        if (e)
            b && b();
        else {
            if (b) {
                var e = Rb(c, b)
                  , h = "" + ia(b);
                bc[h] = e
            }
            f || (d = cc(a, c, function() {
                C(d, "loaded") || (xb(d, "loaded", "true"),
                I(c),
                G(oa(Wb, c), 0))
            }))
        }
    }
    function cc(a, b, c) {
        var d = document.createElement("script");
        d.id = b;
        d.onload = function() {
            c && setTimeout(c, 0)
        }
        ;
        d.onreadystatechange = function() {
            switch (d.readyState) {
            case "loaded":
            case "complete":
                d.onload()
            }
        }
        ;
        d.src = a;
        a = document.getElementsByTagName("head")[0] || document.body;
        a.insertBefore(d, a.firstChild);
        return d
    }
    function ac(a) {
        var b = document.createElement("a");
        wb(b, a);
        a = b.href.replace(/^[a-zA-Z]+:\/\//, "//");
        return "js-" + za(a)
    }
    var Yb = /\.vflset|-vfl[a-zA-Z0-9_+=-]+/
      , Zb = /-[a-zA-Z]{2,3}_[a-zA-Z]{2,3}(?=(\/|$))/
      , bc = {};
    var dc = null;
    function ec() {
        var a = F("BG_I", null)
          , b = F("BG_IU", null)
          , c = F("BG_P");
        b ? Xb(b, function() {
            dc = new botguard.bg(c)
        }) : a && (eval(a),
        dc = new botguard.bg(c))
    }
    function fc() {
        return null != dc
    }
    function gc() {
        return dc ? dc.invoke() : null
    }
    ;function hc(a) {
        if (a.classList)
            return a.classList;
        a = a.className;
        return u(a) && a.match(/\S+/g) || []
    }
    function ic(a, b) {
        return a.classList ? a.classList.contains(b) : Ha(hc(a), b)
    }
    function jc(a, b) {
        a.classList ? a.classList.add(b) : ic(a, b) || (a.className += 0 < a.className.length ? " " + b : b)
    }
    function kc(a, b) {
        a.classList ? a.classList.remove(b) : ic(a, b) && (a.className = Ca(hc(a), function(a) {
            return a != b
        }).join(" "))
    }
    function lc(a, b, c) {
        c ? jc(a, b) : kc(a, b)
    }
    ;function mc(a, b) {
        this.x = p(a) ? a : 0;
        this.y = p(b) ? b : 0
    }
    mc.prototype.clone = function() {
        return new mc(this.x,this.y)
    }
    ;
    mc.prototype.floor = function() {
        this.x = Math.floor(this.x);
        this.y = Math.floor(this.y);
        return this
    }
    ;
    mc.prototype.round = function() {
        this.x = Math.round(this.x);
        this.y = Math.round(this.y);
        return this
    }
    ;
    function nc(a, b) {
        this.width = a;
        this.height = b
    }
    nc.prototype.clone = function() {
        return new nc(this.width,this.height)
    }
    ;
    nc.prototype.isEmpty = function() {
        return !(this.width * this.height)
    }
    ;
    nc.prototype.floor = function() {
        this.width = Math.floor(this.width);
        this.height = Math.floor(this.height);
        return this
    }
    ;
    nc.prototype.round = function() {
        this.width = Math.round(this.width);
        this.height = Math.round(this.height);
        return this
    }
    ;
    var oc;
    t: {
        var pc = m.navigator;
        if (pc) {
            var qc = pc.userAgent;
            if (qc) {
                oc = qc;
                break t
            }
        }
        oc = ""
    }
    function J(a) {
        return -1 != oc.indexOf(a)
    }
    ;function rc() {
        return J("Opera") || J("OPR")
    }
    function sc() {
        return J("Edge") || J("Trident") || J("MSIE")
    }
    function tc() {
        return (J("Chrome") || J("CriOS")) && !rc() && !sc()
    }
    ;function uc() {
        return J("Edge")
    }
    ;function vc() {
        return J("iPhone") && !J("iPod") && !J("iPad")
    }
    ;var wc = rc()
      , K = sc()
      , xc = J("Gecko") && !(-1 != oc.toLowerCase().indexOf("webkit") && !uc()) && !(J("Trident") || J("MSIE")) && !uc()
      , yc = -1 != oc.toLowerCase().indexOf("webkit") && !uc()
      , zc = J("Macintosh")
      , Ac = J("Windows");
    function Bc() {
        var a = oc;
        if (xc)
            return /rv\:([^\);]+)(\)|;)/.exec(a);
        if (K && uc())
            return /Edge\/([\d\.]+)/.exec(a);
        if (K)
            return /\b(?:MSIE|rv)[: ]([^\);]+)(\)|;)/.exec(a);
        if (yc)
            return /WebKit\/(\S+)/.exec(a)
    }
    function Cc() {
        var a = m.document;
        return a ? a.documentMode : void 0
    }
    var Dc = function() {
        if (wc && m.opera) {
            var a = m.opera.version;
            return ga(a) ? a() : a
        }
        var a = ""
          , b = Bc();
        b && (a = b ? b[1] : "");
        return K && !uc() && (b = Cc(),
        b > parseFloat(a)) ? String(b) : a
    }()
      , Ec = {};
    function Fc(a) {
        return Ec[a] || (Ec[a] = 0 <= xa(Dc, a))
    }
    function Gc(a) {
        return K && (uc() || Hc >= a)
    }
    var Ic = m.document
      , Jc = Cc()
      , Hc = !Ic || !K || !Jc && uc() ? void 0 : Jc || ("CSS1Compat" == Ic.compatMode ? parseInt(Dc, 10) : 5);
    !xc && !K || K && Gc(9) || xc && Fc("1.9.1");
    K && Fc("9");
    function Kc(a) {
        return a ? new Lc(Mc(a)) : qa || (qa = new Lc)
    }
    function Nc(a) {
        return u(a) ? document.getElementById(a) : a
    }
    function Oc(a) {
        var b = document;
        return u(a) ? b.getElementById(a) : a
    }
    function Pc(a) {
        var b = document;
        return b.querySelectorAll && b.querySelector ? b.querySelectorAll("." + a) : Qc(a, void 0)
    }
    function Qc(a, b) {
        var c, d, e, f;
        c = document;
        c = b || c;
        if (c.querySelectorAll && c.querySelector && a)
            return c.querySelectorAll("" + (a ? "." + a : ""));
        if (a && c.getElementsByClassName) {
            var h = c.getElementsByClassName(a);
            return h
        }
        h = c.getElementsByTagName("*");
        if (a) {
            f = {};
            for (d = e = 0; c = h[d]; d++) {
                var k = c.className;
                "function" == typeof k.split && Ha(k.split(/\s+/), a) && (f[e++] = c)
            }
            f.length = e;
            return f
        }
        return h
    }
    function Rc(a) {
        return "CSS1Compat" == a.compatMode
    }
    function Sc(a) {
        for (var b; b = a.firstChild; )
            a.removeChild(b)
    }
    function Tc(a) {
        if (!a)
            return null;
        if (a.firstChild)
            return a.firstChild;
        for (; a && !a.nextSibling; )
            a = a.parentNode;
        return a ? a.nextSibling : null
    }
    function Uc(a) {
        if (!a)
            return null;
        if (!a.previousSibling)
            return a.parentNode;
        for (a = a.previousSibling; a && a.lastChild; )
            a = a.lastChild;
        return a
    }
    function Vc(a, b) {
        if (a.contains && 1 == b.nodeType)
            return a == b || a.contains(b);
        if ("undefined" != typeof a.compareDocumentPosition)
            return a == b || Boolean(a.compareDocumentPosition(b) & 16);
        for (; b && a != b; )
            b = b.parentNode;
        return b == a
    }
    function Mc(a) {
        return 9 == a.nodeType ? a : a.ownerDocument || a.document
    }
    function Wc(a) {
        var b = Xc.kd;
        return b ? Yc(a, function(a) {
            return !b || u(a.className) && Ha(a.className.split(/\s+/), b)
        }, !0, void 0) : null
    }
    function Yc(a, b, c, d) {
        c || (a = a.parentNode);
        c = null == d;
        for (var e = 0; a && (c || e <= d); ) {
            if (b(a))
                return a;
            a = a.parentNode;
            e++
        }
        return null
    }
    function Lc(a) {
        this.e = a || m.document || document
    }
    Lc.prototype.createElement = function(a) {
        return this.e.createElement(a)
    }
    ;
    Lc.prototype.appendChild = function(a, b) {
        a.appendChild(b)
    }
    ;
    Lc.prototype.contains = Vc;
    var Zc = yc ? "webkit" : xc ? "moz" : K ? "ms" : wc ? "o" : ""
      , $c = r("yt.dom.getNextId_");
    if (!$c) {
        $c = function() {
            return ++ad
        }
        ;
        q("yt.dom.getNextId_", $c, void 0);
        var ad = 0
    }
    function bd() {
        var a = document, b;
        Da(["fullscreenElement", "fullScreenElement"], function(c) {
            c in a ? b = a[c] : (c = Zc + c.charAt(0).toUpperCase() + c.substr(1),
            b = c in a ? a[c] : void 0);
            return !!b
        });
        return b
    }
    ;function cd(a) {
        if (a = a || window.event) {
            for (var b in a)
                b in dd || (this[b] = a[b]);
            this.Kb = a;
            (b = a.target || a.srcElement) && 3 == b.nodeType && (b = b.parentNode);
            this.target = b;
            if (b = a.relatedTarget)
                try {
                    b = b.nodeName ? b : null
                } catch (c) {
                    b = null
                }
            else
                "mouseover" == this.type ? b = a.fromElement : "mouseout" == this.type && (b = a.toElement);
            this.relatedTarget = b;
            this.clientX = void 0 != a.clientX ? a.clientX : a.pageX;
            this.clientY = void 0 != a.clientY ? a.clientY : a.pageY;
            this.keyCode = a.keyCode ? a.keyCode : a.which;
            this.charCode = a.charCode || ("keypress" == this.type ? this.keyCode : 0);
            this.altKey = a.altKey;
            this.ctrlKey = a.ctrlKey;
            this.shiftKey = a.shiftKey;
            "MozMousePixelScroll" == this.type ? (this.wheelDeltaX = a.axis == a.HORIZONTAL_AXIS ? a.detail : 0,
            this.wheelDeltaY = a.axis == a.HORIZONTAL_AXIS ? 0 : a.detail) : window.opera ? (this.wheelDeltaX = 0,
            this.wheelDeltaY = a.detail) : 0 == a.wheelDelta % 120 ? "WebkitTransform"in document.documentElement.style ? window.chrome && 0 == navigator.platform.indexOf("Mac") ? (this.wheelDeltaX = a.wheelDeltaX / -30,
            this.wheelDeltaY = a.wheelDeltaY / -30) : (this.wheelDeltaX = a.wheelDeltaX / -1.2,
            this.wheelDeltaY = a.wheelDeltaY / -1.2) : (this.wheelDeltaX = 0,
            this.wheelDeltaY = a.wheelDelta / -1.6) : (this.wheelDeltaX = a.wheelDeltaX / -3,
            this.wheelDeltaY = a.wheelDeltaY / -3)
        }
    }
    g = cd.prototype;
    g.Kb = null;
    g.type = "";
    g.target = null;
    g.relatedTarget = null;
    g.currentTarget = null;
    g.data = null;
    g.source = null;
    g.state = null;
    g.keyCode = 0;
    g.charCode = 0;
    g.altKey = !1;
    g.ctrlKey = !1;
    g.shiftKey = !1;
    g.clientX = 0;
    g.clientY = 0;
    g.wheelDeltaX = 0;
    g.wheelDeltaY = 0;
    g.preventDefault = function() {
        this.Kb.returnValue = !1;
        this.Kb.preventDefault && this.Kb.preventDefault()
    }
    ;
    var dd = {
        stopImmediatePropagation: 1,
        stopPropagation: 1,
        preventMouseEvent: 1,
        preventManipulation: 1,
        preventDefault: 1,
        layerX: 1,
        layerY: 1,
        scale: 1,
        rotation: 1
    };
    var cb = r("yt.events.listeners_") || {};
    q("yt.events.listeners_", cb, void 0);
    var ed = r("yt.events.counter_") || {
        count: 0
    };
    q("yt.events.counter_", ed, void 0);
    function fd(a, b, c, d) {
        return bb(function(e) {
            return e[0] == a && e[1] == b && e[2] == c && e[4] == !!d
        })
    }
    function L(a, b, c, d) {
        if (!a || !a.addEventListener && !a.attachEvent)
            return "";
        d = !!d;
        var e = fd(a, b, c, d);
        if (e)
            return e;
        var e = ++ed.count + "", f = !("mouseenter" != b && "mouseleave" != b || !a.addEventListener || "onmouseenter"in document), h;
        h = f ? function(d) {
            d = new cd(d);
            if (!Yc(d.relatedTarget, function(b) {
                return b == a
            }, !0))
                return d.currentTarget = a,
                d.type = b,
                c.call(a, d)
        }
        : function(b) {
            b = new cd(b);
            b.currentTarget = a;
            return c.call(a, b)
        }
        ;
        h = Hb(h);
        cb[e] = [a, b, c, h, d];
        a.addEventListener ? "mouseenter" == b && f ? a.addEventListener("mouseover", h, d) : "mouseleave" == b && f ? a.addEventListener("mouseout", h, d) : "mousewheel" == b && "MozBoxSizing"in document.documentElement.style ? a.addEventListener("MozMousePixelScroll", h, d) : a.addEventListener(b, h, d) : a.attachEvent("on" + b, h);
        return e
    }
    function gd(a) {
        a && ("string" == typeof a && (a = [a]),
        A(a, function(a) {
            if (a in cb) {
                var c = cb[a]
                  , d = c[0]
                  , e = c[1]
                  , f = c[3]
                  , c = c[4];
                d.removeEventListener ? d.removeEventListener(e, f, c) : d.detachEvent && d.detachEvent("on" + e, f);
                delete cb[a]
            }
        }))
    }
    ;function hd(a) {
        this.e = a
    }
    var id = /\s*;\s*/;
    g = hd.prototype;
    g.isEnabled = function() {
        return navigator.cookieEnabled
    }
    ;
    function jd(a, b, c, d, e, f) {
        if (/[;=\s]/.test(b))
            throw Error('Invalid cookie name "' + b + '"');
        if (/[;\r\n]/.test(c))
            throw Error('Invalid cookie value "' + c + '"');
        p(d) || (d = -1);
        f = f ? ";domain=" + f : "";
        e = e ? ";path=" + e : "";
        d = 0 > d ? "" : 0 == d ? ";expires=" + (new Date(1970,1,1)).toUTCString() : ";expires=" + (new Date(w() + 1E3 * d)).toUTCString();
        a.e.cookie = b + "=" + c + f + e + d + ""
    }
    g.get = function(a, b) {
        for (var c = a + "=", d = (this.e.cookie || "").split(id), e = 0, f; f = d[e]; e++) {
            if (0 == f.lastIndexOf(c, 0))
                return f.substr(c.length);
            if (f == a)
                return ""
        }
        return b
    }
    ;
    g.remove = function(a, b, c) {
        var d = p(this.get(a));
        jd(this, a, "", 0, b, c);
        return d
    }
    ;
    g.sa = function() {
        return kd(this).keys
    }
    ;
    g.Y = function() {
        return kd(this).values
    }
    ;
    g.isEmpty = function() {
        return !this.e.cookie
    }
    ;
    g.W = function() {
        return this.e.cookie ? (this.e.cookie || "").split(id).length : 0
    }
    ;
    g.qb = function(a) {
        for (var b = kd(this).values, c = 0; c < b.length; c++)
            if (b[c] == a)
                return !0;
        return !1
    }
    ;
    g.clear = function() {
        for (var a = kd(this).keys, b = a.length - 1; 0 <= b; b--)
            this.remove(a[b])
    }
    ;
    function kd(a) {
        a = (a.e.cookie || "").split(id);
        for (var b = [], c = [], d, e, f = 0; e = a[f]; f++)
            d = e.indexOf("="),
            -1 == d ? (b.push(""),
            c.push(e)) : (b.push(e.substring(0, d)),
            c.push(e.substring(d + 1)));
        return {
            keys: b,
            values: c
        }
    }
    var ld = new hd(document);
    ld.f = 3950;
    function md(a, b, c) {
        jd(ld, "" + a, b, c, "/", "youtube.com")
    }
    function nd(a, b) {
        return ld.get("" + a, b)
    }
    ;function od(a, b) {
        a = !!a;
        q("_lactCookie", a, window);
        if (null == r("_lact", window)) {
            if (F("EXP_LACT_TEMPDATA")) {
                var c = parseInt(F("LACT"), 10)
                  , c = isFinite(c) ? w() - Math.max(c, 0) : -1;
                q("_lact", c, window);
                -1 == c && pd()
            } else
                a && b ? (c = nd("ACTIVITY", "-1"),
                q("_lact", parseInt(c, 10), window)) : (q("_lact", -1, window),
                pd());
            L(document, "keydown", pd);
            L(document, "keyup", pd);
            L(document, "mousedown", pd);
            L(document, "mouseup", pd)
        }
    }
    function pd() {
        var a = r("_lact", window);
        null == a && (od(),
        a = r("_lact", window));
        var b = w();
        q("_lact", b, window);
        F("EXP_LACT_TEMPDATA") || r("_lactCookie", window) && 1E3 <= b - a && md("ACTIVITY", "" + b, -1);
        I("USER_ACTIVE")
    }
    function qd() {
        var a = r("_lact", window);
        return null == a ? -1 : Math.max(w() - a, 0)
    }
    ;var rd = /^(?:([^:/?#.]+):)?(?:\/\/(?:([^/?#]*)@)?([^/#?]*?)(?::([0-9]+))?(?=[/#?]|$))?([^?#]+)?(?:\?([^#]*))?(?:#(.*))?$/;
    function sd(a) {
        if (td) {
            td = !1;
            var b = m.location;
            if (b) {
                var c = b.href;
                if (c && (c = ud(c)) && c != b.hostname)
                    throw td = !0,
                    Error();
            }
        }
        return a.match(rd)
    }
    var td = yc;
    function ud(a) {
        return (a = sd(a)[3] || null) ? decodeURI(a) : a
    }
    function vd(a, b) {
        for (var c = a.split("&"), d = 0; d < c.length; d++) {
            var e = c[d].indexOf("=")
              , f = null
              , h = null;
            0 <= e ? (f = c[d].substring(0, e),
            h = c[d].substring(e + 1)) : f = c[d];
            b(f, h ? ta(h) : "")
        }
    }
    function wd(a) {
        if (a[1]) {
            var b = a[0]
              , c = b.indexOf("#");
            0 <= c && (a.push(b.substr(c)),
            a[0] = b = b.substr(0, c));
            c = b.indexOf("?");
            0 > c ? a[1] = "?" : c == b.length - 1 && (a[1] = void 0)
        }
        return a.join("")
    }
    function xd(a, b, c) {
        if (da(b))
            for (var d = 0; d < b.length; d++)
                xd(a, String(b[d]), c);
        else
            null != b && c.push("&", a, "" === b ? "" : "=", encodeURIComponent(String(b)))
    }
    function yd(a, b, c) {
        Math.max(b.length - (c || 0), 0);
        for (c = c || 0; c < b.length; c += 2)
            xd(b[c], b[c + 1], a);
        return a
    }
    function zd(a, b) {
        for (var c in b)
            xd(c, b[c], a);
        return a
    }
    function Ad(a) {
        a = zd([], a);
        a[0] = "";
        return a.join("")
    }
    function Bd(a, b) {
        return wd(2 == arguments.length ? yd([a], arguments[1], 0) : yd([a], arguments, 1))
    }
    function Cd(a, b) {
        return wd(zd([a], b))
    }
    ;function Dd(a) {
        "?" == a.charAt(0) && (a = a.substr(1));
        a = a.split("&");
        for (var b = {}, c = 0, d = a.length; c < d; c++) {
            var e = a[c].split("=");
            if (1 == e.length && e[0] || 2 == e.length) {
                var f = ta(e[0] || "")
                  , e = ta(e[1] || "");
                f in b ? da(b[f]) ? Pa(b[f], e) : b[f] = [b[f], e] : b[f] = e
            }
        }
        return b
    }
    var Ed = ud;
    function Fd(a, b) {
        var c = a.split("#", 2);
        a = c[0];
        var c = 1 < c.length ? "#" + c[1] : ""
          , d = a.split("?", 2);
        a = d[0];
        var d = Dd(d[1] || ""), e;
        for (e in b)
            d[e] = b[e];
        return Cd(a, d) + c
    }
    function Gd(a) {
        a = Ed(a);
        a = null === a ? null : a.split(".").reverse();
        return (null === a ? !1 : "com" == a[0] && a[1].match(/^youtube(?:-nocookie)?$/) ? !0 : !1) || (null === a ? !1 : "google" == a[1] ? !0 : "google" == a[2] ? "au" == a[0] && "com" == a[1] ? !0 : "uk" == a[0] && "co" == a[1] ? !0 : !1 : !1)
    }
    ;function Hd(a, b) {
        var c = ud(a);
        if (c == ud(window.location.href) || !c && 0 == a.lastIndexOf("/", 0)) {
            var d = sd(a)
              , c = d[5]
              , e = d[6]
              , d = d[7]
              , f = "";
            c && (f += c);
            e && (f += "?" + e);
            d && (f += "#" + d);
            c = f;
            e = c.indexOf("#");
            if (c = 0 > e ? c : c.substr(0, e))
                c = F("SMALLER_SESSION_TEMPDATA_NAME") ? "ST-" + za(c).toString(36) : "s_tempdata-" + za(c),
                e = b ? Ad(b) : "",
                md(c, e, 5)
        }
    }
    ;function Id(a, b, c) {
        var d = F("EVENT_ID");
        d && (b || (b = {}),
        b.ei || (b.ei = d));
        b && Hd(a, b);
        if (c)
            return !1;
        (window.ytspf || {}).enabled ? spf.navigate(a) : (b = window.location,
        a = Cd(a, {}) + "",
        a = a instanceof mb ? a : qb(a),
        b.href = ob(a));
        return !0
    }
    ;function Jd(a, b) {
        return vb(b, null)
    }
    ;var Kd = "StopIteration"in m ? m.StopIteration : Error("StopIteration");
    function Ld() {}
    Ld.prototype.next = function() {
        throw Kd;
    }
    ;
    Ld.prototype.pa = function() {
        return this
    }
    ;
    function Md(a) {
        if (a instanceof Ld)
            return a;
        if ("function" == typeof a.pa)
            return a.pa(!1);
        if (ea(a)) {
            var b = 0
              , c = new Ld;
            c.next = function() {
                for (; ; ) {
                    if (b >= a.length)
                        throw Kd;
                    if (b in a)
                        return a[b++];
                    b++
                }
            }
            ;
            return c
        }
        throw Error("Not implemented");
    }
    function Nd(a, b, c) {
        if (ea(a))
            try {
                A(a, b, c)
            } catch (d) {
                if (d !== Kd)
                    throw d;
            }
        else {
            a = Md(a);
            try {
                for (; ; )
                    b.call(c, a.next(), void 0, a)
            } catch (e) {
                if (e !== Kd)
                    throw e;
            }
        }
    }
    function Od(a) {
        if (ea(a))
            return Oa(a);
        a = Md(a);
        var b = [];
        Nd(a, function(a) {
            b.push(a)
        });
        return b
    }
    ;function Pd(a, b) {
        this.f = {};
        this.e = [];
        this.Ba = this.h = 0;
        var c = arguments.length;
        if (1 < c) {
            if (c % 2)
                throw Error("Uneven number of arguments");
            for (var d = 0; d < c; d += 2)
                Qd(this, arguments[d], arguments[d + 1])
        } else if (a) {
            a instanceof Pd ? (c = a.sa(),
            d = a.Y()) : (c = ab(a),
            d = $a(a));
            for (var e = 0; e < c.length; e++)
                Qd(this, c[e], d[e])
        }
    }
    g = Pd.prototype;
    g.W = function() {
        return this.h
    }
    ;
    g.Y = function() {
        Rd(this);
        for (var a = [], b = 0; b < this.e.length; b++)
            a.push(this.f[this.e[b]]);
        return a
    }
    ;
    g.sa = function() {
        Rd(this);
        return this.e.concat()
    }
    ;
    g.qb = function(a) {
        for (var b = 0; b < this.e.length; b++) {
            var c = this.e[b];
            if (Sd(this.f, c) && this.f[c] == a)
                return !0
        }
        return !1
    }
    ;
    g.equals = function(a, b) {
        if (this === a)
            return !0;
        if (this.h != a.W())
            return !1;
        var c = b || Td;
        Rd(this);
        for (var d, e = 0; d = this.e[e]; e++)
            if (!c(this.get(d), a.get(d)))
                return !1;
        return !0
    }
    ;
    function Td(a, b) {
        return a === b
    }
    g.isEmpty = function() {
        return 0 == this.h
    }
    ;
    g.clear = function() {
        this.f = {};
        this.Ba = this.h = this.e.length = 0
    }
    ;
    g.remove = function(a) {
        return Sd(this.f, a) ? (delete this.f[a],
        this.h--,
        this.Ba++,
        this.e.length > 2 * this.h && Rd(this),
        !0) : !1
    }
    ;
    function Rd(a) {
        if (a.h != a.e.length) {
            for (var b = 0, c = 0; b < a.e.length; ) {
                var d = a.e[b];
                Sd(a.f, d) && (a.e[c++] = d);
                b++
            }
            a.e.length = c
        }
        if (a.h != a.e.length) {
            for (var e = {}, c = b = 0; b < a.e.length; )
                d = a.e[b],
                Sd(e, d) || (a.e[c++] = d,
                e[d] = 1),
                b++;
            a.e.length = c
        }
    }
    g.get = function(a, b) {
        return Sd(this.f, a) ? this.f[a] : b
    }
    ;
    function Qd(a, b, c) {
        Sd(a.f, b) || (a.h++,
        a.e.push(b),
        a.Ba++);
        a.f[b] = c
    }
    g.forEach = function(a, b) {
        for (var c = this.sa(), d = 0; d < c.length; d++) {
            var e = c[d]
              , f = this.get(e);
            a.call(b, f, e, this)
        }
    }
    ;
    g.clone = function() {
        return new Pd(this)
    }
    ;
    g.pa = function(a) {
        Rd(this);
        var b = 0
          , c = this.e
          , d = this.f
          , e = this.Ba
          , f = this
          , h = new Ld;
        h.next = function() {
            for (; ; ) {
                if (e != f.Ba)
                    throw Error("The map has changed since the iterator was created");
                if (b >= c.length)
                    throw Kd;
                var h = c[b++];
                return a ? h : d[h]
            }
        }
        ;
        return h
    }
    ;
    function Sd(a, b) {
        return Object.prototype.hasOwnProperty.call(a, b)
    }
    ;function Ud(a) {
        return "function" == typeof a.W ? a.W() : ea(a) || u(a) ? a.length : Xa(a)
    }
    function Vd(a) {
        if ("function" == typeof a.Y)
            return a.Y();
        if (u(a))
            return a.split("");
        if (ea(a)) {
            for (var b = [], c = a.length, d = 0; d < c; d++)
                b.push(a[d]);
            return b
        }
        return $a(a)
    }
    function Wd(a) {
        if ("function" == typeof a.sa)
            return a.sa();
        if ("function" != typeof a.Y) {
            if (ea(a) || u(a)) {
                var b = [];
                a = a.length;
                for (var c = 0; c < a; c++)
                    b.push(c);
                return b
            }
            return ab(a)
        }
    }
    function Xd(a, b) {
        if ("function" == typeof a.forEach)
            a.forEach(b, void 0);
        else if (ea(a) || u(a))
            A(a, b, void 0);
        else
            for (var c = Wd(a), d = Vd(a), e = d.length, f = 0; f < e; f++)
                b.call(void 0, d[f], c && c[f], a)
    }
    function Yd(a, b) {
        if ("function" == typeof a.every)
            return a.every(b, void 0);
        if (ea(a) || u(a))
            return Ea(a, b, void 0);
        for (var c = Wd(a), d = Vd(a), e = d.length, f = 0; f < e; f++)
            if (!b.call(void 0, d[f], c && c[f], a))
                return !1;
        return !0
    }
    ;function Zd(a) {
        this.e = new Pd;
        if (a) {
            a = Vd(a);
            for (var b = a.length, c = 0; c < b; c++)
                this.add(a[c])
        }
    }
    function $d(a) {
        var b = typeof a;
        return "object" == b && a || "function" == b ? "o" + ia(a) : b.substr(0, 1) + a
    }
    g = Zd.prototype;
    g.W = function() {
        return this.e.W()
    }
    ;
    g.add = function(a) {
        Qd(this.e, $d(a), a)
    }
    ;
    g.removeAll = function(a) {
        a = Vd(a);
        for (var b = a.length, c = 0; c < b; c++)
            this.remove(a[c])
    }
    ;
    g.remove = function(a) {
        return this.e.remove($d(a))
    }
    ;
    g.clear = function() {
        this.e.clear()
    }
    ;
    g.isEmpty = function() {
        return this.e.isEmpty()
    }
    ;
    g.contains = function(a) {
        a = $d(a);
        return Sd(this.e.f, a)
    }
    ;
    g.Y = function() {
        return this.e.Y()
    }
    ;
    g.clone = function() {
        return new Zd(this)
    }
    ;
    g.equals = function(a) {
        return this.W() == Ud(a) && ae(this, a)
    }
    ;
    function ae(a, b) {
        var c = Ud(b);
        if (a.W() > c)
            return !1;
        !(b instanceof Zd) && 5 < c && (b = new Zd(b));
        return Yd(a, function(a) {
            var c = b;
            return "function" == typeof c.contains ? c.contains(a) : "function" == typeof c.qb ? c.qb(a) : ea(c) || u(c) ? Ha(c, a) : Za(c, a)
        })
    }
    g.pa = function() {
        return this.e.pa(!1)
    }
    ;
    function be() {}
    ;function ce(a, b, c) {
        this.k = c;
        this.h = a;
        this.j = b;
        this.f = 0;
        this.e = null
    }
    ce.prototype.get = function() {
        var a;
        0 < this.f ? (this.f--,
        a = this.e,
        this.e = a.next,
        a.next = null) : a = this.h();
        return a
    }
    ;
    ce.prototype.put = function(a) {
        this.j(a);
        this.f < this.k && (this.f++,
        a.next = this.e,
        this.e = a)
    }
    ;
    function de(a) {
        m.setTimeout(function() {
            throw a;
        }, 0)
    }
    var ee;
    function fe() {
        var a = m.MessageChannel;
        "undefined" === typeof a && "undefined" !== typeof window && window.postMessage && window.addEventListener && !J("Presto") && (a = function() {
            var a = document.createElement("iframe");
            a.style.display = "none";
            a.src = "";
            document.documentElement.appendChild(a);
            var b = a.contentWindow
              , a = b.document;
            a.open();
            a.write("");
            a.close();
            var c = "callImmediate" + Math.random()
              , d = "file:" == b.location.protocol ? "*" : b.location.protocol + "//" + b.location.host
              , a = v(function(a) {
                if (("*" == d || a.origin == d) && a.data == c)
                    this.port1.onmessage()
            }, this);
            b.addEventListener("message", a, !1);
            this.port1 = {};
            this.port2 = {
                postMessage: function() {
                    b.postMessage(c, d)
                }
            }
        }
        );
        if ("undefined" !== typeof a && !sc()) {
            var b = new a
              , c = {}
              , d = c;
            b.port1.onmessage = function() {
                if (p(c.next)) {
                    c = c.next;
                    var a = c.sc;
                    c.sc = null;
                    a()
                }
            }
            ;
            return function(a) {
                d.next = {
                    sc: a
                };
                d = d.next;
                b.port2.postMessage(0)
            }
        }
        return "undefined" !== typeof document && "onreadystatechange"in document.createElement("script") ? function(a) {
            var b = document.createElement("script");
            b.onreadystatechange = function() {
                b.onreadystatechange = null;
                b.parentNode.removeChild(b);
                b = null;
                a();
                a = null
            }
            ;
            document.documentElement.appendChild(b)
        }
        : function(a) {
            m.setTimeout(a, 0)
        }
    }
    ;function ge() {
        this.f = this.e = null
    }
    var ie = new ce(function() {
        return new he
    }
    ,function(a) {
        a.reset()
    }
    ,100);
    ge.prototype.add = function(a, b) {
        var c = ie.get();
        c.e = a;
        c.scope = b;
        c.next = null;
        this.f ? this.f.next = c : this.e = c;
        this.f = c
    }
    ;
    ge.prototype.remove = function() {
        var a = null;
        this.e && (a = this.e,
        this.e = this.e.next,
        this.e || (this.f = null),
        a.next = null);
        return a
    }
    ;
    function he() {
        this.next = this.scope = this.e = null
    }
    he.prototype.reset = function() {
        this.next = this.scope = this.e = null
    }
    ;
    function je(a, b) {
        ke || le();
        me || (ke(),
        me = !0);
        ne.add(a, b)
    }
    var ke;
    function le() {
        if (m.Promise && m.Promise.resolve) {
            var a = m.Promise.resolve();
            ke = function() {
                a.then(oe)
            }
        } else
            ke = function() {
                var a = oe;
                !ga(m.setImmediate) || m.Window && m.Window.prototype && m.Window.prototype.setImmediate == m.setImmediate ? (ee || (ee = fe()),
                ee(a)) : m.setImmediate(a)
            }
    }
    var me = !1
      , ne = new ge;
    function oe() {
        for (var a = null; a = ne.remove(); ) {
            try {
                a.e.call(a.scope)
            } catch (b) {
                de(b)
            }
            ie.put(a)
        }
        me = !1
    }
    ;function pe(a, b) {
        this.e = 0;
        this.o = void 0;
        this.k = this.f = this.h = null;
        this.j = this.l = !1;
        if (a == qe)
            re(this, 2, b);
        else
            try {
                var c = this;
                a.call(b, function(a) {
                    re(c, 2, a)
                }, function(a) {
                    re(c, 3, a)
                })
            } catch (d) {
                re(this, 3, d)
            }
    }
    function se() {
        this.next = this.context = this.f = this.h = this.e = null;
        this.k = !1
    }
    se.prototype.reset = function() {
        this.context = this.f = this.h = this.e = null;
        this.k = !1
    }
    ;
    var te = new ce(function() {
        return new se
    }
    ,function(a) {
        a.reset()
    }
    ,100);
    function ue(a, b, c) {
        var d = te.get();
        d.h = a;
        d.f = b;
        d.context = c;
        return d
    }
    function qe() {}
    pe.prototype.then = function(a, b, c) {
        return ve(this, ga(a) ? a : null, ga(b) ? b : null, c)
    }
    ;
    pe.prototype.then = pe.prototype.then;
    pe.prototype.$goog_Thenable = !0;
    pe.prototype.cancel = function(a) {
        0 == this.e && je(function() {
            var b = new we(a);
            xe(this, b)
        }, this)
    }
    ;
    function xe(a, b) {
        if (0 == a.e)
            if (a.h) {
                var c = a.h;
                if (c.f) {
                    for (var d = 0, e = null, f = null, h = c.f; h && (h.k || (d++,
                    h.e == a && (e = h),
                    !(e && 1 < d))); h = h.next)
                        e || (f = h);
                    e && (0 == c.e && 1 == d ? xe(c, b) : (f ? (d = f,
                    d.next == c.k && (c.k = d),
                    d.next = d.next.next) : ye(c),
                    ze(c, e, 3, b)))
                }
                a.h = null
            } else
                re(a, 3, b)
    }
    function Ae(a, b) {
        a.f || 2 != a.e && 3 != a.e || Be(a);
        a.k ? a.k.next = b : a.f = b;
        a.k = b
    }
    function ve(a, b, c, d) {
        var e = ue(null, null, null);
        e.e = new pe(function(a, h) {
            e.h = b ? function(c) {
                try {
                    var e = b.call(d, c);
                    a(e)
                } catch (n) {
                    h(n)
                }
            }
            : a;
            e.f = c ? function(b) {
                try {
                    var e = c.call(d, b);
                    !p(e) && b instanceof we ? h(b) : a(e)
                } catch (n) {
                    h(n)
                }
            }
            : h
        }
        );
        e.e.h = a;
        Ae(a, e);
        return e.e
    }
    pe.prototype.C = function(a) {
        this.e = 0;
        re(this, 2, a)
    }
    ;
    pe.prototype.H = function(a) {
        this.e = 0;
        re(this, 3, a)
    }
    ;
    function re(a, b, c) {
        if (0 == a.e) {
            if (a == c)
                b = 3,
                c = new TypeError("Promise cannot resolve to itself");
            else {
                var d;
                if (c)
                    try {
                        d = !!c.$goog_Thenable
                    } catch (e) {
                        d = !1
                    }
                else
                    d = !1;
                if (d) {
                    a.e = 1;
                    b = c;
                    c = a.C;
                    d = a.H;
                    b instanceof pe ? Ae(b, ue(c || t, d || null, a)) : b.then(c, d, a);
                    return
                }
                if (ha(c))
                    try {
                        var f = c.then;
                        if (ga(f)) {
                            Ce(a, c, f);
                            return
                        }
                    } catch (h) {
                        b = 3,
                        c = h
                    }
            }
            a.o = c;
            a.e = b;
            a.h = null;
            Be(a);
            3 != b || c instanceof we || De(a, c)
        }
    }
    function Ce(a, b, c) {
        function d(b) {
            f || (f = !0,
            a.H(b))
        }
        function e(b) {
            f || (f = !0,
            a.C(b))
        }
        a.e = 1;
        var f = !1;
        try {
            c.call(b, e, d)
        } catch (h) {
            d(h)
        }
    }
    function Be(a) {
        a.l || (a.l = !0,
        je(a.I, a))
    }
    function ye(a) {
        var b = null;
        a.f && (b = a.f,
        a.f = b.next,
        b.next = null);
        a.f || (a.k = null);
        return b
    }
    pe.prototype.I = function() {
        for (var a = null; a = ye(this); )
            ze(this, a, this.e, this.o);
        this.l = !1
    }
    ;
    function ze(a, b, c, d) {
        b.e && (b.e.h = null);
        if (2 == c)
            b.h.call(b.context, d);
        else if (null != b.f) {
            if (!b.k)
                for (; a && a.j; a = a.h)
                    a.j = !1;
            b.f.call(b.context, d)
        }
        te.put(b)
    }
    function De(a, b) {
        a.j = !0;
        je(function() {
            a.j && Ee.call(null, b)
        })
    }
    var Ee = de;
    function we(a) {
        pa.call(this, a)
    }
    y(we, pa);
    we.prototype.name = "cancel";
    function Fe(a) {
        this.e = a;
        a.then(v(function() {}, this))
    }
    function Ge(a, b, c, d) {
        for (var e = Array(arguments.length - 3), f = 3; f < arguments.length; f++)
            e[f - 3] = arguments[f];
        f = He(a, b, c).then(function(a) {
            return a.apply(this, e)
        });
        return new Fe(f)
    }
    var Ie = {};
    function He(a, b, c) {
        var d = "https://www.gstatic.com/feedback/js/help/" + (a && "prod" != a && "canary" != a ? "nonprod" : "prod") + "/" + b;
        if (a = Ie[c])
            return a;
        a = (a = r(c)) ? new pe(qe,a) : (new pe(function(a, b) {
            var c = document.createElement("script");
            c.async = !0;
            c.src = d;
            c.onload = c.onreadystatechange = function() {
                c.readyState && "loaded" != c.readyState && "complete" != c.readyState || a()
            }
            ;
            c.onerror = b;
            (document.head || document.getElementsByTagName("head")[0]).appendChild(c)
        }
        )).then(function() {
            var a = r(c);
            if (!a)
                throw Error("Failed to load " + c + " from " + d);
            return a
        });
        return Ie[c] = a
    }
    function Je(a, b, c) {
        a.e.then(function(a) {
            var e = a[b];
            if (!e)
                throw Error("Method not found: " + b);
            return e.apply(a, c)
        })
    }
    ;function Ke(a) {
        this.e = a
    }
    function Le(a, b) {
        var c = b || {}
          , c = Ge("prod", "service/lazy.min.js", "help.service.Lazy.create", a, {
            apiKey: c.jf || c.apiKey,
            environment: c.lf || c.environment,
            helpCenterPath: c.nf || c.helpCenterPath,
            locale: c.locale || c.locale,
            productData: c.qf || c.productData,
            receiverUri: c.rf || c.receiverUri,
            theme: c.theme || c.theme,
            window: c.window || c.window
        });
        return new Ke(c)
    }
    Ke.prototype.f = function(a) {
        Je(this.e, "startFeedback", arguments)
    }
    ;
    Ke.prototype.h = function(a) {
        Je(this.e, "startHelp", arguments)
    }
    ;
    var Me = !1;
    function Ne(a) {
        if (a = a.match(/[\d]+/g))
            a.length = 3,
            a.join(".")
    }
    if (navigator.plugins && navigator.plugins.length) {
        var Oe = navigator.plugins["Shockwave Flash"];
        Oe && (Me = !0,
        Oe.description && Ne(Oe.description));
        navigator.plugins["Shockwave Flash 2.0"] && (Me = !0)
    } else if (navigator.mimeTypes && navigator.mimeTypes.length) {
        var Pe = navigator.mimeTypes["application/x-shockwave-flash"];
        (Me = Pe && Pe.enabledPlugin) && Ne(Pe.enabledPlugin.description)
    } else
        try {
            var Qe = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")
              , Me = !0;
            Ne(Qe.GetVariable("$version"))
        } catch (Re) {
            try {
                Qe = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"),
                Me = !0
            } catch (Se) {
                try {
                    Qe = new ActiveXObject("ShockwaveFlash.ShockwaveFlash"),
                    Me = !0,
                    Ne(Qe.GetVariable("$version"))
                } catch (Te) {}
            }
        }
    ;var Ue = J("Firefox")
      , Ve = vc() || J("iPod")
      , We = J("iPad")
      , Xe = J("Android") && !(tc() || J("Firefox") || rc() || J("Silk"))
      , Ye = tc()
      , Ze = J("Safari") && !(tc() || J("Coast") || rc() || sc() || J("Silk") || J("Android")) && !(vc() || J("iPad") || J("iPod"));
    function $e(a) {
        return (a = a.exec(oc)) ? a[1] : ""
    }
    (function() {
        if (Ue)
            return $e(/Firefox\/([0-9.]+)/);
        if (K || wc)
            return Dc;
        if (Ye)
            return $e(/Chrome\/([0-9.]+)/);
        if (Ze && !(vc() || J("iPad") || J("iPod")))
            return $e(/Version\/([0-9.]+)/);
        if (Ve || We) {
            var a;
            if (a = /Version\/(\S+).*Mobile\/(\S+)/.exec(oc))
                return a[1] + "." + a[2]
        } else if (Xe)
            return (a = $e(/Android\s+([0-9.]+)/)) ? a : $e(/Version\/([0-9.]+)/);
        return ""
    }
    )();
    function af() {
        this.h = this.f = this.e = 0;
        this.k = "";
        var a = r("window.navigator.plugins")
          , b = r("window.navigator.mimeTypes")
          , a = a && a["Shockwave Flash"]
          , b = b && b["application/x-shockwave-flash"]
          , b = a && b && b.enabledPlugin && a.description || "";
        if (a = b) {
            var c = a.indexOf("Shockwave Flash");
            0 <= c && (a = a.substr(c + 15));
            for (var c = a.split(" "), d = "", a = "", e = 0, f = c.length; e < f; e++)
                if (d)
                    if (a)
                        break;
                    else
                        a = c[e];
                else
                    d = c[e];
            d = d.split(".");
            c = parseInt(d[0], 10) || 0;
            d = parseInt(d[1], 10) || 0;
            e = 0;
            if ("r" == a.charAt(0) || "d" == a.charAt(0))
                e = parseInt(a.substr(1), 10) || 0;
            a = [c, d, e]
        } else
            a = [0, 0, 0];
        this.k = b;
        b = a;
        this.e = b[0];
        this.f = b[1];
        this.h = b[2];
        if (0 >= this.e) {
            var h, k, l, n;
            if (Lb)
                try {
                    h = new ActiveXObject("ShockwaveFlash.ShockwaveFlash")
                } catch (x) {
                    h = null
                }
            else
                l = document.body,
                n = document.createElement("object"),
                n.setAttribute("type", "application/x-shockwave-flash"),
                h = l.appendChild(n);
            if (h && "GetVariable"in h)
                try {
                    k = h.GetVariable("$version")
                } catch (Z) {
                    k = ""
                }
            l && n && l.removeChild(n);
            (h = k || "") ? (h = h.split(" ")[1].split(","),
            h = [parseInt(h[0], 10) || 0, parseInt(h[1], 10) || 0, parseInt(h[2], 10) || 0]) : h = [0, 0, 0];
            this.e = h[0];
            this.f = h[1];
            this.h = h[2]
        }
    }
    ba(af);
    af.prototype.getVersion = function() {
        return [this.e, this.f, this.h]
    }
    ;
    function bf(a, b, c, d) {
        b = "string" == typeof b ? b.split(".") : [b, c, d];
        b[0] = parseInt(b[0], 10) || 0;
        b[1] = parseInt(b[1], 10) || 0;
        b[2] = parseInt(b[2], 10) || 0;
        return a.e > b[0] || a.e == b[0] && a.f > b[1] || a.e == b[0] && a.f == b[1] && a.h >= b[2]
    }
    function cf(a) {
        return -1 < a.k.indexOf("Gnash") && -1 == a.k.indexOf("AVM2") || 9 == a.e && 1 == a.f || 9 == a.e && 0 == a.f && 1 == a.h ? !1 : 9 <= a.e
    }
    function df(a) {
        return Ac ? !bf(a, 11, 2) : zc ? !bf(a, 11, 3) : !cf(a)
    }
    ;function ef(a, b, c, d) {
        this.top = a;
        this.right = b;
        this.bottom = c;
        this.left = d
    }
    ef.prototype.clone = function() {
        return new ef(this.top,this.right,this.bottom,this.left)
    }
    ;
    ef.prototype.contains = function(a) {
        return this && a ? a instanceof ef ? a.left >= this.left && a.right <= this.right && a.top >= this.top && a.bottom <= this.bottom : a.x >= this.left && a.x <= this.right && a.y >= this.top && a.y <= this.bottom : !1
    }
    ;
    ef.prototype.floor = function() {
        this.top = Math.floor(this.top);
        this.right = Math.floor(this.right);
        this.bottom = Math.floor(this.bottom);
        this.left = Math.floor(this.left);
        return this
    }
    ;
    ef.prototype.round = function() {
        this.top = Math.round(this.top);
        this.right = Math.round(this.right);
        this.bottom = Math.round(this.bottom);
        this.left = Math.round(this.left);
        return this
    }
    ;
    function ff(a, b, c, d) {
        this.left = a;
        this.top = b;
        this.width = c;
        this.height = d
    }
    ff.prototype.clone = function() {
        return new ff(this.left,this.top,this.width,this.height)
    }
    ;
    ff.prototype.contains = function(a) {
        return a instanceof ff ? this.left <= a.left && this.left + this.width >= a.left + a.width && this.top <= a.top && this.top + this.height >= a.top + a.height : a.x >= this.left && a.x <= this.left + this.width && a.y >= this.top && a.y <= this.top + this.height
    }
    ;
    ff.prototype.floor = function() {
        this.left = Math.floor(this.left);
        this.top = Math.floor(this.top);
        this.width = Math.floor(this.width);
        this.height = Math.floor(this.height);
        return this
    }
    ;
    ff.prototype.round = function() {
        this.left = Math.round(this.left);
        this.top = Math.round(this.top);
        this.width = Math.round(this.width);
        this.height = Math.round(this.height);
        return this
    }
    ;
    function gf(a, b) {
        var c = Mc(a);
        return c.defaultView && c.defaultView.getComputedStyle && (c = c.defaultView.getComputedStyle(a, null)) ? c[b] || c.getPropertyValue(b) || "" : ""
    }
    function hf(a, b) {
        return gf(a, b) || (a.currentStyle ? a.currentStyle[b] : null) || a.style && a.style[b]
    }
    function jf(a) {
        var b;
        try {
            b = a.getBoundingClientRect()
        } catch (c) {
            return {
                left: 0,
                top: 0,
                right: 0,
                bottom: 0
            }
        }
        K && a.ownerDocument.body && (a = a.ownerDocument,
        b.left -= a.documentElement.clientLeft + a.body.clientLeft,
        b.top -= a.documentElement.clientTop + a.body.clientTop);
        return b
    }
    function kf(a, b) {
        "number" == typeof a && (a = (b ? Math.round(a) : a) + "px");
        return a
    }
    function lf(a) {
        var b = mf;
        if ("none" != hf(a, "display"))
            return b(a);
        var c = a.style
          , d = c.display
          , e = c.visibility
          , f = c.position;
        c.visibility = "hidden";
        c.position = "absolute";
        c.display = "inline";
        a = b(a);
        c.display = d;
        c.position = f;
        c.visibility = e;
        return a
    }
    function mf(a) {
        var b = a.offsetWidth
          , c = a.offsetHeight
          , d = yc && !b && !c;
        return p(b) && !d || !a.getBoundingClientRect ? new nc(b,c) : (a = jf(a),
        new nc(a.right - a.left,a.bottom - a.top))
    }
    function nf(a, b) {
        if (/^\d+px?$/.test(b))
            return parseInt(b, 10);
        var c = a.style.left
          , d = a.runtimeStyle.left;
        a.runtimeStyle.left = a.currentStyle.left;
        a.style.left = b;
        var e = a.style.pixelLeft;
        a.style.left = c;
        a.runtimeStyle.left = d;
        return e
    }
    function of(a, b) {
        var c = a.currentStyle ? a.currentStyle[b] : null;
        return c ? nf(a, c) : 0
    }
    var pf = {
        thin: 2,
        medium: 4,
        thick: 6
    };
    function qf(a, b) {
        if ("none" == (a.currentStyle ? a.currentStyle[b + "Style"] : null))
            return 0;
        var c = a.currentStyle ? a.currentStyle[b + "Width"] : null;
        return c in pf ? pf[c] : nf(a, c)
    }
    ;function rf(a, b) {
        (a = Nc(a)) && a.style && (a.style.display = b ? "" : "none",
        lc(a, "hid", !b))
    }
    function sf(a) {
        A(arguments, function(a) {
            rf(a, !0)
        })
    }
    function tf(a) {
        A(arguments, function(a) {
            rf(a, !1)
        })
    }
    ;var uf = {};
    function vf(a, b) {
        var c = F("FEEDBACK_LOCALE_LANGUAGE")
          , d = F("FEEDBACK_LOCALE_EXTRAS", {});
        a ? ib(uf, a) : ib(uf, d);
        try {
            var e, f = r("yt.player.getPlayerByElement");
            (e = f ? f("player-api") : null) && e.pauseVideo && e.pauseVideo();
            var h = af.getInstance();
            uf.flashVersion = h.getVersion().join(".");
            e && (uf.playback_id = e.getVideoData().cpn)
        } catch (k) {}
        b && ib(uf, {
            trackingParam: b
        });
        return {
            helpCenterPath: "/youtube",
            locale: c,
            productData: uf
        }
    }
    function wf() {
        var a = F("SESSION_INDEX")
          , b = F("FEEDBACK_BUCKET_ID")
          , c = {
            abuseLink: "https://support.google.com/youtube/bin/answer.py?answer=140536",
            customZIndex: "2000000005"
        };
        a && (c.authuser = a + "");
        b && (c.bucket = b);
        return c
    }
    function xf(a, b) {
        try {
            var c = (a || "59") + ""
              , d = vf(b)
              , e = wf();
            Le(c, d).f(e);
            return !1
        } catch (f) {
            return !0
        }
    }
    function yf(a, b, c, d) {
        var e;
        d = (d || "59") + "";
        c = vf(c, void 0);
        a = {
            context: b,
            anchor: void 0,
            enableSendFeedback: !0,
            defaultHelpArticleId: a
        };
        ib(a, wf());
        try {
            Le(d, c).h(a),
            e = !1
        } catch (f) {
            e = !0
        }
        return e
    }
    ;function zf(a) {
        a = a || {};
        this.url = a.url || "";
        this.urlV9As2 = a.url_v9as2 || "";
        this.args = a.args || fb(Af);
        this.assets = a.assets || {};
        this.attrs = a.attrs || fb(Bf);
        this.params = a.params || fb(Cf);
        this.minVersion = a.min_version || "8.0.0";
        this.fallback = a.fallback || null;
        this.fallbackMessage = a.fallbackMessage || null;
        this.html5 = !!a.html5;
        this.disable = a.disable || {};
        this.loaded = !!a.loaded;
        this.messages = a.messages || {}
    }
    var Af = {
        enablejsapi: 1
    }
      , Bf = {}
      , Cf = {
        allowscriptaccess: "always",
        allowfullscreen: "true",
        bgcolor: "#000000"
    };
    function Df(a) {
        a instanceof zf || (a = new zf(a));
        return a
    }
    zf.prototype.clone = function() {
        var a = new zf, b;
        for (b in this) {
            var c = this[b];
            "object" == ca(c) ? a[b] = fb(c) : a[b] = c
        }
        return a
    }
    ;
    function Ef(a) {
        Ef[" "](a);
        return a
    }
    Ef[" "] = t;
    var Ff = !K || Gc(9)
      , Gf = K && !Fc("9");
    !yc || Fc("528");
    xc && Fc("1.9b") || K && Fc("8") || wc && Fc("9.5") || yc && Fc("528");
    xc && !Fc("8") || K && Fc("9");
    function Hf(a, b) {
        this.type = a;
        this.currentTarget = this.target = b;
        this.defaultPrevented = !1;
        this.Uc = !0
    }
    Hf.prototype.preventDefault = function() {
        this.defaultPrevented = !0;
        this.Uc = !1
    }
    ;
    function If(a, b) {
        Hf.call(this, a ? a.type : "");
        this.relatedTarget = this.currentTarget = this.target = null;
        this.charCode = this.keyCode = this.button = this.clientY = this.clientX = 0;
        this.shiftKey = this.altKey = this.ctrlKey = !1;
        this.e = this.state = null;
        a && this.init(a, b)
    }
    y(If, Hf);
    If.prototype.init = function(a, b) {
        var c = this.type = a.type;
        this.target = a.target || a.srcElement;
        this.currentTarget = b;
        var d = a.relatedTarget;
        if (d) {
            if (xc) {
                var e;
                t: {
                    try {
                        Ef(d.nodeName);
                        e = !0;
                        break t
                    } catch (f) {}
                    e = !1
                }
                e || (d = null)
            }
        } else
            "mouseover" == c ? d = a.fromElement : "mouseout" == c && (d = a.toElement);
        this.relatedTarget = d;
        this.clientX = void 0 !== a.clientX ? a.clientX : a.pageX;
        this.clientY = void 0 !== a.clientY ? a.clientY : a.pageY;
        this.button = a.button;
        this.keyCode = a.keyCode || 0;
        this.charCode = a.charCode || ("keypress" == c ? a.keyCode : 0);
        this.ctrlKey = a.ctrlKey;
        this.altKey = a.altKey;
        this.shiftKey = a.shiftKey;
        this.state = a.state;
        this.e = a;
        a.defaultPrevented && this.preventDefault()
    }
    ;
    If.prototype.preventDefault = function() {
        If.G.preventDefault.call(this);
        var a = this.e;
        if (a.preventDefault)
            a.preventDefault();
        else if (a.returnValue = !1,
        Gf)
            try {
                if (a.ctrlKey || 112 <= a.keyCode && 123 >= a.keyCode)
                    a.keyCode = -1
            } catch (b) {}
    }
    ;
    If.prototype.f = function() {
        return this.e
    }
    ;
    var Jf = "closure_listenable_" + (1E6 * Math.random() | 0)
      , Kf = 0;
    function Lf(a, b, c, d, e) {
        this.ua = a;
        this.e = null;
        this.src = b;
        this.type = c;
        this.ob = !!d;
        this.ub = e;
        this.key = ++Kf;
        this.Xa = this.nb = !1
    }
    function Mf(a) {
        a.Xa = !0;
        a.ua = null;
        a.e = null;
        a.src = null;
        a.ub = null
    }
    ;function Nf(a) {
        this.src = a;
        this.e = {};
        this.f = 0
    }
    Nf.prototype.add = function(a, b, c, d, e) {
        var f = a.toString();
        a = this.e[f];
        a || (a = this.e[f] = [],
        this.f++);
        var h = Of(a, b, d, e);
        -1 < h ? (b = a[h],
        c || (b.nb = !1)) : (b = new Lf(b,this.src,f,!!d,e),
        b.nb = c,
        a.push(b));
        return b
    }
    ;
    Nf.prototype.remove = function(a, b, c, d) {
        a = a.toString();
        if (!(a in this.e))
            return !1;
        var e = this.e[a];
        b = Of(e, b, c, d);
        return -1 < b ? (Mf(e[b]),
        z.splice.call(e, b, 1),
        0 == e.length && (delete this.e[a],
        this.f--),
        !0) : !1
    }
    ;
    function Pf(a, b) {
        var c = b.type;
        if (!(c in a.e))
            return !1;
        var d = La(a.e[c], b);
        d && (Mf(b),
        0 == a.e[c].length && (delete a.e[c],
        a.f--));
        return d
    }
    Nf.prototype.removeAll = function(a) {
        a = a && a.toString();
        var b = 0, c;
        for (c in this.e)
            if (!a || c == a) {
                for (var d = this.e[c], e = 0; e < d.length; e++)
                    ++b,
                    Mf(d[e]);
                delete this.e[c];
                this.f--
            }
        return b
    }
    ;
    function Qf(a, b, c, d, e) {
        a = a.e[b.toString()];
        b = -1;
        a && (b = Of(a, c, d, e));
        return -1 < b ? a[b] : null
    }
    function Of(a, b, c, d) {
        for (var e = 0; e < a.length; ++e) {
            var f = a[e];
            if (!f.Xa && f.ua == b && f.ob == !!c && f.ub == d)
                return e
        }
        return -1
    }
    ;var Rf = "closure_lm_" + (1E6 * Math.random() | 0)
      , Sf = {}
      , Tf = 0;
    function Uf(a, b, c, d, e) {
        if (da(b)) {
            for (var f = 0; f < b.length; f++)
                Uf(a, b[f], c, d, e);
            return null
        }
        c = Vf(c);
        if (a && a[Jf])
            a = a.xb(b, c, d, e);
        else {
            if (!b)
                throw Error("Invalid event type");
            var f = !!d
              , h = Wf(a);
            h || (a[Rf] = h = new Nf(a));
            c = h.add(b, c, !1, d, e);
            c.e || (d = Xf(),
            c.e = d,
            d.src = a,
            d.ua = c,
            a.addEventListener ? a.addEventListener(b.toString(), d, f) : a.attachEvent(Yf(b.toString()), d),
            Tf++);
            a = c
        }
        return a
    }
    function Xf() {
        var a = Zf
          , b = Ff ? function(c) {
            return a.call(b.src, b.ua, c)
        }
        : function(c) {
            c = a.call(b.src, b.ua, c);
            if (!c)
                return c
        }
        ;
        return b
    }
    function $f(a, b, c, d, e) {
        if (da(b))
            for (var f = 0; f < b.length; f++)
                $f(a, b[f], c, d, e);
        else
            c = Vf(c),
            a && a[Jf] ? a.ic(b, c, d, e) : a && (a = Wf(a)) && (b = Qf(a, b, c, !!d, e)) && ag(b)
    }
    function ag(a) {
        if (fa(a) || !a || a.Xa)
            return !1;
        var b = a.src;
        if (b && b[Jf])
            return Pf(b.xa, a);
        var c = a.type
          , d = a.e;
        b.removeEventListener ? b.removeEventListener(c, d, a.ob) : b.detachEvent && b.detachEvent(Yf(c), d);
        Tf--;
        (c = Wf(b)) ? (Pf(c, a),
        0 == c.f && (c.src = null,
        b[Rf] = null)) : Mf(a);
        return !0
    }
    function Yf(a) {
        return a in Sf ? Sf[a] : Sf[a] = "on" + a
    }
    function bg(a, b, c, d) {
        var e = !0;
        if (a = Wf(a))
            if (b = a.e[b.toString()])
                for (b = b.concat(),
                a = 0; a < b.length; a++) {
                    var f = b[a];
                    f && f.ob == c && !f.Xa && (f = cg(f, d),
                    e = e && !1 !== f)
                }
        return e
    }
    function cg(a, b) {
        var c = a.ua
          , d = a.ub || a.src;
        a.nb && ag(a);
        return c.call(d, b)
    }
    function Zf(a, b) {
        if (a.Xa)
            return !0;
        if (!Ff) {
            var c = b || r("window.event")
              , d = new If(c,this)
              , e = !0;
            if (!(0 > c.keyCode || void 0 != c.returnValue)) {
                t: {
                    var f = !1;
                    if (0 == c.keyCode)
                        try {
                            c.keyCode = -1;
                            break t
                        } catch (h) {
                            f = !0
                        }
                    if (f || void 0 == c.returnValue)
                        c.returnValue = !0
                }
                c = [];
                for (f = d.currentTarget; f; f = f.parentNode)
                    c.push(f);
                for (var f = a.type, k = c.length - 1; 0 <= k; k--) {
                    d.currentTarget = c[k];
                    var l = bg(c[k], f, !0, d)
                      , e = e && l
                }
                for (k = 0; k < c.length; k++)
                    d.currentTarget = c[k],
                    l = bg(c[k], f, !1, d),
                    e = e && l
            }
            return e
        }
        return cg(a, new If(b,this))
    }
    function Wf(a) {
        a = a[Rf];
        return a instanceof Nf ? a : null
    }
    var dg = "__closure_events_fn_" + (1E9 * Math.random() >>> 0);
    function Vf(a) {
        if (ga(a))
            return a;
        a[dg] || (a[dg] = function(b) {
            return a.handleEvent(b)
        }
        );
        return a[dg]
    }
    ;function eg() {
        var a = nd("PREF");
        if (a)
            for (var a = unescape(a).split("&"), b = 0; b < a.length; b++) {
                var c = a[b].split("=")
                  , d = c[0];
                (c = c[1]) && (fg[d] = c.toString())
            }
    }
    ba(eg);
    var fg = r("yt.prefs.UserPrefs.prefs_") || {};
    q("yt.prefs.UserPrefs.prefs_", fg, void 0);
    function gg(a) {
        if (/^f([1-9][0-9]*)$/.test(a))
            throw "ExpectedRegexMatch: " + a;
    }
    function hg(a) {
        if (!/^\w+$/.test(a))
            throw "ExpectedRegexMismatch: " + a;
    }
    function ig(a) {
        return void 0 !== fg[a] ? fg[a].toString() : null
    }
    eg.prototype.get = function(a, b) {
        hg(a);
        gg(a);
        var c = ig(a);
        return null != c ? c : b ? b : ""
    }
    ;
    eg.prototype.remove = function(a) {
        hg(a);
        gg(a);
        delete fg[a]
    }
    ;
    eg.prototype.clear = function() {
        fg = {}
    }
    ;
    function jg(a, b, c) {
        if (b) {
            a = u(a) ? Oc(a) : a;
            c = Df(c);
            var d = fb(c.attrs);
            d.tabindex = 0;
            var e = fb(c.params);
            e.flashvars = Ad(c.args);
            if (Lb) {
                d.classid = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
                e.movie = b;
                b = document.createElement("object");
                for (var f in d)
                    b.setAttribute(f, d[f]);
                for (f in e)
                    d = document.createElement("param"),
                    d.setAttribute("name", f),
                    d.setAttribute("value", e[f]),
                    b.appendChild(d)
            } else {
                d.type = "application/x-shockwave-flash";
                d.src = b;
                b = document.createElement("embed");
                b.setAttribute("name", d.id);
                for (f in d)
                    b.setAttribute(f, d[f]);
                for (f in e)
                    b.setAttribute(f, e[f])
            }
            e = document.createElement("div");
            e.appendChild(b);
            a.innerHTML = e.innerHTML
        }
    }
    function kg(a, b, c) {
        if (a && a.attrs && a.attrs.id) {
            a = Df(a);
            var d = !!b
              , e = Nc(a.attrs.id)
              , f = e ? e.parentNode : null;
            if (e && f) {
                if (window != window.top) {
                    var h = null;
                    if (document.referrer) {
                        var k = document.referrer.substring(0, 128);
                        Gd(k) || (h = k)
                    } else
                        h = "unknown";
                    h && (d = !0,
                    a.args.framer = h)
                }
                h = af.getInstance();
                if (bf(h, a.minVersion)) {
                    var k = lg(a, h)
                      , l = "";
                    -1 < navigator.userAgent.indexOf("Sony/COM2") || (l = e.getAttribute("src") || e.movie);
                    (l != k || d) && jg(f, k, a);
                    df(h) && mg()
                } else
                    ng(f, a, h);
                c && c()
            } else
                G(function() {
                    kg(a, b, c)
                }, 50)
        }
    }
    function ng(a, b, c) {
        0 == c.e && b.fallback ? b.fallback() : 0 == c.e && b.fallbackMessage ? b.fallbackMessage() : a.innerHTML = '<div id="flash-upgrade">' + Kb() + "</div>"
    }
    function lg(a, b) {
        return cf(b) && a.url || (-1 < navigator.userAgent.indexOf("Sony/COM2") && !bf(b, 9, 1, 58) ? !1 : !0) && a.urlV9As2 || a.url
    }
    function mg() {
        var a = Nc("flash10-promo-div"), b;
        eg.getInstance();
        b = ig("f" + (Math.floor(107 / 31) + 1));
        b = !!(((null != b && /^[A-Fa-f0-9]+$/.test(b) ? parseInt(b, 16) : null) || 0) & 16384);
        a && !b && sf(a)
    }
    ;function og(a) {
        if (window.spf) {
            var b = a.match(pg);
            spf.style.load(a, b ? b[1] : "", void 0)
        } else
            qg(a)
    }
    function qg(a) {
        var b = rg(a)
          , c = document.getElementById(b)
          , d = c && C(c, "loaded");
        d || c && !d || (c = sg(a, b, function() {
            C(c, "loaded") || (xb(c, "loaded", "true"),
            I(b),
            G(oa(Wb, b), 0))
        }))
    }
    function sg(a, b, c) {
        var d = document.createElement("link");
        d.id = b;
        d.rel = "stylesheet";
        d.onload = function() {
            c && setTimeout(c, 0)
        }
        ;
        wb(d, a);
        (document.getElementsByTagName("head")[0] || document.body).appendChild(d);
        return d
    }
    function rg(a) {
        var b = document.createElement("a");
        wb(b, a);
        a = b.href.replace(/^[a-zA-Z]+:\/\//, "//");
        return "css-" + za(a)
    }
    var pg = /cssbin\/(?:debug-)?([a-zA-Z0-9_-]+?)(?:-2x|-web|-rtl|-vfl|.css)/;
    var tg;
    var ug = oc
      , ug = ug.toLowerCase();
    if (-1 != ug.indexOf("android")) {
        var vg = ug.match(/android\D*(\d\.\d)[^\;|\)]*[\;\)]/);
        if (vg)
            tg = Number(vg[1]);
        else {
            var wg = {
                cupcake: 1.5,
                donut: 1.6,
                eclair: 2,
                froyo: 2.2,
                gingerbread: 2.3,
                honeycomb: 3,
                "ice cream sandwich": 4,
                jellybean: 4.1
            }
              , xg = ug.match("(" + ab(wg).join("|") + ")");
            tg = xg ? wg[xg[0]] : 0
        }
    } else
        tg = void 0;
    function yg() {
        if (2.2 == tg)
            return !0;
        var a;
        a = r("yt.player.utils.videoElement_");
        a || (a = document.createElement("video"),
        q("yt.player.utils.videoElement_", a, void 0));
        try {
            return !(!a || !a.canPlayType || !a.canPlayType('video/mp4; codecs="avc1.42001E, mp4a.40.2"') && !a.canPlayType('video/webm; codecs="vp8.0, vorbis"'))
        } catch (b) {
            return !1
        }
    }
    ;function zg(a, b) {
        var c;
        a instanceof zg ? (this.Ia = p(b) ? b : a.Ia,
        Ag(this, a.Aa),
        this.Oa = a.Oa,
        Bg(this, a.ra),
        Cg(this, a.La),
        this.ea = a.ea,
        Dg(this, a.e.clone()),
        this.Ha = a.Ha) : a && (c = sd(String(a))) ? (this.Ia = !!b,
        Ag(this, c[1] || "", !0),
        this.Oa = Eg(c[2] || ""),
        Bg(this, c[3] || "", !0),
        Cg(this, c[4]),
        this.ea = Eg(c[5] || "", !0),
        Dg(this, c[6] || "", !0),
        this.Ha = Eg(c[7] || "")) : (this.Ia = !!b,
        this.e = new Fg(null,0,this.Ia))
    }
    g = zg.prototype;
    g.Aa = "";
    g.Oa = "";
    g.ra = "";
    g.La = null;
    g.ea = "";
    g.Ha = "";
    g.Ia = !1;
    g.toString = function() {
        var a = []
          , b = this.Aa;
        b && a.push(Gg(b, Hg, !0), ":");
        if (b = this.ra) {
            a.push("//");
            var c = this.Oa;
            c && a.push(Gg(c, Hg, !0), "@");
            a.push(encodeURIComponent(String(b)).replace(/%25([0-9a-fA-F]{2})/g, "%$1"));
            b = this.La;
            null != b && a.push(":", String(b))
        }
        if (b = this.ea)
            this.ra && "/" != b.charAt(0) && a.push("/"),
            a.push(Gg(b, "/" == b.charAt(0) ? Ig : Jg, !0));
        (b = this.e.toString()) && a.push("?", b);
        (b = this.Ha) && a.push("#", Gg(b, Kg));
        return a.join("")
    }
    ;
    g.resolve = function(a) {
        var b = this.clone()
          , c = !!a.Aa;
        c ? Ag(b, a.Aa) : c = !!a.Oa;
        c ? b.Oa = a.Oa : c = !!a.ra;
        c ? Bg(b, a.ra) : c = null != a.La;
        var d = a.ea;
        if (c)
            Cg(b, a.La);
        else if (c = !!a.ea) {
            if ("/" != d.charAt(0))
                if (this.ra && !this.ea)
                    d = "/" + d;
                else {
                    var e = b.ea.lastIndexOf("/");
                    -1 != e && (d = b.ea.substr(0, e + 1) + d)
                }
            e = d;
            if (".." == e || "." == e)
                d = "";
            else if (-1 != e.indexOf("./") || -1 != e.indexOf("/.")) {
                for (var d = 0 == e.lastIndexOf("/", 0), e = e.split("/"), f = [], h = 0; h < e.length; ) {
                    var k = e[h++];
                    "." == k ? d && h == e.length && f.push("") : ".." == k ? ((1 < f.length || 1 == f.length && "" != f[0]) && f.pop(),
                    d && h == e.length && f.push("")) : (f.push(k),
                    d = !0)
                }
                d = f.join("/")
            } else
                d = e
        }
        c ? b.ea = d : c = "" !== a.e.toString();
        c ? Dg(b, Eg(a.e.toString())) : c = !!a.Ha;
        c && (b.Ha = a.Ha);
        return b
    }
    ;
    g.clone = function() {
        return new zg(this)
    }
    ;
    function Ag(a, b, c) {
        a.Aa = c ? Eg(b, !0) : b;
        a.Aa && (a.Aa = a.Aa.replace(/:$/, ""))
    }
    function Bg(a, b, c) {
        a.ra = c ? Eg(b, !0) : b
    }
    function Cg(a, b) {
        if (b) {
            b = Number(b);
            if (isNaN(b) || 0 > b)
                throw Error("Bad port number " + b);
            a.La = b
        } else
            a.La = null
    }
    function Dg(a, b, c) {
        b instanceof Fg ? (a.e = b,
        Lg(a.e, a.Ia)) : (c || (b = Gg(b, Mg)),
        a.e = new Fg(b,0,a.Ia))
    }
    function M(a, b, c) {
        a = a.e;
        Ng(a);
        a.e = null;
        b = Og(a, b);
        Pg(a, b) && (a.U -= a.J.get(b).length);
        Qd(a.J, b, [c]);
        a.U++
    }
    function Qg(a, b, c) {
        da(c) || (c = [String(c)]);
        Rg(a.e, b, c)
    }
    function Sg(a) {
        M(a, "zx", Math.floor(2147483648 * Math.random()).toString(36) + Math.abs(Math.floor(2147483648 * Math.random()) ^ w()).toString(36));
        return a
    }
    function Tg(a) {
        return a instanceof zg ? a.clone() : new zg(a,void 0)
    }
    function Ug(a, b, c, d) {
        var e = new zg(null,void 0);
        a && Ag(e, a);
        b && Bg(e, b);
        c && Cg(e, c);
        d && (e.ea = d);
        return e
    }
    function Eg(a, b) {
        return a ? b ? decodeURI(a) : decodeURIComponent(a) : ""
    }
    function Gg(a, b, c) {
        return u(a) ? (a = encodeURI(a).replace(b, Vg),
        c && (a = a.replace(/%25([0-9a-fA-F]{2})/g, "%$1")),
        a) : null
    }
    function Vg(a) {
        a = a.charCodeAt(0);
        return "%" + (a >> 4 & 15).toString(16) + (a & 15).toString(16)
    }
    var Hg = /[#\/\?@]/g
      , Jg = /[\#\?:]/g
      , Ig = /[\#\?]/g
      , Mg = /[\#\?@]/g
      , Kg = /#/g;
    function Fg(a, b, c) {
        this.e = a || null;
        this.f = !!c
    }
    function Ng(a) {
        a.J || (a.J = new Pd,
        a.U = 0,
        a.e && vd(a.e, function(b, c) {
            a.add(ta(b), c)
        }))
    }
    g = Fg.prototype;
    g.J = null;
    g.U = null;
    g.W = function() {
        Ng(this);
        return this.U
    }
    ;
    g.add = function(a, b) {
        Ng(this);
        this.e = null;
        a = Og(this, a);
        var c = this.J.get(a);
        c || Qd(this.J, a, c = []);
        c.push(b);
        this.U++;
        return this
    }
    ;
    g.remove = function(a) {
        Ng(this);
        a = Og(this, a);
        return Sd(this.J.f, a) ? (this.e = null,
        this.U -= this.J.get(a).length,
        this.J.remove(a)) : !1
    }
    ;
    g.clear = function() {
        this.J = this.e = null;
        this.U = 0
    }
    ;
    g.isEmpty = function() {
        Ng(this);
        return 0 == this.U
    }
    ;
    function Pg(a, b) {
        Ng(a);
        b = Og(a, b);
        return Sd(a.J.f, b)
    }
    g.qb = function(a) {
        var b = this.Y();
        return Ha(b, a)
    }
    ;
    g.sa = function() {
        Ng(this);
        for (var a = this.J.Y(), b = this.J.sa(), c = [], d = 0; d < b.length; d++)
            for (var e = a[d], f = 0; f < e.length; f++)
                c.push(b[d]);
        return c
    }
    ;
    g.Y = function(a) {
        Ng(this);
        var b = [];
        if (u(a))
            Pg(this, a) && (b = Na(b, this.J.get(Og(this, a))));
        else {
            a = this.J.Y();
            for (var c = 0; c < a.length; c++)
                b = Na(b, a[c])
        }
        return b
    }
    ;
    g.get = function(a, b) {
        var c = a ? this.Y(a) : [];
        return 0 < c.length ? String(c[0]) : b
    }
    ;
    function Rg(a, b, c) {
        a.remove(b);
        0 < c.length && (a.e = null,
        Qd(a.J, Og(a, b), Oa(c)),
        a.U += c.length)
    }
    g.toString = function() {
        if (this.e)
            return this.e;
        if (!this.J)
            return "";
        for (var a = [], b = this.J.sa(), c = 0; c < b.length; c++)
            for (var d = b[c], e = encodeURIComponent(String(d)), d = this.Y(d), f = 0; f < d.length; f++) {
                var h = e;
                "" !== d[f] && (h += "=" + encodeURIComponent(String(d[f])));
                a.push(h)
            }
        return this.e = a.join("&")
    }
    ;
    g.clone = function() {
        var a = new Fg;
        a.e = this.e;
        this.J && (a.J = this.J.clone(),
        a.U = this.U);
        return a
    }
    ;
    function Og(a, b) {
        var c = String(b);
        a.f && (c = c.toLowerCase());
        return c
    }
    function Lg(a, b) {
        b && !a.f && (Ng(a),
        a.e = null,
        a.J.forEach(function(a, b) {
            var e = b.toLowerCase();
            b != e && (this.remove(b),
            Rg(this, e, a))
        }, a));
        a.f = b
    }
    ;var Wg = "corp.google.com googleplex.com youtube.com youtube-nocookie.com youtubeeducation.com borg.google.com prod.google.com sandbox.google.com books.googleusercontent.com docs.google.com drive.google.com mail.google.com photos.google.com plus.google.com play.google.com googlevideo.com talkgadget.google.com survey.g.doubleclick.net youtube.googleapis.com vevo.com".split(" ")
      , Xg = "";
    function Yg(a) {
        return a && a == Xg ? !0 : (new RegExp("^(https?:)?//([a-z0-9-]{1,63}\\.)*(" + Wg.join("|").replace(/\./g, ".") + ")(:[0-9]+)?([/?#]|$)","i")).test(a) ? (Xg = a,
        !0) : !1
    }
    ;var Zg = {}
      , $g = 0
      , ah = r("yt.net.ping.workerUrl_") || null;
    q("yt.net.ping.workerUrl_", ah, void 0);
    function bh(a) {
        var b = new Image
          , c = "" + $g++;
        Zg[c] = b;
        b.onload = b.onerror = function() {
            delete Zg[c]
        }
        ;
        b.src = a;
        b = eval("null")
    }
    ;function ch(a) {
        var b = void 0;
        void 0 === b && (b = NaN);
        var c = r("yt.scheduler.instance.addJob");
        c ? (isNaN(b) && (b = 0),
        c(a, 0, b)) : isNaN(b) ? a() : G(a, b || 0)
    }
    ;function N(a, b) {
        this.version = a;
        this.args = b
    }
    function dh(a) {
        if (!a.Ba) {
            var b = {};
            a.call(b);
            a.Ba = b.version
        }
        return a.Ba
    }
    function eh(a, b) {
        function c() {
            a.apply(this, b.args)
        }
        if (!b.args || !b.version)
            throw Error("yt.pubsub2.Data.deserialize(): serializedData is incomplete.");
        var d;
        try {
            d = dh(a)
        } catch (e) {}
        if (!d || b.version != d)
            throw Error("yt.pubsub2.Data.deserialize(): serializedData version is incompatible.");
        c.prototype = a.prototype;
        try {
            return new c
        } catch (f) {
            throw f.message = "yt.pubsub2.Data.deserialize(): " + f.message,
            f;
        }
    }
    function O(a, b) {
        this.f = a;
        this.e = b
    }
    O.prototype.toString = function() {
        return this.f
    }
    ;
    var fh = r("yt.pubsub2.instance_") || new E;
    E.prototype.subscribe = E.prototype.subscribe;
    E.prototype.unsubscribeByKey = E.prototype.ka;
    E.prototype.publish = E.prototype.A;
    E.prototype.clear = E.prototype.clear;
    q("yt.pubsub2.instance_", fh, void 0);
    var gh = r("yt.pubsub2.subscribedKeys_") || {};
    q("yt.pubsub2.subscribedKeys_", gh, void 0);
    var hh = r("yt.pubsub2.topicToKeys_") || {};
    q("yt.pubsub2.topicToKeys_", hh, void 0);
    var ih = r("yt.pubsub2.isAsync_") || {};
    q("yt.pubsub2.isAsync_", ih, void 0);
    q("yt.pubsub2.skipSubKey_", null, void 0);
    function P(a, b) {
        var c = jh();
        c && c.publish.call(c, a.toString(), a, b)
    }
    function kh(a, b, c) {
        var d = jh();
        if (!d)
            return 0;
        var e = d.subscribe(a.toString(), function(d, h) {
            if (!window.yt.pubsub2.skipSubKey_ || window.yt.pubsub2.skipSubKey_ != e) {
                var k = function() {
                    if (gh[e])
                        try {
                            if (h && a instanceof O && a != d)
                                try {
                                    h = eh(a.e, h)
                                } catch (k) {
                                    throw k.message = "yt.pubsub2 cross-binary conversion error for " + a.toString() + ": " + k.message,
                                    k;
                                }
                            b.call(c || window, h)
                        } catch (n) {
                            Jb(n)
                        }
                };
                ih[a.toString()] ? r("yt.scheduler.instance") ? ch(k) : G(k, 0) : k()
            }
        });
        gh[e] = !0;
        hh[a.toString()] || (hh[a.toString()] = []);
        hh[a.toString()].push(e);
        return e
    }
    function lh(a) {
        var b = jh();
        b && (fa(a) && (a = [a]),
        A(a, function(a) {
            b.unsubscribeByKey(a);
            delete gh[a]
        }))
    }
    function jh() {
        return r("yt.pubsub2.instance_")
    }
    ;function mh(a) {
        N.call(this, 1, arguments)
    }
    y(mh, N);
    var nh = new O("timing-sent",mh);
    var Q = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance || {}
      , oh = v(Q.clearResourceTimings || Q.webkitClearResourceTimings || Q.mozClearResourceTimings || Q.msClearResourceTimings || Q.oClearResourceTimings || t, Q)
      , ph = Q.mark ? function(a) {
        Q.mark(a)
    }
    : t;
    function qh(a) {
        rh().tick[a] = w();
        ph(a);
        F("CSI_LOG_ON_TICK") && sh()
    }
    function th() {
        uh();
        oh();
        q("yt.timing.pingSent_", !1, void 0)
    }
    function vh() {
        var a = rh().tick;
        if (a.aft)
            return a.aft;
        for (var b = F("TIMING_AFT_KEYS", ["ol"]), c = b.length, d = 0; d < c; d++) {
            var e = a[b[d]];
            if (e)
                return e
        }
        return NaN
    }
    function wh(a) {
        return Math.round(Q.timing.navigationStart + a)
    }
    function xh(a) {
        var b = window.location.protocol
          , c = Q.getEntriesByType("resource")
          , d = c.filter(function(a) {
            return 0 == a.name.indexOf(b + "//fonts.googleapis.com/css?family=")
        })[0]
          , c = c.filter(function(a) {
            return 0 == a.name.indexOf(b + "//fonts.gstatic.com/s/")
        }).reduce(function(a, b) {
            return b.duration > a.duration ? b : a
        }, {
            duration: 0
        });
        d && 0 < d.startTime && 0 < d.responseEnd && (a.wfcs = wh(d.startTime),
        a.wfce = wh(d.responseEnd));
        c && 0 < c.startTime && 0 < c.responseEnd && (a.wffs = wh(c.startTime),
        a.wffe = wh(c.responseEnd))
    }
    function sh() {
        var a = F("TIMING_ACTION")
          , b = rh().tick;
        if (a && b._start && vh()) {
            var a = !0
              , c = F("TIMING_WAIT", []);
            if (c.length)
                for (var d = 0, e = c.length; d < e; ++d)
                    if (!(c[d]in b)) {
                        a = !1;
                        break
                    }
            if (a)
                if (c = rh().tick,
                b = rh().span,
                d = rh().info,
                a = r("yt.timing.reportbuilder_")) {
                    if (a = a(c, b, d, void 0))
                        yh(a),
                        th()
                } else {
                    a = {
                        v: 2,
                        s: F("CSI_SERVICE_NAME", "youtube"),
                        action: F("TIMING_ACTION")
                    };
                    Q.now && Q.timing && (e = Q.timing.navigationStart + Q.now(),
                    e = Math.round(w() - e),
                    d.yt_hrd = e);
                    var e = F("TIMING_INFO") || {}, f;
                    for (f in e)
                        d[f] = e[f];
                    f = d.srt;
                    delete d.srt;
                    var h;
                    f || 0 === f || (h = Q.timing || {},
                    f = Math.max(0, h.responseStart - h.navigationStart),
                    isNaN(f) && d.pt && (f = d.pt));
                    if (f || 0 === f)
                        d.srt = f;
                    d.h5jse && (e = window.location.protocol + r("ytplayer.config.assets.js"),
                    (e = Q.getEntriesByName ? Q.getEntriesByName(e)[0] : null) ? d.h5jse = Math.round(d.h5jse - e.responseEnd) : delete d.h5jse);
                    c.aft = vh();
                    e = c._start;
                    if ("cold" == d.yt_lt) {
                        h || (h = Q.timing || {});
                        var k;
                        t: if (k = h,
                        k.msFirstPaint)
                            k = Math.max(0, k.msFirstPaint);
                        else {
                            var l = window.chrome;
                            if (l && (l = l.loadTimes,
                            ga(l))) {
                                var l = l()
                                  , n = 1E3 * Math.min(l.requestTime || Infinity, l.startLoadTime || Infinity)
                                  , n = Infinity === n ? 0 : k.navigationStart - n;
                                k = Math.max(0, Math.round(1E3 * l.firstPaintTime + n) || 0);
                                break t
                            }
                            k = 0
                        }
                        0 < k && k > e && (c.fpt = k);
                        k = rh().span;
                        l = h.redirectEnd - h.redirectStart;
                        0 < l && (k.rtime_ = l);
                        l = h.domainLookupEnd - h.domainLookupStart;
                        0 < l && (k.dns_ = l);
                        l = h.connectEnd - h.connectStart;
                        0 < l && (k.tcp_ = l);
                        l = h.connectEnd - h.secureConnectionStart;
                        h.secureConnectionStart >= h.navigationStart && 0 < l && (k.stcp_ = l);
                        l = h.responseStart - h.requestStart;
                        0 < l && (k.req_ = l);
                        l = h.responseEnd - h.responseStart;
                        0 < l && (k.rcv_ = l);
                        F("EXP_WEBFONT_ENABLED") && Q.getEntriesByType && xh(c)
                    }
                    d.p = F("CLIENT_PROTOCOL") || "unknown";
                    d.t = F("CLIENT_TRANSPORT") || "unknown";
                    for (var x in d)
                        "_" != x.charAt(0) && (a[x] = d[x]);
                    F("CSI_MORE") && (c.ps = w());
                    x = {};
                    h = [];
                    for (var Z in c)
                        "_" != Z.charAt(0) && (k = Math.max(Math.round(c[Z] - e), 0),
                        x[Z] = k,
                        h.push(Z + "." + k));
                    a.rt = h.join(",");
                    Z = {};
                    h = [];
                    for (var la in b)
                        "_" != la.charAt(0) && (Z[la] = b[la],
                        h.push(la + "." + b[la]));
                    a.it = h.join(",");
                    (la = r("ytdebug.logTiming")) && la(a, x, Z);
                    th();
                    F("EXP_DEFER_CSI_PING") ? (zh(),
                    q("yt.timing.deferredPingArgs_", a, void 0),
                    la = G(zh, 0),
                    q("yt.timing.deferredPingTimer_", la, void 0)) : yh(a);
                    P(nh, new mh(x.aft + (f || 0)))
                }
        }
    }
    function yh(a) {
        F("EXP_DEFER_CSI_PING") && (H(r("yt.timing.deferredPingTimer_")),
        q("yt.timing.deferredPingArgs_", null, void 0));
        var b = "https:" == window.location.protocol ? "https://gg.google.com/csi" : "http://csi.gstatic.com/csi", c = "", d;
        for (d in a)
            c += "&" + d + "=" + a[d];
        (a = b + "?" + c.substring(1)) && bh(a);
        q("yt.timing.pingSent_", !0, void 0)
    }
    function zh(a) {
        if (F("EXP_DEFER_CSI_PING")) {
            var b = r("yt.timing.deferredPingArgs_");
            b && (a && (b.yt_fss = a),
            yh(b))
        }
    }
    function rh() {
        return r("ytcsi.data_") || uh()
    }
    function uh() {
        var a = {
            tick: {},
            span: {},
            info: {}
        };
        q("ytcsi.data_", a, void 0);
        return a
    }
    ;function Ah() {}
    ;function Bh() {}
    y(Bh, Ah);
    Bh.prototype.W = function() {
        var a = 0;
        Nd(this.pa(!0), function() {
            a++
        });
        return a
    }
    ;
    Bh.prototype.clear = function() {
        var a = Od(this.pa(!0))
          , b = this;
        A(a, function(a) {
            b.remove(a)
        })
    }
    ;
    function Ch(a) {
        this.e = a
    }
    y(Ch, Bh);
    g = Ch.prototype;
    g.isAvailable = function() {
        if (!this.e)
            return !1;
        try {
            return this.e.setItem("__sak", "1"),
            this.e.removeItem("__sak"),
            !0
        } catch (a) {
            return !1
        }
    }
    ;
    g.Gd = function(a, b) {
        try {
            this.e.setItem(a, b)
        } catch (c) {
            if (0 == this.e.length)
                throw "Storage mechanism: Storage disabled";
            throw "Storage mechanism: Quota exceeded";
        }
    }
    ;
    g.get = function(a) {
        a = this.e.getItem(a);
        if (!u(a) && null !== a)
            throw "Storage mechanism: Invalid value was encountered";
        return a
    }
    ;
    g.remove = function(a) {
        this.e.removeItem(a)
    }
    ;
    g.W = function() {
        return this.e.length
    }
    ;
    g.pa = function(a) {
        var b = 0
          , c = this.e
          , d = new Ld;
        d.next = function() {
            if (b >= c.length)
                throw Kd;
            var d;
            d = c.key(b++);
            if (a)
                return d;
            d = c.getItem(d);
            if (!u(d))
                throw "Storage mechanism: Invalid value was encountered";
            return d
        }
        ;
        return d
    }
    ;
    g.clear = function() {
        this.e.clear()
    }
    ;
    g.key = function(a) {
        return this.e.key(a)
    }
    ;
    function Dh() {
        var a = null;
        try {
            a = window.localStorage || null
        } catch (b) {}
        this.e = a
    }
    y(Dh, Ch);
    function Eh() {
        var a = null;
        try {
            a = window.sessionStorage || null
        } catch (b) {}
        this.e = a
    }
    y(Eh, Ch);
    function Fh(a) {
        a = String(a);
        if (/^\s*$/.test(a) ? 0 : /^[\],:{}\s\u2028\u2029]*$/.test(a.replace(/\\["\\\/bfnrtu]/g, "@").replace(/"[^"\\\n\r\u2028\u2029\x00-\x08\x0a-\x1f]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:[\s\u2028\u2029]*\[)+/g, "")))
            try {
                return eval("(" + a + ")")
            } catch (b) {}
        throw Error("Invalid JSON string: " + a);
    }
    function Gh(a) {
        return eval("(" + a + ")")
    }
    function R(a) {
        return Hh(new Ih(void 0), a)
    }
    function Ih(a) {
        this.e = a
    }
    function Hh(a, b) {
        var c = [];
        Jh(a, b, c);
        return c.join("")
    }
    function Jh(a, b, c) {
        switch (typeof b) {
        case "string":
            Kh(b, c);
            break;
        case "number":
            c.push(isFinite(b) && !isNaN(b) ? b : "null");
            break;
        case "boolean":
            c.push(b);
            break;
        case "undefined":
            c.push("null");
            break;
        case "object":
            if (null == b) {
                c.push("null");
                break
            }
            if (da(b)) {
                var d = b.length;
                c.push("[");
                for (var e = "", f = 0; f < d; f++)
                    c.push(e),
                    e = b[f],
                    Jh(a, a.e ? a.e.call(b, String(f), e) : e, c),
                    e = ",";
                c.push("]");
                break
            }
            c.push("{");
            d = "";
            for (f in b)
                Object.prototype.hasOwnProperty.call(b, f) && (e = b[f],
                "function" != typeof e && (c.push(d),
                Kh(f, c),
                c.push(":"),
                Jh(a, a.e ? a.e.call(b, f, e) : e, c),
                d = ","));
            c.push("}");
            break;
        case "function":
            break;
        default:
            throw Error("Unknown type: " + typeof b);
        }
    }
    var Lh = {
        '"': '\\"',
        "\\": "\\\\",
        "/": "\\/",
        "\b": "\\b",
        "\f": "\\f",
        "\n": "\\n",
        "\r": "\\r",
        "\t": "\\t",
        "\x0B": "\\u000b"
    }
      , Mh = /\uffff/.test("\uffff") ? /[\\\"\x00-\x1f\x7f-\uffff]/g : /[\\\"\x00-\x1f\x7f-\xff]/g;
    function Kh(a, b) {
        b.push('"', a.replace(Mh, function(a) {
            if (a in Lh)
                return Lh[a];
            var b = a.charCodeAt(0)
              , e = "\\u";
            16 > b ? e += "000" : 256 > b ? e += "00" : 4096 > b && (e += "0");
            return Lh[a] = e + b.toString(16)
        }), '"')
    }
    ;function Nh(a) {
        this.e = a
    }
    Nh.prototype.f = function(a, b) {
        p(b) ? this.e.Gd(a, R(b)) : this.e.remove(a)
    }
    ;
    Nh.prototype.get = function(a) {
        var b;
        try {
            b = this.e.get(a)
        } catch (c) {
            return
        }
        if (null !== b)
            try {
                return Fh(b)
            } catch (d) {
                throw "Storage: Invalid value was encountered";
            }
    }
    ;
    Nh.prototype.remove = function(a) {
        this.e.remove(a)
    }
    ;
    function Oh(a) {
        this.e = a
    }
    y(Oh, Nh);
    function Ph(a) {
        this.data = a
    }
    function Qh(a) {
        return !p(a) || a instanceof Ph ? a : new Ph(a)
    }
    Oh.prototype.f = function(a, b) {
        Oh.G.f.call(this, a, Qh(b))
    }
    ;
    Oh.prototype.h = function(a) {
        a = Oh.G.get.call(this, a);
        if (!p(a) || a instanceof Object)
            return a;
        throw "Storage: Invalid value was encountered";
    }
    ;
    Oh.prototype.get = function(a) {
        if (a = this.h(a)) {
            if (a = a.data,
            !p(a))
                throw "Storage: Invalid value was encountered";
        } else
            a = void 0;
        return a
    }
    ;
    function Rh(a) {
        this.e = a
    }
    y(Rh, Oh);
    function Sh(a) {
        var b = a.creation;
        a = a.expiration;
        return !!a && a < w() || !!b && b > w()
    }
    Rh.prototype.f = function(a, b, c) {
        if (b = Qh(b)) {
            if (c) {
                if (c < w()) {
                    Rh.prototype.remove.call(this, a);
                    return
                }
                b.expiration = c
            }
            b.creation = w()
        }
        Rh.G.f.call(this, a, b)
    }
    ;
    Rh.prototype.h = function(a, b) {
        var c = Rh.G.h.call(this, a);
        if (c)
            if (!b && Sh(c))
                Rh.prototype.remove.call(this, a);
            else
                return c
    }
    ;
    function Th(a) {
        this.e = a
    }
    y(Th, Rh);
    function Uh(a, b) {
        var c = [];
        Nd(b, function(a) {
            var b;
            try {
                b = Th.prototype.h.call(this, a, !0)
            } catch (f) {
                if ("Storage: Invalid value was encountered" == f)
                    return;
                throw f;
            }
            p(b) ? Sh(b) && c.push(a) : c.push(a)
        }, a);
        return c
    }
    function Vh(a, b) {
        var c = Uh(a, b);
        A(c, function(a) {
            Th.prototype.remove.call(this, a)
        }, a)
    }
    function Wh() {
        var a = Xh;
        Vh(a, a.e.pa(!0))
    }
    ;function S(a, b, c) {
        var d = c && 0 < c ? c : 0;
        c = d ? w() + 1E3 * d : 0;
        if ((d = d ? Xh : Yh) && window.JSON) {
            u(b) || (b = JSON.stringify(b, void 0));
            try {
                d.f(a, b, c)
            } catch (e) {
                d.remove(a)
            }
        }
    }
    function T(a) {
        if (!Yh && !Xh || !window.JSON)
            return null;
        var b;
        try {
            b = Yh.get(a)
        } catch (c) {}
        if (!u(b))
            try {
                b = Xh.get(a)
            } catch (d) {}
        if (!u(b))
            return null;
        try {
            b = JSON.parse(b, void 0)
        } catch (e) {}
        return b
    }
    function Zh(a) {
        Yh && Yh.remove(a);
        Xh && Xh.remove(a)
    }
    var Xh, $h = new Dh;
    Xh = $h.isAvailable() ? new Th($h) : null;
    var Yh, ai = new Eh;
    Yh = ai.isAvailable() ? new Th(ai) : null;
    function bi() {
        var a = {
            volume: 100,
            muted: !1
        }
          , b = T("yt-player-volume") || {};
        a.volume = isNaN(b.volume) ? 100 : Math.min(Math.max(b.volume, 0), 100);
        a.muted = void 0 == b.muted ? !1 : b.muted;
        return a
    }
    ;function ci(a, b) {
        D.call(this);
        this.k = this.l = a;
        this.S = b;
        this.I = !1;
        this.f = {};
        this.Da = this.Q = null;
        this.da = new E;
        Ab(this, oa(Bb, this.da));
        this.j = {};
        this.C = this.Qa = this.h = this.Nb = this.e = null;
        this.ma = !1;
        this.N = this.H = this.Lb = this.o = null;
        this.mb = {};
        this.qd = ["onReady"];
        this.Ca = [];
        this.Ob = null;
        this.pc = 0;
        this.oa = {};
        di(this);
        this.na("onVolumeChange", v(this.ce, this));
        this.na("onError", v(this.be, this));
        this.na("onTabOrderChange", v(this.vd, this));
        this.na("onTabAnnounce", v(this.Ie, this));
        this.na("WATCH_LATER_VIDEO_ADDED", v(this.de, this));
        this.na("WATCH_LATER_VIDEO_REMOVED", v(this.ee, this));
        this.na("onMouseWheelCapture", v(this.Zd, this));
        this.na("onMouseWheelRelease", v(this.$d, this));
        this.na("onMouseMoveCapture", v(this.Xd, this));
        this.Mb = !1;
        Uf(this.l, "mousewheel", this.Hc, !1, this);
        Uf(this.l, "wheel", this.Hc, !1, this)
    }
    y(ci, D);
    g = ci.prototype;
    g.lc = function(a, b) {
        this.F() || (ei(this, a),
        fi(this, b),
        this.I && gi(this))
    }
    ;
    function ei(a, b) {
        a.Nb = b;
        a.e = b.clone();
        a.h = a.e.attrs.id || a.h;
        "video-player" == a.h && (a.h = a.S,
        a.e.attrs.id = a.S);
        a.k.id == a.h && (a.h = a.h + "-player",
        a.e.attrs.id = a.h);
        a.e.args.enablejsapi = "1";
        a.e.args.playerapiid = a.S;
        a.Qa || (a.Qa = hi(a, a.e.args.jsapicallback || "onYouTubePlayerReady"));
        a.e.args.jsapicallback = null;
        var c = a.e.attrs.width;
        c && (a.k.style.width = kf(Number(c) || c, !0));
        if (c = a.e.attrs.height)
            a.k.style.height = kf(Number(c) || c, !0);
        a.k.style.overflow = "hidden"
    }
    g.Dd = function() {
        return this.Nb
    }
    ;
    function gi(a) {
        a.e.loaded || (a.e.loaded = !0,
        "0" != a.e.args.autoplay ? a.f.loadVideoByPlayerVars(a.e.args) : a.f.cueVideoByPlayerVars(a.e.args))
    }
    function ii(a) {
        if (!p(a.e.disable.flash)) {
            var b = a.e.disable, c;
            c = bf(af.getInstance(), a.e.minVersion);
            b.flash = !c
        }
        return !a.e.disable.flash
    }
    function ji(a) {
        var b = ki(a);
        b && b.stopVideo && b.stopVideo();
        if (ii(a)) {
            var c = a.e;
            b && b.getUpdatedConfigurationData && (c = Df(b.getUpdatedConfigurationData()));
            c.args.autoplay = 1;
            c.args.html5_unavailable = "1";
            ei(a, c);
            fi(a, "flash")
        }
    }
    function fi(a, b) {
        if (!a.F()) {
            if (!b) {
                var c;
                if (!(c = !a.e.html5 && ii(a))) {
                    if (!p(a.e.disable.html5)) {
                        if (c = yg())
                            c = li(a) || a.e.assets.js;
                        a.e.disable.html5 = !c;
                        c || (a.e.args.html5_unavailable = "1")
                    }
                    c = !!a.e.disable.html5
                }
                b = c ? ii(a) ? "flash" : "unsupported" : "html5"
            }
            ("flash" == b ? a.Ne : "html5" == b ? a.Oe : a.Pe).call(a)
        }
    }
    function li(a) {
        var b = !0
          , c = ki(a);
        c && a.e && (a = a.e,
        b = C(c, "version") == a.assets.js);
        return b && !!r("yt.player.Application.create")
    }
    g.Oe = function() {
        if (!this.ma) {
            var a = li(this);
            if (a && "html5" == mi(this))
                this.C = "html5",
                this.I || this.Wa();
            else if (ni(this),
            this.C = "html5",
            a && this.Lb)
                this.l.appendChild(this.Lb),
                this.Wa();
            else {
                this.e.loaded = !0;
                var b = v(function() {
                    var a = this.l
                      , b = this.e.clone();
                    r("yt.player.Application.create")(a, b);
                    this.Wa()
                }, this);
                this.o = b;
                this.ma = !0;
                a ? this.o() : (this.e.assets.js2 ? (this.o = a = v(function() {
                    Xb(this.e.assets.js2, b);
                    this.o = b
                }, this),
                Xb(this.e.assets.js, a)) : Xb(this.e.assets.js, this.o),
                og(this.e.assets.css))
            }
        }
    }
    ;
    g.Ne = function() {
        var a = this.e.clone();
        if (!this.H) {
            var b = ki(this);
            b && (this.H = document.createElement("span"),
            this.H.tabIndex = 0,
            this.Ca.push(L(this.H, "focus", v(this.Ec, this))),
            this.N = document.createElement("span"),
            this.N.tabIndex = 0,
            this.Ca.push(L(this.N, "focus", v(this.Ec, this))),
            b.parentNode && b.parentNode.insertBefore(this.H, b),
            b.parentNode && b.parentNode.insertBefore(this.N, b.nextSibling))
        }
        a.attrs.width = a.attrs.width || "100%";
        a.attrs.height = a.attrs.height || "100%";
        if ("flash" == mi(this))
            this.C = "flash",
            this.I || kg(a, !1, v(this.Wa, this));
        else {
            ni(this);
            this.C = "flash";
            this.e.loaded = !0;
            b = this.l;
            b = u(b) ? Oc(b) : b;
            a = Df(a);
            if (window != window.top) {
                var c = null;
                document.referrer && (c = document.referrer.substring(0, 128));
                a.args.framer = c
            }
            c = af.getInstance();
            bf(c, a.minVersion) ? (c = lg(a, c),
            jg(b, c, a)) : ng(b, a, c);
            this.Wa()
        }
    }
    ;
    g.Ec = function() {
        ki(this).focus()
    }
    ;
    function ki(a) {
        var b = Nc(a.h);
        !b && a.k && a.k.querySelector && (b = a.k.querySelector("#" + a.h));
        return b
    }
    g.Wa = function() {
        var a = ki(this)
          , b = !1;
        try {
            a && a.getApiInterface && a.getApiInterface() && (b = !0)
        } catch (c) {}
        if (b)
            if (this.ma = !1,
            a.isNotServable && a.isNotServable(this.e.args.video_id))
                ji(this);
            else {
                di(this);
                this.I = !0;
                a = ki(this);
                a.addEventListener && (this.Q = oi(this, a, "addEventListener"));
                a.removeEventListener && (this.Da = oi(this, a, "removeEventListener"));
                for (var b = a.getApiInterface(), b = b.concat(a.getInternalApiInterface()), d = 0; d < b.length; d++) {
                    var e = b[d];
                    this.f[e] || (this.f[e] = oi(this, a, e))
                }
                for (var f in this.j)
                    this.Q(f, this.j[f]);
                gi(this);
                this.Qa && this.Qa(this.f);
                this.da.A("onReady", this.f);
                a.apiProxyReady && a.apiProxyReady()
            }
        else
            this.pc = G(v(this.Wa, this), 50)
    }
    ;
    function oi(a, b, c) {
        var d = b[c];
        return function() {
            try {
                return a.Ob = null,
                d.apply(b, arguments)
            } catch (e) {
                "Bad NPObject as private data!" != e.message && "sendAbandonmentPing" != c && (e.message += " (" + c + ")",
                a.Ob = e,
                Jb(e, "WARNING"))
            }
        }
    }
    function di(a) {
        a.I = !1;
        if (a.Da)
            for (var b in a.j)
                a.Da(b, a.j[b]);
        for (var c in a.oa)
            H(parseInt(c, 10));
        a.oa = {};
        a.Q = null;
        a.Da = null;
        for (var d in a.f)
            a.f[d] = null;
        a.f.addEventListener = v(a.na, a);
        a.f.removeEventListener = v(a.xe, a);
        a.f.destroy = v(a.dispose, a);
        a.f.getLastError = v(a.Ed, a);
        a.f.getPlayerType = v(a.Fd, a);
        a.f.getCurrentVideoConfig = v(a.Dd, a);
        a.f.loadNewVideoConfig = v(a.lc, a);
        a.f.isReady = v(a.Ze, a)
    }
    g.Ze = function() {
        return this.I
    }
    ;
    g.na = function(a, b) {
        if (!this.F()) {
            var c = hi(this, b);
            if (c) {
                if (!Ha(this.qd, a) && !this.j[a]) {
                    var d = pi(this, a);
                    this.Q && this.Q(a, d)
                }
                this.da.subscribe(a, c);
                "onReady" == a && this.I && G(oa(c, this.f), 0)
            }
        }
    }
    ;
    g.xe = function(a, b) {
        if (!this.F()) {
            var c = hi(this, b);
            c && this.da.Na(a, c)
        }
    }
    ;
    function hi(a, b) {
        var c = b;
        if ("string" == typeof b) {
            if (a.mb[b])
                return a.mb[b];
            c = function() {
                var a = r(b);
                a && a.apply(m, arguments)
            }
            ;
            a.mb[b] = c
        }
        return c ? c : null
    }
    function pi(a, b) {
        var c = "ytPlayer" + b + a.S;
        a.j[b] = c;
        m[c] = function(c) {
            var e = G(function() {
                if (!a.F()) {
                    a.da.A(b, c);
                    var f = a.oa
                      , h = e.toString();
                    h in f && delete f[h]
                }
            }, 0);
            eb(a.oa, e.toString())
        }
        ;
        return c
    }
    g.vd = function(a) {
        a = a ? Uc : Tc;
        for (var b = a(document.activeElement); b && (1 != b.nodeType || b == this.H || b == this.N || (b.focus(),
        b != document.activeElement)); )
            b = a(b)
    }
    ;
    g.Ie = function(a) {
        I("a11y-announce", a)
    }
    ;
    g.ce = function(a) {
        var b = {};
        b.volume = isNaN(a.volume) ? bi().volume : Math.min(Math.max(a.volume, 0), 100);
        b.muted = void 0 == a.muted ? bi().muted : a.muted;
        S("yt-player-volume", b, 2592E3)
    }
    ;
    g.be = function(a) {
        5 == a && ji(this)
    }
    ;
    g.de = function(a) {
        I("WATCH_LATER_VIDEO_ADDED", a)
    }
    ;
    g.ee = function(a) {
        I("WATCH_LATER_VIDEO_REMOVED", a)
    }
    ;
    g.Yd = function(a) {
        a = a || window.event;
        a = a.target || a.srcElement;
        3 == a.nodeType && (a = a.parentNode);
        if (!Vc(this.l, a))
            this.f.onExternalMouseMove()
    }
    ;
    g.Xd = function() {
        Uf(document, "mousemove", this.Yd, !1, this)
    }
    ;
    g.Zd = function() {
        this.Mb = !0
    }
    ;
    g.$d = function() {
        this.Mb = !1
    }
    ;
    g.Hc = function(a) {
        this.Mb && a.preventDefault()
    }
    ;
    g.Pe = function() {
        ni(this);
        this.C = "unsupported";
        var a = 'Adobe Flash Player or an HTML5 supported browser is required for video playback. <br> <a href="http://get.adobe.com/flashplayer/">Get the latest Flash Player</a> <br> <a href="/html5">Learn more about upgrading to an HTML5 browser</a>'
          , b = navigator.userAgent.match(/Version\/(\d).*Safari/);
        b && 5 <= parseInt(b[1], 10) && (a = 'Adobe Flash Player or QuickTime is required for video playback. <br> <a href="http://get.adobe.com/flashplayer/"> Get the latest Flash Player</a> <br> <a href="http://www.apple.com/quicktime/download/">Get the latest version of QuickTime</a>');
        b = this.e.messages.player_fallback || a;
        a = Nc("player-unavailable");
        if (Nc("unavailable-submessage") && a) {
            Nc("unavailable-submessage").innerHTML = b;
            var b = a || document
              , c = null;
            b.getElementsByClassName ? c = b.getElementsByClassName("icon")[0] : b.querySelectorAll && b.querySelector ? c = b.querySelector(".icon") : c = Qc("icon", a)[0];
            if (c = b = c || null)
                c = b ? b.dataset ? yb("icon")in b.dataset : b.hasAttribute ? !!b.hasAttribute("data-icon") : !!b.getAttribute("data-icon") : !1;
            c && (b.src = C(b, "icon"));
            kc(a, "hid");
            jc(Nc("player"), "off-screen-trigger")
        }
    }
    ;
    g.Fd = function() {
        return this.C || mi(this)
    }
    ;
    g.Ed = function() {
        return this.Ob
    }
    ;
    function mi(a) {
        return (a = ki(a)) ? "div" == a.tagName.toLowerCase() ? "html5" : "flash" : null
    }
    function ni(a) {
        qh("dcp");
        a.cancel();
        di(a);
        a.C = null;
        a.e && (a.e.loaded = !1);
        var b = ki(a);
        "html5" == mi(a) ? a.Lb = b : b && b.destroy && b.destroy();
        Sc(a.l);
        gd(a.Ca);
        a.Ca.length = 0;
        a.H = null;
        a.N = null
    }
    g.cancel = function() {
        if (this.o) {
            var a = this.o;
            this.e.assets.js && a && (a = "" + ia(a),
            (a = bc[a]) && Tb(a))
        }
        H(this.pc);
        this.ma = !1
    }
    ;
    g.B = function() {
        ni(this);
        this.mb = null;
        for (var a in this.j)
            m[this.j[a]] = null;
        this.f = null;
        delete this.l;
        delete this.k;
        this.e && (this.Nb = this.e = this.e.fallback = null);
        ci.G.B.call(this)
    }
    ;
    var qi = {}
      , ri = "player_uid_" + (1E9 * Math.random() >>> 0);
    function si(a, b) {
        a = u(a) ? Oc(a) : a;
        b = Df(b);
        var c = ri + "_" + ia(a)
          , d = qi[c];
        if (d)
            return d.lc(b),
            d.f;
        d = new ci(a,c);
        qi[c] = d;
        I("player-added", d.f);
        Ab(d, oa(ti, d));
        G(function() {
            d.lc(b)
        }, 0);
        return d.f
    }
    function ui() {
        for (var a in qi) {
            var b = qi[a];
            b && b.cancel()
        }
    }
    function vi(a) {
        if (a = Nc(a))
            a = ri + "_" + ia(a),
            (a = qi[a]) && a.dispose()
    }
    function ti(a) {
        qi[a.S] = null
    }
    function wi(a) {
        a = Nc(a);
        if (!a)
            return null;
        var b = ri + "_" + ia(a)
          , c = qi[b];
        c || (c = new ci(a,b),
        qi[b] = c);
        return c.f
    }
    ;var xi = r("yt.abuse.botguardInitialized") || fc;
    q("yt.abuse.botguardInitialized", xi, void 0);
    var yi = r("yt.abuse.invokeBotguard") || gc;
    q("yt.abuse.invokeBotguard", yi, void 0);
    var zi = r("yt.player.exports.navigate") || Id;
    q("yt.player.exports.navigate", zi, void 0);
    var Ai = r("yt.player.embed") || si;
    q("yt.player.embed", Ai, void 0);
    var Bi = r("yt.player.destroy") || vi;
    q("yt.player.destroy", Bi, void 0);
    var Ci = r("yt.player.cancelAll") || ui;
    q("yt.player.cancelAll", Ci, void 0);
    var Di = r("yt.player.getPlayerByElement") || wi;
    q("yt.player.getPlayerByElement", Di, void 0);
    var Ei = r("yt.player.exports.feedbackStart") || xf;
    q("yt.player.exports.feedbackStart", Ei, void 0);
    var Fi = r("yt.player.exports.feedbackShowArticle") || yf;
    q("yt.player.exports.feedbackShowArticle", Fi, void 0);
    var Gi = r("yt.util.activity.init") || od;
    q("yt.util.activity.init", Gi, void 0);
    var Hi = r("yt.util.activity.getTimeSinceActive") || qd;
    q("yt.util.activity.getTimeSinceActive", Hi, void 0);
    var Ii = r("yt.util.activity.setTimestamp") || pd;
    q("yt.util.activity.setTimestamp", Ii, void 0);
    function Ji(a) {
        N.call(this, 1, arguments);
        this.e = a
    }
    y(Ji, N);
    function Ki(a) {
        N.call(this, 1, arguments);
        this.e = a
    }
    y(Ki, N);
    function Li(a, b, c) {
        N.call(this, 1, arguments);
        this.f = a;
        this.isEnabled = b;
        this.e = c || null
    }
    y(Li, N);
    function Mi(a, b) {
        N.call(this, 1, arguments);
        this.e = a;
        this.isEnabled = b
    }
    y(Mi, N);
    function Ni(a, b, c, d, e) {
        N.call(this, 2, arguments);
        this.f = a;
        this.e = b;
        this.k = c || null;
        this.h = d || null;
        this.source = e || null
    }
    y(Ni, N);
    function Oi(a, b, c) {
        N.call(this, 1, arguments);
        this.e = a;
        this.Ya = b
    }
    y(Oi, N);
    function Pi(a, b, c, d, e, f, h) {
        N.call(this, 1, arguments);
        this.f = a;
        this.Ya = b;
        this.e = c;
        this.j = d || null;
        this.k = e || null;
        this.h = f || null;
        this.source = h || null
    }
    y(Pi, N);
    var Qi = new O("subscription-batch-pref-email",Li)
      , Ri = new O("subscription-batch-pref-uploads",Li)
      , Si = new O("subscription-batch-subscribe",Ji)
      , Ti = new O("subscription-batch-unsubscribe",Ji)
      , Ui = new O("subscription-pref-email",Mi)
      , Vi = new O("subscription-pref-uploads",Mi)
      , Wi = new O("subscription-subscribe",Ni)
      , Xi = new O("subscription-subscribe-loading",Ki)
      , Yi = new O("subscription-subscribe-loaded",Ki)
      , Zi = new O("subscription-subscribe-success",Oi)
      , $i = new O("subscription-subscribe-external",Ni)
      , aj = new O("subscription-unsubscribe",Pi)
      , bj = new O("subscription-unsubscirbe-loading",Ki)
      , cj = new O("subscription-unsubscribe-loaded",Ki)
      , dj = new O("subscription-unsubscribe-success",Ki)
      , ej = new O("subscription-external-unsubscribe",Pi)
      , fj = new O("subscription-enable-ypc",Ki)
      , gj = new O("subscription-disable-ypc",Ki);
    function hj(a, b, c) {
        var d = document.location.protocol + "//" + document.domain + "/post_login";
        b && (d = Bd(d, "mode", b));
        b = Bd("/signin?context=popup", "next", d);
        c && (b = Bd(b, "feature", c));
        if (c = window.open(b, "loginPopup", "width=375,height=440,resizable=yes,scrollbars=yes", !0))
            b = Rb("LOGGED_IN", function(b) {
                Tb(F("LOGGED_IN_PUBSUB_KEY"));
                Fb("LOGGED_IN", !0);
                a(b)
            }),
            Fb("LOGGED_IN_PUBSUB_KEY", b),
            c.moveTo((screen.width - 375) / 2, (screen.height - 440) / 2)
    }
    q("yt.pubsub.publish", I, void 0);
    var ij = null;
    "undefined" != typeof XMLHttpRequest ? ij = function() {
        return new XMLHttpRequest
    }
    : "undefined" != typeof ActiveXObject && (ij = function() {
        return new ActiveXObject("Microsoft.XMLHTTP")
    }
    );
    function jj(a, b, c, d, e, f, h) {
        function k() {
            4 == (l && "readyState"in l ? l.readyState : 0) && b && Hb(b)(l)
        }
        var l = ij && ij();
        if (!("open"in l))
            return null;
        "onloadend"in l ? l.addEventListener("loadend", k, !1) : l.onreadystatechange = k;
        c = (c || "GET").toUpperCase();
        d = d || "";
        l.open(c, a, !0);
        f && (l.responseType = f);
        h && (l.withCredentials = !0);
        f = "POST" == c;
        if (e = kj(a, e))
            for (var n in e)
                l.setRequestHeader(n, e[n]),
                "content-type" == n.toLowerCase() && (f = !1);
        f && l.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        l.send(d);
        return l
    }
    function kj(a, b) {
        b = b || {};
        for (var c in lj) {
            var d = F(lj[c]), e;
            if (e = d) {
                e = a;
                var f = void 0;
                f = window.location.href;
                var h = sd(e)[1] || null
                  , k = Ed(e);
                h && k ? (e = sd(e),
                f = sd(f),
                e = e[3] == f[3] && e[1] == f[1] && e[4] == f[4]) : e = k ? Ed(f) == k && (Number(sd(f)[4] || null) || null) == (Number(sd(e)[4] || null) || null) : !0;
                e || (e = c,
                f = F("CORS_HEADER_WHITELIST") || {},
                e = (h = Ed(a)) ? (f = f[h]) ? Ha(f, e) : !1 : !0)
            }
            e && (b[c] = d)
        }
        return b
    }
    function mj(a, b) {
        var c = F("XSRF_FIELD_NAME"), d;
        b.headers && (d = b.headers["Content-Type"]);
        return !b.mf && (!Ed(a) || Ed(a) == document.location.hostname) && "POST" == b.method && (!d || "application/x-www-form-urlencoded" == d) && !(b.O && b.O[c])
    }
    function nj(a, b) {
        var c = b.format || "JSON";
        b.of && (a = document.location.protocol + "//" + document.location.hostname + (document.location.port ? ":" + document.location.port : "") + a);
        var d = F("XSRF_FIELD_NAME")
          , e = F("XSRF_TOKEN")
          , f = b.jc;
        f && (f[d] && delete f[d],
        a = Fd(a, f));
        var h = b.pf || ""
          , f = b.O;
        mj(a, b) && (f || (f = {}),
        f[d] = e);
        f && u(h) && (d = Dd(h),
        ib(d, f),
        h = Ad(d));
        var k = !1, l, n = jj(a, function(a) {
            if (!k) {
                k = !0;
                l && H(l);
                var d;
                t: switch (a && "status"in a ? a.status : -1) {
                case 200:
                case 201:
                case 202:
                case 203:
                case 204:
                case 205:
                case 206:
                case 304:
                    d = !0;
                    break t;
                default:
                    d = !1
                }
                var e = null;
                if (d || 400 <= a.status && 500 > a.status)
                    e = oj(c, a, b.kf);
                if (d)
                    t: {
                        switch (c) {
                        case "XML":
                            d = 0 == parseInt(e && e.return_code, 10);
                            break t;
                        case "RAW":
                            d = !0;
                            break t
                        }
                        d = !!e
                    }
                var e = e || {}
                  , f = b.context || m;
                d ? b.Z && b.Z.call(f, a, e) : b.onError && b.onError.call(f, a, e);
                b.cc && b.cc.call(f, a, e)
            }
        }, b.method, h, b.headers, b.responseType, b.withCredentials);
        b.Ab && 0 < b.timeout && (l = G(function() {
            k || (k = !0,
            n.abort(),
            H(l),
            b.Ab.call(b.context || m, n))
        }, b.timeout));
        return n
    }
    function oj(a, b, c) {
        var d = null;
        switch (a) {
        case "JSON":
            a = b.responseText;
            b = b.getResponseHeader("Content-Type") || "";
            a && 0 <= b.indexOf("json") && (d = Gh(a));
            break;
        case "XML":
            if (b = (b = b.responseXML) ? pj(b) : null)
                d = {},
                A(b.getElementsByTagName("*"), function(a) {
                    d[a.tagName] = qj(a)
                })
        }
        if (c)
            for (var e in d)
                if ("html_content" == e || ra(e))
                    d[e] = Jd(lb(), d[e]);
        return d
    }
    function pj(a) {
        return a ? (a = ("responseXML"in a ? a.responseXML : a).getElementsByTagName("root")) && 0 < a.length ? a[0] : null : null
    }
    function qj(a) {
        var b = "";
        A(a.childNodes, function(a) {
            b += a.nodeValue
        });
        return b
    }
    var lj = {
        "X-YouTube-Page-CL": "PAGE_CL",
        "X-YouTube-Page-Timestamp": "PAGE_BUILD_TIMESTAMP",
        "X-YouTube-Variants-Checksum": "VARIANTS_CHECKSUM"
    };
    function rj() {
        var a = F("PLAYER_CONFIG");
        return a && a.args && void 0 !== a.args.authuser ? !0 : !(!F("SESSION_INDEX") && !F("LOGGED_IN"))
    }
    ;var sj = {}
      , tj = "ontouchstart"in document;
    function uj(a, b, c) {
        var d;
        switch (a) {
        case "mouseover":
        case "mouseout":
            d = 3;
            break;
        case "mouseenter":
        case "mouseleave":
            d = 9
        }
        return Yc(c, function(a) {
            return ic(a, b)
        }, !0, d)
    }
    function vj(a) {
        var b = "mouseover" == a.type && "mouseenter"in sj || "mouseout" == a.type && "mouseleave"in sj
          , c = a.type in sj || b;
        if ("HTML" != a.target.tagName && c) {
            if (b) {
                var b = "mouseover" == a.type ? "mouseenter" : "mouseleave", c = sj[b], d;
                for (d in c.aa) {
                    var e = uj(b, d, a.target);
                    e && !Yc(a.relatedTarget, function(a) {
                        return a == e
                    }, !0) && c.A(d, e, b, a)
                }
            }
            if (b = sj[a.type])
                for (d in b.aa)
                    (e = uj(a.type, d, a.target)) && b.A(d, e, a.type, a)
        }
    }
    L(document, "blur", vj, !0);
    L(document, "change", vj, !0);
    L(document, "click", vj);
    L(document, "focus", vj, !0);
    L(document, "mouseover", vj);
    L(document, "mouseout", vj);
    L(document, "mousedown", vj);
    L(document, "keydown", vj);
    L(document, "keyup", vj);
    L(document, "keypress", vj);
    L(document, "cut", vj);
    L(document, "paste", vj);
    tj && (L(document, "touchstart", vj),
    L(document, "touchend", vj),
    L(document, "touchcancel", vj));
    function wj() {
        this.f = {};
        this.Rc = [];
        this.h = []
    }
    function xj(a, b) {
        return "yt-uix" + (a.Sb ? "-" + a.Sb : "") + (b ? "-" + b : "")
    }
    wj.prototype.init = t;
    wj.prototype.dispose = t;
    function yj(a, b, c) {
        a.h.push(kh(b, c, a))
    }
    function zj(a, b, c) {
        var d = xj(a, void 0)
          , e = v(c, a);
        b in sj || (sj[b] = new E);
        sj[b].subscribe(d, e);
        a.f[c] = e
    }
    function Aj(a, b) {
        xb(a, "tooltip-text", b)
    }
    wj.prototype.removeData = function(a, b) {
        a && (a.dataset ? delete a.dataset[yb(b)] : a.removeAttribute("data-" + b))
    }
    ;
    function Bj() {
        wj.call(this);
        this.e = {}
    }
    y(Bj, wj);
    ba(Bj);
    g = Bj.prototype;
    g.Sb = "tooltip";
    g.wb = 0;
    g.register = function() {
        zj(this, "mouseover", this.Dc);
        zj(this, "mouseout", this.yb);
        zj(this, "click", this.yb);
        zj(this, "touchstart", this.Le);
        zj(this, "touchend", this.Zc);
        zj(this, "touchcancel", this.Zc)
    }
    ;
    g.dispose = function() {
        for (var a in this.e)
            this.yb(this.e[a]);
        this.e = {}
    }
    ;
    g.Dc = function(a) {
        if (!(this.wb && 1E3 > w() - this.wb)) {
            var b = parseInt(C(a, "tooltip-hide-timer"), 10);
            b && (this.removeData(a, "tooltip-hide-timer"),
            H(b));
            var b = v(function() {
                Cj(this, a);
                this.removeData(a, "tooltip-show-timer")
            }, this)
              , c = parseInt(C(a, "tooltip-show-delay"), 10) || 0
              , b = G(b, c);
            xb(a, "tooltip-show-timer", b.toString());
            a.title && (Aj(a, Dj(a)),
            a.title = "");
            b = ia(a).toString();
            this.e[b] = a
        }
    }
    ;
    g.yb = function(a) {
        var b = parseInt(C(a, "tooltip-show-timer"), 10);
        b && (H(b),
        this.removeData(a, "tooltip-show-timer"));
        b = v(function() {
            if (a) {
                var b = Nc(Ej(this, a));
                b && (Fj(b),
                b && b.parentNode && b.parentNode.removeChild(b),
                this.removeData(a, "content-id"))
            }
            this.removeData(a, "tooltip-hide-timer")
        }, this);
        b = G(b, 50);
        xb(a, "tooltip-hide-timer", b.toString());
        if (b = C(a, "tooltip-text"))
            a.title = b;
        b = ia(a).toString();
        delete this.e[b]
    }
    ;
    g.Le = function(a, b) {
        this.wb = 0;
        var c = uj(b, xj(this), null[0].target);
        this.Dc(c)
    }
    ;
    g.Zc = function(a, b) {
        this.wb = w();
        var c = uj(b, xj(this), null[0].target);
        this.yb(c)
    }
    ;
    function Gj(a, b) {
        Aj(a, b);
        var c = C(a, "content-id");
        if (c = Nc(c))
            if ("textContent"in c)
                c.textContent = b;
            else if (3 == c.nodeType)
                c.data = b;
            else if (c.firstChild && 3 == c.firstChild.nodeType) {
                for (; c.lastChild != c.firstChild; )
                    c.removeChild(c.lastChild);
                c.firstChild.data = b
            } else {
                Sc(c);
                var d = Mc(c);
                c.appendChild(d.createTextNode(String(b)))
            }
    }
    function Dj(a) {
        return C(a, "tooltip-text") || a.title
    }
    function Cj(a, b) {
        if (b) {
            var c = Dj(b);
            if (c) {
                var d = Nc(Ej(a, b));
                if (!d) {
                    d = document.createElement("div");
                    d.id = Ej(a, b);
                    d.className = xj(a, "tip");
                    var e = document.createElement("div");
                    e.className = xj(a, "tip-body");
                    var f = document.createElement("div");
                    f.className = xj(a, "tip-arrow");
                    var h = document.createElement("div");
                    h.className = xj(a, "tip-content");
                    var k = Hj(a, b)
                      , l = Ej(a, b, "content");
                    h.id = l;
                    xb(b, "content-id", l);
                    e.appendChild(h);
                    k && d.appendChild(k);
                    d.appendChild(e);
                    d.appendChild(f);
                    (bd() || document.body).appendChild(d);
                    Gj(b, c);
                    (c = parseInt(C(b, "tooltip-max-width"), 10)) && e.offsetWidth > c && (e.style.width = c + "px",
                    jc(h, xj(a, "normal-wrap")));
                    h = ic(b, xj(a, "reverse"));
                    Ij(a, b, d, e, k, h) || Ij(a, b, d, e, k, !h);
                    var n = xj(a, "tip-visible");
                    G(function() {
                        jc(d, n)
                    }, 0)
                }
            }
        }
    }
    function Ij(a, b, c, d, e, f) {
        lc(c, xj(a, "tip-reverse"), f);
        var h = 0;
        f && (h = 1);
        a = lf(b);
        f = new mc((a.width - 10) / 2,f ? a.height : 0);
        var k = Mc(b), l = new mc(0,0), n;
        n = k ? Mc(k) : document;
        var x;
        (x = !K || Gc(9)) || (x = Kc(n),
        x = Rc(x.e));
        b != (x ? n.documentElement : n.body) && (n = jf(b),
        x = Kc(k).e,
        k = !yc && Rc(x) ? x.documentElement : x.body || x.documentElement,
        x = x.parentWindow || x.defaultView,
        k = K && Fc("10") && x.pageYOffset != k.scrollTop ? new mc(k.scrollLeft,k.scrollTop) : new mc(x.pageXOffset || k.scrollLeft,x.pageYOffset || k.scrollTop),
        l.x = n.left + k.x,
        l.y = n.top + k.y);
        f = new mc(l.x + f.x,l.y + f.y);
        f = f.clone();
        l = (h & 4 && "rtl" == hf(c, "direction") ? h ^ 2 : h) & -5;
        h = lf(c);
        n = h.clone();
        k = f.clone();
        n = n.clone();
        0 != l && (l & 2 && (k.x -= n.width + 0),
        l & 1 && (k.y -= n.height + 0));
        f = new ff(0,0,0,0);
        f.left = k.x;
        f.top = k.y;
        f.width = n.width;
        f.height = n.height;
        n = new mc(f.left,f.top);
        n instanceof mc ? (l = n.x,
        n = n.y) : (l = n,
        n = void 0);
        c.style.left = kf(l, !1);
        c.style.top = kf(n, !1);
        n = new nc(f.width,f.height);
        if (!(h == n || h && n && h.width == n.width && h.height == n.height))
            if (h = n,
            f = Mc(c),
            f = Kc(f),
            l = Rc(f.e),
            !K || Fc("10") || l && Fc("8"))
                f = c.style,
                xc ? f.MozBoxSizing = "border-box" : yc ? f.WebkitBoxSizing = "border-box" : f.boxSizing = "border-box",
                f.width = Math.max(h.width, 0) + "px",
                f.height = Math.max(h.height, 0) + "px";
            else if (f = c.style,
            l) {
                K ? (l = of(c, "paddingLeft"),
                n = of(c, "paddingRight"),
                k = of(c, "paddingTop"),
                x = of(c, "paddingBottom"),
                l = new ef(k,n,x,l)) : (l = gf(c, "paddingLeft"),
                n = gf(c, "paddingRight"),
                k = gf(c, "paddingTop"),
                x = gf(c, "paddingBottom"),
                l = new ef(parseFloat(k),parseFloat(n),parseFloat(x),parseFloat(l)));
                if (K && !Gc(9)) {
                    n = qf(c, "borderLeft");
                    k = qf(c, "borderRight");
                    x = qf(c, "borderTop");
                    var Z = qf(c, "borderBottom");
                    n = new ef(x,k,Z,n)
                } else
                    n = gf(c, "borderLeftWidth"),
                    k = gf(c, "borderRightWidth"),
                    x = gf(c, "borderTopWidth"),
                    Z = gf(c, "borderBottomWidth"),
                    n = new ef(parseFloat(x),parseFloat(k),parseFloat(Z),parseFloat(n));
                f.pixelWidth = h.width - n.left - l.left - l.right - n.right;
                f.pixelHeight = h.height - n.top - l.top - l.bottom - n.bottom
            } else
                f.pixelWidth = h.width,
                f.pixelHeight = h.height;
        h = window.document;
        h = Rc(h) ? h.documentElement : h.body;
        h = new nc(h.clientWidth,h.clientHeight);
        1 == c.nodeType ? (c = jf(c),
        n = new mc(c.left,c.top)) : (f = ga(c.f),
        l = c,
        c.targetTouches && c.targetTouches.length ? l = c.targetTouches[0] : f && c.e.targetTouches && c.e.targetTouches.length && (l = c.e.targetTouches[0]),
        n = new mc(l.clientX,l.clientY));
        c = lf(d);
        k = Math.floor(c.width / 2);
        f = !!(h.height < n.y + a.height);
        a = !!(n.y < a.height);
        l = !!(n.x < k);
        h = !!(h.width < n.x + k);
        n = (c.width + 3) / -2 - -5;
        b = C(b, "force-tooltip-direction");
        if ("left" == b || l)
            n = -5;
        else if ("right" == b || h)
            n = 20 - c.width - 3;
        b = Math.floor(n) + "px";
        d.style.left = b;
        e && (e.style.left = b,
        e.style.height = c.height + "px",
        e.style.width = c.width + "px");
        return !(f || a)
    }
    function Ej(a, b, c) {
        a = xj(a);
        var d = b.__yt_uid_key;
        d || (d = $c(),
        b.__yt_uid_key = d);
        b = a + d;
        c && (b += "-" + c);
        return b
    }
    function Hj(a, b) {
        var c = null;
        Ac && ic(b, xj(a, "masked")) && ((c = Nc("yt-uix-tooltip-shared-mask")) ? (c.parentNode.removeChild(c),
        sf(c)) : (c = document.createElement("iframe"),
        c.src = 'javascript:""',
        c.id = "yt-uix-tooltip-shared-mask",
        c.className = xj(a, "tip-mask")));
        return c
    }
    function Fj(a) {
        var b = Nc("yt-uix-tooltip-shared-mask")
          , c = b && Yc(b, function(b) {
            return b == a
        }, !1, 2);
        b && c && (b.parentNode.removeChild(b),
        tf(b),
        document.body.appendChild(b))
    }
    ;function Jj() {
        wj.call(this)
    }
    y(Jj, wj);
    ba(Jj);
    Jj.prototype.Sb = "subscription-button";
    Jj.prototype.register = function() {
        zj(this, "click", this.wc);
        yj(this, Xi, this.Gc);
        yj(this, Yi, this.Fc);
        yj(this, Zi, this.me);
        yj(this, bj, this.Gc);
        yj(this, cj, this.Fc);
        yj(this, dj, this.re);
        yj(this, fj, this.Wd);
        yj(this, gj, this.Vd)
    }
    ;
    var Xc = {
        mc: "hover-enabled",
        hd: "yt-uix-button-subscribe",
        jd: "yt-uix-button-subscribed",
        $e: "ypc-enabled",
        kd: "yt-uix-button-subscription-container",
        ld: "yt-subscription-button-disabled-mask-container"
    }
      , Kj = {
        af: "channel-external-id",
        md: "subscriber-count-show-when-subscribed",
        nd: "subscriber-count-tooltip",
        od: "subscriber-count-title",
        bf: "href",
        nc: "is-subscribed",
        df: "parent-url",
        ff: "sessionlink",
        pd: "style-type",
        oc: "subscription-id",
        hf: "target",
        rd: "ypc-enabled"
    };
    g = Jj.prototype;
    g.wc = function(a) {
        var b = C(a, "href")
          , c = rj();
        if (b)
            a = C(a, "target") || "_self",
            window.open(b, a);
        else if (c) {
            var b = C(a, "channel-external-id"), c = C(a, "sessionlink"), d;
            if (C(a, "ypc-enabled")) {
                d = C(a, "ypc-item-type");
                var e = C(a, "ypc-item-id");
                d = {
                    itemType: d,
                    itemId: e,
                    subscriptionElement: a
                }
            } else
                d = null;
            e = C(a, "parent-url");
            if (C(a, "is-subscribed")) {
                var f = C(a, "subscription-id");
                P(aj, new Pi(b,f,d,a,c,e))
            } else
                P(Wi, new Ni(b,d,c,e))
        } else
            Lj(this, a)
    }
    ;
    g.Gc = function(a) {
        this.Ra(a.e, this.Wc, !0)
    }
    ;
    g.Fc = function(a) {
        this.Ra(a.e, this.Wc, !1)
    }
    ;
    g.me = function(a) {
        this.Ra(a.e, this.Xc, !0, a.Ya)
    }
    ;
    g.re = function(a) {
        this.Ra(a.e, this.Xc, !1)
    }
    ;
    g.Wd = function(a) {
        this.Ra(a.e, this.yd)
    }
    ;
    g.Vd = function(a) {
        this.Ra(a.e, this.xd)
    }
    ;
    g.Xc = function(a, b, c) {
        b ? (xb(a, Kj.nc, "true"),
        c && xb(a, Kj.oc, c)) : (this.removeData(a, Kj.nc),
        this.removeData(a, Kj.oc));
        Mj(a)
    }
    ;
    g.Wc = function(a, b) {
        var c;
        c = Wc(a);
        lc(c, Xc.ld, b);
        a.setAttribute("aria-busy", b ? "true" : "false");
        a.disabled = b
    }
    ;
    function Mj(a) {
        var b = C(a, Kj.pd)
          , c = !!C(a, "is-subscribed")
          , b = "-" + b
          , d = Xc.jd + b;
        lc(a, Xc.hd + b, !c);
        lc(a, d, c);
        C(a, Kj.nd) && !C(a, Kj.md) && (b = xj(Bj.getInstance()),
        lc(a, b, !c),
        a.title = c ? "" : C(a, Kj.od));
        c ? G(function() {
            jc(a, Xc.mc)
        }, 1E3) : kc(a, Xc.mc)
    }
    g.yd = function(a) {
        var b = !!C(a, "ypc-item-type")
          , c = !!C(a, "ypc-item-id");
        !C(a, "ypc-enabled") && b && c && (jc(a, "ypc-enabled"),
        xb(a, Kj.rd, "true"))
    }
    ;
    g.xd = function(a) {
        C(a, "ypc-enabled") && (kc(a, "ypc-enabled"),
        this.removeData(a, "ypc-enabled"))
    }
    ;
    function Nj(a, b) {
        var c = Pc(xj(a));
        return Ca(c, function(a) {
            return b == C(a, "channel-external-id")
        }, a)
    }
    g.ud = function(a, b, c) {
        var d = Ra(arguments, 2);
        A(a, function(a) {
            b.apply(this, Na(a, d))
        }, this)
    }
    ;
    g.Ra = function(a, b, c) {
        var d = Nj(this, a)
          , d = Na([d], Ra(arguments, 1));
        this.ud.apply(this, d)
    }
    ;
    function Lj(a, b) {
        var c = v(function(a) {
            a.discoverable_subscriptions && Fb("SUBSCRIBE_EMBED_DISCOVERABLE_SUBSCRIPTIONS", a.discoverable_subscriptions);
            this.wc(b)
        }, a);
        hj(c, "subscribe", "sub_button")
    }
    ;var Oj = window.yt && window.yt.uix && window.yt.uix.widgets_ || {};
    q("yt.uix.widgets_", Oj, void 0);
    function Pj(a) {
        return (0 == a.search("cue") || 0 == a.search("load")) && "loadModule" != a
    }
    function Qj(a, b, c) {
        u(a) && (a = {
            mediaContentUrl: a,
            startSeconds: b,
            suggestedQuality: c
        });
        b = a;
        c = /\/([ve]|embed)\/([^#?]+)/.exec(a.mediaContentUrl);
        b.videoId = c && c[2] ? c[2] : null;
        return Rj(a)
    }
    function Rj(a, b, c) {
        if (ha(a)) {
            b = "endSeconds startSeconds mediaContentUrl suggestedQuality videoId two_stage_token".split(" ");
            c = {};
            for (var d = 0; d < b.length; d++) {
                var e = b[d];
                a[e] && (c[e] = a[e])
            }
            return c
        }
        return {
            videoId: a,
            startSeconds: b,
            suggestedQuality: c
        }
    }
    function Sj(a, b, c, d) {
        if (ha(a) && !da(a)) {
            b = "playlist list listType index startSeconds suggestedQuality".split(" ");
            c = {};
            for (d = 0; d < b.length; d++) {
                var e = b[d];
                a[e] && (c[e] = a[e])
            }
            return c
        }
        c = {
            index: b,
            startSeconds: c,
            suggestedQuality: d
        };
        u(a) && 16 == a.length ? c.list = "PL" + a : c.playlist = a;
        return c
    }
    function Tj(a) {
        var b = a.video_id || a.videoId;
        if (u(b)) {
            var c = T("yt-player-two-stage-token") || {}
              , d = T("yt-player-two-stage-token") || {};
            p(void 0) ? d[b] = void 0 : delete d[b];
            S("yt-player-two-stage-token", d, 300);
            (b = c[b]) && (a.two_stage_token = b)
        }
    }
    ;var Uj = w()
      , Vj = null
      , Wj = Array(50)
      , Xj = -1
      , Yj = !1;
    function Zj(a) {
        ak();
        Vj.push(a);
        bk(Vj)
    }
    function ck(a) {
        var b = r("yt.mdx.remote.debug.handlers_");
        La(b || [], a)
    }
    function dk(a, b) {
        ak();
        var c = Vj
          , d = ek(a, String(b));
        0 == c.length ? fk(d) : (bk(c),
        A(c, function(a) {
            a(d)
        }))
    }
    function ak() {
        Vj || (Vj = r("yt.mdx.remote.debug.handlers_") || [],
        q("yt.mdx.remote.debug.handlers_", Vj, void 0))
    }
    function fk(a) {
        var b = (Xj + 1) % 50;
        Xj = b;
        Wj[b] = a;
        Yj || (Yj = 49 == b)
    }
    function bk(a) {
        var b = Wj;
        if (b[0]) {
            var c = Xj
              , d = Yj ? c : -1;
            do {
                var d = (d + 1) % 50
                  , e = b[d];
                A(a, function(a) {
                    a(e)
                })
            } while (d != c);
            Wj = Array(50);
            Xj = -1;
            Yj = !1
        }
    }
    function ek(a, b) {
        var c = (w() - Uj) / 1E3;
        c.toFixed && (c = c.toFixed(3));
        var d = [];
        d.push("[", c + "s", "] ");
        d.push("[", "yt.mdx.remote", "] ");
        d.push(a + ": " + b, "\n");
        return d.join("")
    }
    ;function gk(a) {
        a = a || {};
        this.name = a.name || "";
        this.id = a.id || a.screenId || "";
        this.token = a.token || a.loungeToken || "";
        this.uuid = a.uuid || a.dialId || ""
    }
    function hk(a, b) {
        return !!b && (a.id == b || a.uuid == b)
    }
    function ik(a, b) {
        return a || b ? !a != !b ? !1 : a.id == b.id : !0
    }
    function jk(a, b) {
        return a || b ? !a != !b ? !1 : a.id == b.id && a.token == b.token && a.name == b.name && a.uuid == b.uuid : !0
    }
    function kk(a) {
        return {
            name: a.name,
            screenId: a.id,
            loungeToken: a.token,
            dialId: a.uuid
        }
    }
    function lk(a) {
        return new gk(a)
    }
    function mk(a) {
        return da(a) ? B(a, lk) : []
    }
    function nk(a) {
        return a ? '{name:"' + a.name + '",id:' + a.id.substr(0, 6) + "..,token:" + (a.token ? ".." + a.token.slice(-6) : "-") + ",uuid:" + (a.uuid ? ".." + a.uuid.slice(-6) : "-") + "}" : "null"
    }
    function ok(a) {
        return da(a) ? "[" + B(a, nk).join(",") + "]" : "null"
    }
    ;var pk = ["boadgeojelhgndaghljhdicfkmllpafd", "dliochdbjfkdbacpmhlcpmleaejidimm", "hfaagokkkhdbgiakmmlclaapfelnkoah", "fmfcbgogabcbclcofgocippekhfcmgfj", "enhhojjnijigcajfphajepfemndkmdlo"];
    function qk(a, b) {
        a == pk.length ? b(null) : rk(pk[a], function(c) {
            c ? (c = pk[a],
            S("yt-remote-cast-last-extension", c),
            b(c)) : qk(a + 1, b)
        })
    }
    function sk(a) {
        return "chrome-extension://" + a + "/cast_sender.js"
    }
    function rk(a, b) {
        var c = new XMLHttpRequest;
        c.onreadystatechange = function() {
            4 == c.readyState && 200 == c.status && b(!0)
        }
        ;
        c.onerror = function() {
            b(!1)
        }
        ;
        try {
            c.open("GET", sk(a), !0),
            c.send()
        } catch (d) {
            b(!1)
        }
    }
    function tk(a) {
        window.__onGCastApiAvailable = a;
        uk(function(b) {
            if (b) {
                dk("bootstrap", "Found cast extension: " + b);
                q("chrome.cast.extensionId", b, void 0);
                var c = document.createElement("script");
                c.src = sk(b);
                c.onerror = function() {
                    vk();
                    Zh("yt-remote-cast-last-extension");
                    a(!1, "Extension JS failed to load.")
                }
                ;
                (document.head || document.documentElement).appendChild(c)
            } else
                dk("bootstrap", "No cast extension found"),
                a(!1, "No cast extension found")
        })
    }
    function vk() {
        window.__onGCastApiAvailable && delete window.__onGCastApiAvailable
    }
    function uk(a) {
        var b = T("yt-remote-cast-last-extension");
        b ? a(b) : qk(0, a)
    }
    ;var wk = {
        SKA_SKIPPABLE_ADS: "ska",
        ef: "que",
        cf: "mus",
        gf: "sus"
    };
    function xk(a) {
        this.port = this.h = "";
        this.e = "/api/lounge";
        this.f = !0;
        a = a || document.location.href;
        var b = Number(sd(a)[4] || null) || null || "";
        b && (this.port = ":" + b);
        this.h = ud(a) || "";
        a = oc;
        0 <= a.search("MSIE") && (a = a.match(/MSIE ([\d.]+)/)[1],
        0 > xa(a, "10.0") && (this.f = !1))
    }
    function yk(a, b, c, d) {
        var e = a.e;
        if (p(d) ? d : a.f)
            e = "https://" + a.h + a.port + a.e;
        return Cd(e + b, c || {})
    }
    function zk(a, b, c, d, e) {
        a = {
            format: "JSON",
            method: "POST",
            context: a,
            timeout: 5E3,
            withCredentials: !1,
            Z: oa(a.j, d, !0),
            onError: oa(a.k, e),
            Ab: oa(a.l, e)
        };
        c && (a.O = c,
        a.headers = {
            "Content-Type": "application/x-www-form-urlencoded"
        });
        return nj(b, a)
    }
    xk.prototype.j = function(a, b, c, d) {
        b ? a(d) : a({
            text: c.responseText
        })
    }
    ;
    xk.prototype.k = function(a, b) {
        a(Error("Request error: " + b.status))
    }
    ;
    xk.prototype.l = function(a) {
        a(Error("request timed out"))
    }
    ;
    function Ak(a) {
        a && (this.id = a.id || "",
        this.name = a.name || "",
        this.activityId = a.activityId || "",
        this.status = a.status || "UNKNOWN")
    }
    Ak.prototype.id = "";
    Ak.prototype.name = "";
    Ak.prototype.activityId = "";
    Ak.prototype.status = "UNKNOWN";
    function Bk(a) {
        return {
            id: a.id,
            name: a.name,
            activityId: a.activityId,
            status: a.status
        }
    }
    Ak.prototype.toString = function() {
        return "{id:" + this.id + ",name:" + this.name + ",activityId:" + this.activityId + ",status:" + this.status + "}"
    }
    ;
    function Ck(a) {
        a = a || [];
        return "[" + B(a, function(a) {
            return a ? a.toString() : "null"
        }).join(",") + "]"
    }
    ;function Dk() {
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(a) {
            var b = 16 * Math.random() | 0;
            return ("x" == a ? b : b & 3 | 8).toString(16)
        })
    }
    function Ek(a, b) {
        return Fa(a, function(a) {
            return a.key == b
        })
    }
    function Fk(a) {
        return B(a, function(a) {
            return {
                key: a.id,
                name: a.name
            }
        })
    }
    function Gk(a) {
        return B(a, function(a) {
            return Bk(a)
        })
    }
    function Hk(a) {
        return B(a, function(a) {
            return new Ak(a)
        })
    }
    function Ik(a, b) {
        return a || b ? a && b ? a.id == b.id && a.name == b.name : !1 : !0
    }
    function Jk(a, b) {
        return Fa(a, function(a) {
            return a.id == b
        })
    }
    function Kk(a, b) {
        return Fa(a, function(a) {
            return ik(a, b)
        })
    }
    function Lk(a, b) {
        return Fa(a, function(a) {
            return hk(a, b)
        })
    }
    ;function U() {
        D.call(this);
        this.k = new E;
        Ab(this, oa(Bb, this.k))
    }
    y(U, D);
    U.prototype.subscribe = function(a, b, c) {
        return this.F() ? 0 : this.k.subscribe(a, b, c)
    }
    ;
    U.prototype.Na = function(a, b, c) {
        return this.F() ? !1 : this.k.Na(a, b, c)
    }
    ;
    U.prototype.ka = function(a) {
        return this.F() ? !1 : this.k.ka(a)
    }
    ;
    U.prototype.A = function(a, b) {
        return this.F() ? !1 : this.k.A.apply(this.k, arguments)
    }
    ;
    function Mk(a) {
        U.call(this);
        this.o = a;
        this.screens = []
    }
    y(Mk, U);
    g = Mk.prototype;
    g.X = function() {
        return this.screens
    }
    ;
    g.contains = function(a) {
        return !!Kk(this.screens, a)
    }
    ;
    g.get = function(a) {
        return a ? Lk(this.screens, a) : null
    }
    ;
    function Nk(a, b) {
        var c = a.get(b.uuid) || a.get(b.id);
        if (c) {
            var d = c.name;
            c.id = b.id || c.id;
            c.name = b.name;
            c.token = b.token;
            c.uuid = b.uuid || c.uuid;
            return c.name != d
        }
        a.screens.push(b);
        return !0
    }
    function Ok(a, b) {
        var c = a.screens.length != b.length;
        a.screens = Ca(a.screens, function(a) {
            return !!Kk(b, a)
        });
        for (var d = 0, e = b.length; d < e; d++)
            c = Nk(a, b[d]) || c;
        return c
    }
    function Pk(a, b) {
        var c = a.screens.length;
        a.screens = Ca(a.screens, function(a) {
            return !ik(a, b)
        });
        return a.screens.length < c
    }
    g.info = function(a) {
        dk(this.o, a)
    }
    ;
    g.warn = function(a) {
        dk(this.o, a)
    }
    ;
    function Qk(a, b, c, d) {
        U.call(this);
        this.C = a;
        this.o = b;
        this.j = c;
        this.l = d;
        this.h = 0;
        this.e = null;
        this.f = NaN
    }
    y(Qk, U);
    var Rk = [2E3, 2E3, 1E3, 1E3, 1E3, 2E3, 2E3, 5E3, 5E3, 1E4];
    g = Qk.prototype;
    g.start = function() {
        !this.e && isNaN(this.f) && this.Qc()
    }
    ;
    g.stop = function() {
        this.e && (this.e.abort(),
        this.e = null);
        isNaN(this.f) || (H(this.f),
        this.f = NaN)
    }
    ;
    g.B = function() {
        this.stop();
        Qk.G.B.call(this)
    }
    ;
    g.Qc = function() {
        this.f = NaN;
        this.e = nj(yk(this.C, "/pairing/get_screen"), {
            method: "POST",
            O: {
                pairing_code: this.o
            },
            timeout: 5E3,
            Z: v(this.Se, this),
            onError: v(this.Re, this),
            Ab: v(this.Te, this)
        })
    }
    ;
    g.Se = function(a, b) {
        this.e = null;
        var c = b.screen || {};
        c.dialId = this.j;
        c.name = this.l;
        this.A("pairingComplete", new gk(c))
    }
    ;
    g.Re = function(a) {
        this.e = null;
        a.status && 404 == a.status ? this.h >= Rk.length ? this.A("pairingFailed", Error("DIAL polling timed out")) : (a = Rk[this.h],
        this.f = G(v(this.Qc, this), a),
        this.h++) : this.A("pairingFailed", Error("Server error " + a.status))
    }
    ;
    g.Te = function() {
        this.e = null;
        this.A("pairingFailed", Error("Server not responding"))
    }
    ;
    function Sk(a) {
        a && (this.id = a.id || a.name,
        this.name = a.name,
        this.sd = a.app,
        this.type = a.type || "REMOTE_CONTROL",
        this.avatar = a.userAvatarUri || "",
        this.theme = a.theme || "u",
        this.capabilities = new Zd(Ca((a.capabilities || "").split(","), oa(Ya, wk))))
    }
    Sk.prototype.id = "";
    Sk.prototype.name = "";
    g = Sk.prototype;
    g.sd = "";
    g.type = "REMOTE_CONTROL";
    g.avatar = "";
    g.theme = "u";
    g.equals = function(a) {
        return a ? this.id == a.id : !1
    }
    ;
    var Tk;
    function Uk() {
        var a = Vk()
          , b = Wk();
        Ha(a, b);
        if (Xk()) {
            var c = a, d;
            d = 0;
            for (var e = c.length, f; d < e; ) {
                var h = d + e >> 1, k;
                k = Ua(b, c[h]);
                0 < k ? d = h + 1 : (e = h,
                f = !k)
            }
            d = f ? d : ~d;
            0 > d && Qa(c, -(d + 1), 0, b)
        }
        a = Yk(a);
        if (0 == a.length)
            try {
                a = "remote_sid",
                ld.remove("" + a, "/", "youtube.com")
            } catch (l) {}
        else
            try {
                md("remote_sid", a.join(","), -1)
            } catch (n) {}
    }
    function Vk() {
        var a = T("yt-remote-connected-devices") || [];
        a.sort(Ua);
        return a
    }
    function Yk(a) {
        if (0 == a.length)
            return [];
        var b = a[0].indexOf("#")
          , c = -1 == b ? a[0] : a[0].substring(0, b);
        return B(a, function(a, b) {
            return 0 == b ? a : a.substring(c.length)
        })
    }
    function Zk(a) {
        S("yt-remote-connected-devices", a, 86400)
    }
    function Wk() {
        if ($k)
            return $k;
        var a = T("yt-remote-device-id");
        a || (a = Dk(),
        S("yt-remote-device-id", a, 31536E3));
        for (var b = Vk(), c = 1, d = a; Ha(b, d); )
            c++,
            d = a + "#" + c;
        return $k = d
    }
    function al() {
        return T("yt-remote-session-browser-channel")
    }
    function Xk() {
        return T("yt-remote-session-screen-id")
    }
    function bl(a) {
        5 < a.length && (a = a.slice(a.length - 5));
        var b = B(cl(), function(a) {
            return a.loungeToken
        })
          , c = B(a, function(a) {
            return a.loungeToken
        });
        Ea(c, function(a) {
            return !Ha(b, a)
        }) && dl();
        S("yt-remote-local-screens", a, 31536E3)
    }
    function cl() {
        return T("yt-remote-local-screens") || []
    }
    function dl() {
        S("yt-remote-lounge-token-expiration", !0, 86400)
    }
    function el() {
        return !T("yt-remote-lounge-token-expiration")
    }
    function fl(a) {
        S("yt-remote-online-screens", a, 60)
    }
    function gl() {
        return T("yt-remote-online-screens") || []
    }
    function hl(a) {
        S("yt-remote-online-dial-devices", a, 30)
    }
    function il() {
        return T("yt-remote-online-dial-devices") || []
    }
    function jl(a, b) {
        S("yt-remote-session-browser-channel", a);
        S("yt-remote-session-screen-id", b);
        var c = Vk()
          , d = Wk();
        Ha(c, d) || c.push(d);
        Zk(c);
        Uk()
    }
    function kl(a) {
        a || (Zh("yt-remote-session-screen-id"),
        Zh("yt-remote-session-video-id"));
        Uk();
        a = Vk();
        La(a, Wk());
        Zk(a)
    }
    function ll() {
        if (!Tk) {
            var a;
            a = new Dh;
            (a = a.isAvailable() ? a : null) && (Tk = new Nh(a))
        }
        return Tk ? !!Tk.get("yt-remote-use-staging-server") : !1
    }
    var $k = "";
    function ml(a) {
        Mk.call(this, "LocalScreenService");
        this.f = a;
        this.e = NaN;
        nl(this);
        this.info("Initializing with " + ok(this.screens))
    }
    y(ml, Mk);
    g = ml.prototype;
    g.start = function() {
        nl(this) && this.A("screenChange");
        el() && ol(this);
        H(this.e);
        this.e = G(v(this.start, this), 1E4)
    }
    ;
    g.add = function(a, b) {
        nl(this);
        Nk(this, a);
        pl(this, !1);
        this.A("screenChange");
        b(a);
        a.token || ol(this)
    }
    ;
    g.remove = function(a, b) {
        var c = nl(this);
        Pk(this, a) && (pl(this, !1),
        c = !0);
        b(a);
        c && this.A("screenChange")
    }
    ;
    g.Hb = function(a, b, c, d) {
        var e = nl(this)
          , f = this.get(a.id);
        f ? (f.name != b && (f.name = b,
        pl(this, !1),
        e = !0),
        c(a)) : d(Error("no such local screen."));
        e && this.A("screenChange")
    }
    ;
    g.B = function() {
        H(this.e);
        ml.G.B.call(this)
    }
    ;
    function ol(a) {
        if (a.screens.length) {
            var b = B(a.screens, function(a) {
                return a.id
            })
              , c = yk(a.f, "/pairing/get_lounge_token_batch");
            zk(a.f, c, {
                screen_ids: b.join(",")
            }, v(a.Jd, a), v(a.Id, a))
        }
    }
    g.Jd = function(a) {
        nl(this);
        var b = this.screens.length;
        a = a && a.screens || [];
        for (var c = 0, d = a.length; c < d; ++c) {
            var e = a[c]
              , f = this.get(e.screenId);
            f && (f.token = e.loungeToken,
            --b)
        }
        pl(this, !b);
        b && this.warn("Missed " + b + " lounge tokens.")
    }
    ;
    g.Id = function(a) {
        this.warn("Requesting lounge tokens failed: " + a)
    }
    ;
    function nl(a) {
        var b = mk(cl())
          , b = Ca(b, function(a) {
            return !a.uuid
        });
        return Ok(a, b)
    }
    function pl(a, b) {
        bl(B(a.screens, kk));
        b && dl()
    }
    ;function ql(a, b) {
        U.call(this);
        this.l = b;
        for (var c = T("yt-remote-online-screen-ids") || "", c = c ? c.split(",") : [], d = {}, e = this.l(), f = 0, h = e.length; f < h; ++f) {
            var k = e[f].id;
            d[k] = Ha(c, k)
        }
        this.e = d;
        this.o = a;
        this.h = this.j = NaN;
        this.f = null;
        rl("Initialized with " + R(this.e))
    }
    y(ql, U);
    g = ql.prototype;
    g.start = function() {
        var a = parseInt(T("yt-remote-fast-check-period") || "0", 10);
        (this.j = w() - 144E5 < a ? 0 : a) ? sl(this) : (this.j = w() + 3E5,
        S("yt-remote-fast-check-period", this.j),
        this.fc())
    }
    ;
    g.isEmpty = function() {
        return db(this.e)
    }
    ;
    g.update = function() {
        rl("Updating availability on schedule.");
        var a = this.l()
          , b = Wa(this.e, function(b, d) {
            return b && !!Lk(a, d)
        }, this);
        tl(this, b)
    }
    ;
    function ul(a, b, c) {
        var d = yk(a.o, "/pairing/get_screen_availability");
        zk(a.o, d, {
            lounge_token: b.token
        }, v(function(a) {
            a = a.screens || [];
            for (var d = 0, h = a.length; d < h; ++d)
                if (a[d].loungeToken == b.token) {
                    c("online" == a[d].status);
                    return
                }
            c(!1)
        }, a), v(function() {
            c(!1)
        }, a))
    }
    g.B = function() {
        H(this.h);
        this.h = NaN;
        this.f && (this.f.abort(),
        this.f = null);
        ql.G.B.call(this)
    }
    ;
    function tl(a, b) {
        var c;
        t: if (Xa(b) != Xa(a.e))
            c = !1;
        else {
            c = ab(b);
            for (var d = 0, e = c.length; d < e; ++d)
                if (!a.e[c[d]]) {
                    c = !1;
                    break t
                }
            c = !0
        }
        c || (rl("Updated online screens: " + R(a.e)),
        a.e = b,
        a.A("screenChange"));
        vl(a)
    }
    function sl(a) {
        isNaN(a.h) || H(a.h);
        a.h = G(v(a.fc, a), 0 < a.j && a.j < w() ? 2E4 : 1E4)
    }
    g.fc = function() {
        H(this.h);
        this.h = NaN;
        this.f && this.f.abort();
        var a = wl(this);
        if (Xa(a)) {
            var b = yk(this.o, "/pairing/get_screen_availability")
              , c = {
                lounge_token: ab(a).join(",")
            };
            this.f = zk(this.o, b, c, v(this.ke, this, a), v(this.je, this))
        } else
            tl(this, {}),
            sl(this)
    }
    ;
    g.ke = function(a, b) {
        this.f = null;
        var c = ab(wl(this));
        if (Sa(c, ab(a))) {
            for (var c = b.screens || [], d = {}, e = 0, f = c.length; e < f; ++e)
                d[a[c[e].loungeToken]] = "online" == c[e].status;
            tl(this, d);
            sl(this)
        } else
            this.L("Changing Screen set during request."),
            this.fc()
    }
    ;
    g.je = function(a) {
        this.L("Screen availability failed: " + a);
        this.f = null;
        sl(this)
    }
    ;
    function rl(a) {
        dk("OnlineScreenService", a)
    }
    g.L = function(a) {
        dk("OnlineScreenService", a)
    }
    ;
    function wl(a) {
        var b = {};
        A(a.l(), function(a) {
            a.token ? b[a.token] = a.id : this.L("Requesting availability of screen w/o lounge token.")
        });
        return b
    }
    function vl(a) {
        var b = ab(Wa(a.e, function(a) {
            return a
        }));
        b.sort(Ua);
        b.length ? S("yt-remote-online-screen-ids", b.join(","), 60) : Zh("yt-remote-online-screen-ids");
        a = Ca(a.l(), function(a) {
            return !!this.e[a.id]
        }, a);
        fl(B(a, kk))
    }
    ;function V(a) {
        Mk.call(this, "ScreenService");
        this.l = a;
        this.e = this.f = null;
        this.h = [];
        this.j = {};
        xl(this)
    }
    y(V, Mk);
    g = V.prototype;
    g.start = function() {
        this.f.start();
        this.e.start();
        this.screens.length && (this.A("screenChange"),
        this.e.isEmpty() || this.A("onlineScreenChange"))
    }
    ;
    g.add = function(a, b, c) {
        this.f.add(a, b, c)
    }
    ;
    g.remove = function(a, b, c) {
        this.f.remove(a, b, c);
        this.e.update()
    }
    ;
    g.Hb = function(a, b, c, d) {
        this.f.contains(a) ? this.f.Hb(a, b, c, d) : (a = "Updating name of unknown screen: " + a.name,
        this.warn(a),
        d(Error(a)))
    }
    ;
    g.X = function(a) {
        return a ? this.screens : Na(this.screens, Ca(this.h, function(a) {
            return !this.contains(a)
        }, this))
    }
    ;
    g.ad = function() {
        return Ca(this.X(!0), function(a) {
            return !!this.e.e[a.id]
        }, this)
    }
    ;
    function yl(a, b, c, d, e, f) {
        a.info("getAutomaticScreenByIds " + c + " / " + b);
        c || (c = a.j[b]);
        var h = a.X();
        if (h = (c ? Lk(h, c) : null) || Lk(h, b)) {
            h.uuid = b;
            var k = zl(a, h);
            ul(a.e, k, function(a) {
                e(a ? k : null)
            })
        } else
            c ? Al(a, c, v(function(a) {
                var f = zl(this, new gk({
                    name: d,
                    screenId: c,
                    loungeToken: a,
                    dialId: b || ""
                }));
                ul(this.e, f, function(a) {
                    e(a ? f : null)
                })
            }, a), f) : e(null)
    }
    g.bd = function(a, b, c, d, e) {
        this.info("getDialScreenByPairingCode " + a + " / " + b);
        var f = new Qk(this.l,a,b,c);
        f.subscribe("pairingComplete", v(function(a) {
            Bb(f);
            d(zl(this, a))
        }, this));
        f.subscribe("pairingFailed", function(a) {
            Bb(f);
            e(a)
        });
        f.start();
        return v(f.stop, f)
    }
    ;
    function Bl(a, b) {
        for (var c = 0, d = a.screens.length; c < d; ++c)
            if (a.screens[c].name == b)
                return a.screens[c];
        return null
    }
    g.yc = function(a, b) {
        for (var c = 2, d = b(a, c); Bl(this, d); ) {
            c++;
            if (20 < c)
                return a;
            d = b(a, c)
        }
        return d
    }
    ;
    g.Ve = function(a, b, c, d) {
        nj(yk(this.l, "/pairing/get_screen"), {
            method: "POST",
            O: {
                pairing_code: a
            },
            timeout: 5E3,
            Z: v(function(a, d) {
                var h = new gk(d.screen || {});
                if (!h.name || Bl(this, h.name))
                    h.name = this.yc(h.name, b);
                c(zl(this, h))
            }, this),
            onError: v(function(a) {
                d(Error("pairing request failed: " + a.status))
            }, this),
            Ab: v(function() {
                d(Error("pairing request timed out."))
            }, this)
        })
    }
    ;
    g.B = function() {
        Bb(this.f);
        Bb(this.e);
        V.G.B.call(this)
    }
    ;
    function Al(a, b, c, d) {
        a.info("requestLoungeToken_ for " + b);
        var e = {
            O: {
                screen_ids: b
            },
            method: "POST",
            context: a,
            Z: function(a, e) {
                var k = e && e.screens || [];
                k[0] && k[0].screenId == b ? c(k[0].loungeToken) : d(Error("Missing lounge token in token response"))
            },
            onError: function() {
                d(Error("Request screen lounge token failed"))
            }
        };
        nj(yk(a.l, "/pairing/get_lounge_token_batch"), e)
    }
    function Cl(a) {
        a.screens = a.f.X();
        var b = a.j, c = {}, d;
        for (d in b)
            c[b[d]] = d;
        b = 0;
        for (d = a.screens.length; b < d; ++b) {
            var e = a.screens[b];
            e.uuid = c[e.id] || ""
        }
        a.info("Updated manual screens: " + ok(a.screens))
    }
    g.Kd = function() {
        Cl(this);
        this.A("screenChange");
        this.e.update()
    }
    ;
    function xl(a) {
        Dl(a);
        a.f = new ml(a.l);
        a.f.subscribe("screenChange", v(a.Kd, a));
        Cl(a);
        a.h = mk(T("yt-remote-automatic-screen-cache") || []);
        Dl(a);
        a.info("Initializing automatic screens: " + ok(a.h));
        a.e = new ql(a.l,v(a.X, a, !0));
        a.e.subscribe("screenChange", v(function() {
            this.A("onlineScreenChange")
        }, a))
    }
    function zl(a, b) {
        var c = a.get(b.id);
        c ? (c.uuid = b.uuid,
        b = c) : ((c = Lk(a.h, b.uuid)) ? (c.id = b.id,
        c.token = b.token,
        b = c) : a.h.push(b),
        S("yt-remote-automatic-screen-cache", B(a.h, kk)));
        Dl(a);
        a.j[b.uuid] = b.id;
        S("yt-remote-device-id-map", a.j, 31536E3);
        return b
    }
    function Dl(a) {
        a.j = T("yt-remote-device-id-map") || {}
    }
    V.prototype.dispose = V.prototype.dispose;
    function El(a, b, c) {
        U.call(this);
        this.S = c;
        this.I = a;
        this.f = b;
        this.h = null
    }
    y(El, U);
    function Fl(a, b) {
        a.h = b;
        a.A("sessionScreen", a.h)
    }
    g = El.prototype;
    g.V = function(a) {
        this.F() || (a && this.warn("" + a),
        this.h = null,
        this.A("sessionScreen", null))
    }
    ;
    g.info = function(a) {
        dk(this.S, a)
    }
    ;
    g.warn = function(a) {
        dk(this.S, a)
    }
    ;
    g.ed = function() {
        return null
    }
    ;
    g.hc = function(a) {
        var b = this.f;
        a ? (b.displayStatus = new chrome.cast.ReceiverDisplayStatus(a,[]),
        b.displayStatus.showStop = !0) : b.displayStatus = null;
        chrome.cast.setReceiverDisplayStatus(b, v(function() {
            this.info("Updated receiver status for " + b.friendlyName + ": " + a)
        }, this), v(function() {
            this.warn("Failed to update receiver status for: " + b.friendlyName)
        }, this))
    }
    ;
    g.B = function() {
        this.hc("");
        El.G.B.call(this)
    }
    ;
    function Gl(a, b) {
        El.call(this, a, b, "CastSession");
        this.e = null;
        this.l = 0;
        this.j = null;
        this.C = v(this.We, this);
        this.o = v(this.te, this);
        this.l = G(v(function() {
            Hl(this, null)
        }, this), 12E4)
    }
    y(Gl, El);
    g = Gl.prototype;
    g.gc = function(a) {
        if (this.e) {
            if (this.e == a)
                return;
            this.warn("Overriding cast sesison with new session object");
            this.e.removeUpdateListener(this.C);
            this.e.removeMessageListener("urn:x-cast:com.google.youtube.mdx", this.o)
        }
        this.e = a;
        this.e.addUpdateListener(this.C);
        this.e.addMessageListener("urn:x-cast:com.google.youtube.mdx", this.o);
        this.j && Il(this);
        Jl(this, "getMdxSessionStatus")
    }
    ;
    g.Va = function(a) {
        this.info("launchWithParams: " + R(a));
        this.j = a;
        this.e && Il(this)
    }
    ;
    g.stop = function() {
        this.e ? this.e.stop(v(function() {
            this.V()
        }, this), v(function() {
            this.V(Error("Failed to stop receiver app."))
        }, this)) : this.V(Error("Stopping cast device witout session."))
    }
    ;
    g.hc = t;
    g.B = function() {
        this.info("disposeInternal");
        H(this.l);
        this.l = 0;
        this.e && (this.e.removeUpdateListener(this.C),
        this.e.removeMessageListener("urn:x-cast:com.google.youtube.mdx", this.o));
        this.e = null;
        Gl.G.B.call(this)
    }
    ;
    function Il(a) {
        var b = a.j.videoId || a.j.videoIds[a.j.index];
        b && Jl(a, "flingVideo", {
            videoId: b,
            currentTime: a.j.currentTime || 0
        });
        a.j = null
    }
    function Jl(a, b, c) {
        a.info("sendYoutubeMessage_: " + b + " " + R(c));
        var d = {};
        d.type = b;
        c && (d.data = c);
        a.e ? a.e.sendMessage("urn:x-cast:com.google.youtube.mdx", d, t, v(function() {
            this.warn("Failed to send message: " + b + ".")
        }, a)) : a.warn("Sending yt message without session: " + R(d))
    }
    g.te = function(a, b) {
        if (!this.F())
            if (b) {
                var c = Gh(b);
                if (c) {
                    var d = "" + c.type
                      , c = c.data || {};
                    this.info("onYoutubeMessage_: " + d + " " + R(c));
                    switch (d) {
                    case "mdxSessionStatus":
                        Hl(this, c.screenId);
                        break;
                    default:
                        this.warn("Unknown youtube message: " + d)
                    }
                } else
                    this.warn("Unable to parse message.")
            } else
                this.warn("No data in message.")
    }
    ;
    function Hl(a, b) {
        H(a.l);
        b ? (a.info("onConnectedScreenId_: Received screenId: " + b),
        a.h && a.h.id == b || yl(a.I, a.f.label, b, a.f.friendlyName, v(function(a) {
            a ? Fl(this, a) : this.V(Error("Unable to fetch screen."))
        }, a), v(a.V, a))) : a.V(Error("Waiting for session status timed out."))
    }
    g.ed = function() {
        return this.e
    }
    ;
    g.We = function(a) {
        this.F() || a || (this.warn("Cast session died."),
        this.V())
    }
    ;
    function Kl(a, b) {
        El.call(this, a, b, "DialSession");
        this.l = this.H = null;
        this.N = "";
        this.j = null;
        this.C = t;
        this.o = NaN;
        this.Q = v(this.Ye, this);
        this.e = t
    }
    y(Kl, El);
    g = Kl.prototype;
    g.gc = function(a) {
        this.l = a;
        this.l.addUpdateListener(this.Q)
    }
    ;
    g.Va = function(a) {
        this.j = a;
        this.C()
    }
    ;
    g.stop = function() {
        this.e();
        this.e = t;
        H(this.o);
        this.l ? this.l.stop(v(this.V, this, null), v(this.V, this, "Failed to stop DIAL device.")) : this.V()
    }
    ;
    g.B = function() {
        this.e();
        this.e = t;
        H(this.o);
        this.l && this.l.removeUpdateListener(this.Q);
        this.l = null;
        Kl.G.B.call(this)
    }
    ;
    function Ll(a) {
        a.e = a.I.bd(a.N, a.f.label, a.f.friendlyName, v(function(a) {
            this.e = t;
            Fl(this, a)
        }, a), v(function(a) {
            this.e = t;
            this.V(a)
        }, a))
    }
    g.Ye = function(a) {
        this.F() || a || (this.warn("DIAL session died."),
        this.e(),
        this.e = t,
        this.V())
    }
    ;
    function Ml(a) {
        var b = {};
        b.pairingCode = a.N;
        if (a.j) {
            var c = a.j.index || 0
              , d = a.j.currentTime || 0;
            b.v = a.j.videoId || a.j.videoIds[c];
            b.t = d
        }
        ll() && (b.env_useStageMdx = 1);
        return Ad(b)
    }
    g.ac = function(a) {
        this.N = Dk();
        if (this.j) {
            var b = new chrome.cast.DialLaunchResponse(!0,Ml(this));
            a(b);
            Ll(this)
        } else
            this.C = v(function() {
                H(this.o);
                this.C = t;
                this.o = NaN;
                var b = new chrome.cast.DialLaunchResponse(!0,Ml(this));
                a(b);
                Ll(this)
            }, this),
            this.o = G(v(function() {
                this.C()
            }, this), 100)
    }
    ;
    g.Ld = function(a, b) {
        yl(this.I, this.H.receiver.label, a, this.f.friendlyName, v(function(a) {
            a && a.token ? (Fl(this, a),
            b(new chrome.cast.DialLaunchResponse(!1))) : this.ac(b)
        }, this), v(function(a) {
            this.warn("Failed to get DIAL screen: " + a);
            this.ac(b)
        }, this))
    }
    ;
    function Nl(a, b) {
        El.call(this, a, b, "ManualSession");
        this.e = G(v(this.Va, this, null), 150)
    }
    y(Nl, El);
    Nl.prototype.stop = function() {
        this.V()
    }
    ;
    Nl.prototype.gc = t;
    Nl.prototype.Va = function() {
        H(this.e);
        this.e = NaN;
        var a = Lk(this.I.X(), this.f.label);
        a ? Fl(this, a) : this.V(Error("No such screen"))
    }
    ;
    Nl.prototype.B = function() {
        H(this.e);
        this.e = NaN;
        Nl.G.B.call(this)
    }
    ;
    function Ol(a) {
        U.call(this);
        this.f = a;
        this.e = null;
        this.l = !1;
        this.h = [];
        this.j = v(this.he, this)
    }
    y(Ol, U);
    g = Ol.prototype;
    g.init = function(a, b) {
        chrome.cast.timeout.requestSession = 3E4;
        var c = new chrome.cast.SessionRequest("233637DE");
        c.dialRequest = new chrome.cast.DialRequest("YouTube");
        var d = chrome.cast.AutoJoinPolicy.TAB_AND_ORIGIN_SCOPED
          , e = a ? chrome.cast.DefaultActionPolicy.CAST_THIS_TAB : chrome.cast.DefaultActionPolicy.CREATE_SESSION
          , c = new chrome.cast.ApiConfig(c,v(this.Kc, this),v(this.ie, this),d,e);
        c.customDialLaunchCallback = v(this.Ud, this);
        chrome.cast.initialize(c, v(function() {
            this.F() || (chrome.cast.addReceiverActionListener(this.j),
            Zj(Pl),
            this.f.subscribe("onlineScreenChange", v(this.cd, this)),
            this.h = Ql(this),
            chrome.cast.setCustomReceivers(this.h, t, v(function(a) {
                this.L("Failed to set initial custom receivers: " + R(a))
            }, this)),
            this.A("yt-remote-cast2-availability-change", Rl(this)),
            b(!0))
        }, this), function(a) {
            this.L("Failed to initialize API: " + R(a));
            b(!1)
        })
    }
    ;
    g.Ge = function(a, b) {
        Sl("Setting connected screen ID: " + a + " -> " + b);
        if (this.e) {
            var c = this.e.h;
            if (!a || c && c.id != a)
                Sl("Unsetting old screen status: " + this.e.f.friendlyName),
                Bb(this.e),
                this.e = null
        }
        if (a && b) {
            if (!this.e) {
                c = Lk(this.f.X(), a);
                if (!c) {
                    Sl("setConnectedScreenStatus: Unknown screen.");
                    return
                }
                var d = Tl(this, c);
                d || (Sl("setConnectedScreenStatus: Connected receiver not custom..."),
                d = new chrome.cast.Receiver(c.uuid ? c.uuid : c.id,c.name),
                d.receiverType = chrome.cast.ReceiverType.CUSTOM,
                this.h.push(d),
                chrome.cast.setCustomReceivers(this.h, t, v(function(a) {
                    this.L("Failed to set initial custom receivers: " + R(a))
                }, this)));
                Sl("setConnectedScreenStatus: new active receiver: " + d.friendlyName);
                Ul(this, new Nl(this.f,d), !0)
            }
            this.e.hc(b)
        } else
            Sl("setConnectedScreenStatus: no screen.")
    }
    ;
    function Tl(a, b) {
        return b ? Fa(a.h, function(a) {
            return hk(b, a.label)
        }, a) : null
    }
    g.He = function(a) {
        this.F() ? this.L("Setting connection data on disposed cast v2") : this.e ? this.e.Va(a) : this.L("Setting connection data without a session")
    }
    ;
    g.stopSession = function() {
        this.F() ? this.L("Stopping session on disposed cast v2") : this.e ? (this.e.stop(),
        Bb(this.e),
        this.e = null) : Sl("Stopping non-existing session")
    }
    ;
    g.requestSession = function() {
        chrome.cast.requestSession(v(this.Kc, this), v(this.le, this))
    }
    ;
    g.B = function() {
        this.f.Na("onlineScreenChange", v(this.cd, this));
        window.chrome && chrome.cast && chrome.cast.removeReceiverActionListener(this.j);
        ck(Pl);
        Bb(this.e);
        Ol.G.B.call(this)
    }
    ;
    function Sl(a) {
        dk("Controller", a)
    }
    g.L = function(a) {
        dk("Controller", a)
    }
    ;
    function Pl(a) {
        window.chrome && chrome.cast && chrome.cast.logMessage && chrome.cast.logMessage(a)
    }
    function Rl(a) {
        return a.l || !!a.h.length || !!a.e
    }
    function Ul(a, b, c) {
        Bb(a.e);
        (a.e = b) ? (c ? a.A("yt-remote-cast2-receiver-resumed", b.f) : a.A("yt-remote-cast2-receiver-selected", b.f),
        b.subscribe("sessionScreen", v(a.Lc, a, b)),
        b.h ? a.A("yt-remote-cast2-session-change", b.h) : c && a.e.Va(null)) : a.A("yt-remote-cast2-session-change", null)
    }
    g.Lc = function(a, b) {
        this.e == a && (b || Ul(this, null),
        this.A("yt-remote-cast2-session-change", b))
    }
    ;
    g.he = function(a, b) {
        if (!this.F())
            if (a)
                switch (Sl("onReceiverAction_ " + a.label + " / " + a.friendlyName + "-- " + b),
                b) {
                case chrome.cast.ReceiverAction.CAST:
                    if (this.e)
                        if (this.e.f.label != a.label)
                            Sl("onReceiverAction_: Stopping active receiver: " + this.e.f.friendlyName),
                            this.e.stop();
                        else {
                            Sl("onReceiverAction_: Casting to active receiver.");
                            this.e.h && this.A("yt-remote-cast2-session-change", this.e.h);
                            break
                        }
                    switch (a.receiverType) {
                    case chrome.cast.ReceiverType.CUSTOM:
                        Ul(this, new Nl(this.f,a));
                        break;
                    case chrome.cast.ReceiverType.DIAL:
                        Ul(this, new Kl(this.f,a));
                        break;
                    case chrome.cast.ReceiverType.CAST:
                        Ul(this, new Gl(this.f,a));
                        break;
                    default:
                        this.L("Unknown receiver type: " + a.receiverType);
                        return
                    }
                    break;
                case chrome.cast.ReceiverAction.STOP:
                    this.e && this.e.f.label == a.label ? this.e.stop() : this.L("Stopping receiver w/o session: " + a.friendlyName)
                }
            else
                this.L("onReceiverAction_ called without receiver.")
    }
    ;
    g.Ud = function(a) {
        if (this.F())
            return Promise.reject(Error("disposed"));
        var b = a.receiver;
        b.receiverType != chrome.cast.ReceiverType.DIAL && (this.L("Not DIAL receiver: " + b.friendlyName),
        b.receiverType = chrome.cast.ReceiverType.DIAL);
        var c = this.e ? this.e.f : null;
        if (!c || c.label != b.label)
            return this.L("Receiving DIAL launch request for non-clicked DIAL receiver: " + b.friendlyName),
            Promise.reject(Error("illegal DIAL launch"));
        if (c && c.label == b.label && c.receiverType != chrome.cast.ReceiverType.DIAL) {
            if (this.e.h)
                return Sl("Reselecting dial screen."),
                this.A("yt-remote-cast2-session-change", this.e.h),
                Promise.resolve(new chrome.cast.DialLaunchResponse(!1));
            this.L('Changing CAST intent from "' + c.receiverType + '" to "dial" for ' + b.friendlyName);
            Ul(this, new Kl(this.f,b))
        }
        b = this.e;
        b.H = a;
        return b.H.appState == chrome.cast.DialAppState.RUNNING ? new Promise(v(b.Ld, b, (b.H.extraData || {}).screenId || null)) : new Promise(v(b.ac, b))
    }
    ;
    g.Kc = function(a) {
        if (!this.F()) {
            Sl("New cast session ID: " + a.sessionId);
            var b = a.receiver;
            if (b.receiverType != chrome.cast.ReceiverType.CUSTOM) {
                if (!this.e)
                    if (b.receiverType == chrome.cast.ReceiverType.CAST)
                        Sl("Got resumed cast session before resumed mdx connection."),
                        Ul(this, new Gl(this.f,b), !0);
                    else {
                        this.L("Got non-cast session without previous mdx receiver event, or mdx resume.");
                        return
                    }
                var c = this.e.f
                  , d = Lk(this.f.X(), c.label);
                d && hk(d, b.label) && c.receiverType != chrome.cast.ReceiverType.CAST && b.receiverType == chrome.cast.ReceiverType.CAST && (Sl("onSessionEstablished_: manual to cast session change " + b.friendlyName),
                Bb(this.e),
                this.e = new Gl(this.f,b),
                this.e.subscribe("sessionScreen", v(this.Lc, this, this.e)),
                this.e.Va(null));
                this.e.gc(a)
            }
        }
    }
    ;
    g.Xe = function() {
        return this.e ? this.e.ed() : null
    }
    ;
    g.le = function(a) {
        this.F() || (this.L("Failed to estabilish a session: " + R(a)),
        a.code != chrome.cast.ErrorCode.CANCEL && Ul(this, null))
    }
    ;
    g.ie = function(a) {
        Sl("Receiver availability updated: " + a);
        if (!this.F()) {
            var b = Rl(this);
            this.l = a == chrome.cast.ReceiverAvailability.AVAILABLE;
            Rl(this) != b && this.A("yt-remote-cast2-availability-change", Rl(this))
        }
    }
    ;
    function Ql(a) {
        var b = a.f.ad()
          , c = a.e && a.e.f;
        a = B(b, function(a) {
            c && hk(a, c.label) && (c = null);
            var b = a.uuid ? a.uuid : a.id
              , f = Tl(this, a);
            f ? (f.label = b,
            f.friendlyName = a.name) : (f = new chrome.cast.Receiver(b,a.name),
            f.receiverType = chrome.cast.ReceiverType.CUSTOM);
            return f
        }, a);
        c && (c.receiverType != chrome.cast.ReceiverType.CUSTOM && (c = new chrome.cast.Receiver(c.label,c.friendlyName),
        c.receiverType = chrome.cast.ReceiverType.CUSTOM),
        a.push(c));
        return a
    }
    g.cd = function() {
        if (!this.F()) {
            var a = Rl(this);
            this.h = Ql(this);
            Sl("Updating custom receivers: " + R(this.h));
            chrome.cast.setCustomReceivers(this.h, t, v(function() {
                this.L("Failed to set custom receivers.")
            }, this));
            var b = Rl(this);
            b != a && this.A("yt-remote-cast2-availability-change", b)
        }
    }
    ;
    Ol.prototype.setLaunchParams = Ol.prototype.He;
    Ol.prototype.setConnectedScreenStatus = Ol.prototype.Ge;
    Ol.prototype.stopSession = Ol.prototype.stopSession;
    Ol.prototype.getCastSession = Ol.prototype.Xe;
    Ol.prototype.requestSession = Ol.prototype.requestSession;
    Ol.prototype.init = Ol.prototype.init;
    Ol.prototype.dispose = Ol.prototype.dispose;
    function Vl(a, b, c) {
        Wl() ? Yl(a) && (Zl(!0),
        window.chrome && chrome.cast && chrome.cast.isAvailable ? $l(b) : c ? (window.__onGCastApiAvailable = function(a, c) {
            am(b, a, c)
        }
        ,
        Xb("https://www.gstatic.com/cv/js/sender/v1/cast_sender.js")) : tk(function(a, c) {
            am(b, a, c)
        })) : Xl("Cannot initialize because not running Chrome")
    }
    function bm() {
        Xl("dispose");
        vk();
        var a = cm();
        a && a.dispose();
        dm = null;
        q("yt.mdx.remote.cloudview.instance_", null, void 0);
        em(!1);
        Tb(fm);
        fm.length = 0
    }
    function gm() {
        return !!T("yt-remote-cast-installed")
    }
    function hm() {
        var a = T("yt-remote-cast-receiver");
        return a ? a.friendlyName : null
    }
    function im() {
        Xl("clearCurrentReciever");
        Zh("yt-remote-cast-receiver")
    }
    function jm() {
        gm() ? cm() ? km() ? (Xl("Requesting cast selector."),
        dm.requestSession()) : (Xl("Wait for cast API to be ready to request the session."),
        fm.push(Rb("yt-remote-cast2-api-ready", jm))) : lm("requestCastSelector: Cast is not initialized.") : lm("requestCastSelector: Cast API is not installed!")
    }
    function mm(a) {
        km() ? cm().setLaunchParams(a) : lm("setLaunchParams called before ready.")
    }
    function nm(a, b) {
        km() ? cm().setConnectedScreenStatus(a, b) : lm("setConnectedScreenStatus called before ready.")
    }
    var dm = null;
    function Wl() {
        var a;
        a = 0 <= oc.search(/\ (CrMo|Chrome|CriOS)\//);
        return Ye || a
    }
    function am(a, b, c) {
        b ? $l(a) : (lm("Failed to load cast API: " + c),
        om(!1),
        Zl(!1),
        Zh("yt-remote-cast-available"),
        Zh("yt-remote-cast-receiver"),
        bm(),
        a(!1))
    }
    function pm(a) {
        dm.init(!0, a)
    }
    function Yl(a) {
        var b = !1;
        if (!dm) {
            var c = r("yt.mdx.remote.cloudview.instance_");
            c || (c = new Ol(a),
            c.subscribe("yt-remote-cast2-availability-change", function(a) {
                S("yt-remote-cast-available", a);
                I("yt-remote-cast2-availability-change", a)
            }),
            c.subscribe("yt-remote-cast2-receiver-selected", function(a) {
                Xl("onReceiverSelected: " + a.friendlyName);
                S("yt-remote-cast-receiver", a);
                I("yt-remote-cast2-receiver-selected", a)
            }),
            c.subscribe("yt-remote-cast2-receiver-resumed", function(a) {
                Xl("onReceiverResumed: " + a.friendlyName);
                S("yt-remote-cast-receiver", a)
            }),
            c.subscribe("yt-remote-cast2-session-change", function(a) {
                Xl("onSessionChange: " + nk(a));
                a || Zh("yt-remote-cast-receiver");
                I("yt-remote-cast2-session-change", a)
            }),
            q("yt.mdx.remote.cloudview.instance_", c, void 0),
            b = !0);
            dm = c
        }
        Xl("cloudview.createSingleton_: " + b);
        return b
    }
    function cm() {
        dm || (dm = r("yt.mdx.remote.cloudview.instance_"));
        return dm
    }
    function $l(a) {
        om(!0);
        Zl(!1);
        pm(function(b) {
            b ? (em(!0),
            I("yt-remote-cast2-api-ready")) : (lm("Failed to initialize cast API."),
            om(!1),
            Zh("yt-remote-cast-available"),
            Zh("yt-remote-cast-receiver"),
            bm());
            a(b)
        })
    }
    function Xl(a) {
        dk("cloudview", a)
    }
    function lm(a) {
        dk("cloudview", a)
    }
    function om(a) {
        Xl("setCastInstalled_ " + a);
        S("yt-remote-cast-installed", a)
    }
    function km() {
        return !!r("yt.mdx.remote.cloudview.apiReady_")
    }
    function em(a) {
        Xl("setApiReady_ " + a);
        q("yt.mdx.remote.cloudview.apiReady_", a, void 0)
    }
    function Zl(a) {
        q("yt.mdx.remote.cloudview.initializing_", a, void 0)
    }
    var fm = [];
    function qm() {
        if (!("cast"in window))
            return !1;
        var a = window.cast || {};
        return "ActivityStatus"in a && "Api"in a && "LaunchRequest"in a && "Receiver"in a
    }
    function rm(a) {
        dk("CAST", a)
    }
    function sm(a) {
        var b = tm();
        b && b.logMessage && b.logMessage(a)
    }
    function um(a) {
        if (a.source == window && a.data && "CastApi" == a.data.source && "Hello" == a.data.event)
            for (; vm.length; )
                vm.shift()()
    }
    function wm() {
        if (!r("yt.mdx.remote.castv2_") && !xm && (0 == Ja.length && Pa(Ja, il()),
        qm())) {
            var a = tm();
            a ? (a.removeReceiverListener("YouTube", ym),
            a.addReceiverListener("YouTube", ym),
            rm("API initialized in the other binary")) : (a = new cast.Api,
            zm(a),
            a.addReceiverListener("YouTube", ym),
            a.setReloadTabRequestHandler && a.setReloadTabRequestHandler(function() {
                G(function() {
                    window.location.reload(!0)
                }, 1E3)
            }),
            Zj(sm),
            rm("API initialized"));
            xm = !0
        }
    }
    function Am() {
        var a = tm();
        a && (rm("API disposed"),
        ck(sm),
        a.setReloadTabRequestHandler && a.setReloadTabRequestHandler(t),
        a.removeReceiverListener("YouTube", ym),
        zm(null));
        xm = !1;
        vm = null;
        (a = fd(window, "message", um, !1)) && gd(a)
    }
    function Bm(a) {
        var b = Ga(Ja, function(b) {
            return b.id == a.id
        });
        0 <= b && (Ja[b] = Bk(a))
    }
    function ym(a) {
        a.length && rm("Updating receivers: " + R(a));
        Cm(a);
        I("yt-remote-cast-device-list-update");
        A(Dm(), function(a) {
            Em(a.id)
        });
        A(a, function(a) {
            if (a.isTabProjected) {
                var c = Fm(a.id);
                rm("Detected device: " + c.id + " is tab projected. Firing DEVICE_TAB_PROJECTED event.");
                G(function() {
                    I("yt-remote-cast-device-tab-projected", c.id)
                }, 1E3)
            }
        })
    }
    function Gm(a, b) {
        rm("Updating " + a + " activity status: " + R(b));
        var c = Fm(a);
        c ? (b.activityId && (c.activityId = b.activityId),
        c.status = "running" == b.status ? "RUNNING" : "stopped" == b.status ? "STOPPED" : "error" == b.status ? "ERROR" : "UNKNOWN",
        "RUNNING" != c.status && (c.activityId = ""),
        Bm(c),
        I("yt-remote-cast-device-status-update", c)) : rm("Device not found")
    }
    function Dm() {
        wm();
        return Hk(Ja)
    }
    function Cm(a) {
        a = B(a, function(a) {
            var c = a.id, d;
            d = a.name;
            d = -1 != d.indexOf("&") ? "document"in m ? ua(d) : wa(d) : d;
            c = {
                id: c,
                name: d
            };
            if (a = Fm(a.id))
                c.activityId = a.activityId,
                c.status = a.status;
            return c
        });
        Ia();
        Pa(Ja, a)
    }
    function Fm(a) {
        var b = Dm();
        return Fa(b, function(b) {
            return b.id == a
        }) || null
    }
    function Em(a) {
        var b = Fm(a)
          , c = tm();
        c && b && b.activityId && c.getActivityStatus(b.activityId, function(b) {
            "error" == b.status && (b.status = "stopped");
            Gm(a, b)
        })
    }
    function Hm(a) {
        wm();
        var b = Fm(a)
          , c = tm();
        c && b && b.activityId ? (rm("Stopping cast activity"),
        c.stopActivity(b.activityId, oa(Gm, a))) : rm("Dropping cast activity stop")
    }
    function tm() {
        return r("yt.mdx.remote.castapi.api_")
    }
    function zm(a) {
        q("yt.mdx.remote.castapi.api_", a, void 0)
    }
    var xm = !1
      , vm = null
      , Ja = r("yt.mdx.remote.castapi.devices_") || [];
    q("yt.mdx.remote.castapi.devices_", Ja, void 0);
    function Im(a, b) {
        this.action = a;
        this.params = b || null
    }
    ;function Jm() {
        this.e = w()
    }
    new Jm;
    Jm.prototype.reset = function() {
        this.e = w()
    }
    ;
    Jm.prototype.get = function() {
        return this.e
    }
    ;
    function Km() {
        D.call(this);
        this.xa = new Nf(this);
        this.Qa = this;
        this.ma = null
    }
    y(Km, D);
    Km.prototype[Jf] = !0;
    g = Km.prototype;
    g.addEventListener = function(a, b, c, d) {
        Uf(this, a, b, c, d)
    }
    ;
    g.removeEventListener = function(a, b, c, d) {
        $f(this, a, b, c, d)
    }
    ;
    function Lm(a, b) {
        var c, d = a.ma;
        if (d) {
            c = [];
            for (var e = 1; d; d = d.ma)
                c.push(d),
                ++e
        }
        var d = a.Qa
          , e = b
          , f = e.type || e;
        if (u(e))
            e = new Hf(e,d);
        else if (e instanceof Hf)
            e.target = e.target || d;
        else {
            var h = e
              , e = new Hf(f,d);
            ib(e, h)
        }
        var h = !0, k;
        if (c)
            for (var l = c.length - 1; 0 <= l; l--)
                k = e.currentTarget = c[l],
                h = Mm(k, f, !0, e) && h;
        k = e.currentTarget = d;
        h = Mm(k, f, !0, e) && h;
        h = Mm(k, f, !1, e) && h;
        if (c)
            for (l = 0; l < c.length; l++)
                k = e.currentTarget = c[l],
                h = Mm(k, f, !1, e) && h
    }
    g.B = function() {
        Km.G.B.call(this);
        this.removeAllListeners();
        this.ma = null
    }
    ;
    g.xb = function(a, b, c, d) {
        return this.xa.add(String(a), b, !1, c, d)
    }
    ;
    g.ic = function(a, b, c, d) {
        return this.xa.remove(String(a), b, c, d)
    }
    ;
    g.removeAllListeners = function(a) {
        return this.xa ? this.xa.removeAll(a) : 0
    }
    ;
    function Mm(a, b, c, d) {
        b = a.xa.e[String(b)];
        if (!b)
            return !0;
        b = b.concat();
        for (var e = !0, f = 0; f < b.length; ++f) {
            var h = b[f];
            if (h && !h.Xa && h.ob == c) {
                var k = h.ua
                  , l = h.ub || h.src;
                h.nb && Pf(a.xa, h);
                e = !1 !== k.call(l, d) && e
            }
        }
        return e && 0 != d.Uc
    }
    ;function Nm(a, b) {
        this.f = new Ih(a);
        this.e = b ? Gh : Fh
    }
    Nm.prototype.stringify = function(a) {
        return Hh(this.f, a)
    }
    ;
    Nm.prototype.parse = function(a) {
        return this.e(a)
    }
    ;
    function Om(a, b) {
        Km.call(this);
        this.e = a || 1;
        this.f = b || m;
        this.h = v(this.Ke, this);
        this.k = w()
    }
    y(Om, Km);
    g = Om.prototype;
    g.enabled = !1;
    g.ba = null;
    function Pm(a, b) {
        a.e = b;
        a.ba && a.enabled ? (a.stop(),
        a.start()) : a.ba && a.stop()
    }
    g.Ke = function() {
        if (this.enabled) {
            var a = w() - this.k;
            0 < a && a < .8 * this.e ? this.ba = this.f.setTimeout(this.h, this.e - a) : (this.ba && (this.f.clearTimeout(this.ba),
            this.ba = null),
            Lm(this, "tick"),
            this.enabled && (this.ba = this.f.setTimeout(this.h, this.e),
            this.k = w()))
        }
    }
    ;
    g.start = function() {
        this.enabled = !0;
        this.ba || (this.ba = this.f.setTimeout(this.h, this.e),
        this.k = w())
    }
    ;
    g.stop = function() {
        this.enabled = !1;
        this.ba && (this.f.clearTimeout(this.ba),
        this.ba = null)
    }
    ;
    g.B = function() {
        Om.G.B.call(this);
        this.stop();
        delete this.f
    }
    ;
    function Qm(a, b, c) {
        if (ga(a))
            c && (a = v(a, c));
        else if (a && "function" == typeof a.handleEvent)
            a = v(a.handleEvent, a);
        else
            throw Error("Invalid listener argument");
        return 2147483647 < b ? -1 : m.setTimeout(a, b || 0)
    }
    ;function Rm(a, b, c) {
        D.call(this);
        this.k = a;
        this.h = b;
        this.f = c;
        this.e = v(this.ne, this)
    }
    y(Rm, D);
    g = Rm.prototype;
    g.Gb = !1;
    g.Ta = null;
    g.stop = function() {
        this.Ta && (m.clearTimeout(this.Ta),
        this.Ta = null,
        this.Gb = !1)
    }
    ;
    g.B = function() {
        Rm.G.B.call(this);
        this.stop()
    }
    ;
    g.ne = function() {
        this.Ta = null;
        this.Gb && (this.Gb = !1,
        Sm(this))
    }
    ;
    function Sm(a) {
        a.Ta = Qm(a.e, a.h);
        a.k.call(a.f)
    }
    ;function Tm(a) {
        D.call(this);
        this.f = a;
        this.e = {}
    }
    y(Tm, D);
    var Um = [];
    g = Tm.prototype;
    g.xb = function(a, b, c, d) {
        da(b) || (b && (Um[0] = b.toString()),
        b = Um);
        for (var e = 0; e < b.length; e++) {
            var f = Uf(a, b[e], c || this.handleEvent, d || !1, this.f || this);
            if (!f)
                break;
            this.e[f.key] = f
        }
        return this
    }
    ;
    g.ic = function(a, b, c, d, e) {
        if (da(b))
            for (var f = 0; f < b.length; f++)
                this.ic(a, b[f], c, d, e);
        else
            c = c || this.handleEvent,
            e = e || this.f || this,
            c = Vf(c),
            d = !!d,
            b = a && a[Jf] ? Qf(a.xa, String(b), c, d, e) : a ? (a = Wf(a)) ? Qf(a, b, c, d, e) : null : null,
            b && (ag(b),
            delete this.e[b.key]);
        return this
    }
    ;
    g.removeAll = function() {
        Va(this.e, ag);
        this.e = {}
    }
    ;
    g.B = function() {
        Tm.G.B.call(this);
        this.removeAll()
    }
    ;
    g.handleEvent = function() {
        throw Error("EventHandler.handleEvent not implemented");
    }
    ;
    function Vm() {}
    Vm.prototype.e = null;
    function Wm(a) {
        var b;
        (b = a.e) || (b = {},
        Xm(a) && (b[0] = !0,
        b[1] = !0),
        b = a.e = b);
        return b
    }
    ;var Ym;
    function Zm() {}
    y(Zm, Vm);
    function $m(a) {
        return (a = Xm(a)) ? new ActiveXObject(a) : new XMLHttpRequest
    }
    function Xm(a) {
        if (!a.f && "undefined" == typeof XMLHttpRequest && "undefined" != typeof ActiveXObject) {
            for (var b = ["MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"], c = 0; c < b.length; c++) {
                var d = b[c];
                try {
                    return new ActiveXObject(d),
                    a.f = d
                } catch (e) {}
            }
            throw Error("Could not create ActiveXObject. ActiveX might be disabled, or MSXML might not be installed");
        }
        return a.f
    }
    Ym = new Zm;
    function an(a, b, c, d, e) {
        this.e = a;
        this.h = c;
        this.C = d;
        this.o = e || 1;
        this.j = 45E3;
        this.k = new Tm(this);
        this.f = new Om;
        Pm(this.f, 250)
    }
    g = an.prototype;
    g.Ja = null;
    g.ia = !1;
    g.$a = null;
    g.kc = null;
    g.jb = null;
    g.Za = null;
    g.va = null;
    g.za = null;
    g.Ma = null;
    g.M = null;
    g.lb = 0;
    g.ja = null;
    g.Jb = null;
    g.Ka = null;
    g.hb = -1;
    g.Vc = !0;
    g.Ea = !1;
    g.Zb = 0;
    g.Db = null;
    var bn = {}
      , cn = {};
    g = an.prototype;
    g.setTimeout = function(a) {
        this.j = a
    }
    ;
    function dn(a, b, c) {
        a.Za = 1;
        a.va = Sg(b.clone());
        a.Ma = c;
        a.l = !0;
        en(a, null)
    }
    function fn(a, b, c, d, e) {
        a.Za = 1;
        a.va = Sg(b.clone());
        a.Ma = null;
        a.l = c;
        e && (a.Vc = !1);
        en(a, d)
    }
    function en(a, b) {
        a.jb = w();
        gn(a);
        a.za = a.va.clone();
        Qg(a.za, "t", a.o);
        a.lb = 0;
        a.M = a.e.Rb(a.e.kb() ? b : null);
        0 < a.Zb && (a.Db = new Rm(v(a.$c, a, a.M),a.Zb));
        a.k.xb(a.M, "readystatechange", a.ve);
        var c = a.Ja ? fb(a.Ja) : {};
        a.Ma ? (a.Jb = "POST",
        c["Content-Type"] = "application/x-www-form-urlencoded",
        a.M.send(a.za, a.Jb, a.Ma, c)) : (a.Jb = "GET",
        a.Vc && !yc && (c.Connection = "close"),
        a.M.send(a.za, a.Jb, null, c));
        a.e.ha(1)
    }
    g.ve = function(a) {
        a = a.target;
        var b = this.Db;
        b && 3 == hn(a) ? b.Ta ? b.Gb = !0 : Sm(b) : this.$c(a)
    }
    ;
    g.$c = function(a) {
        try {
            if (a == this.M)
                t: {
                    var b = hn(this.M)
                      , c = this.M.k
                      , d = this.M.getStatus();
                    if (K && !Gc(10) || yc && !Fc("420+")) {
                        if (4 > b)
                            break t
                    } else if (3 > b || 3 == b && !wc && !jn(this.M))
                        break t;
                    this.Ea || 4 != b || 7 == c || (8 == c || 0 >= d ? this.e.ha(3) : this.e.ha(2));
                    kn(this);
                    var e = this.M.getStatus();
                    this.hb = e;
                    var f = jn(this.M);
                    (this.ia = 200 == e) ? (4 == b && ln(this),
                    this.l ? (mn(this, b, f),
                    wc && this.ia && 3 == b && (this.k.xb(this.f, "tick", this.ue),
                    this.f.start())) : nn(this, f),
                    this.ia && !this.Ea && (4 == b ? this.e.zb(this) : (this.ia = !1,
                    gn(this)))) : (this.Ka = 400 == e && 0 < f.indexOf("Unknown SID") ? 3 : 0,
                    W(),
                    ln(this),
                    on(this))
                }
        } catch (h) {
            this.M && jn(this.M)
        } finally {}
    }
    ;
    function mn(a, b, c) {
        for (var d = !0; !a.Ea && a.lb < c.length; ) {
            var e = pn(a, c);
            if (e == cn) {
                4 == b && (a.Ka = 4,
                W(),
                d = !1);
                break
            } else if (e == bn) {
                a.Ka = 4;
                W();
                d = !1;
                break
            } else
                nn(a, e)
        }
        4 == b && 0 == c.length && (a.Ka = 1,
        W(),
        d = !1);
        a.ia = a.ia && d;
        d || (ln(a),
        on(a))
    }
    g.ue = function() {
        var a = hn(this.M)
          , b = jn(this.M);
        this.lb < b.length && (kn(this),
        mn(this, a, b),
        this.ia && 4 != a && gn(this))
    }
    ;
    function pn(a, b) {
        var c = a.lb
          , d = b.indexOf("\n", c);
        if (-1 == d)
            return cn;
        c = Number(b.substring(c, d));
        if (isNaN(c))
            return bn;
        d += 1;
        if (d + c > b.length)
            return cn;
        var e = b.substr(d, c);
        a.lb = d + c;
        return e
    }
    function qn(a, b) {
        a.jb = w();
        gn(a);
        var c = b ? window.location.hostname : "";
        a.za = a.va.clone();
        M(a.za, "DOMAIN", c);
        M(a.za, "t", a.o);
        try {
            a.ja = new ActiveXObject("htmlfile")
        } catch (d) {
            ln(a);
            a.Ka = 7;
            W();
            on(a);
            return
        }
        var e = "<html><body>";
        b && (e += '<script>document.domain="' + c + '"\x3c/script>');
        e += "</body></html>";
        a.ja.open();
        a.ja.write(e);
        a.ja.close();
        a.ja.parentWindow.m = v(a.qe, a);
        a.ja.parentWindow.d = v(a.Oc, a, !0);
        a.ja.parentWindow.rpcClose = v(a.Oc, a, !1);
        c = a.ja.createElement("div");
        a.ja.parentWindow.document.body.appendChild(c);
        c.innerHTML = '<iframe src="' + a.za + '"></iframe>';
        a.e.ha(1)
    }
    g.qe = function(a) {
        rn(v(this.pe, this, a), 0)
    }
    ;
    g.pe = function(a) {
        this.Ea || (kn(this),
        nn(this, a),
        gn(this))
    }
    ;
    g.Oc = function(a) {
        rn(v(this.oe, this, a), 0)
    }
    ;
    g.oe = function(a) {
        this.Ea || (ln(this),
        this.ia = a,
        this.e.zb(this),
        this.e.ha(4))
    }
    ;
    g.cancel = function() {
        this.Ea = !0;
        ln(this)
    }
    ;
    function gn(a) {
        a.kc = w() + a.j;
        sn(a, a.j)
    }
    function sn(a, b) {
        if (null != a.$a)
            throw Error("WatchDog timer not null");
        a.$a = rn(v(a.se, a), b)
    }
    function kn(a) {
        a.$a && (m.clearTimeout(a.$a),
        a.$a = null)
    }
    g.se = function() {
        this.$a = null;
        var a = w();
        0 <= a - this.kc ? (2 != this.Za && this.e.ha(3),
        ln(this),
        this.Ka = 2,
        W(),
        on(this)) : sn(this, this.kc - a)
    }
    ;
    function on(a) {
        a.e.Ac() || a.Ea || a.e.zb(a)
    }
    function ln(a) {
        kn(a);
        Bb(a.Db);
        a.Db = null;
        a.f.stop();
        a.k.removeAll();
        if (a.M) {
            var b = a.M;
            a.M = null;
            tn(b);
            b.dispose()
        }
        a.ja && (a.ja = null)
    }
    function nn(a, b) {
        try {
            a.e.Jc(a, b),
            a.e.ha(4)
        } catch (c) {}
    }
    ;function un(a, b, c, d, e) {
        if (0 == d)
            c(!1);
        else {
            var f = e || 0;
            d--;
            vn(a, b, function(e) {
                e ? c(!0) : m.setTimeout(function() {
                    un(a, b, c, d, f)
                }, f)
            })
        }
    }
    function vn(a, b, c) {
        var d = new Image;
        d.onload = function() {
            try {
                wn(d),
                c(!0)
            } catch (a) {}
        }
        ;
        d.onerror = function() {
            try {
                wn(d),
                c(!1)
            } catch (a) {}
        }
        ;
        d.onabort = function() {
            try {
                wn(d),
                c(!1)
            } catch (a) {}
        }
        ;
        d.ontimeout = function() {
            try {
                wn(d),
                c(!1)
            } catch (a) {}
        }
        ;
        m.setTimeout(function() {
            if (d.ontimeout)
                d.ontimeout()
        }, b);
        d.src = a
    }
    function wn(a) {
        a.onload = null;
        a.onerror = null;
        a.onabort = null;
        a.ontimeout = null
    }
    ;function xn(a) {
        this.e = a;
        this.f = new Nm(null,!0)
    }
    g = xn.prototype;
    g.Xb = null;
    g.$ = null;
    g.Eb = !1;
    g.Yc = null;
    g.rb = null;
    g.bc = null;
    g.Yb = null;
    g.ca = null;
    g.ta = -1;
    g.gb = null;
    g.ab = null;
    function yn(a) {
        var b = zn(a.e, a.ab, "/mail/images/cleardot.gif");
        Sg(b);
        un(b.toString(), 5E3, v(a.wd, a), 3, 2E3);
        a.ha(1)
    }
    g.wd = function(a) {
        if (a)
            this.ca = 2,
            An(this);
        else {
            W();
            var b = this.e;
            b.fa = b.wa.ta;
            Bn(b, 9)
        }
        a && this.ha(2)
    }
    ;
    function An(a) {
        var b = a.e.H;
        if (null != b)
            W(),
            b ? (W(),
            Cn(a.e, a, !1)) : (W(),
            Cn(a.e, a, !0));
        else if (a.$ = new an(a,0,void 0,void 0,void 0),
        a.$.Ja = a.Xb,
        b = a.e,
        b = zn(b, b.kb() ? a.gb : null, a.Yb),
        W(),
        !K || Gc(10))
            Qg(b, "TYPE", "xmlhttp"),
            fn(a.$, b, !1, a.gb, !1);
        else {
            Qg(b, "TYPE", "html");
            var c = a.$;
            a = Boolean(a.gb);
            c.Za = 3;
            c.va = Sg(b.clone());
            qn(c, a)
        }
    }
    g.Rb = function(a) {
        return this.e.Rb(a)
    }
    ;
    g.Ac = function() {
        return !1
    }
    ;
    g.Jc = function(a, b) {
        this.ta = a.hb;
        if (0 == this.ca)
            if (b) {
                try {
                    var c = this.f.parse(b)
                } catch (d) {
                    c = this.e;
                    c.fa = this.ta;
                    Bn(c, 2);
                    return
                }
                this.gb = c[0];
                this.ab = c[1]
            } else
                c = this.e,
                c.fa = this.ta,
                Bn(c, 2);
        else if (2 == this.ca)
            if (this.Eb)
                W(),
                this.bc = w();
            else if ("11111" == b) {
                if (W(),
                this.Eb = !0,
                this.rb = w(),
                c = this.rb - this.Yc,
                !K || Gc(10) || 500 > c)
                    this.ta = 200,
                    this.$.cancel(),
                    W(),
                    Cn(this.e, this, !0)
            } else
                W(),
                this.rb = this.bc = w(),
                this.Eb = !1
    }
    ;
    g.zb = function() {
        this.ta = this.$.hb;
        if (this.$.ia)
            0 == this.ca ? this.ab ? (this.ca = 1,
            yn(this)) : (this.ca = 2,
            An(this)) : 2 == this.ca && (a = !1,
            (a = !K || Gc(10) ? this.Eb : 200 > this.bc - this.rb ? !1 : !0) ? (W(),
            Cn(this.e, this, !0)) : (W(),
            Cn(this.e, this, !1)));
        else {
            0 == this.ca ? W() : 2 == this.ca && W();
            var a = this.e;
            a.fa = this.ta;
            Bn(a, 2)
        }
    }
    ;
    g.kb = function() {
        return this.e.kb()
    }
    ;
    g.isActive = function() {
        return this.e.isActive()
    }
    ;
    g.ha = function(a) {
        this.e.ha(a)
    }
    ;
    function Dn(a) {
        Km.call(this);
        this.headers = new Pd;
        this.Q = a || null;
        this.f = !1;
        this.N = this.e = null;
        this.oa = this.C = "";
        this.k = 0;
        this.j = "";
        this.h = this.da = this.o = this.S = !1;
        this.l = 0;
        this.H = null;
        this.Ca = "";
        this.I = this.Da = !1
    }
    y(Dn, Km);
    var En = /^https?$/i
      , Fn = ["POST", "PUT"];
    g = Dn.prototype;
    g.send = function(a, b, c, d) {
        if (this.e)
            throw Error("[goog.net.XhrIo] Object is active with another request=" + this.C + "; newUri=" + a);
        b = b ? b.toUpperCase() : "GET";
        this.C = a;
        this.j = "";
        this.k = 0;
        this.oa = b;
        this.S = !1;
        this.f = !0;
        this.e = this.Q ? $m(this.Q) : $m(Ym);
        this.N = this.Q ? Wm(this.Q) : Wm(Ym);
        this.e.onreadystatechange = v(this.Ic, this);
        try {
            be(Gn(this, "Opening Xhr")),
            this.da = !0,
            this.e.open(b, String(a), !0),
            this.da = !1
        } catch (e) {
            be(Gn(this, "Error opening Xhr: " + e.message));
            Hn(this, e);
            return
        }
        a = c || "";
        var f = this.headers.clone();
        d && Xd(d, function(a, b) {
            Qd(f, b, a)
        });
        d = Fa(f.sa(), In);
        c = m.FormData && a instanceof m.FormData;
        !Ha(Fn, b) || d || c || Qd(f, "Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
        f.forEach(function(a, b) {
            this.e.setRequestHeader(b, a)
        }, this);
        this.Ca && (this.e.responseType = this.Ca);
        "withCredentials"in this.e && (this.e.withCredentials = this.Da);
        try {
            Jn(this),
            0 < this.l && (this.I = Kn(this.e),
            be(Gn(this, "Will abort after " + this.l + "ms if incomplete, xhr2 " + this.I)),
            this.I ? (this.e.timeout = this.l,
            this.e.ontimeout = v(this.zc, this)) : this.H = Qm(this.zc, this.l, this)),
            be(Gn(this, "Sending request")),
            this.o = !0,
            this.e.send(a),
            this.o = !1
        } catch (h) {
            be(Gn(this, "Send error: " + h.message)),
            Hn(this, h)
        }
    }
    ;
    function Kn(a) {
        return K && Fc(9) && fa(a.timeout) && p(a.ontimeout)
    }
    function In(a) {
        return "content-type" == a.toLowerCase()
    }
    g.zc = function() {
        "undefined" != typeof aa && this.e && (this.j = "Timed out after " + this.l + "ms, aborting",
        this.k = 8,
        Gn(this, this.j),
        Lm(this, "timeout"),
        tn(this, 8))
    }
    ;
    function Hn(a, b) {
        a.f = !1;
        a.e && (a.h = !0,
        a.e.abort(),
        a.h = !1);
        a.j = b;
        a.k = 5;
        Ln(a);
        Mn(a)
    }
    function Ln(a) {
        a.S || (a.S = !0,
        Lm(a, "complete"),
        Lm(a, "error"))
    }
    function tn(a, b) {
        a.e && a.f && (Gn(a, "Aborting"),
        a.f = !1,
        a.h = !0,
        a.e.abort(),
        a.h = !1,
        a.k = b || 7,
        Lm(a, "complete"),
        Lm(a, "abort"),
        Mn(a))
    }
    g.B = function() {
        this.e && (this.f && (this.f = !1,
        this.h = !0,
        this.e.abort(),
        this.h = !1),
        Mn(this, !0));
        Dn.G.B.call(this)
    }
    ;
    g.Ic = function() {
        this.F() || (this.da || this.o || this.h ? Nn(this) : this.fe())
    }
    ;
    g.fe = function() {
        Nn(this)
    }
    ;
    function Nn(a) {
        if (a.f && "undefined" != typeof aa)
            if (a.N[1] && 4 == hn(a) && 2 == a.getStatus())
                Gn(a, "Local request error detected and ignored");
            else if (a.o && 4 == hn(a))
                Qm(a.Ic, 0, a);
            else if (Lm(a, "readystatechange"),
            4 == hn(a)) {
                Gn(a, "Request complete");
                a.f = !1;
                try {
                    var b = a.getStatus(), c;
                    t: switch (b) {
                    case 200:
                    case 201:
                    case 202:
                    case 204:
                    case 206:
                    case 304:
                    case 1223:
                        c = !0;
                        break t;
                    default:
                        c = !1
                    }
                    var d;
                    if (!(d = c)) {
                        var e;
                        if (e = 0 === b) {
                            var f = sd(String(a.C))[1] || null;
                            if (!f && self.location)
                                var h = self.location.protocol
                                  , f = h.substr(0, h.length - 1);
                            e = !En.test(f ? f.toLowerCase() : "")
                        }
                        d = e
                    }
                    if (d)
                        Lm(a, "complete"),
                        Lm(a, "success");
                    else {
                        a.k = 6;
                        var k;
                        try {
                            k = 2 < hn(a) ? a.e.statusText : ""
                        } catch (l) {
                            k = ""
                        }
                        a.j = k + " [" + a.getStatus() + "]";
                        Ln(a)
                    }
                } finally {
                    Mn(a)
                }
            }
    }
    function Mn(a, b) {
        if (a.e) {
            Jn(a);
            var c = a.e
              , d = a.N[0] ? t : null;
            a.e = null;
            a.N = null;
            b || Lm(a, "ready");
            try {
                c.onreadystatechange = d
            } catch (e) {}
        }
    }
    function Jn(a) {
        a.e && a.I && (a.e.ontimeout = null);
        fa(a.H) && (m.clearTimeout(a.H),
        a.H = null)
    }
    g.isActive = function() {
        return !!this.e
    }
    ;
    function hn(a) {
        return a.e ? a.e.readyState : 0
    }
    g.getStatus = function() {
        try {
            return 2 < hn(this) ? this.e.status : -1
        } catch (a) {
            return -1
        }
    }
    ;
    function jn(a) {
        try {
            return a.e ? a.e.responseText : ""
        } catch (b) {
            return ""
        }
    }
    function Gn(a, b) {
        return b + " [" + a.oa + " " + a.C + " " + a.getStatus() + "]"
    }
    ;function On(a, b, c) {
        this.o = a || null;
        this.e = 1;
        this.f = [];
        this.k = [];
        this.j = new Nm(null,!0);
        this.C = b || null;
        this.H = null != c ? c : null
    }
    function Pn(a, b) {
        this.e = a;
        this.map = b;
        this.context = null
    }
    g = On.prototype;
    g.eb = null;
    g.T = null;
    g.K = null;
    g.Wb = null;
    g.sb = null;
    g.qc = null;
    g.tb = null;
    g.ib = 0;
    g.Nd = 0;
    g.P = null;
    g.ya = null;
    g.qa = null;
    g.Ga = null;
    g.wa = null;
    g.Ib = null;
    g.Ua = -1;
    g.Cc = -1;
    g.fa = -1;
    g.fb = 0;
    g.Sa = 0;
    g.Fa = 8;
    var Qn = new Km;
    function Rn(a) {
        Hf.call(this, "statevent", a)
    }
    y(Rn, Hf);
    function Sn(a, b) {
        Hf.call(this, "timingevent", a);
        this.size = b
    }
    y(Sn, Hf);
    function Tn(a) {
        Hf.call(this, "serverreachability", a)
    }
    y(Tn, Hf);
    function Un(a, b, c, d, e, f) {
        W();
        a.Wb = c;
        a.eb = d || {};
        e && p(f) && (a.eb.OSID = e,
        a.eb.OAID = f);
        a.wa = new xn(a);
        a.wa.Xb = null;
        a.wa.f = a.j;
        a = a.wa;
        a.Yb = b;
        b = zn(a.e, null, a.Yb);
        W();
        a.Yc = w();
        c = a.e.C;
        null != c ? (a.gb = c[0],
        a.ab = c[1],
        a.ab ? (a.ca = 1,
        yn(a)) : (a.ca = 2,
        An(a))) : (Qg(b, "MODE", "init"),
        a.$ = new an(a,0,void 0,void 0,void 0),
        a.$.Ja = a.Xb,
        fn(a.$, b, !1, null, !0),
        a.ca = 0)
    }
    function Vn(a) {
        Wn(a);
        if (3 == a.e) {
            var b = a.ib++
              , c = a.sb.clone();
            M(c, "SID", a.h);
            M(c, "RID", b);
            M(c, "TYPE", "terminate");
            Xn(a, c);
            b = new an(a,0,a.h,b,void 0);
            b.Za = 2;
            b.va = Sg(c.clone());
            (new Image).src = b.va;
            b.jb = w();
            gn(b)
        }
        Yn(a)
    }
    function Wn(a) {
        if (a.wa) {
            var b = a.wa;
            b.$ && (b.$.cancel(),
            b.$ = null);
            b.ta = -1;
            a.wa = null
        }
        a.K && (a.K.cancel(),
        a.K = null);
        a.qa && (m.clearTimeout(a.qa),
        a.qa = null);
        Zn(a);
        a.T && (a.T.cancel(),
        a.T = null);
        a.ya && (m.clearTimeout(a.ya),
        a.ya = null)
    }
    function $n(a, b) {
        if (0 == a.e)
            throw Error("Invalid operation: sending map when state is closed");
        a.f.push(new Pn(a.Nd++,b));
        2 != a.e && 3 != a.e || ao(a)
    }
    g = On.prototype;
    g.Ac = function() {
        return 0 == this.e
    }
    ;
    function ao(a) {
        a.T || a.ya || (a.ya = rn(v(a.Nc, a), 0),
        a.fb = 0)
    }
    g.Nc = function(a) {
        this.ya = null;
        bo(this, a)
    }
    ;
    function bo(a, b) {
        if (1 == a.e) {
            if (!b) {
                a.ib = Math.floor(1E5 * Math.random());
                var c = a.ib++
                  , d = new an(a,0,"",c,void 0);
                d.Ja = null;
                var e = co(a)
                  , f = a.sb.clone();
                M(f, "RID", c);
                a.o && M(f, "CVER", a.o);
                Xn(a, f);
                dn(d, f, e);
                a.T = d;
                a.e = 2
            }
        } else
            3 == a.e && (b ? eo(a, b) : 0 != a.f.length && (a.T || eo(a)))
    }
    function eo(a, b) {
        var c, d;
        b ? 6 < a.Fa ? (a.f = a.k.concat(a.f),
        a.k.length = 0,
        c = a.ib - 1,
        d = co(a)) : (c = b.C,
        d = b.Ma) : (c = a.ib++,
        d = co(a));
        var e = a.sb.clone();
        M(e, "SID", a.h);
        M(e, "RID", c);
        M(e, "AID", a.Ua);
        Xn(a, e);
        c = new an(a,0,a.h,c,a.fb + 1);
        c.Ja = null;
        c.setTimeout(Math.round(1E4) + Math.round(1E4 * Math.random()));
        a.T = c;
        dn(c, e, d)
    }
    function Xn(a, b) {
        if (a.P) {
            var c = a.P.xc(a);
            c && Va(c, function(a, c) {
                M(b, c, a)
            })
        }
    }
    function co(a) {
        var b = Math.min(a.f.length, 1E3), c = ["count=" + b], d;
        6 < a.Fa && 0 < b ? (d = a.f[0].e,
        c.push("ofs=" + d)) : d = 0;
        for (var e = 0; e < b; e++) {
            var f = a.f[e].e
              , h = a.f[e].map
              , f = 6 >= a.Fa ? e : f - d;
            try {
                Xd(h, function(a, b) {
                    c.push("req" + f + "_" + b + "=" + encodeURIComponent(a))
                })
            } catch (k) {
                c.push("req" + f + "_type=" + encodeURIComponent("_badmap"))
            }
        }
        a.k = a.k.concat(a.f.splice(0, b));
        return c.join("&")
    }
    function fo(a) {
        a.K || a.qa || (a.l = 1,
        a.qa = rn(v(a.Mc, a), 0),
        a.Sa = 0)
    }
    function go(a) {
        if (a.K || a.qa || 3 <= a.Sa)
            return !1;
        a.l++;
        a.qa = rn(v(a.Mc, a), ho(a, a.Sa));
        a.Sa++;
        return !0
    }
    g.Mc = function() {
        this.qa = null;
        this.K = new an(this,0,this.h,"rpc",this.l);
        this.K.Ja = null;
        this.K.Zb = 0;
        var a = this.qc.clone();
        M(a, "RID", "rpc");
        M(a, "SID", this.h);
        M(a, "CI", this.Ib ? "0" : "1");
        M(a, "AID", this.Ua);
        Xn(this, a);
        if (!K || Gc(10))
            M(a, "TYPE", "xmlhttp"),
            fn(this.K, a, !0, this.tb, !1);
        else {
            M(a, "TYPE", "html");
            var b = this.K
              , c = Boolean(this.tb);
            b.Za = 3;
            b.va = Sg(a.clone());
            qn(b, c)
        }
    }
    ;
    function Cn(a, b, c) {
        a.Ib = c;
        a.fa = b.ta;
        a.zd(1, 0);
        a.sb = zn(a, null, a.Wb);
        ao(a)
    }
    g.Jc = function(a, b) {
        if (0 != this.e && (this.K == a || this.T == a))
            if (this.fa = a.hb,
            this.T == a && 3 == this.e)
                if (7 < this.Fa) {
                    var c;
                    try {
                        c = this.j.parse(b)
                    } catch (d) {
                        c = null
                    }
                    if (da(c) && 3 == c.length)
                        if (0 == c[0])
                            t: {
                                if (!this.qa) {
                                    if (this.K)
                                        if (this.K.jb + 3E3 < this.T.jb)
                                            Zn(this),
                                            this.K.cancel(),
                                            this.K = null;
                                        else
                                            break t;
                                    go(this);
                                    W()
                                }
                            }
                        else
                            this.Cc = c[1],
                            0 < this.Cc - this.Ua && 37500 > c[2] && this.Ib && 0 == this.Sa && !this.Ga && (this.Ga = rn(v(this.Od, this), 6E3));
                    else
                        Bn(this, 11)
                } else
                    "y2f%" != b && Bn(this, 11);
            else if (this.K == a && Zn(this),
            !/^[\s\xa0]*$/.test(b)) {
                c = this.j.parse(b);
                da(c);
                for (var e = 0; e < c.length; e++) {
                    var f = c[e];
                    this.Ua = f[0];
                    f = f[1];
                    2 == this.e ? "c" == f[0] ? (this.h = f[1],
                    this.tb = f[2],
                    f = f[3],
                    null != f ? this.Fa = f : this.Fa = 6,
                    this.e = 3,
                    this.P && this.P.vc(this),
                    this.qc = zn(this, this.kb() ? this.tb : null, this.Wb),
                    fo(this)) : "stop" == f[0] && Bn(this, 7) : 3 == this.e && ("stop" == f[0] ? Bn(this, 7) : "noop" != f[0] && this.P && this.P.uc(this, f),
                    this.Sa = 0)
                }
            }
    }
    ;
    g.Od = function() {
        null != this.Ga && (this.Ga = null,
        this.K.cancel(),
        this.K = null,
        go(this),
        W())
    }
    ;
    function Zn(a) {
        null != a.Ga && (m.clearTimeout(a.Ga),
        a.Ga = null)
    }
    g.zb = function(a) {
        var b;
        if (this.K == a)
            Zn(this),
            this.K = null,
            b = 2;
        else if (this.T == a)
            this.T = null,
            b = 1;
        else
            return;
        this.fa = a.hb;
        if (0 != this.e)
            if (a.ia)
                1 == b ? (w(),
                Lm(Qn, new Sn(Qn,a.Ma ? a.Ma.length : 0)),
                ao(this),
                this.k.length = 0) : fo(this);
            else {
                var c = a.Ka, d;
                if (!(d = 3 == c || 7 == c || 0 == c && 0 < this.fa)) {
                    if (d = 1 == b)
                        this.T || this.ya || 1 == this.e || 2 <= this.fb ? d = !1 : (this.ya = rn(v(this.Nc, this, a), ho(this, this.fb)),
                        this.fb++,
                        d = !0);
                    d = !(d || 2 == b && go(this))
                }
                if (d)
                    switch (c) {
                    case 1:
                        Bn(this, 5);
                        break;
                    case 4:
                        Bn(this, 10);
                        break;
                    case 3:
                        Bn(this, 6);
                        break;
                    case 7:
                        Bn(this, 12);
                        break;
                    default:
                        Bn(this, 2)
                    }
            }
    }
    ;
    function ho(a, b) {
        var c = 5E3 + Math.floor(1E4 * Math.random());
        a.isActive() || (c *= 2);
        return c * b
    }
    g.zd = function(a) {
        if (!Ha(arguments, this.e))
            throw Error("Unexpected channel state: " + this.e);
    }
    ;
    function Bn(a, b) {
        if (2 == b || 9 == b) {
            var c = null;
            a.P && (c = null);
            var d = v(a.Je, a);
            c || (c = new zg("//www.google.com/images/cleardot.gif"),
            Sg(c));
            vn(c.toString(), 1E4, d)
        } else
            W();
        io(a, b)
    }
    g.Je = function(a) {
        a ? W() : (W(),
        io(this, 8))
    }
    ;
    function io(a, b) {
        a.e = 0;
        a.P && a.P.tc(a, b);
        Yn(a);
        Wn(a)
    }
    function Yn(a) {
        a.e = 0;
        a.fa = -1;
        if (a.P)
            if (0 == a.k.length && 0 == a.f.length)
                a.P.Pb(a);
            else {
                var b = Oa(a.k)
                  , c = Oa(a.f);
                a.k.length = 0;
                a.f.length = 0;
                a.P.Pb(a, b, c)
            }
    }
    function zn(a, b, c) {
        var d = Tg(c);
        if ("" != d.ra)
            b && Bg(d, b + "." + d.ra),
            Cg(d, d.La);
        else
            var e = window.location
              , d = Ug(e.protocol, b ? b + "." + e.hostname : e.hostname, e.port, c);
        a.eb && Va(a.eb, function(a, b) {
            M(d, b, a)
        });
        M(d, "VER", a.Fa);
        Xn(a, d);
        return d
    }
    g.Rb = function(a) {
        if (a)
            throw Error("Can't create secondary domain capable XhrIo object.");
        a = new Dn;
        a.Da = !1;
        return a
    }
    ;
    g.isActive = function() {
        return !!this.P && this.P.isActive(this)
    }
    ;
    function rn(a, b) {
        if (!ga(a))
            throw Error("Fn must not be null and must be a function");
        return m.setTimeout(function() {
            a()
        }, b)
    }
    g.ha = function() {
        Lm(Qn, new Tn(Qn))
    }
    ;
    function W() {
        Lm(Qn, new Rn(Qn))
    }
    g.kb = function() {
        return !(!K || Gc(10))
    }
    ;
    function jo() {}
    g = jo.prototype;
    g.vc = function() {}
    ;
    g.uc = function() {}
    ;
    g.tc = function() {}
    ;
    g.Pb = function() {}
    ;
    g.xc = function() {
        return {}
    }
    ;
    g.isActive = function() {
        return !0
    }
    ;
    function ko(a, b) {
        Om.call(this);
        if (ga(a))
            b && (a = v(a, b));
        else if (a && ga(a.handleEvent))
            a = v(a.handleEvent, a);
        else
            throw Error("Invalid listener argument");
        this.o = a;
        Uf(this, "tick", v(this.l, this));
        this.stop();
        Pm(this, 5E3 + 2E4 * Math.random())
    }
    y(ko, Om);
    ko.prototype.j = 0;
    ko.prototype.l = function() {
        if (500 < this.e) {
            var a = this.e;
            24E4 > 2 * a && (a *= 2);
            Pm(this, a)
        }
        this.o()
    }
    ;
    ko.prototype.start = function() {
        ko.G.start.call(this);
        this.j = w() + this.e
    }
    ;
    ko.prototype.stop = function() {
        this.j = 0;
        ko.G.stop.call(this)
    }
    ;
    function lo(a, b) {
        this.la = a;
        this.k = b;
        this.h = new E;
        this.f = new ko(this.Qe,this);
        this.e = null;
        this.I = !1;
        this.l = null;
        this.H = "";
        this.C = this.j = 0;
        this.o = []
    }
    y(lo, jo);
    g = lo.prototype;
    g.subscribe = function(a, b, c) {
        return this.h.subscribe(a, b, c)
    }
    ;
    g.Na = function(a, b, c) {
        return this.h.Na(a, b, c)
    }
    ;
    g.ka = function(a) {
        return this.h.ka(a)
    }
    ;
    g.A = function(a, b) {
        return this.h.A.apply(this.h, arguments)
    }
    ;
    g.dispose = function() {
        this.I || (this.I = !0,
        this.h.clear(),
        mo(this),
        Bb(this.h))
    }
    ;
    g.F = function() {
        return this.I
    }
    ;
    function no(a) {
        return {
            firstTestResults: [""],
            secondTestResults: !a.e.Ib,
            sessionId: a.e.h,
            arrayId: a.e.Ua
        }
    }
    function oo(a, b, c, d) {
        if (!a.e || 2 != a.e.e) {
            a.H = "";
            a.f.stop();
            a.l = b || null;
            a.j = c || 0;
            b = a.la + "/test";
            c = a.la + "/bind";
            var e = new On("1",d ? d.firstTestResults : null,d ? d.secondTestResults : null)
              , f = a.e;
            f && (f.P = null);
            e.P = a;
            a.e = e;
            f ? Un(a.e, b, c, a.k, f.h, f.Ua) : d ? Un(a.e, b, c, a.k, d.sessionId, d.arrayId) : Un(a.e, b, c, a.k)
        }
    }
    function mo(a, b) {
        a.C = b || 0;
        a.f.stop();
        a.e && (3 == a.e.e && bo(a.e),
        Vn(a.e));
        a.C = 0
    }
    g.vc = function() {
        var a = this.f;
        a.stop();
        Pm(a, 5E3 + 2E4 * Math.random());
        this.l = null;
        this.j = 0;
        if (this.o.length) {
            a = this.o;
            this.o = [];
            for (var b = 0, c = a.length; b < c; ++b)
                $n(this.e, a[b])
        }
        this.A("handlerOpened")
    }
    ;
    g.tc = function(a, b) {
        var c = 2 == b && 401 == this.e.fa;
        if (4 != b && !c) {
            if (6 == b || 410 == this.e.fa)
                c = this.f,
                c.stop(),
                Pm(c, 500);
            this.f.start()
        }
        this.A("handlerError", b)
    }
    ;
    g.Pb = function(a, b, c) {
        if (!this.f.enabled)
            this.A("handlerClosed");
        else if (c)
            for (a = 0,
            b = c.length; a < b; ++a)
                this.o.push(c[a].map)
    }
    ;
    g.xc = function() {
        var a = {
            v: 2
        };
        this.H && (a.gsessionid = this.H);
        0 != this.j && (a.ui = "" + this.j);
        0 != this.C && (a.ui = "" + this.C);
        this.l && ib(a, this.l);
        return a
    }
    ;
    g.uc = function(a, b) {
        if ("S" == b[0])
            this.H = b[1];
        else if ("gracefulReconnect" == b[0]) {
            var c = this.f;
            c.stop();
            Pm(c, 500);
            this.f.start();
            Vn(this.e)
        } else
            this.A("handlerMessage", new Im(b[0],b[1]))
    }
    ;
    function po(a, b) {
        (a.k.loungeIdToken = b) || a.f.stop()
    }
    g.Qe = function() {
        this.f.stop();
        var a = this.e
          , b = 0;
        a.K && b++;
        a.T && b++;
        0 != b ? this.f.start() : oo(this, this.l, this.j)
    }
    ;
    function qo(a) {
        this.reset(a)
    }
    function ro(a, b) {
        if (a.f)
            throw Error(b + " is not allowed in V3.");
    }
    function so(a) {
        a.volume = -1;
        a.l = !1;
        a.k = null;
        a.e = -1;
        a.h = null;
        a.j = 0;
        a.o = w()
    }
    g = qo.prototype;
    g.reset = function(a) {
        this.videoIds = [];
        this.f = "";
        this.index = -1;
        this.videoId = "";
        so(this);
        a && (this.videoIds = a.videoIds,
        this.index = a.index,
        this.f = a.listId,
        this.videoId = a.videoId,
        this.e = a.playerState,
        this.h = a.errorReason,
        this.volume = a.volume,
        this.l = a.muted,
        this.k = a.trackData,
        this.j = a.playerTime,
        this.o = a.playerTimeAt)
    }
    ;
    function to(a) {
        return a.f ? a.videoId : a.videoIds[a.index]
    }
    function uo(a) {
        switch (a.e) {
        case 1:
            return (w() - a.o) / 1E3 + a.j;
        case -1E3:
            return 0
        }
        return a.j
    }
    g.setVideoId = function(a) {
        ro(this, "setVideoId");
        var b = this.index;
        this.index = Ba(this.videoIds, a);
        b != this.index && so(this);
        return -1 != b
    }
    ;
    function vo(a, b, c) {
        ro(a, "setPlaylist");
        c = c || to(a);
        Sa(a.videoIds, b) && c == to(a) || (a.videoIds = Oa(b),
        a.setVideoId(c))
    }
    g.add = function(a) {
        ro(this, "add");
        return a && !Ha(this.videoIds, a) ? (this.videoIds.push(a),
        !0) : !1
    }
    ;
    g.remove = function(a) {
        ro(this, "remove");
        var b = to(this);
        return La(this.videoIds, a) ? (this.index = Ba(this.videoIds, b),
        !0) : !1
    }
    ;
    function wo(a) {
        var b = {};
        b.videoIds = Oa(a.videoIds);
        b.index = a.index;
        b.listId = a.f;
        b.videoId = a.videoId;
        b.playerState = a.e;
        b.errorReason = a.h;
        b.volume = a.volume;
        b.muted = a.l;
        b.trackData = gb(a.k);
        b.playerTime = a.j;
        b.playerTimeAt = a.o;
        return b
    }
    g.clone = function() {
        return new qo(wo(this))
    }
    ;
    function X(a, b, c) {
        U.call(this);
        this.ma = a;
        this.C = [];
        this.C.push(L(window, "beforeunload", v(this.Hd, this)));
        this.f = [];
        this.n = new qo;
        3 == c["mdx-version"] && (this.n.f = "RQ" + b.token);
        this.H = b.id;
        this.e = xo(this, c);
        this.e.subscribe("handlerOpened", this.Td, this);
        this.e.subscribe("handlerClosed", this.Pd, this);
        this.e.subscribe("handlerError", this.Qd, this);
        this.n.f ? this.e.subscribe("handlerMessage", this.Rd, this) : this.e.subscribe("handlerMessage", this.Sd, this);
        po(this.e, b.token);
        this.subscribe("remoteQueueChange", function() {
            var a = this.n.videoId;
            Xk() && S("yt-remote-session-video-id", a)
        }, this)
    }
    y(X, U);
    g = X.prototype;
    g.bb = NaN;
    g.ec = !1;
    g.Fb = NaN;
    g.dc = NaN;
    g.pb = NaN;
    g.vb = NaN;
    function yo(a, b) {
        var c = zo();
        if (c) {
            if (a.n.f) {
                var d = c.listId
                  , e = c.videoId
                  , f = c.index
                  , c = c.currentTime || 0;
                5 >= c && (c = 0);
                h = {
                    videoId: e,
                    currentTime: c
                };
                d && (h.listId = d);
                p(f) && (h.currentIndex = f);
                d && (a.n.f = d);
                a.n.videoId = e;
                a.n.index = f || 0
            } else {
                var e = c.videoIds[c.index]
                  , c = c.currentTime || 0;
                5 >= c && (c = 0);
                var h = {
                    videoIds: e,
                    videoId: e,
                    currentTime: c
                };
                a.n.videoIds = [e];
                a.n.index = 0
            }
            a.n.state = 3;
            d = a.n;
            d.j = c;
            d.o = w();
            a.D("Connecting with setPlaylist and params: " + R(h));
            oo(a.e, {
                method: "setPlaylist",
                params: R(h)
            }, b, al())
        } else
            a.D("Connecting without params"),
            oo(a.e, {}, b, al());
        Ao(a)
    }
    g.dispose = function() {
        this.F() || (this.A("beforeDispose"),
        Bo(this, 3));
        X.G.dispose.call(this)
    }
    ;
    g.B = function() {
        Co(this);
        Do(this);
        Eo(this);
        H(this.pb);
        this.pb = NaN;
        H(this.vb);
        this.vb = NaN;
        this.j = null;
        gd(this.C);
        this.C.length = 0;
        this.e.dispose();
        X.G.B.call(this);
        this.f = this.n = this.e = null
    }
    ;
    g.D = function(a) {
        dk("conn", a)
    }
    ;
    g.Hd = function() {
        this.h(2)
    }
    ;
    function xo(a, b) {
        return new lo(yk(a.ma, "/bc", void 0, !1),b)
    }
    function Bo(a, b) {
        a.A("proxyStateChange", b)
    }
    function Ao(a) {
        a.bb = G(v(function() {
            this.D("Connecting timeout");
            this.h(1)
        }, a), 2E4)
    }
    function Co(a) {
        H(a.bb);
        a.bb = NaN
    }
    function Eo(a) {
        H(a.Fb);
        a.Fb = NaN
    }
    function Fo(a) {
        Do(a);
        a.dc = G(v(function() {
            Go(this, "getNowPlaying")
        }, a), 2E4)
    }
    function Do(a) {
        H(a.dc);
        a.dc = NaN
    }
    function Ho(a) {
        var b = a.e;
        return !!b.e && 3 == b.e.e && isNaN(a.bb)
    }
    g.Td = function() {
        this.D("Channel opened");
        this.ec && (this.ec = !1,
        Eo(this),
        this.Fb = G(v(function() {
            this.D("Timing out waiting for a screen.");
            this.h(1)
        }, this), 15E3));
        jl(no(this.e), this.H)
    }
    ;
    g.Pd = function() {
        this.D("Channel closed");
        isNaN(this.bb) ? kl(!0) : kl();
        this.dispose()
    }
    ;
    g.Qd = function(a) {
        kl();
        isNaN(this.l()) ? (this.D("Channel error: " + a + " without reconnection"),
        this.dispose()) : (this.ec = !0,
        this.D("Channel error: " + a + " with reconnection in " + this.l() + " ms"),
        Bo(this, 2))
    }
    ;
    function Io(a, b) {
        b && (Co(a),
        Eo(a));
        b == Ho(a) ? b && (Bo(a, 1),
        Go(a, "getSubtitlesTrack")) : b ? (a.o() && a.n.reset(),
        Bo(a, 1),
        Go(a, "getNowPlaying"),
        Jo(a)) : a.h(1)
    }
    function Ko(a, b) {
        var c = b.params.videoId;
        delete b.params.videoId;
        c == a.n.videoId && (db(b.params) ? a.n.k = null : a.n.k = b.params,
        a.A("remotePlayerChange"))
    }
    function Lo(a, b) {
        var c = b.params.videoId || b.params.video_id
          , d = parseInt(b.params.currentIndex, 10);
        a.n.f = b.params.listId || a.n.f;
        var e = a.n
          , f = e.videoId;
        e.videoId = c;
        e.index = d;
        c != f && so(e);
        a.A("remoteQueueChange")
    }
    function Mo(a, b) {
        b.params = b.params || {};
        Lo(a, b);
        No(a, b)
    }
    function No(a, b) {
        var c = parseInt(b.params.currentTime || b.params.current_time, 10)
          , d = a.n;
        d.j = isNaN(c) ? 0 : c;
        d.o = w();
        c = parseInt(b.params.state, 10);
        c = isNaN(c) ? -1 : c;
        -1 == c && -1E3 == a.n.e && (c = -1E3);
        a.n.e = c;
        d = null;
        -1E3 == c && (d = a.n.h || "unknown",
        p(b.params.currentError) && (d = Fh(b.params.currentError).reason || d));
        a.n.h = d;
        1 == a.n.e ? Fo(a) : Do(a);
        a.A("remotePlayerChange")
    }
    function Oo(a, b) {
        var c = "true" == b.params.muted;
        a.n.volume = parseInt(b.params.volume, 10);
        a.n.l = c;
        a.A("remotePlayerChange")
    }
    g.Rd = function(a) {
        a.params ? this.D("Received: action=" + a.action + ", params=" + R(a.params)) : this.D("Received: action=" + a.action + " {}");
        switch (a.action) {
        case "loungeStatus":
            a = Fh(a.params.devices);
            this.f = B(a, function(a) {
                return new Sk(a)
            });
            a = !!Fa(this.f, function(a) {
                return "LOUNGE_SCREEN" == a.type
            });
            Io(this, a);
            break;
        case "loungeScreenConnected":
            Io(this, !0);
            break;
        case "loungeScreenDisconnected":
            Ma(this.f, function(a) {
                return "LOUNGE_SCREEN" == a.type
            });
            Io(this, !1);
            break;
        case "remoteConnected":
            var b = new Sk(Fh(a.params.device));
            Fa(this.f, function(a) {
                return a.equals(b)
            }) || Ka(this.f, b);
            break;
        case "remoteDisconnected":
            b = new Sk(Fh(a.params.device));
            Ma(this.f, function(a) {
                return a.equals(b)
            });
            break;
        case "gracefulDisconnect":
            break;
        case "playlistModified":
            Lo(this, a);
            break;
        case "nowPlaying":
            Mo(this, a);
            break;
        case "onStateChange":
            No(this, a);
            break;
        case "onVolumeChanged":
            Oo(this, a);
            break;
        case "onSubtitlesTrackChanged":
            Ko(this, a);
            break;
        default:
            this.D("Unrecognized action: " + a.action)
        }
    }
    ;
    g.Sd = function(a) {
        a.params ? this.D("Received: action=" + a.action + ", params=" + R(a.params)) : this.D("Received: action=" + a.action);
        Po(this, a);
        Qo(this, a);
        if (Ho(this)) {
            var b = this.n.clone(), c = !1, d, e, f, h, k, l, n;
            a.params && (d = a.params.videoId || a.params.video_id,
            e = a.params.videoIds || a.params.video_ids,
            f = a.params.state,
            h = a.params.currentTime || a.params.current_time,
            k = a.params.volume,
            l = a.params.muted,
            p(a.params.currentError) && (n = Fh(a.params.currentError)));
            if ("onSubtitlesTrackChanged" == a.action)
                d == to(this.n) && (delete a.params.videoId,
                db(a.params) ? this.n.k = null : this.n.k = a.params,
                this.A("remotePlayerChange"));
            else if (to(this.n) || "onStateChange" != a.action)
                "playlistModified" != a.action && "nowPlayingPlaylist" != a.action || e ? (d || "nowPlaying" != a.action && "nowPlayingPlaylist" != a.action ? d || (d = to(this.n)) : this.n.setVideoId(""),
                e && (e = e.split(","),
                vo(this.n, e, d))) : vo(this.n, []),
                this.n.add(d) && Go(this, "getPlaylist"),
                d && this.n.setVideoId(d),
                b.index == this.n.index && Sa(b.videoIds, this.n.videoIds) || this.A("remoteQueueChange"),
                p(f) && (b = parseInt(f, 10),
                b = isNaN(b) ? -1 : b,
                -1 == b && -1E3 == this.n.e && (b = -1E3),
                0 == b && "0" == h && (b = -1),
                c = c || b != this.n.e,
                this.n.e = b,
                d = null,
                -1E3 == b && (d = this.n.h || "unknown",
                n && (d = n.reason || d)),
                c = c || this.n.h != d,
                this.n.h = d,
                1 == this.n.e ? Fo(this) : Do(this)),
                "onError" != a.action || -1 != this.n.e && -1E3 != this.n.e || (a = Fh(a.params.errors) || [],
                1 == a.length && "PLAYER_ERROR" == a[0].error && a[0].videoId == to(this.n) && (this.n.e = -1E3,
                this.n.h = a[0].reason || "unknown",
                c = !0)),
                h && (b = parseInt(h, 10),
                c = this.n,
                c.j = isNaN(b) ? 0 : b,
                c.o = w(),
                c = !0),
                p(k) && (b = parseInt(k, 10),
                isNaN(b) || (c = c || this.n.volume != b,
                this.n.volume = b),
                p(l) && (l = "true" == l,
                c = c || this.n.l != l,
                this.n.l = l)),
                c && this.A("remotePlayerChange")
        }
    }
    ;
    function Po(a, b) {
        switch (b.action) {
        case "loungeStatus":
            var c = Fh(b.params.devices);
            a.f = B(c, function(a) {
                return new Sk(a)
            });
            break;
        case "loungeScreenDisconnected":
            Ma(a.f, function(a) {
                return "LOUNGE_SCREEN" == a.type
            });
            break;
        case "remoteConnected":
            var d = new Sk(Fh(b.params.device));
            Fa(a.f, function(a) {
                return a.equals(d)
            }) || Ka(a.f, d);
            break;
        case "remoteDisconnected":
            d = new Sk(Fh(b.params.device)),
            Ma(a.f, function(a) {
                return a.equals(d)
            })
        }
    }
    function Qo(a, b) {
        var c = !1;
        if ("loungeStatus" == b.action)
            c = !!Fa(a.f, function(a) {
                return "LOUNGE_SCREEN" == a.type
            });
        else if ("loungeScreenConnected" == b.action)
            c = !0;
        else if ("loungeScreenDisconnected" == b.action)
            c = !1;
        else
            return;
        if (!isNaN(a.Fb))
            if (c)
                Eo(a);
            else
                return;
        c == Ho(a) ? c && Bo(a, 1) : c ? (Co(a),
        a.o() && a.n.reset(),
        Bo(a, 1),
        Go(a, "getNowPlaying"),
        Jo(a)) : a.h(1)
    }
    g.ye = function() {
        if (this.j) {
            var a = this.j;
            this.j = null;
            this.n.videoId != a && Go(this, "getNowPlaying")
        }
    }
    ;
    X.prototype.subscribe = X.prototype.subscribe;
    X.prototype.unsubscribeByKey = X.prototype.ka;
    X.prototype.Q = function() {
        var a = 3;
        this.F() || (a = 0,
        isNaN(this.l()) ? Ho(this) && (a = 1) : a = 2);
        return a
    }
    ;
    X.prototype.getProxyState = X.prototype.Q;
    X.prototype.h = function(a) {
        this.D("Disconnecting with " + a);
        Co(this);
        this.A("beforeDisconnect", a);
        1 == a && kl();
        mo(this.e, a);
        this.dispose()
    }
    ;
    X.prototype.disconnect = X.prototype.h;
    X.prototype.N = function() {
        var a = this.n;
        if (this.j) {
            var b = a = this.n.clone()
              , c = this.j
              , d = a.index
              , e = b.videoId;
            b.videoId = c;
            b.index = d;
            c != e && so(b)
        }
        return wo(a)
    }
    ;
    X.prototype.getPlayerContextData = X.prototype.N;
    X.prototype.da = function(a) {
        var b = new qo(a);
        b.videoId && b.videoId != this.n.videoId && (this.j = b.videoId,
        H(this.pb),
        this.pb = G(v(this.ye, this), 5E3));
        var c = [];
        this.n.f == b.f && this.n.videoId == b.videoId && this.n.index == b.index && Sa(this.n.videoIds, b.videoIds) || c.push("remoteQueueChange");
        this.n.e == b.e && this.n.volume == b.volume && this.n.l == b.l && uo(this.n) == uo(b) && R(this.n.k) == R(b.k) || c.push("remotePlayerChange");
        this.n.reset(a);
        A(c, function(a) {
            this.A(a)
        }, this)
    }
    ;
    X.prototype.setPlayerContextData = X.prototype.da;
    X.prototype.I = function() {
        return this.e.k.loungeIdToken
    }
    ;
    X.prototype.getLoungeToken = X.prototype.I;
    X.prototype.o = function() {
        var a = this.e.k.id
          , b = Fa(this.f, function(b) {
            return "REMOTE_CONTROL" == b.type && b.id != a
        });
        return b ? b.id : ""
    }
    ;
    X.prototype.getOtherConnectedRemoteId = X.prototype.o;
    X.prototype.l = function() {
        var a = this.e;
        return a.f.enabled ? a.f.j - w() : NaN
    }
    ;
    X.prototype.getReconnectTimeout = X.prototype.l;
    X.prototype.oa = function() {
        if (!isNaN(this.l())) {
            var a = this.e.f;
            a.enabled && (a.stop(),
            a.start(),
            a.l())
        }
    }
    ;
    X.prototype.reconnect = X.prototype.oa;
    function Jo(a) {
        H(a.vb);
        a.vb = G(v(a.h, a, 1), 864E5)
    }
    function Go(a, b, c) {
        c ? a.D("Sending: action=" + b + ", params=" + R(c)) : a.D("Sending: action=" + b);
        a = a.e;
        b = {
            _sc: b
        };
        c && ib(b, c);
        a.f.enabled || 2 == (a.e ? a.e.e : 0) ? a.o.push(b) : a.e && 3 == a.e.e && $n(a.e, b)
    }
    X.prototype.S = function(a, b) {
        Go(this, a, b);
        Jo(this)
    }
    ;
    X.prototype.sendMessage = X.prototype.S;
    function Ro(a) {
        U.call(this);
        this.j = a;
        this.ga = So();
        this.D("Initializing local screens: " + ok(this.ga));
        this.h = To();
        this.D("Initializing account screens: " + ok(this.h));
        this.Qb = null;
        this.e = [];
        this.f = [];
        Uo(this, Dm() || []);
        this.D("Initializing DIAL devices: " + Ck(this.f));
        a = mk(gl());
        Vo(this, a);
        this.D("Initializing online screens: " + ok(this.e));
        this.l = w() + 3E5;
        Wo(this)
    }
    y(Ro, U);
    var Xo = [2E3, 2E3, 1E3, 1E3, 1E3, 2E3, 2E3, 5E3, 5E3, 1E4];
    g = Ro.prototype;
    g.cb = NaN;
    g.Bb = "";
    g.D = function(a) {
        dk("RM", a)
    }
    ;
    g.L = function(a) {
        dk("RM", a)
    }
    ;
    function To() {
        var a = So()
          , b = mk(gl());
        return Ca(b, function(b) {
            return !Kk(a, b)
        })
    }
    function So() {
        var a = mk(cl());
        return Ca(a, function(a) {
            return !a.uuid
        })
    }
    function Wo(a) {
        Rb("yt-remote-cast-device-list-update", function() {
            var a = Dm();
            Uo(this, a || [])
        }, a);
        Rb("yt-remote-cast-device-status-update", a.Me, a);
        a.Tc();
        var b = w() > a.l ? 2E4 : 1E4;
        Ib(v(a.Tc, a), b)
    }
    g.A = function(a, b) {
        if (this.F())
            return !1;
        this.D("Firing " + a);
        return this.k.A.apply(this.k, arguments)
    }
    ;
    g.Tc = function() {
        var a = Dm() || [];
        0 == a.length || Uo(this, a);
        a = Yo(this);
        0 == a.length || (Da(a, function(a) {
            return !Kk(this.h, a)
        }, this) && el() ? Zo(this) : $o(this, a))
    }
    ;
    function ap(a, b) {
        var c = Yo(a);
        return Ca(b, function(a) {
            return a.uuid ? (a = Jk(this.f, a.uuid),
            !!a && "RUNNING" == a.status) : !!Kk(c, a)
        }, a)
    }
    function Uo(a, b) {
        var c = !1;
        A(b, function(a) {
            var b = Lk(this.ga, a.id);
            b && b.name != a.name && (this.D("Renaming screen id " + b.id + " from " + b.name + " to " + a.name),
            b.name = a.name,
            c = !0)
        }, a);
        c && (a.D("Renaming due to DIAL."),
        bp(a));
        hl(Gk(b));
        var d = !Sa(a.f, b, Ik);
        d && a.D("Updating DIAL devices: " + Ck(a.f) + " to " + Ck(b));
        a.f = b;
        Vo(a, a.e);
        d && a.A("onlineReceiverChange")
    }
    g.Me = function(a) {
        var b = Jk(this.f, a.id);
        b && (this.D("Updating DIAL device: " + b.id + "(" + b.name + ") from status: " + b.status + " to status: " + a.status + " and from activityId: " + b.activityId + " to activityId: " + a.activityId),
        b.activityId = a.activityId,
        b.status = a.status,
        hl(Gk(this.f)));
        Vo(this, this.e)
    }
    ;
    function Vo(a, b, c) {
        var d = ap(a, b)
          , e = !Sa(a.e, d, jk);
        if (e || c)
            0 == b.length || fl(B(d, kk));
        e && (a.D("Updating online screens: " + ok(a.e) + " -> " + ok(d)),
        a.e = d,
        a.A("onlineReceiverChange"))
    }
    function $o(a, b) {
        var c = []
          , d = {};
        A(b, function(a) {
            a.token && (d[a.token] = a,
            c.push(a.token))
        });
        var e = {
            method: "POST",
            O: {
                lounge_token: c.join(",")
            },
            context: a,
            Z: function(a, b) {
                var c = [];
                A(b.screens || [], function(a) {
                    "online" == a.status && c.push(d[a.loungeToken])
                });
                var e = this.Qb ? cp(this, this.Qb) : null;
                e && !Kk(c, e) && c.push(e);
                Vo(this, c, !0)
            }
        };
        nj(yk(a.j, "/pairing/get_screen_availability"), e)
    }
    function Zo(a) {
        var b = Yo(a)
          , c = B(b, function(a) {
            return a.id
        });
        0 != c.length && (a.D("Updating lounge tokens for: " + R(c)),
        nj(yk(a.j, "/pairing/get_lounge_token_batch"), {
            O: {
                screen_ids: c.join(",")
            },
            method: "POST",
            context: a,
            Z: function(a, c) {
                dp(this, c.screens || []);
                this.ga = Ca(this.ga, function(a) {
                    return !!a.token
                });
                bp(this);
                $o(this, b)
            }
        }))
    }
    function dp(a, b) {
        A(Na(a.ga, a.h), function(a) {
            var d = Fa(b, function(b) {
                return a.id == b.screenId
            });
            d && (a.token = d.loungeToken)
        })
    }
    function bp(a) {
        var b = So();
        Sa(a.ga, b, jk) || (a.D("Saving local screens: " + ok(b) + " to " + ok(a.ga)),
        bl(B(a.ga, kk)),
        Vo(a, a.e, !0),
        Uo(a, Dm() || []),
        a.A("managedScreenChange", Yo(a)))
    }
    function ep(a, b, c) {
        var d = Ga(b, function(a) {
            return ik(c, a)
        })
          , e = 0 > d;
        0 > d ? b.push(c) : b[d] = c;
        Kk(a.e, c) || a.e.push(c);
        return e
    }
    g.yc = function(a, b) {
        for (var c = Yo(this), c = B(c, function(a) {
            return a.name
        }), d = a, e = 2; Ha(c, d); )
            d = b.call(m, e),
            e++;
        return d
    }
    ;
    g.Pc = function(a, b, c) {
        var d = !1;
        b >= Xo.length && (this.D("Pairing DIAL device " + a + " with " + c + " timed out."),
        d = !0);
        var e = Jk(this.f, a);
        if (!e)
            this.D("Pairing DIAL device " + a + " with " + c + " failed: no device for " + a),
            d = !0;
        else if ("ERROR" == e.status || "STOPPED" == e.status)
            this.D("Pairing DIAL device " + a + " with " + c + " failed: launch error on " + a),
            d = !0;
        d ? (fp(this),
        this.A("screenPair", null)) : nj(yk(this.j, "/pairing/get_screen"), {
            method: "POST",
            O: {
                pairing_code: c
            },
            context: this,
            Z: function(a, b) {
                if (c == this.Bb) {
                    fp(this);
                    var d = new gk(b.screen);
                    d.name = e.name;
                    d.uuid = e.id;
                    this.D("Pairing " + c + " succeeded.");
                    var l = ep(this, this.ga, d);
                    this.D("Paired with " + (l ? "a new" : "an old") + " local screen:" + nk(d));
                    bp(this);
                    this.A("screenPair", d)
                }
            },
            onError: function() {
                c == this.Bb && (this.D("Polling pairing code: " + c),
                H(this.cb),
                this.cb = G(v(this.Pc, this, a, b + 1, c), Xo[b]))
            }
        })
    }
    ;
    function gp(a, b, c) {
        var d = Y
          , e = "";
        fp(d);
        if (Jk(d.f, a)) {
            if (!e) {
                var f = e = Dk();
                wm();
                var h = Fm(a)
                  , k = tm();
                if (k && h) {
                    var l = new cast.Receiver(h.id,h.name)
                      , l = new cast.LaunchRequest("YouTube",l);
                    l.parameters = "pairingCode=" + f;
                    l.description = new cast.LaunchDescription;
                    l.description.text = document.title;
                    b && (l.parameters += "&v=" + b,
                    c && (l.parameters += "&t=" + Math.round(c)),
                    l.description.url = "http://i.ytimg.com/vi/" + b + "/default.jpg");
                    "UNKNOWN" != h.status && (h.status = "UNKNOWN",
                    Bm(h),
                    I("yt-remote-cast-device-status-update", h));
                    rm("Sending a cast launch request with params: " + l.parameters);
                    k.launch(l, oa(Gm, a))
                } else
                    rm("No cast API or no cast device. Dropping cast launch.")
            }
            d.Bb = e;
            d.cb = G(v(d.Pc, d, a, 0, e), Xo[0])
        } else
            d.D("No DIAL device with id: " + a)
    }
    function fp(a) {
        H(a.cb);
        a.cb = NaN;
        a.Bb = ""
    }
    function cp(a, b) {
        var c = Lk(Yo(a), b);
        a.D("Found screen: " + nk(c) + " with key: " + b);
        return c
    }
    function hp(a) {
        var b = Y
          , c = Lk(b.e, a);
        b.D("Found online screen: " + nk(c) + " with key: " + a);
        return c
    }
    function ip(a) {
        var b = Y
          , c = Jk(b.f, a);
        if (!c) {
            var d = Lk(b.ga, a);
            d && (c = Jk(b.f, d.uuid))
        }
        b.D("Found DIAL: " + (c ? c.toString() : "null") + " with key: " + a);
        return c
    }
    function Yo(a) {
        return Na(a.h, Ca(a.ga, function(a) {
            return !Kk(this.h, a)
        }, a))
    }
    ;function jp(a) {
        Mk.call(this, "ScreenServiceProxy");
        this.R = a;
        this.e = [];
        this.e.push(this.R.$_s("screenChange", v(this.Ue, this)));
        this.e.push(this.R.$_s("onlineScreenChange", v(this.ae, this)))
    }
    y(jp, Mk);
    g = jp.prototype;
    g.X = function(a) {
        return this.R.$_gs(a)
    }
    ;
    g.contains = function(a) {
        return !!this.R.$_c(a)
    }
    ;
    g.get = function(a) {
        return this.R.$_g(a)
    }
    ;
    g.start = function() {
        this.R.$_st()
    }
    ;
    g.add = function(a, b, c) {
        this.R.$_a(a, b, c)
    }
    ;
    g.remove = function(a, b, c) {
        this.R.$_r(a, b, c)
    }
    ;
    g.Hb = function(a, b, c, d) {
        this.R.$_un(a, b, c, d)
    }
    ;
    g.B = function() {
        for (var a = 0, b = this.e.length; a < b; ++a)
            this.R.$_ubk(this.e[a]);
        this.e.length = 0;
        this.R = null;
        jp.G.B.call(this)
    }
    ;
    g.Ue = function() {
        this.A("screenChange")
    }
    ;
    g.ae = function() {
        this.A("onlineScreenChange")
    }
    ;
    V.prototype.$_st = V.prototype.start;
    V.prototype.$_gspc = V.prototype.Ve;
    V.prototype.$_gsppc = V.prototype.bd;
    V.prototype.$_c = V.prototype.contains;
    V.prototype.$_g = V.prototype.get;
    V.prototype.$_a = V.prototype.add;
    V.prototype.$_un = V.prototype.Hb;
    V.prototype.$_r = V.prototype.remove;
    V.prototype.$_gs = V.prototype.X;
    V.prototype.$_gos = V.prototype.ad;
    V.prototype.$_s = V.prototype.subscribe;
    V.prototype.$_ubk = V.prototype.ka;
    function kp() {
        var a = !!F("MDX_ENABLE_CASTV2")
          , b = !!F("MDX_ENABLE_QUEUE")
          , c = {
            device: "Desktop",
            app: "youtube-desktop"
        };
        a ? q("yt.mdx.remote.castv2_", !0, void 0) : wm();
        Xh && Wh();
        Uk();
        lp || (lp = new xk,
        ll() && (lp.e = "/api/loungedev"));
        Y || a || (Y = new Ro(lp),
        Y.subscribe("screenPair", mp),
        Y.subscribe("managedScreenChange", np),
        Y.subscribe("onlineReceiverChange", function() {
            I("yt-remote-receiver-availability-change")
        }));
        op || (op = r("yt.mdx.remote.deferredProxies_") || [],
        q("yt.mdx.remote.deferredProxies_", op, void 0));
        pp(b);
        b = qp();
        if (a && !b) {
            var d = new V(lp);
            q("yt.mdx.remote.screenService_", d, void 0);
            b = qp();
            Vl(d, function(a) {
                a ? rp() && nm(rp(), "YouTube TV") : d.subscribe("onlineScreenChange", function() {
                    I("yt-remote-receiver-availability-change")
                })
            }, !(!c || !c.loadCastApiSetupScript))
        }
        if (c && !r("yt.mdx.remote.initialized_")) {
            q("yt.mdx.remote.initialized_", !0, void 0);
            sp("Initializing: " + R(c));
            tp.push(Rb("yt-remote-cast2-availability-change", function() {
                I("yt-remote-receiver-availability-change")
            }));
            tp.push(Rb("yt-remote-cast2-receiver-selected", function() {
                up(null);
                I("yt-remote-auto-connect", "cast-selector-receiver")
            }));
            tp.push(Rb("yt-remote-cast2-session-change", vp));
            tp.push(Rb("yt-remote-connection-change", function(a) {
                a ? nm(rp(), "YouTube TV") : wp() || (nm(null, null),
                im())
            }));
            var e = xp();
            c.isAuto && (e.id += "#dial");
            e.name = c.device;
            e.app = c.app;
            sp(" -- with channel params: " + R(e));
            yp(e);
            a && b.start();
            rp() || zp()
        }
    }
    function Ap() {
        Tb(tp);
        tp.length = 0;
        Bb(Bp);
        Bp = null;
        op && (A(op, function(a) {
            a(null)
        }),
        op.length = 0,
        op = null,
        q("yt.mdx.remote.deferredProxies_", null, void 0));
        Y && (Bb(Y),
        Y = null);
        lp = null;
        Am()
    }
    function Cp() {
        if (Dp() && gm()) {
            var a = [];
            if (T("yt-remote-cast-available") || r("yt.mdx.remote.cloudview.castButtonShown_") || Ep())
                a.push({
                    key: "cast-selector-receiver",
                    name: Fp()
                }),
                q("yt.mdx.remote.cloudview.castButtonShown_", !0, void 0);
            return a
        }
        return r("yt.mdx.remote.cloudview.initializing_") ? [] : Gp()
    }
    function Gp() {
        var a = []
          , a = Hp() ? qp().R.$_gos() : mk(gl())
          , b = Ip();
        b && Ep() && (Kk(a, b) || a.push(b));
        Hp() || (b = Hk(il()),
        b = Ca(b, function(b) {
            return !Lk(a, b.id)
        }),
        a = Na(a, b));
        return Fk(a)
    }
    function Jp() {
        if (Dp() && gm()) {
            var a = hm();
            return a ? {
                key: "cast-selector-receiver",
                name: a
            } : null
        }
        return Kp()
    }
    function Kp() {
        var a = Gp()
          , b = Lp()
          , c = Ip();
        c || (c = wp());
        return Fa(a, function(a) {
            return c && hk(c, a.key) || b && (a = ip(a.key)) && a.id == b ? !0 : !1
        })
    }
    function Fp() {
        if (Dp() && gm())
            return hm();
        var a = Ip();
        return a ? a.name : null
    }
    function Ip() {
        var a = rp();
        if (!a)
            return null;
        if (!Y) {
            var b = qp().X();
            return Lk(b, a)
        }
        return cp(Y, a)
    }
    function vp(a) {
        sp("remote.onCastSessionChange_: " + nk(a));
        if (a) {
            var b = Ip();
            b && b.id == a.id ? nm(b.id, "YouTube TV") : (b && Mp(),
            Np(a, 1))
        } else
            Mp()
    }
    function Op(a, b) {
        sp("Connecting to: " + R(a));
        if ("cast-selector-receiver" == a.key)
            up(b || null),
            mm(b || null);
        else {
            Mp();
            up(b || null);
            var c = null;
            Y ? c = hp(a.key) : (c = qp().X(),
            c = Lk(c, a.key));
            if (c)
                Np(c, 1);
            else {
                if (Y && (c = ip(a.key))) {
                    Pp(c);
                    return
                }
                G(function() {
                    Qp(null)
                }, 0)
            }
        }
    }
    function Mp() {
        Y && fp(Y);
        t: {
            var a = Ep();
            if (a && (a = a.getOtherConnectedRemoteId())) {
                sp("Do not stop DIAL due to " + a);
                Rp("");
                break t
            }
            (a = Lp()) ? (sp("Stopping DIAL: " + a),
            Hm(a),
            Rp("")) : (a = Ip()) && a.uuid && (sp("Stopping DIAL: " + a.uuid),
            Hm(a.uuid))
        }
        km() ? cm().stopSession() : lm("stopSession called before API ready.");
        (a = Ep()) ? a.disconnect(1) : (Ub("yt-remote-before-disconnect", 1),
        Ub("yt-remote-connection-change", !1));
        Qp(null)
    }
    function sp(a) {
        dk("remote", a)
    }
    function Dp() {
        return !!r("yt.mdx.remote.castv2_")
    }
    function Hp() {
        return r("yt.mdx.remote.screenService_")
    }
    function qp() {
        if (!Bp) {
            var a = Hp();
            Bp = a ? new jp(a) : null
        }
        return Bp
    }
    function rp() {
        return r("yt.mdx.remote.currentScreenId_")
    }
    function Sp(a) {
        q("yt.mdx.remote.currentScreenId_", a, void 0);
        if (Y) {
            var b = Y;
            b.l = w() + 3E5;
            if ((b.Qb = a) && (a = cp(b, a)) && !Kk(b.e, a)) {
                var c = Oa(b.e);
                c.push(a);
                Vo(b, c, !0)
            }
        }
    }
    function Lp() {
        return r("yt.mdx.remote.currentDialId_")
    }
    function Rp(a) {
        q("yt.mdx.remote.currentDialId_", a, void 0)
    }
    function zo() {
        return r("yt.mdx.remote.connectData_")
    }
    function up(a) {
        q("yt.mdx.remote.connectData_", a, void 0)
    }
    function Ep() {
        return r("yt.mdx.remote.connection_")
    }
    function Qp(a) {
        var b = Ep();
        up(null);
        a ? Aa(!Ep()) : (Sp(""),
        Rp(""));
        q("yt.mdx.remote.connection_", a, void 0);
        op && (A(op, function(b) {
            b(a)
        }),
        op.length = 0);
        b && !a ? Ub("yt-remote-connection-change", !1) : !b && a && I("yt-remote-connection-change", !0)
    }
    function wp() {
        var a = Xk();
        if (!a)
            return null;
        if (Hp()) {
            var b = qp().X();
            return Lk(b, a)
        }
        return Y ? cp(Y, a) : null
    }
    function Np(a, b) {
        Aa(!rp());
        Sp(a.id);
        var c = new X(lp,a,xp());
        yo(c, b);
        c.subscribe("beforeDisconnect", function(a) {
            Ub("yt-remote-before-disconnect", a)
        });
        c.subscribe("beforeDispose", function() {
            Ep() && (Ep(),
            Qp(null))
        });
        Qp(c)
    }
    function Pp(a) {
        Lp();
        sp("Connecting to: " + (a ? a.toString() : "null"));
        Rp(a.id);
        var b = zo();
        b ? gp(a.id, b.videoIds[b.index], b.currentTime) : gp(a.id)
    }
    function zp() {
        var a = wp();
        a ? (sp("Resume connection to: " + nk(a)),
        Np(a, 0)) : (kl(),
        im(),
        sp("Skipping connecting because no session screen found."))
    }
    function mp(a) {
        sp("Paired with: " + nk(a));
        a ? Np(a, 1) : Qp(null)
    }
    function np() {
        var a = rp();
        a && !Ip() && (sp("Dropping current screen with id: " + a),
        Mp());
        wp() || kl()
    }
    var lp = null
      , op = null
      , Bp = null
      , Y = null;
    function pp(a) {
        var b = xp();
        if (db(b)) {
            var b = Wk()
              , c = T("yt-remote-session-name") || ""
              , d = T("yt-remote-session-app") || ""
              , b = {
                device: "REMOTE_CONTROL",
                id: b,
                name: c,
                app: d
            };
            a && (b["mdx-version"] = 3);
            q("yt.mdx.remote.channelParams_", b, void 0)
        }
    }
    function xp() {
        return r("yt.mdx.remote.channelParams_") || {}
    }
    function yp(a) {
        a ? (S("yt-remote-session-app", a.app),
        S("yt-remote-session-name", a.name)) : (Zh("yt-remote-session-app"),
        Zh("yt-remote-session-name"));
        q("yt.mdx.remote.channelParams_", a, void 0)
    }
    var tp = [];
    var Tp = null
      , Up = [];
    function Vp() {
        Wp();
        if (Jp()) {
            var a = Tp;
            "html5" != a.getPlayerType() && a.loadNewVideoConfig(a.getCurrentVideoConfig(), "html5")
        }
    }
    function Xp(a) {
        "cast-selector-receiver" == a ? jm() : Yp(a)
    }
    function Yp(a) {
        var b = Cp();
        if (a = Ek(b, a)) {
            var c = Tp
              , d = c.getVideoData().video_id
              , e = c.getVideoData().list
              , f = c.getCurrentTime();
            Op(a, {
                videoIds: [d],
                listId: e,
                videoId: d,
                index: 0,
                currentTime: f
            });
            "html5" != c.getPlayerType() ? c.loadNewVideoConfig(c.getCurrentVideoConfig(), "html5") : c.updateRemoteReceivers && c.updateRemoteReceivers(b, a)
        }
    }
    function Wp() {
        var a = Tp;
        a && a.updateRemoteReceivers && a.updateRemoteReceivers(Cp(), Jp())
    }
    ;var Zp = null
      , $p = [];
    function aq(a) {
        return {
            externalChannelId: a.externalChannelId,
            Md: !!a.isChannelPaid,
            source: a.source,
            Ya: a.subscriptionId
        }
    }
    function bq(a) {
        cq(aq(a))
    }
    function cq(a) {
        rj() ? (P(Wi, new Ni(a.externalChannelId,a.Md ? {
            itemType: "U",
            itemId: a.externalChannelId
        } : null)),
        (a = "/gen_204?" + Ad({
            event: "subscribe",
            source: a.source
        })) && bh(a)) : dq(a)
    }
    function dq(a) {
        hj(function(b) {
            b.subscription_ajax && cq(a)
        }, null, "sub_button")
    }
    function eq(a) {
        a = aq(a);
        P(aj, new Pi(a.externalChannelId,a.Ya,null));
        (a = "/gen_204?" + Ad({
            event: "unsubscribe",
            source: a.source
        })) && bh(a)
    }
    function fq(a) {
        Zp && Zp.channelSubscribed(a.e, a.Ya)
    }
    function gq(a) {
        Zp && Zp.channelUnsubscribed(a.e)
    }
    ;function hq(a) {
        D.call(this);
        this.f = a;
        this.f.subscribe("command", this.Sc, this);
        this.h = {};
        this.k = !1
    }
    y(hq, D);
    g = hq.prototype;
    g.start = function() {
        this.k || this.F() || (this.k = !0,
        iq(this.f, "RECEIVING"))
    }
    ;
    g.Sc = function(a, b) {
        if (this.k && !this.F()) {
            var c = b || {};
            switch (a) {
            case "addEventListener":
                if (u(c.event) && (c = c.event,
                !(c in this.h))) {
                    var d = v(this.Ae, this, c);
                    this.h[c] = d;
                    this.addEventListener(c, d)
                }
                break;
            case "removeEventListener":
                u(c.event) && jq(this, c.event);
                break;
            default:
                this.e.isReady() && this.e[a] && (c = kq(a, b || {}),
                c = this.e[a].apply(this.e, c),
                (c = lq(a, c)) && this.k && !this.F() && iq(this.f, a, c))
            }
        }
    }
    ;
    g.Ae = function(a, b) {
        this.k && !this.F() && iq(this.f, a, this.Tb(a, b))
    }
    ;
    g.Tb = function(a, b) {
        if (null != b)
            return {
                value: b
            }
    }
    ;
    function jq(a, b) {
        b in a.h && (a.removeEventListener(b, a.h[b]),
        delete a.h[b])
    }
    g.B = function() {
        this.f.Na("command", this.Sc, this);
        this.f = null;
        for (var a in this.h)
            jq(this, a);
        hq.G.B.call(this)
    }
    ;
    function mq(a, b) {
        hq.call(this, b);
        this.e = a;
        this.start()
    }
    y(mq, hq);
    mq.prototype.addEventListener = function(a, b) {
        this.e.addEventListener(a, b)
    }
    ;
    mq.prototype.removeEventListener = function(a, b) {
        this.e.removeEventListener(a, b)
    }
    ;
    function kq(a, b) {
        switch (a) {
        case "loadVideoById":
            return b = Rj(b),
            Tj(b),
            [b];
        case "cueVideoById":
            return b = Rj(b),
            Tj(b),
            [b];
        case "loadVideoByPlayerVars":
            return Tj(b),
            [b];
        case "cueVideoByPlayerVars":
            return Tj(b),
            [b];
        case "loadPlaylist":
            return b = Sj(b),
            Tj(b),
            [b];
        case "cuePlaylist":
            return b = Sj(b),
            Tj(b),
            [b];
        case "seekTo":
            return [b.seconds, b.allowSeekAhead];
        case "playVideoAt":
            return [b.index];
        case "setVolume":
            return [b.volume];
        case "setPlaybackQuality":
            return [b.suggestedQuality];
        case "setPlaybackRate":
            return [b.suggestedRate];
        case "setLoop":
            return [b.loopPlaylists];
        case "setShuffle":
            return [b.shufflePlaylist];
        case "getOptions":
            return [b.module];
        case "getOption":
            return [b.module, b.option];
        case "setOption":
            return [b.module, b.option, b.value]
        }
        return []
    }
    function lq(a, b) {
        switch (a) {
        case "isMuted":
            return {
                muted: b
            };
        case "getVolume":
            return {
                volume: b
            };
        case "getPlaybackRate":
            return {
                playbackRate: b
            };
        case "getAvailablePlaybackRates":
            return {
                availablePlaybackRates: b
            };
        case "getVideoLoadedFraction":
            return {
                videoLoadedFraction: b
            };
        case "getPlayerState":
            return {
                playerState: b
            };
        case "getCurrentTime":
            return {
                currentTime: b
            };
        case "getPlaybackQuality":
            return {
                playbackQuality: b
            };
        case "getAvailableQualityLevels":
            return {
                availableQualityLevels: b
            };
        case "getDuration":
            return {
                duration: b
            };
        case "getVideoUrl":
            return {
                videoUrl: b
            };
        case "getVideoEmbedCode":
            return {
                videoEmbedCode: b
            };
        case "getPlaylist":
            return {
                playlist: b
            };
        case "getPlaylistIndex":
            return {
                playlistIndex: b
            };
        case "getOptions":
            return {
                options: b
            };
        case "getOption":
            return {
                option: b
            }
        }
    }
    mq.prototype.Tb = function(a, b) {
        switch (a) {
        case "onReady":
            return;
        case "onStateChange":
            return {
                playerState: b
            };
        case "onPlaybackQualityChange":
            return {
                playbackQuality: b
            };
        case "onPlaybackRateChange":
            return {
                playbackRate: b
            };
        case "onError":
            return {
                errorCode: b
            }
        }
        return mq.G.Tb.call(this, a, b)
    }
    ;
    mq.prototype.B = function() {
        mq.G.B.call(this);
        delete this.e
    }
    ;
    function nq(a, b) {
        this.source = null;
        this.k = a || null;
        this.origin = "*";
        this.C = window.document.location.protocol + "//" + window.document.location.hostname;
        this.l = b;
        this.h = this.e = this.f = this.j = null;
        L(window, "message", v(this.o, this))
    }
    nq.prototype.o = function(a) {
        var b = this.l || F("POST_MESSAGE_ORIGIN") || this.C;
        if ("*" != b && a.origin != b)
            window.console && window.console.warn("Untrusted origin: " + a.origin);
        else if (!this.k || a.source == this.k)
            if (this.source = a.source,
            this.origin = "null" == a.origin ? this.origin : a.origin,
            a = a.data,
            u(a)) {
                try {
                    a = Fh(a)
                } catch (c) {
                    return
                }
                this.j = a.id;
                switch (a.event) {
                case "listening":
                    this.e && (this.e(),
                    this.e = null);
                    break;
                case "command":
                    this.f && (this.h && !Ha(this.h, a.func) || this.f(a.func, a.args))
                }
            }
    }
    ;
    function oq() {
        var a = this.f = new nq
          , b = v(this.we, this);
        a.f = b;
        a.h = null;
        this.k = [];
        this.o = !1;
        this.j = (a = F("POST_MESSAGE_ORIGIN")) && Yg(a) ? a : null;
        this.l = {}
    }
    g = oq.prototype;
    g.we = function(a, b) {
        if (this.j && this.j != this.f.origin)
            this.dispose();
        else if ("addEventListener" == a && b) {
            var c = b[0];
            this.l[c] || "onReady" == c || (this.addEventListener(c, pq(this, c)),
            this.l[c] = !0)
        } else
            this.fd(a, b)
    }
    ;
    g.fd = function() {}
    ;
    function pq(a, b) {
        return v(function(a) {
            qq(this, b, a)
        }, a)
    }
    g.addEventListener = function() {}
    ;
    g.Cd = function() {
        this.o = !0;
        qq(this, "initialDelivery", this.Ub());
        qq(this, "onReady");
        A(this.k, this.gd, this);
        this.k = []
    }
    ;
    g.Ub = function() {
        return null
    }
    ;
    function rq(a, b) {
        qq(a, "infoDelivery", b)
    }
    g.gd = function(a) {
        if (this.o) {
            var b = this.f;
            b.source && (a.id = b.j,
            a = R(a),
            b.source.postMessage(a, b.origin))
        } else
            this.k.push(a)
    }
    ;
    function qq(a, b, c) {
        a.gd({
            event: b,
            info: void 0 == c ? null : c
        })
    }
    g.dispose = function() {
        this.f = null
    }
    ;
    function sq(a) {
        oq.call(this);
        this.e = a;
        this.h = [];
        this.addEventListener("onReady", v(this.ge, this));
        this.addEventListener("onVideoProgress", v(this.Ee, this));
        this.addEventListener("onVolumeChange", v(this.Fe, this));
        this.addEventListener("onApiChange", v(this.ze, this));
        this.addEventListener("onPlaybackQualityChange", v(this.Be, this));
        this.addEventListener("onPlaybackRateChange", v(this.Ce, this));
        this.addEventListener("onStateChange", v(this.De, this))
    }
    y(sq, oq);
    g = sq.prototype;
    g.fd = function(a, b) {
        if (this.e[a]) {
            b = b || [];
            if (0 < b.length && Pj(a)) {
                var c;
                c = b;
                if (ha(c[0]) && !da(c[0]))
                    c = c[0];
                else {
                    var d = {};
                    switch (a) {
                    case "loadVideoById":
                    case "cueVideoById":
                        d = Rj.apply(window, c);
                        break;
                    case "loadVideoByUrl":
                    case "cueVideoByUrl":
                        d = Qj.apply(window, c);
                        break;
                    case "loadPlaylist":
                    case "cuePlaylist":
                        d = Sj.apply(window, c)
                    }
                    c = d
                }
                Tj(c);
                b.length = 1;
                b[0] = c
            }
            this.e[a].apply(this.e, b);
            Pj(a) && rq(this, this.Ub())
        }
    }
    ;
    g.ge = function() {
        var a = v(this.Cd, this);
        this.f.e = a
    }
    ;
    g.addEventListener = function(a, b) {
        this.h.push({
            Bd: a,
            ua: b
        });
        this.e.addEventListener(a, b)
    }
    ;
    g.Ub = function() {
        if (!this.e)
            return null;
        var a = this.e.getApiInterface();
        La(a, "getVideoData");
        for (var b = {
            apiInterface: a
        }, c = 0, d = a.length; c < d; c++) {
            var e = a[c]
              , f = e;
            if (0 == f.search("get") || 0 == f.search("is")) {
                var f = e
                  , h = 0;
                0 == f.search("get") ? h = 3 : 0 == f.search("is") && (h = 2);
                f = f.charAt(h).toLowerCase() + f.substr(h + 1);
                try {
                    var k = this.e[e]();
                    b[f] = k
                } catch (l) {}
            }
        }
        b.videoData = this.e.getVideoData();
        return b
    }
    ;
    g.De = function(a) {
        a = {
            playerState: a,
            currentTime: this.e.getCurrentTime(),
            duration: this.e.getDuration(),
            videoData: this.e.getVideoData(),
            videoStartBytes: 0,
            videoBytesTotal: this.e.getVideoBytesTotal(),
            videoLoadedFraction: this.e.getVideoLoadedFraction(),
            playbackQuality: this.e.getPlaybackQuality(),
            availableQualityLevels: this.e.getAvailableQualityLevels(),
            videoUrl: this.e.getVideoUrl(),
            playlist: this.e.getPlaylist(),
            playlistIndex: this.e.getPlaylistIndex()
        };
        this.e.getProgressState && (a.progressState = this.e.getProgressState());
        this.e.getStoryboardFormat && (a.storyboardFormat = this.e.getStoryboardFormat());
        rq(this, a)
    }
    ;
    g.Be = function(a) {
        rq(this, {
            playbackQuality: a
        })
    }
    ;
    g.Ce = function(a) {
        rq(this, {
            playbackRate: a
        })
    }
    ;
    g.ze = function() {
        for (var a = this.e.getOptions(), b = {
            namespaces: a
        }, c = 0, d = a.length; c < d; c++) {
            var e = a[c]
              , f = this.e.getOptions(e);
            b[e] = {
                options: f
            };
            for (var h = 0, k = f.length; h < k; h++) {
                var l = f[h]
                  , n = this.e.getOption(e, l);
                b[e][l] = n
            }
        }
        qq(this, "apiInfoDelivery", b)
    }
    ;
    g.Fe = function() {
        rq(this, {
            muted: this.e.isMuted(),
            volume: this.e.getVolume()
        })
    }
    ;
    g.Ee = function(a) {
        a = {
            currentTime: a,
            videoBytesLoaded: this.e.getVideoBytesLoaded(),
            videoLoadedFraction: this.e.getVideoLoadedFraction()
        };
        this.e.getProgressState && (a.progressState = this.e.getProgressState());
        rq(this, a)
    }
    ;
    g.dispose = function() {
        sq.G.dispose.call(this);
        for (var a = 0; a < this.h.length; a++) {
            var b = this.h[a];
            this.e.removeEventListener(b.Bd, b.ua)
        }
        this.h = []
    }
    ;
    function tq(a, b, c) {
        U.call(this);
        this.e = a;
        this.f = b;
        this.h = c
    }
    y(tq, U);
    function iq(a, b, c) {
        if (!a.F()) {
            var d = a.e;
            d.F() || a.f != d.e || (a = {
                id: a.h,
                command: b
            },
            c && (a.data = c),
            d.e.postMessage(R(a), d.h))
        }
    }
    tq.prototype.B = function() {
        this.f = this.e = null;
        tq.G.B.call(this)
    }
    ;
    function uq(a, b, c) {
        D.call(this);
        this.e = a;
        this.h = c;
        this.k = L(window, "message", v(this.j, this));
        this.f = new tq(this,a,b);
        Ab(this, oa(Bb, this.f))
    }
    y(uq, D);
    uq.prototype.j = function(a) {
        if (!this.F() && a.origin == this.h && a.source == this.e && (a = a.data,
        u(a))) {
            try {
                a = Fh(a)
            } catch (b) {
                return
            }
            if (a.command) {
                var c = this.f;
                c.F() || c.A("command", a.command, a.data)
            }
        }
    }
    ;
    uq.prototype.B = function() {
        gd(this.k);
        this.e = null;
        uq.G.B.call(this)
    }
    ;
    var vq = {};
    function wq(a) {
        return a ? 24 == a.length && "UC" == a.slice(0, 2) ? a.substr(2) : 22 == a.length ? a : null : null
    }
    ;var xq = []
      , yq = [];
    function zq(a, b) {
        if ("view" != a && "cvisit" != a) {
            if (!b) {
                var c = F("CONVERSION_CONFIG_DICT");
                if (!c)
                    return;
                b = c.uid || null;
                if (!b)
                    return
            }
            if ("subscribe" == a || "unsubscribe" == a) {
                if (u(b)) {
                    var d = wq(b);
                    d && (d = {
                        label: "followon_" + a,
                        foc_id: d,
                        r: Math.round(1E4 * Math.random())
                    },
                    (d = Cd("//googleads.g.doubleclick.net/pagead/viewthroughconversion/962985656/", d)) && d && bh(d))
                }
            } else
                t: {
                    c = F("CONVERSION_CONFIG_DICT");
                    if (u(b)) {
                        var e = wq(b);
                        if (!c || c.uid != e)
                            if (c = vq[e],
                            !c || c.uid != e)
                                break t
                    }
                    if (a && c && c.baseUrl && c.uid) {
                        var f = c.rmktEnabled
                          , e = c.focEnabled && (!c.isAd || "view" != a);
                        if (f || e) {
                            var h = {};
                            if (f) {
                                f = {
                                    utuid: c.uid,
                                    type: a,
                                    client_name: "html5"
                                };
                                "cvisit" == a && (f.type = "cview");
                                c.vid && (f.utvid = c.vid);
                                c.eventLabel && (f.el = c.eventLabel);
                                c.playerStyle && (f.ps = c.playerStyle);
                                c.feature && (f.feature = c.feature);
                                c.ppe && (f.ppe = c.ppe);
                                c.subscribed && (f.subscribed = c.subscribed);
                                c.engaged && (f.engaged = c.engaged);
                                var k = [];
                                for (d in f)
                                    k.push(encodeURIComponent(d) + "=" + encodeURIComponent(f[d]));
                                d = k.join(";");
                                h.data = d
                            }
                            e && (h.label = "followon_" + a,
                            h.foc_id = c.uid,
                            h.r = Math.round(1E4 * Math.random()));
                            if ("unsubscribe" == a || "dislike" == a)
                                h.r = Math.round(1E4 * Math.random());
                            d = Cd(c.baseUrl, h)
                        } else
                            d = null
                    } else
                        d = null;
                    d && d && bh(d)
                }
        }
    }
    function Aq(a) {
        zq("subscribe", a.e)
    }
    function Bq(a) {
        zq("unsubscribe", a.e)
    }
    ;function Cq(a) {
        N.call(this, 1, arguments)
    }
    y(Cq, N);
    function Dq(a, b) {
        N.call(this, 2, arguments);
        this.f = a;
        this.e = b
    }
    y(Dq, N);
    function Eq(a, b, c, d) {
        N.call(this, 1, arguments);
        this.e = b;
        this.h = c || null;
        this.f = d || null
    }
    y(Eq, N);
    function Fq(a, b) {
        N.call(this, 1, arguments);
        this.f = a;
        this.e = b || null
    }
    y(Fq, N);
    function Gq(a) {
        N.call(this, 1, arguments)
    }
    y(Gq, N);
    var Hq = new O("ypc-core-load",Cq)
      , Iq = new O("ypc-guide-sync-success",Dq)
      , Jq = new O("ypc-purchase-success",Eq)
      , Kq = new O("ypc-subscription-cancel",Gq)
      , Lq = new O("ypc-subscription-cancel-success",Fq)
      , Mq = new O("ypc-init-subscription",Gq);
    var Nq = !1
      , Oq = []
      , Pq = [];
    function Qq(a) {
        a.e ? Nq ? P($i, a) : P(Hq, new Cq(function() {
            P(Mq, new Gq(a.e))
        }
        )) : Rq(a.f, a.k, a.h, a.source)
    }
    function Sq(a) {
        a.e ? Nq ? P(ej, a) : P(Hq, new Cq(function() {
            P(Kq, new Gq(a.e))
        }
        )) : Tq(a.f, a.Ya, a.k, a.h, a.source)
    }
    function Uq(a) {
        Vq(Oa(a.e))
    }
    function Wq(a) {
        Xq(Oa(a.e))
    }
    function Yq(a) {
        Zq(a.e, a.isEnabled, null, null)
    }
    function $q(a) {
        Zq(a.e, null, null, a.isEnabled)
    }
    function ar(a) {
        br(a.f, a.isEnabled, null, a.e)
    }
    function cr(a) {
        br(a.f, null, a.isEnabled, a.e)
    }
    function dr(a, b, c, d, e) {
        Zq(a, b, c, d, e)
    }
    function er(a) {
        var b = a.f
          , c = a.e.subscriptionId;
        b && c && P(Zi, new Oi(b,c,a.e.channelInfo))
    }
    function fr(a) {
        var b = a.e;
        Va(a.f, function(a, d) {
            P(Zi, new Oi(d,a,b[d]))
        })
    }
    function gr(a) {
        P(dj, new Ki(a.f.itemId));
        a.e && a.e.length && (hr(a.e, dj),
        hr(a.e, fj))
    }
    function Rq(a, b, c, d) {
        var e = new Ki(a);
        P(Xi, e);
        var f = {};
        f.c = a;
        c && (f.eurl = c);
        d && (f.source = d);
        c = {};
        (d = F("PLAYBACK_ID")) && (c.plid = d);
        b && ir("/subscription_ajax?action_create_subscription_to_channel=1", f, b);
        nj("/subscription_ajax?action_create_subscription_to_channel=1", {
            method: "POST",
            jc: f,
            O: c,
            Z: function(b, c) {
                var d = c.response;
                P(Zi, new Oi(a,d.id,d.channel_info));
                d.show_feed_privacy_dialog && I("SHOW-FEED-PRIVACY-SUBSCRIBE-DIALOG", a)
            },
            cc: function() {
                P(Yi, e)
            }
        })
    }
    function Tq(a, b, c, d, e) {
        var f = new Ki(a);
        P(bj, f);
        var h = {};
        d && (h.eurl = d);
        e && (h.source = e);
        d = {};
        d.c = a;
        d.s = b;
        (a = F("PLAYBACK_ID")) && (d.plid = a);
        c && ir("/subscription_ajax?action_remove_subscriptions=1", {}, c);
        nj("/subscription_ajax?action_remove_subscriptions=1", {
            method: "POST",
            jc: h,
            O: d,
            Z: function() {
                P(dj, f)
            },
            cc: function() {
                P(cj, f)
            }
        })
    }
    function Zq(a, b, c, d, e) {
        if (null !== b || null !== c || null !== d) {
            var f = {};
            a && (f.channel_id = a);
            null === b || (f.email_on_upload = b);
            null === c || (f.receive_no_updates = c);
            null === d || (f.uploads_only = d);
            nj("/subscription_ajax?action_update_subscription_preferences=1", {
                method: "POST",
                O: f,
                onError: function() {
                    e && e()
                }
            })
        }
    }
    function Vq(a) {
        if (a.length) {
            var b = Qa(a, 0, 40);
            P("subscription-batch-subscribe-loading");
            hr(b, Xi);
            var c = {};
            c.a = b.join(",");
            var d = function() {
                P("subscription-batch-subscribe-loaded");
                hr(b, Yi)
            };
            nj("/subscription_ajax?action_create_subscription_to_all=1", {
                method: "POST",
                O: c,
                Z: function(c, f) {
                    d();
                    var h = f.response
                      , k = h.id;
                    if (da(k) && k.length == b.length) {
                        var l = h.channel_info_map;
                        A(k, function(a, c) {
                            var d = b[c];
                            P(Zi, new Oi(d,a,l[d]))
                        });
                        a.length ? Vq(a) : P("subscription-batch-subscribe-finished")
                    }
                },
                onError: function() {
                    d();
                    P("subscription-batch-subscribe-failure")
                }
            })
        }
    }
    function Xq(a) {
        if (a.length) {
            var b = Qa(a, 0, 40);
            P("subscription-batch-unsubscribe-loading");
            hr(b, bj);
            var c = {};
            c.c = b.join(",");
            var d = function() {
                P("subscription-batch-unsubscribe-loaded");
                hr(b, cj)
            };
            nj("/subscription_ajax?action_remove_subscriptions=1", {
                method: "POST",
                O: c,
                Z: function() {
                    d();
                    hr(b, dj);
                    a.length && Xq(a)
                },
                onError: function() {
                    d()
                }
            })
        }
    }
    function br(a, b, c, d) {
        if (a.length && (null !== b || null !== c)) {
            var e = Qa(a, 0, 40);
            I("subscription-batch-prefs-loading", e);
            var f = {};
            f.s = e.join(",");
            null !== b && (f.email_on_upload = b,
            f.receive_no_updates = !b);
            null === c || (f.uploads_only = c);
            var h = function() {
                I("subscription-batch-prefs-loaded", e)
            };
            nj("/subscription_ajax?action_update_subscription_preferences_batch=1", {
                method: "POST",
                O: f,
                Z: function() {
                    h();
                    I("subscription-batch-prefs-success", e);
                    a.length && br(a, b, c, d)
                },
                onError: function() {
                    h();
                    d && d();
                    I("subscription-batch-prefs-failure", e)
                }
            })
        }
    }
    function hr(a, b) {
        A(a, function(a) {
            P(b, new Ki(a))
        })
    }
    function ir(a, b, c) {
        a = Fd(a, b);
        c = Dd(c);
        Hd(a, c)
    }
    ;var jr = null
      , kr = null
      , lr = null
      , mr = !1;
    var nr = {}
      , or = 0;
    q("yt.setConfig", Fb, void 0);
    q("yt.setMsg", function(a) {
        Gb(Eb, arguments)
    }, void 0);
    q("yt.www.errors.log", function(a, b) {
        if (a && window && window.yterr && !(5 <= or)) {
            var c = a.stacktrace
              , d = a.columnNumber;
            var e = a
              , f = r("window.location.href");
            if (u(e))
                a = {
                    message: e,
                    name: "Unknown error",
                    lineNumber: "Not available",
                    fileName: f,
                    stack: "Not available"
                };
            else {
                var h, k, l = !1;
                try {
                    h = e.lineNumber || e.line || "Not available"
                } catch (n) {
                    h = "Not available",
                    l = !0
                }
                try {
                    k = e.fileName || e.filename || e.sourceURL || m.$googDebugFname || f
                } catch (x) {
                    k = "Not available",
                    l = !0
                }
                a = !l && e.lineNumber && e.fileName && e.stack && e.message && e.name ? e : {
                    message: e.message || "Not available",
                    name: e.name || "UnknownError",
                    lineNumber: h,
                    fileName: k,
                    stack: e.stack || "Not available"
                }
            }
            c = c || a.stack;
            e = a.lineNumber.toString();
            isNaN(e) || isNaN(d) || (e = e + ":" + d);
            nr[a.message] || (d = {
                jc: {
                    a: "logerror",
                    t: "jserror",
                    type: a.name,
                    msg: a.message.substr(0, 1E3),
                    line: e,
                    level: b || "ERROR"
                },
                O: {
                    url: window.location.href,
                    file: a.fileName
                },
                method: "POST"
            },
            c && (d.O.stack = c),
            nj("/gen_204", d),
            nr[a.message] = !0,
            or++)
        }
    }, void 0);
    q("yt.embed.openLoginDialog", function() {
        hj(function(a) {
            if (jr.onLoginDialogSuccess)
                jr.onLoginDialogSuccess(a)
        })
    }, void 0);
    q("writeEmbed", function() {
        var a = new zf(F("PLAYER_CONFIG"))
          , b = document.referrer
          , c = F("POST_MESSAGE_ORIGIN")
          , d = !1;
        u(b) && u(c) && -1 < b.indexOf(c) && Yg(c) && Yg(b) && (d = !0);
        window != window.top && b && b != document.URL && (a.args.loaderUrl = b);
        F("LIGHTWEIGHT_AUTOPLAY") && (a.args.autoplay = "1");
        a.args.autoplay && Tj(a.args);
        jr = si("player", a);
        b = F("POST_MESSAGE_ID", "player");
        F("ENABLE_JS_API") ? lr = new sq(jr) : F("ENABLE_POST_API") && u(b) && u(c) && (kr = new uq(window.parent,b,c),
        lr = new mq(jr,kr.f));
        (mr = d && !F("ENABLE_CAST_API")) ? a.args.disableCast = "1" : (a = jr,
        kp(),
        Tp = a,
        Tp.addEventListener("onReady", Vp),
        Tp.addEventListener("onRemoteReceiverSelected", Xp),
        Up.push(Rb("yt-remote-receiver-availability-change", Wp)),
        Up.push(Rb("yt-remote-auto-connect", Yp)));
        F("BG_P") && (F("BG_I") || F("BG_IU")) && ec();
        Zp = jr;
        Zp.addEventListener("SUBSCRIBE", bq);
        Zp.addEventListener("UNSUBSCRIBE", eq);
        $p.push(kh(Zi, fq), kh(dj, gq))
    }, void 0);
    q("yt.www.watch.ads.restrictioncookie.spr", function(a) {
        (a = a + "mac_204?action_fcts=1") && bh(a);
        return !0
    }, void 0);
    L(window, "load", function() {
        qh("ol");
        F("CSI_LOG_ON_TICK") || sh();
        Nq = !0;
        Pq.push(kh(Wi, Qq), kh(aj, Sq));
        Nq || (Pq.push(kh($i, Qq), kh(ej, Sq), kh(Si, Uq), kh(Ti, Wq), kh(Ui, Yq), kh(Vi, $q), kh(Qi, ar), kh(Ri, cr)),
        Oq.push(Rb("subscription-prefs", dr)),
        Pq.push(kh(Jq, er), kh(Lq, gr), kh(Iq, fr)),
        xq.push(Rb("player-subscribe", oa(zq, "subscribe")), Rb("player-unsubscribe", oa(zq, "unsubscribe"))),
        yq.push(kh(Zi, Aq), kh(dj, Bq)))
    });
    L(window, "unload", function() {
        var a = jr;
        a && a.sendAbandonmentPing && a.sendAbandonmentPing();
        F("PL_ATT") && (dc = null);
        Tb(Oq);
        Oq.length = 0;
        lh(Pq);
        Pq.length = 0;
        Nq = !1;
        Tb(xq);
        xq.length = 0;
        lh(yq);
        yq.length = 0;
        Zp && (Zp.removeEventListener("SUBSCRIBE", cq),
        Zp.removeEventListener("UNSUBSCRIBE", eq));
        Zp = null;
        lh($p);
        $p.length = 0;
        mr || (Tb(Up),
        Up.length = 0,
        Tp && (Tp.removeEventListener("onRemoteReceiverSelected", Xp),
        Tp.removeEventListener("onReady", Vp),
        Tp = null),
        Ap());
        Cb(lr, kr);
        jr && jr.destroy()
    });
    var pr = Jj.getInstance()
      , qr = xj(pr);
    qr in Oj || (pr.register(),
    pr.Rc.push(Rb("yt-uix-init-" + qr, pr.init, pr)),
    pr.Rc.push(Rb("yt-uix-dispose-" + qr, pr.dispose, pr)),
    Oj[qr] = pr);
}
)();
