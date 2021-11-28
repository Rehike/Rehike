var ytcsi = {
    gt: function(n) {
        n = (n || '') + 'data_';
        return ytcsi[n] || (ytcsi[n] = {
            tick: {},
            info: {}
        });
    },
    now: window.performance && window.performance.timing && window.performance.now ? function() {
        return window.performance.timing.navigationStart + window.performance.now();
    } : function() {
        return (new Date()).getTime();
    },
    tick: function(l, t, n) {
        ticks = ytcsi.gt(n).tick;
        var v = t || ytcsi.now();
        if (ticks[l]) {
            ticks['_' + l] = (ticks['_' + l] || [ticks[l]]);
            ticks['_' + l].push(v);
        }
        ticks[l] = v;
    },
    info: function(k, v, n) {
        ytcsi.gt(n).info[k] = v;
    },
    setStart: function(s, t, n) {
        ytcsi.info('yt_sts', s, n);
        ytcsi.tick('_start', t, n);
    }
};
(function(w, d) {
    ytcsi.setStart('dhs', w.performance ? w.performance.timing.responseStart : null);
    var isPrerender = (d.visibilityState || d.webkitVisibilityState) == 'prerender';
    var vName = (!d.visibilityState && d.webkitVisibilityState) ? 'webkitvisibilitychange' : 'visibilitychange';
    if (isPrerender) {
        ytcsi.info('prerender', 1);
        var startTick = function() {
            ytcsi.setStart('dhs');
            d.removeEventListener(vName, startTick);
        };
        d.addEventListener(vName, startTick, false);
    }
    if (d.addEventListener) {
        d.addEventListener(vName, function() {
            ytcsi.tick('vc');
        }, false);
    }
    var slt = function(el, t) {
        setTimeout(function() {
            var n = ytcsi.now();
            el.loadTime = n;
            if (el.slt) {
                el.slt();
            }
        }, t);
    };
    w.__ytRIL = function(el) {
        if (!el.getAttribute('data-thumb')) {
            if (w.requestAnimationFrame) {
                w.requestAnimationFrame(function() {
                    slt(el, 0);
                });
            } else {
                slt(el, 16);
            }
        }
    };
})(window, document);     