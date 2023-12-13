/**
 * Modified from https://s.ytimg.com/yts/jsbin/www-en_US-vfl2g6bso/base.js
 * 
 * Current changes:
 * 
 * - Patched qn @ "qn=function(a,b,c)"... to support newer player
 *   versions.
 */
var _yt_www={};(function(g){var window=this;/*

 Copyright The Closure Library Authors.
 SPDX-License-Identifier: Apache-2.0
*/
var ca,da,ha,ia,pa,qa,ra,za,wa,xa,Ca,Da,Ea,Fa,Sa,Ta,Wa,Za,jb,Nb,ec,fc,gc,Oc,Pc,Qc,Rc,Sc,Tc,Uc,Vc,Xc,$c,jd,ld,md,kd,nd,od,pd,qd,rd,yd,Hd,Id,Jd,Md,ke,le,ne,te,ue,ve,we,xe,ye,ze,Ae,De,Ee,Ge,Fe,Ie,Ke,Le,Ne,Me,Pe,Re,Se,Te,Ve,Xe,Ze,bf,$e,af,ef,cf,df,ff,mf,nf,of,pf,qf,uf,xf,zf,Hf,Gf,Bf,Kf,Lf,Mf,Nf,Of,Pf,Qf,Vf,Sf,Wf,bg,jg,hg,eg,pg,mg,kg,lg,qg,sg,xg,yg,zg,Bg,Dg,Eg,Fg,Gg,Ig,Kg,Ng,Tg,Sg,Vg,$g,bh,ch,dh,gh,hh,lh,nh,oh,ph,qh,sh,vh,wh,Fh,Hh,Lh,Zh,ei,$h,hi,li,vi,Hi,Gi,Ri,Ui,cj,fj,hj,kj,ij,jj,mj,qj,oj,lj,sj,tj,uj,
Bj,Cj,Dj,Ej,Qj,Hj,Uj,Vj,Wj,Xj,Yj,ak,bk,Zj,ck,dk,ek,fk,ik,jk,kk,lk,qk,pk,rk,sk,uk,tk,mk,ok,wk,xk,yk,zk,vk,Ak,Bk,Ck,Dk,Ek,Gk,Hk,Ik,Jk,Kk,Lk,Pk,Tk,Vk,Wk,Xk,Yk,$k,Zk,al,bl,cl,dl,el,fl,gl,il,ml,jl,ll,kl,nl,pl,ol,sl,tl,wl,yl,xl,Il,wg,Hl,Ml,Ll,Ol,Kl,Nl,Vl,Wl,Xl,Zl,$l,am,fm,gm,hm,im,jm,lm,pm,qm,rm,tm,vm,ym,Nj,Em,Mm,Pm,Rm,Sm,Tm,fn,dn,gn,kn,en,hn,ln,pn,qn,cn,rn,sn,mn,on,jn,nn,vn,zn,An,Bn,Dn,Gn,Hn,Fn,In,Jn,Kn,Mn,Nn,Cn,On,Ln,Rn,Sn,Tn,Vn,Un,Wn,Xn,Zn,$n,bo,ho,ao,io,ko,vo,oo,lo,yo,xo,wo,mo,zo,Bo,so,po,qo,Mo,Lo,
Ko,Fo,Jo,Go,Ho,Ro,So,Uo,Zo,$o,Xo,cp,ap,dp,fp,gp,hp,ip,jp,pp,up,vp,xp,Bp,Cp,Ep,Fp,rp,np,Dp,tp,Ip,Jp,Kp,fa,ea,Ia,Ga,Oa,Qa;g.ba=function(a){return function(){return g.aa[a].apply(this,arguments)}};
ca=function(a){var b=0;return function(){return b<a.length?{done:!1,value:a[b++]}:{done:!0}}};
da=function(a){a=["object"==typeof globalThis&&globalThis,a,"object"==typeof window&&window,"object"==typeof self&&self,"object"==typeof global&&global];for(var b=0;b<a.length;++b){var c=a[b];if(c&&c.Math==Math)return c}throw Error("Cannot find global object");};
ha=function(a,b){if(b)a:{for(var c=ea,d=a.split("."),e=0;e<d.length-1;e++){var f=d[e];if(!(f in c))break a;c=c[f]}d=d[d.length-1];e=c[d];f=b(e);f!=e&&null!=f&&fa(c,d,{configurable:!0,writable:!0,value:f})}};
ia=function(a){a={next:a};a[Symbol.iterator]=function(){return this};
return a};
g.ja=function(a){var b="undefined"!=typeof Symbol&&Symbol.iterator&&a[Symbol.iterator];return b?b.call(a):{next:ca(a)}};
g.ka=function(a){for(var b,c=[];!(b=a.next()).done;)c.push(b.value);return c};
g.ma=function(a){return a instanceof Array?a:g.ka(g.ja(a))};
g.p=function(a,b){a.prototype=na(b.prototype);a.prototype.constructor=a;if(oa)oa(a,b);else for(var c in b)if("prototype"!=c)if(Object.defineProperties){var d=Object.getOwnPropertyDescriptor(b,c);d&&Object.defineProperty(a,c,d)}else a[c]=b[c];a.T=b.prototype};
pa=function(){this.F=!1;this.D=null;this.l=void 0;this.i=1;this.H=this.C=0;this.R=this.j=null};
qa=function(a){if(a.F)throw new TypeError("Generator is already running");a.F=!0};
ra=function(a,b){a.j={al:b,nm:!0};a.i=a.C||a.H};
g.sa=function(a,b,c){a.i=c;return{value:b}};
g.ta=function(a,b,c){a.C=b;void 0!=c&&(a.H=c)};
g.ua=function(a){a.C=0;var b=a.j.al;a.j=null;return b};
g.va=function(a){this.i=new pa;this.l=a};
za=function(a,b){qa(a.i);var c=a.i.D;if(c)return wa(a,"return"in c?c["return"]:function(d){return{value:d,done:!0}},b,a.i["return"]);
a.i["return"](b);return xa(a)};
wa=function(a,b,c,d){try{var e=b.call(a.i.D,c);if(!(e instanceof Object))throw new TypeError("Iterator result "+e+" is not an object");if(!e.done)return a.i.F=!1,e;var f=e.value}catch(k){return a.i.D=null,ra(a.i,k),xa(a)}a.i.D=null;d.call(a.i,f);return xa(a)};
xa=function(a){for(;a.i.i;)try{var b=a.l(a.i);if(b)return a.i.F=!1,{value:b.value,done:!1}}catch(c){a.i.l=void 0,ra(a.i,c)}a.i.F=!1;if(a.i.j){b=a.i.j;a.i.j=null;if(b.nm)throw b.al;return{value:b["return"],done:!0}}return{value:void 0,done:!0}};
g.Aa=function(a){this.next=function(b){qa(a.i);a.i.D?b=wa(a,a.i.D.next,b,a.i.M):(a.i.M(b),b=xa(a));return b};
this["throw"]=function(b){qa(a.i);a.i.D?b=wa(a,a.i.D["throw"],b,a.i.M):(ra(a.i,b),b=xa(a));return b};
this["return"]=function(b){return za(a,b)};
this[Symbol.iterator]=function(){return this}};
g.Ba=function(a,b){var c=new g.Aa(new g.va(b));oa&&a.prototype&&oa(c,a.prototype);return c};
Ca=function(a,b,c){if(null==a)throw new TypeError("The 'this' value for String.prototype."+c+" must not be null or undefined");if(b instanceof RegExp)throw new TypeError("First argument to String.prototype."+c+" must not be a regular expression");return a+""};
Da=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)};
Ea=function(a,b){a instanceof String&&(a+="");var c=0,d=!1,e={next:function(){if(!d&&c<a.length){var f=c++;return{value:b(f,a[f]),done:!1}}d=!0;return{done:!0,value:void 0}}};
e[Symbol.iterator]=function(){return e};
return e};
g.r=function(a,b,c){a=a.split(".");c=c||g.q;a[0]in c||"undefined"==typeof c.execScript||c.execScript("var "+a[0]);for(var d;a.length&&(d=a.shift());)a.length||void 0===b?c[d]&&c[d]!==Object.prototype[d]?c=c[d]:c=c[d]={}:c[d]=b};
g.Ha=function(a){if(a&&a!=g.q)return Fa(a.document);null===Ga&&(Ga=Fa(g.q.document));return Ga};
Fa=function(a){return(a=a.querySelector&&a.querySelector("script[nonce]"))&&(a=a.nonce||a.getAttribute("nonce"))&&Ia.test(a)?a:""};
g.u=function(a,b){for(var c=a.split("."),d=b||g.q,e=0;e<c.length;e++)if(d=d[c[e]],null==d)return null;return d};
g.Ja=function(){};
g.Ka=function(a){a.Bb=void 0;a.getInstance=function(){return a.Bb?a.Bb:a.Bb=new a}};
g.La=function(a){var b=typeof a;return"object"!=b?b:a?Array.isArray(a)?"array":b:"null"};
g.Ma=function(a){var b=g.La(a);return"array"==b||"object"==b&&"number"==typeof a.length};
g.Na=function(a){var b=typeof a;return"object"==b&&null!=a||"function"==b};
g.Ra=function(a){return Object.prototype.hasOwnProperty.call(a,Oa)&&a[Oa]||(a[Oa]=++Qa)};
Sa=function(a,b,c){return a.call.apply(a.bind,arguments)};
Ta=function(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var e=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(e,d);return a.apply(b,e)}}return function(){return a.apply(b,arguments)}};
g.v=function(a,b,c){Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?g.v=Sa:g.v=Ta;return g.v.apply(null,arguments)};
g.Ua=function(a,b){var c=Array.prototype.slice.call(arguments,1);return function(){var d=c.slice();d.push.apply(d,arguments);return a.apply(this,d)}};
g.Va=function(){return Date.now()};
g.y=function(a,b){function c(){}
c.prototype=b.prototype;a.T=b.prototype;a.prototype=new c;a.prototype.constructor=a;a.gJ=function(d,e,f){for(var k=Array(arguments.length-2),l=2;l<arguments.length;l++)k[l-2]=arguments[l];return b.prototype[e].apply(d,k)}};
Wa=function(a){return a};
g.Xa=function(a){var b=null,c=g.q.trustedTypes;if(!c||!c.createPolicy)return b;try{b=c.createPolicy(a,{createHTML:Wa,createScript:Wa,createScriptURL:Wa})}catch(d){g.q.console&&g.q.console.error(d.message)}return b};
g.Ya=function(a){if(Error.captureStackTrace)Error.captureStackTrace(this,g.Ya);else{var b=Error().stack;b&&(this.stack=b)}a&&(this.message=String(a))};
Za=function(a){a=a.url;var b=/[?&]dsh=1(&|$)/.test(a);this.j=!b&&/[?&]ae=1(&|$)/.test(a);this.C=!b&&/[?&]ae=2(&|$)/.test(a);if((this.i=/[?&]adurl=([^&]*)/.exec(a))&&this.i[1]){try{var c=decodeURIComponent(this.i[1])}catch(d){c=null}this.l=c}};
g.$a=function(a){var b=!1,c;return function(){b||(c=a(),b=!0);return c}};
g.bb=function(a,b,c){b=g.ab(a,b,c);return 0>b?null:"string"===typeof a?a.charAt(b):a[b]};
g.ab=function(a,b,c){for(var d=a.length,e="string"===typeof a?a.split(""):a,f=0;f<d;f++)if(f in e&&b.call(c,e[f],f,a))return f;return-1};
g.db=function(a,b){return 0<=(0,g.cb)(a,b)};
g.fb=function(a,b){var c=(0,g.cb)(a,b),d;(d=0<=c)&&g.eb(a,c);return d};
g.eb=function(a,b){Array.prototype.splice.call(a,b,1)};
g.gb=function(a){var b=a.length;if(0<b){for(var c=Array(b),d=0;d<b;d++)c[d]=a[d];return c}return[]};
g.hb=function(a,b){for(var c=1;c<arguments.length;c++){var d=arguments[c];if(g.Ma(d)){var e=a.length||0,f=d.length||0;a.length=e+f;for(var k=0;k<f;k++)a[e+k]=d[k]}else a.push(d)}};
g.ib=function(a,b,c){for(var d in a)b.call(c,a[d],d,a)};
jb=function(a,b){for(var c in a)if(b.call(void 0,a[c],c,a))return!0;return!1};
g.kb=function(a,b){for(var c in a)if(b.call(void 0,a[c],c,a))return c};
g.lb=function(a){for(var b in a)return!1;return!0};
g.mb=function(a){for(var b in a)delete a[b]};
g.nb=function(a,b){b in a&&delete a[b]};
g.ob=function(a,b,c){if(null!==a&&b in a)throw Error('The object already contains the key "'+b+'"');a[b]=c};
g.pb=function(a,b){for(var c in a)if(!(c in b)||a[c]!==b[c])return!1;for(var d in b)if(!(d in a))return!1;return!0};
g.qb=function(a){var b={},c;for(c in a)b[c]=a[c];return b};
g.rb=function(a){if(!a||"object"!==typeof a)return a;if("function"===typeof a.clone)return a.clone();var b=Array.isArray(a)?[]:"function"!==typeof ArrayBuffer||"function"!==typeof ArrayBuffer.isView||!ArrayBuffer.isView(a)||a instanceof DataView?{}:new a.constructor(a.length),c;for(c in a)b[c]=g.rb(a[c]);return b};
g.tb=function(a,b){for(var c,d,e=1;e<arguments.length;e++){d=arguments[e];for(c in d)a[c]=d[c];for(var f=0;f<sb.length;f++)c=sb[f],Object.prototype.hasOwnProperty.call(d,c)&&(a[c]=d[c])}};
g.vb=function(){void 0===ub&&(ub=g.Xa("goog#html"));return ub};
g.yb=function(a,b){this.i=a===wb&&b||"";this.j=xb};
g.zb=function(a){return a instanceof g.yb&&a.constructor===g.yb&&a.j===xb?a.i:"type_error:Const"};
g.Ab=function(a){return new g.yb(wb,a)};
g.Bb=function(a,b){return 0==a.lastIndexOf(b,0)};
g.Cb=function(a,b){var c=a.length-b.length;return 0<=c&&a.indexOf(b,c)==c};
g.Db=function(a){return/^[\s\xa0]*$/.test(a)};
g.Lb=function(a,b){if(b)a=a.replace(Eb,"&amp;").replace(Fb,"&lt;").replace(Gb,"&gt;").replace(Hb,"&quot;").replace(Ib,"&#39;").replace(Jb,"&#0;");else{if(!Kb.test(a))return a;-1!=a.indexOf("&")&&(a=a.replace(Eb,"&amp;"));-1!=a.indexOf("<")&&(a=a.replace(Fb,"&lt;"));-1!=a.indexOf(">")&&(a=a.replace(Gb,"&gt;"));-1!=a.indexOf('"')&&(a=a.replace(Hb,"&quot;"));-1!=a.indexOf("'")&&(a=a.replace(Ib,"&#39;"));-1!=a.indexOf("\x00")&&(a=a.replace(Jb,"&#0;"))}return a};
g.Ob=function(a,b){for(var c=0,d=(0,g.Mb)(String(a)).split("."),e=(0,g.Mb)(String(b)).split("."),f=Math.max(d.length,e.length),k=0;0==c&&k<f;k++){var l=d[k]||"",m=e[k]||"";do{l=/(\d*)(\D*)(.*)/.exec(l)||["","","",""];m=/(\d*)(\D*)(.*)/.exec(m)||["","","",""];if(0==l[0].length&&0==m[0].length)break;c=Nb(0==l[1].length?0:parseInt(l[1],10),0==m[1].length?0:parseInt(m[1],10))||Nb(0==l[2].length,0==m[2].length)||Nb(l[2],m[2]);l=l[3];m=m[3]}while(0==c)}return c};
Nb=function(a,b){return a<b?-1:a>b?1:0};
g.Qb=function(a,b){this.j=b===Pb?a:""};
g.Rb=function(a){if(a instanceof g.Qb&&a.constructor===g.Qb)return a.j;g.La(a);return"type_error:SafeUrl"};
g.Vb=function(a){a=String(a);a=a.replace(/(%0A|%0D)/g,"");var b=a.match(Sb);return b&&Tb.test(b[1])?g.Ub(a):null};
g.Yb=function(a){a instanceof g.Qb||(a="object"==typeof a&&a.Gc?a.Vb():String(a),a=Wb.test(a)?g.Ub(a):g.Vb(a));return a||g.Xb};
g.Zb=function(a,b){if(a instanceof g.Qb)return a;a="object"==typeof a&&a.Gc?a.Vb():String(a);if(b&&/^data:/i.test(a)){var c=g.Vb(a)||g.Xb;if(c.Vb()==a)return c}Wb.test(a)||(a="about:invalid#zClosurez");return g.Ub(a)};
g.Ub=function(a){return new g.Qb(a,Pb)};
g.ac=function(a,b){this.i=b===g.$b?a:""};
g.cc=function(a,b){this.i=b===g.bc?a:"";this.Gc=!0};
ec=function(a){return-1!=g.dc.indexOf(a)};
fc=function(){return ec("Firefox")||ec("FxiOS")};
g.hc=function(){return ec("Safari")&&!(gc()||ec("Coast")||ec("Opera")||ec("Edge")||ec("Edg/")||ec("OPR")||fc()||ec("Silk")||ec("Android"))};
gc=function(){return(ec("Chrome")||ec("CriOS"))&&!ec("Edge")};
g.ic=function(){return ec("Android")&&!(gc()||fc()||ec("Opera")||ec("Silk"))};
g.kc=function(a,b,c){this.j=c===jc?a:"";this.C=b};
g.mc=function(a){return g.lc(a).toString()};
g.lc=function(a){if(a instanceof g.kc&&a.constructor===g.kc)return a.j;g.La(a);return"type_error:SafeHtml"};
g.oc=function(a){if(a instanceof g.kc)return a;var b="object"==typeof a,c=null;b&&a.l&&(c=a.i());return g.nc(g.Lb(b&&a.Gc?a.Vb():String(a)),c)};
g.nc=function(a,b){var c=g.vb();c=c?c.createHTML(a):a;return new g.kc(c,b,jc)};
g.pc=function(a,b){g.zb(a);g.zb(a);return g.nc(b,null)};
g.qc=function(a,b){var c=b instanceof g.Qb?b:g.Zb(b);a.href=g.Rb(c)};
g.rc=function(a,b){var c=b instanceof g.Qb?b:g.Zb(b);a.href=g.Rb(c)};
g.sc=function(a){return encodeURIComponent(String(a))};
g.tc=function(a){return decodeURIComponent(a.replace(/\+/g," "))};
g.uc=function(a){return a=g.Lb(a,void 0)};
g.vc=function(a){for(var b=0,c=0;c<a.length;++c)b=31*b+a.charCodeAt(c)>>>0;return b};
g.wc=function(a,b,c,d,e,f,k){var l="";a&&(l+=a+":");c&&(l+="//",b&&(l+=b+"@"),l+=c,d&&(l+=":"+d));e&&(l+=e);f&&(l+="?"+f);k&&(l+="#"+k);return l};
g.xc=function(a){return a?decodeURI(a):a};
g.zc=function(a,b){return b.match(g.yc)[a]||null};
g.Ac=function(a){return g.xc(g.zc(3,a))};
g.Bc=function(a){a=a.match(g.yc);return g.wc(null,null,null,null,a[5],a[6],a[7])};
g.Cc=function(a){var b=a.indexOf("#");0>b&&(b=a.length);var c=a.indexOf("?");if(0>c||c>b){c=b;var d=""}else d=a.substring(c+1,b);return[a.substr(0,c),d,a.substr(b)]};
g.Dc=function(a,b){return b?a?a+"&"+b:b:a};
g.Ec=function(a,b){if(!b)return a;var c=g.Cc(a);c[1]=g.Dc(c[1],b);return c[0]+(c[1]?"?"+c[1]:"")+c[2]};
g.Fc=function(a,b,c){if(Array.isArray(b))for(var d=0;d<b.length;d++)g.Fc(a,String(b[d]),c);else null!=b&&c.push(a+(""===b?"":"="+g.sc(b)))};
g.Gc=function(a){var b=[],c;for(c in a)g.Fc(c,a[c],b);return b.join("&")};
g.Hc=function(a,b){var c=g.Gc(b);return g.Ec(a,c)};
g.Ic=function(a,b,c,d){for(var e=c.length;0<=(b=a.indexOf(c,b))&&b<d;){var f=a.charCodeAt(b-1);if(38==f||63==f)if(f=a.charCodeAt(b+e),!f||61==f||38==f||35==f)return b;b+=e+1}return-1};
g.Kc=function(a,b){var c=a.search(g.Jc),d=g.Ic(a,0,b,c);if(0>d)return null;var e=a.indexOf("&",d);if(0>e||e>c)e=c;d+=b.length+1;return g.tc(a.substr(d,e-d))};
g.Lc=function(a,b){var c=void 0;return new (c||(c=Promise))(function(d,e){function f(m){try{l(b.next(m))}catch(n){e(n)}}
function k(m){try{l(b["throw"](m))}catch(n){e(n)}}
function l(m){m.done?d(m.value):(new c(function(n){n(m.value)})).then(f,k)}
l((b=b.apply(a,void 0)).next())})};
Oc=function(a){var b=0>a;a=Math.abs(a);var c=a>>>0;a=Math.floor((a-c)/4294967296);a>>>=0;b&&(a=~a>>>0,c=(~c>>>0)+1,4294967295<c&&(c=0,a++,4294967295<a&&(a=0)));Mc=c;Nc=a};
Pc=function(a){var b=0>a?1:0;a=b?-a:a;if(0===a)Nc=0<1/a?0:2147483648,Mc=0;else if(isNaN(a))Nc=2147483647,Mc=4294967295;else if(1.7976931348623157E308<a)Nc=(b<<31|2146435072)>>>0,Mc=0;else if(2.2250738585072014E-308>a)a/=Math.pow(2,-1074),Nc=(b<<31|a/4294967296)>>>0,Mc=a>>>0;else{var c=a,d=0;if(2<=c)for(;2<=c&&1023>d;)d++,c/=2;else for(;1>c&&-1022<d;)c*=2,d--;a*=Math.pow(2,-d);Nc=(b<<31|d+1023<<20|1048576*a&1048575)>>>0;Mc=4503599627370496*a>>>0}};
Qc=function(){this.i=[]};
Rc=function(a){for(var b=Mc,c=Nc;0<c||127<b;)a.i.push(b&127|128),b=(b>>>7|c<<25)>>>0,c>>>=7;a.i.push(b)};
Sc=function(a,b){for(;127<b;)a.i.push(b&127|128),b>>>=7;a.i.push(b)};
Tc=function(a,b){if(0<=b)Sc(a,b);else{for(var c=0;9>c;c++)a.i.push(b&127|128),b>>=7;a.i.push(1)}};
Uc=function(a,b){a.i.push(b>>>0&255);a.i.push(b>>>8&255);a.i.push(b>>>16&255);a.i.push(b>>>24&255)};
Vc=function(){return ec("iPhone")&&!ec("iPod")&&!ec("iPad")};
g.Wc=function(){return Vc()||ec("iPad")||ec("iPod")};
Xc=function(a){Xc[" "](a);return a};
g.Yc=function(a,b){try{return Xc(a[b]),!0}catch(c){}return!1};
g.Zc=function(a,b,c,d){d=d?d(b):b;return Object.prototype.hasOwnProperty.call(a,d)?a[d]:a[d]=c(b)};
$c=function(){var a=g.q.document;return a?a.documentMode:void 0};
g.cd=function(a){return g.Zc(ad,a,function(){return 0<=g.Ob(g.bd,a)})};
g.ed=function(a){return Number(g.dd)>=a};
g.hd=function(a,b){g.Ma(a);void 0===b&&(b=0);g.fd();for(var c=gd[b],d=[],e=0;e<a.length;e+=3){var f=a[e],k=e+1<a.length,l=k?a[e+1]:0,m=e+2<a.length,n=m?a[e+2]:0,t=f>>2;f=(f&3)<<4|l>>4;l=(l&15)<<2|n>>6;n&=63;m||(n=64,k||(l=64));d.push(c[t],c[f],c[l]||"",c[n]||"")}return d.join("")};
g.fd=function(){if(!g.id){g.id={};for(var a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".split(""),b=["+/=","+/","-_=","-_.","-_"],c=0;5>c;c++){var d=a.concat(b[c].split(""));gd[c]=d;for(var e=0;e<d.length;e++){var f=d[e];void 0===g.id[f]&&(g.id[f]=e)}}}};
jd=function(){this.j=[];this.l=0;this.i=new Qc};
ld=function(a,b){kd(a,b,2);var c=a.i.end();a.j.push(c);a.l+=c.length;c.push(a.l);return c};
md=function(a,b){var c=b.pop();for(c=a.l+a.i.length()-c;127<c;)b.push(c&127|128),c>>>=7,a.l++;b.push(c);a.l++};
kd=function(a,b,c){Sc(a.i,8*b+c)};
nd=function(a,b,c){null!=c&&(kd(a,b,1),a=a.i,b=c>>>0,c=Math.floor((c-b)/4294967296)>>>0,Mc=b,Nc=c,Uc(a,Mc),Uc(a,Nc))};
od=function(a,b,c){null!=c&&(kd(a,b,0),a.i.i.push(c?1:0))};
pd=function(a,b,c){if(null!=c){b=ld(a,b);for(var d=a.i,e=0;e<c.length;e++){var f=c.charCodeAt(e);if(128>f)d.i.push(f);else if(2048>f)d.i.push(f>>6|192),d.i.push(f&63|128);else if(65536>f)if(55296<=f&&56319>=f&&e+1<c.length){var k=c.charCodeAt(e+1);56320<=k&&57343>=k&&(f=1024*(f-55296)+k-56320+65536,d.i.push(f>>18|240),d.i.push(f>>12&63|128),d.i.push(f>>6&63|128),d.i.push(f&63|128),e++)}else d.i.push(f>>12|224),d.i.push(f>>6&63|128),d.i.push(f&63|128)}md(a,b)}};
qd=function(a,b,c,d){null!=c&&(b=ld(a,b),d(c,a),md(a,b))};
rd=function(a,b,c,d){if(null!=c)for(var e=0;e<c.length;e++){var f=ld(a,b);d(c[e],a);md(a,f)}};
g.sd=function(a){this.i=0;this.l=a};
g.td=function(){};
g.zd=function(a,b,c,d,e){a.i=null;b||(b=c?[c]:[]);a.F=c?String(c):void 0;a.C=0===c?-1:0;a.vb=b;a:{if(b=a.vb.length)if(--b,c=a.vb[b],!(null===c||"object"!=typeof c||Array.isArray(c)||ud&&c instanceof Uint8Array)){a.D=b-a.C;a.j=c;break a}a.D=Number.MAX_VALUE}a.H={};if(d)for(b=0;b<d.length;b++)c=d[b],c<a.D?(c+=a.C,a.vb[c]=a.vb[c]||vd):(g.xd(a),a.j[c]=a.j[c]||vd);if(e&&e.length)for(b=0;b<e.length;b++)yd(a,e[b])};
g.xd=function(a){var b=a.D+a.C;a.vb[b]||(a.j=a.vb[b]={})};
g.Ad=function(a,b){if(b<a.D){var c=b+a.C,d=a.vb[c];return d!==vd?d:a.vb[c]=[]}if(a.j)return d=a.j[b],d===vd?a.j[b]=[]:d};
g.Bd=function(a,b,c){b<a.D?a.vb[b+a.C]=c:(g.xd(a),a.j[b]=c);return a};
g.Cd=function(a,b,c,d){(c=yd(a,c))&&c!==b&&void 0!==d&&(a.i&&c in a.i&&(a.i[c]=void 0),g.Bd(a,c,void 0));return g.Bd(a,b,d)};
yd=function(a,b){for(var c,d,e=0;e<b.length;e++){var f=b[e],k=g.Ad(a,f);null!=k&&(c=f,d=k,g.Bd(a,f,void 0))}return c?(g.Bd(a,c,d),c):0};
g.Dd=function(a,b,c){a.i||(a.i={});if(!a.i[c]){var d=g.Ad(a,c);d&&(a.i[c]=new b(d))}return a.i[c]};
g.Ed=function(a,b,c){a.i||(a.i={});if(!a.i[c]){for(var d=g.Ad(a,c),e=[],f=0;f<d.length;f++)e[f]=new b(d[f]);a.i[c]=e}b=a.i[c];b==vd&&(b=a.i[c]=[]);return b};
g.Fd=function(a,b,c){a.i||(a.i={});var d=c?c.hc():c;a.i[b]=c;return g.Bd(a,b,d)};
g.Gd=function(a,b,c){a.i||(a.i={});c=c||[];for(var d=[],e=0;e<c.length;e++)d[e]=c[e].hc();a.i[b]=c;return g.Bd(a,b,d)};
Hd=function(a,b){return"number"!==typeof b||!isNaN(b)&&Infinity!==b&&-Infinity!==b?b:String(b)};
Id=function(a){if(Array.isArray(a)){for(var b=Array(a.length),c=0;c<a.length;c++){var d=a[c];null!=d&&(b[c]="object"==typeof d?Id(d):d)}return b}if(ud&&a instanceof Uint8Array)return new Uint8Array(a);b={};for(c in a)d=a[c],null!=d&&(b[c]="object"==typeof d?Id(d):d);return b};
g.Ld=function(a){var b=g.u("window.location.href");null==a&&(a='Unknown Error of type "null/undefined"');if("string"===typeof a)return{message:a,name:"Unknown error",lineNumber:"Not available",fileName:b,stack:"Not available"};var c=!1;try{var d=a.lineNumber||a.line||"Not available"}catch(f){d="Not available",c=!0}try{var e=a.fileName||a.filename||a.sourceURL||g.q.$googDebugFname||b}catch(f){e="Not available",c=!0}b=Jd(a);if(!(!c&&a.lineNumber&&a.fileName&&a.stack&&a.message&&a.name))return c=a.message,
null==c&&(c=a.constructor&&a.constructor instanceof Function?'Unknown Error of type "'+(a.constructor.name?a.constructor.name:g.Kd(a.constructor))+'"':"Unknown Error of unknown type","function"===typeof a.toString&&Object.prototype.toString!==a.toString&&(c+=": "+a.toString())),{message:c,name:a.name||"UnknownError",lineNumber:d,fileName:e,stack:b||"Not available"};a.stack=b;return a};
Jd=function(a,b){b||(b={});b[Md(a)]=!0;var c=a.stack||"",d=a.hJ;d&&!b[Md(d)]&&(c+="\nCaused by: ",d.stack&&0==d.stack.indexOf(d.toString())||(c+="string"===typeof d?d:d.message+"\n"),c+=Jd(d,b));return c};
Md=function(a){var b="";"function"===typeof a.toString&&(b=""+a);return b+a.stack};
g.Kd=function(a){if(Nd[a])return Nd[a];a=String(a);if(!Nd[a]){var b=/function\s+([^\(]+)/m.exec(a);Nd[a]=b?b[1]:"[Anonymous]"}return Nd[a]};
g.Od=function(a){this.i=a||{cookie:""}};
g.Pd=function(a){a=(a.i.cookie||"").split(";");for(var b=[],c=[],d,e,f=0;f<a.length;f++)e=(0,g.Mb)(a[f]),d=e.indexOf("="),-1==d?(b.push(""),c.push(e)):(b.push(e.substring(0,d)),c.push(e.substring(d+1)));return{keys:b,values:c}};
g.Qd=function(a,b){this.x=void 0!==a?a:0;this.y=void 0!==b?b:0};
g.Rd=function(a,b){return a==b?!0:a&&b?a.x==b.x&&a.y==b.y:!1};
g.Sd=function(a,b){var c=a.x-b.x,d=a.y-b.y;return Math.sqrt(c*c+d*d)};
g.Td=function(a,b){this.width=a;this.height=b};
g.z=function(a){return g.Ud(document,a)};
g.Ud=function(a,b){return"string"===typeof b?a.getElementById(b):b};
g.Vd=function(a){return g.Ud(document,a)};
g.Xd=function(a,b){g.ib(b,function(c,d){c&&"object"==typeof c&&c.Gc&&(c=c.Vb());"style"==d?a.style.cssText=c:"class"==d?a.className=c:"for"==d?a.htmlFor=c:Wd.hasOwnProperty(d)?a.setAttribute(Wd[d],c):g.Bb(d,"aria-")||g.Bb(d,"data-")?a.setAttribute(d,c):a[d]=c})};
g.Zd=function(a){return g.Yd(a||window)};
g.Yd=function(a){a=a.document;a=g.$d(a)?a.documentElement:a.body;return new g.Td(a.clientWidth,a.clientHeight)};
g.be=function(a,b,c){return g.ae(document,arguments)};
g.ae=function(a,b){var c=String(b[0]),d=b[1];if(!ce&&d&&(d.name||d.type)){c=["<",c];d.name&&c.push(' name="',g.uc(d.name),'"');if(d.type){c.push(' type="',g.uc(d.type),'"');var e={};g.tb(e,d);delete e.type;d=e}c.push(">");c=c.join("")}c=g.de(a,c);d&&("string"===typeof d?c.className=d:Array.isArray(d)?c.className=d.join(" "):g.Xd(c,d));2<b.length&&g.ee(a,c,b,2);return c};
g.ee=function(a,b,c,d){function e(l){l&&b.appendChild("string"===typeof l?a.createTextNode(l):l)}
for(;d<c.length;d++){var f=c[d];if(!g.Ma(f)||g.Na(f)&&0<f.nodeType)e(f);else{a:{if(f&&"number"==typeof f.length){if(g.Na(f)){var k="function"==typeof f.item||"string"==typeof f.item;break a}if("function"===typeof f){k="function"==typeof f.item;break a}}k=!1}g.A(k?g.gb(f):f,e)}}};
g.fe=function(a){return g.de(document,a)};
g.de=function(a,b){b=String(b);"application/xhtml+xml"===a.contentType&&(b=b.toLowerCase());return a.createElement(b)};
g.$d=function(a){return"CSS1Compat"==a.compatMode};
g.ge=function(a){for(var b;b=a.firstChild;)a.removeChild(b)};
g.he=function(a){return 9==a.nodeType?a:a.ownerDocument||a.document};
g.ie=function(a,b,c,d){a&&!c&&(a=a.parentNode);for(c=0;a&&(null==d||c<=d);){if(b(a))return a;a=a.parentNode;c++}return null};
ke=function(a){var b=je;if(b)for(var c in b)Object.prototype.hasOwnProperty.call(b,c)&&a.call(void 0,b[c],c,b)};
le=function(){var a=[];ke(function(b){a.push(b)});
return a};
ne=function(){var a=g.fe("IFRAME"),b={};g.A(me(),function(c){a.sandbox&&a.sandbox.supports&&a.sandbox.supports(c)&&(b[c]=!0)});
return b};
g.B=function(){this.Ma=this.Ma;this.za=this.za};
g.qe=function(a,b){g.oe(a,g.Ua(g.pe,b))};
g.oe=function(a,b){a.Ma?b():(a.za||(a.za=[]),a.za.push(b))};
g.pe=function(a){a&&"function"==typeof a.dispose&&a.dispose()};
g.re=function(a,b,c,d){this.left=a;this.top=b;this.width=c;this.height=d};
te=function(a){if(a!==se)throw Error("Bad secret");};
ue=function(){var a="undefined"!==typeof window?window.trustedTypes:void 0;return null!==a&&void 0!==a?a:null};
ve=function(){};
we=function(a,b){te(b);this.i=a};
xe=function(){};
ye=function(a,b){te(b);this.i=a};
ze=function(){};
Ae=function(a,b){te(b);this.i=a};
g.Be=function(a,b){"number"==typeof a&&(a=(b?Math.round(a):a)+"px");return a};
g.Ce=function(a,b){a.style.width=g.Be(b,!0)};
De=function(a){if(!a)return"";a=a.split("#")[0].split("?")[0];a=a.toLowerCase();0==a.indexOf("//")&&(a=window.location.protocol+a);/^[\w\-]*:\/\//.test(a)||(a=window.location.href);var b=a.substring(a.indexOf("://")+3),c=b.indexOf("/");-1!=c&&(b=b.substring(0,c));a=a.substring(0,a.indexOf("://"));if("http"!==a&&"https"!==a&&"chrome-extension"!==a&&"moz-extension"!==a&&"file"!==a&&"android-app"!==a&&"chrome-search"!==a&&"chrome-untrusted"!==a&&"chrome"!==a&&"app"!==a&&"devtools"!==a)throw Error("Invalid URI scheme in origin: "+
a);c="";var d=b.indexOf(":");if(-1!=d){var e=b.substring(d+1);b=b.substring(0,d);if("http"===a&&"80"!==e||"https"===a&&"443"!==e)c=":"+e}return a+"://"+b+c};
Ee=function(){function a(){e[0]=1732584193;e[1]=4023233417;e[2]=2562383102;e[3]=271733878;e[4]=3285377520;t=n=0}
function b(w){for(var x=k,D=0;64>D;D+=4)x[D/4]=w[D]<<24|w[D+1]<<16|w[D+2]<<8|w[D+3];for(D=16;80>D;D++)w=x[D-3]^x[D-8]^x[D-14]^x[D-16],x[D]=(w<<1|w>>>31)&4294967295;w=e[0];var O=e[1],M=e[2],la=e[3],ya=e[4];for(D=0;80>D;D++){if(40>D)if(20>D){var Pa=la^O&(M^la);var wd=1518500249}else Pa=O^M^la,wd=1859775393;else 60>D?(Pa=O&M|la&(O|M),wd=2400959708):(Pa=O^M^la,wd=3395469782);Pa=((w<<5|w>>>27)&4294967295)+Pa+ya+wd+x[D]&4294967295;ya=la;la=M;M=(O<<30|O>>>2)&4294967295;O=w;w=Pa}e[0]=e[0]+w&4294967295;e[1]=
e[1]+O&4294967295;e[2]=e[2]+M&4294967295;e[3]=e[3]+la&4294967295;e[4]=e[4]+ya&4294967295}
function c(w,x){if("string"===typeof w){w=unescape(encodeURIComponent(w));for(var D=[],O=0,M=w.length;O<M;++O)D.push(w.charCodeAt(O));w=D}x||(x=w.length);D=0;if(0==n)for(;D+64<x;)b(w.slice(D,D+64)),D+=64,t+=64;for(;D<x;)if(f[n++]=w[D++],t++,64==n)for(n=0,b(f);D+64<x;)b(w.slice(D,D+64)),D+=64,t+=64}
function d(){var w=[],x=8*t;56>n?c(l,56-n):c(l,64-(n-56));for(var D=63;56<=D;D--)f[D]=x&255,x>>>=8;b(f);for(D=x=0;5>D;D++)for(var O=24;0<=O;O-=8)w[x++]=e[D]>>O&255;return w}
for(var e=[],f=[],k=[],l=[128],m=1;64>m;++m)l[m]=0;var n,t;a();return{reset:a,update:c,digest:d,Vp:function(){for(var w=d(),x="",D=0;D<w.length;D++)x+="0123456789ABCDEF".charAt(Math.floor(w[D]/16))+"0123456789ABCDEF".charAt(w[D]%16);return x}}};
Ge=function(a,b,c){var d=[],e=[];if(1==(Array.isArray(c)?2:1))return e=[b,a],g.A(d,function(l){e.push(l)}),Fe(e.join(" "));
var f=[],k=[];g.A(c,function(l){k.push(l.key);f.push(l.value)});
c=Math.floor((new Date).getTime()/1E3);e=0==f.length?[c,b,a]:[f.join(":"),c,b,a];g.A(d,function(l){e.push(l)});
a=Fe(e.join(" "));a=[c,a];0==k.length||a.push(k.join(""));return a.join("_")};
Fe=function(a){var b=Ee();b.update(a);return b.Vp().toLowerCase()};
g.He=function(a){var b=De(String(g.q.location.href)),c;(c=g.q.__SAPISID||g.q.__APISID||g.q.__OVERRIDE_SID)?c=!0:(c=new g.Od(document),c=c.get("SAPISID")||c.get("APISID")||c.get("__Secure-3PAPISID")||c.get("SID"),c=!!c);if(c&&(c=(b=0==b.indexOf("https:")||0==b.indexOf("chrome-extension:")||0==b.indexOf("moz-extension:"))?g.q.__SAPISID:g.q.__APISID,c||(c=new g.Od(document),c=c.get(b?"SAPISID":"APISID")||c.get("__Secure-3PAPISID")),c)){b=b?"SAPISIDHASH":"APISIDHASH";var d=String(g.q.location.href);return d&&
c&&b?[b,Ge(De(d),c,a||null)].join(" "):null}return null};
Ie=function(a,b,c){this.D=a;this.l=b;this.i=c||[];this.hf=new Map};
Ke=function(a){g.zd(this,a,0,Je,null)};
Le=function(a){g.zd(this,a,0,null,null)};
Ne=function(a,b){var c=g.Ed(a,Le,1);0<c.length&&rd(b,1,c,Me)};
Me=function(a,b){var c=g.Ad(a,1);if(null!=c&&null!=c){kd(b,1,1);var d=b.i;Pc(c);Uc(d,Mc);Uc(d,Nc)}c=g.Ad(a,2);null!=c&&null!=c&&null!=c&&(kd(b,2,0),d=b.i,Oc(c),Rc(d))};
Pe=function(a){g.zd(this,a,0,Oe,null)};
Re=function(a){g.zd(this,a,0,null,Qe)};
Se=function(a){g.zd(this,a,0,null,null)};
Te=function(a){g.zd(this,a,0,null,null)};
Ve=function(a){g.zd(this,a,0,Ue,null)};
Xe=function(a){g.zd(this,a,0,null,We)};
Ze=function(a){g.zd(this,a,0,null,Ye)};
bf=function(a,b){var c=g.Dd(a,Se,1);null!=c&&qd(b,1,c,$e);c=g.Dd(a,Te,2);null!=c&&qd(b,2,c,af)};
$e=function(a,b){var c=g.Ad(a,1);null!=c&&pd(b,1,c);c=g.Ad(a,2);null!=c&&pd(b,2,c);c=g.Ad(a,3);null!=c&&od(b,3,c)};
af=function(a,b){var c=g.Ad(a,1);null!=c&&pd(b,1,c);c=g.Ad(a,2);null!=c&&pd(b,2,c);c=g.Ad(a,3);null!=c&&null!=c&&null!=c&&(kd(b,3,0),Tc(b.i,c));c=g.Ad(a,4);null!=c&&od(b,4,c)};
ef=function(a,b){var c=g.Ed(a,Xe,1);0<c.length&&rd(b,1,c,cf);c=g.Dd(a,Ze,2);null!=c&&qd(b,2,c,df)};
cf=function(a,b){var c=g.Ad(a,1);null!=c&&pd(b,1,c);c=g.Ad(a,2);null!=c&&null!=c&&null!=c&&(kd(b,2,0),Tc(b.i,c));c=g.Ad(a,3);null!=c&&od(b,3,c)};
df=function(a,b){var c=g.Ad(a,1);if(null!=c&&null!=c&&null!=c){kd(b,1,0);var d=b.i;Oc(c);Rc(d)}c=g.Ad(a,2);null!=c&&null!=c&&(kd(b,2,1),d=b.i,Pc(c),Uc(d,Mc),Uc(d,Nc));c=g.Dd(a,Ke,3);null!=c&&qd(b,3,c,Ne)};
ff=function(a,b){Ie.call(this,a,3,b)};
g.gf=function(a,b){this.type=a;this.currentTarget=this.target=b;this.defaultPrevented=this.i=!1};
g.hf=function(a,b){g.gf.call(this,a?a.type:"");this.relatedTarget=this.currentTarget=this.target=null;this.button=this.screenY=this.screenX=this.clientY=this.clientX=0;this.key="";this.charCode=this.keyCode=0;this.metaKey=this.shiftKey=this.altKey=this.ctrlKey=!1;this.state=null;this.l=!1;this.pointerId=0;this.pointerType="";this.yb=null;a&&this.init(a,b)};
g.kf=function(a){return!(!a||!a[jf])};
mf=function(a,b,c,d,e){this.listener=a;this.i=null;this.src=b;this.type=c;this.capture=!!d;this.Ng=e;this.key=++lf;this.Te=this.eg=!1};
nf=function(a){a.Te=!0;a.listener=null;a.i=null;a.src=null;a.Ng=null};
of=function(a){this.src=a;this.listeners={};this.i=0};
pf=function(a,b){var c=b.type;if(!(c in a.listeners))return!1;var d=g.fb(a.listeners[c],b);d&&(nf(b),0==a.listeners[c].length&&(delete a.listeners[c],a.i--));return d};
qf=function(a,b,c,d){for(var e=0;e<a.length;++e){var f=a[e];if(!f.Te&&f.listener==b&&f.capture==!!c&&f.Ng==d)return e}return-1};
g.sf=function(a,b,c,d,e){if(d&&d.once)return g.rf(a,b,c,d,e);if(Array.isArray(b)){for(var f=0;f<b.length;f++)g.sf(a,b[f],c,d,e);return null}c=g.tf(c);return g.kf(a)?a.L(b,c,g.Na(d)?!!d.capture:!!d,e):uf(a,b,c,!1,d,e)};
uf=function(a,b,c,d,e,f){if(!b)throw Error("Invalid event type");var k=g.Na(e)?!!e.capture:!!e,l=g.vf(a);l||(a[wf]=l=new of(a));c=l.add(b,c,d,k,f);if(c.i)return c;d=xf();c.i=d;d.src=a;d.listener=c;if(a.addEventListener)yf||(e=k),void 0===e&&(e=!1),a.addEventListener(b.toString(),d,e);else if(a.attachEvent)a.attachEvent(zf(b.toString()),d);else if(a.addListener&&a.removeListener)a.addListener(d);else throw Error("addEventListener and attachEvent are unavailable.");Af++;return c};
xf=function(){var a=Bf,b=Cf?function(c){return a.call(b.src,b.listener,c)}:function(c){c=a.call(b.src,b.listener,c);
if(!c)return c};
return b};
g.rf=function(a,b,c,d,e){if(Array.isArray(b)){for(var f=0;f<b.length;f++)g.rf(a,b[f],c,d,e);return null}c=g.tf(c);return g.kf(a)?a.Yd(b,c,g.Na(d)?!!d.capture:!!d,e):uf(a,b,c,!0,d,e)};
g.Df=function(a,b,c,d,e){if(Array.isArray(b))for(var f=0;f<b.length;f++)g.Df(a,b[f],c,d,e);else d=g.Na(d)?!!d.capture:!!d,c=g.tf(c),g.kf(a)?a.ua(b,c,d,e):a&&(a=g.vf(a))&&(b=a.Id(b,c,d,e))&&g.Ef(b)};
g.Ef=function(a){if("number"===typeof a||!a||a.Te)return!1;var b=a.src;if(g.kf(b))return pf(b.Bc,a);var c=a.type,d=a.i;b.removeEventListener?b.removeEventListener(c,d,a.capture):b.detachEvent?b.detachEvent(zf(c),d):b.addListener&&b.removeListener&&b.removeListener(d);Af--;(c=g.vf(b))?(pf(c,a),0==c.i&&(c.src=null,b[wf]=null)):nf(a);return!0};
zf=function(a){return a in Ff?Ff[a]:Ff[a]="on"+a};
Hf=function(a,b,c,d){var e=!0;if(a=g.vf(a))if(b=a.listeners[b.toString()])for(b=b.concat(),a=0;a<b.length;a++){var f=b[a];f&&f.capture==c&&!f.Te&&(f=Gf(f,d),e=e&&!1!==f)}return e};
Gf=function(a,b){var c=a.listener,d=a.Ng||a.src;a.eg&&g.Ef(a);return c.call(d,b)};
Bf=function(a,b){if(a.Te)return!0;if(!Cf){var c=b||g.u("window.event"),d=new g.hf(c,this),e=!0;if(!(0>c.keyCode||void 0!=c.returnValue)){a:{var f=!1;if(0==c.keyCode)try{c.keyCode=-1;break a}catch(m){f=!0}if(f||void 0==c.returnValue)c.returnValue=!0}c=[];for(f=d.currentTarget;f;f=f.parentNode)c.push(f);f=a.type;for(var k=c.length-1;!d.i&&0<=k;k--){d.currentTarget=c[k];var l=Hf(c[k],f,!0,d);e=e&&l}for(k=0;!d.i&&k<c.length;k++)d.currentTarget=c[k],l=Hf(c[k],f,!1,d),e=e&&l}return e}return Gf(a,new g.hf(b,
this))};
g.vf=function(a){a=a[wf];return a instanceof of?a:null};
g.tf=function(a){if("function"===typeof a)return a;a[If]||(a[If]=function(b){return a.handleEvent(b)});
return a[If]};
g.Jf=function(){g.B.call(this);this.Bc=new of(this);this.wp=this;this.oj=null};
Kf=function(a,b,c,d){b=a.Bc.listeners[String(b)];if(!b)return!0;b=b.concat();for(var e=!0,f=0;f<b.length;++f){var k=b[f];if(k&&!k.Te&&k.capture==c){var l=k.listener,m=k.Ng||k.src;k.eg&&pf(a.Bc,k);e=!1!==l.call(m,d)&&e}}return e&&!d.defaultPrevented};
Lf=function(a,b){this.j=a;this.C=b;this.l=0;this.i=null};
Mf=function(a,b){a.C(b);100>a.l&&(a.l++,b.next=a.i,a.i=b)};
Nf=function(a){g.q.setTimeout(function(){throw a;},0)};
Of=function(){var a=g.q.MessageChannel;"undefined"===typeof a&&"undefined"!==typeof window&&window.postMessage&&window.addEventListener&&!ec("Presto")&&(a=function(){var e=g.fe("IFRAME");e.style.display="none";document.documentElement.appendChild(e);var f=e.contentWindow;e=f.document;e.open();e.close();var k="callImmediate"+Math.random(),l="file:"==f.location.protocol?"*":f.location.protocol+"//"+f.location.host;e=(0,g.v)(function(m){if(("*"==l||m.origin==l)&&m.data==k)this.port1.onmessage()},this);
f.addEventListener("message",e,!1);this.port1={};this.port2={postMessage:function(){f.postMessage(k,l)}}});
if("undefined"!==typeof a&&!ec("Trident")&&!ec("MSIE")){var b=new a,c={},d=c;b.port1.onmessage=function(){if(void 0!==c.next){c=c.next;var e=c.zk;c.zk=null;e()}};
return function(e){d.next={zk:e};d=d.next;b.port2.postMessage(0)}}return function(e){g.q.setTimeout(e,0)}};
Pf=function(){this.l=this.i=null};
Qf=function(){this.next=this.scope=this.Eb=null};
Vf=function(a,b){Rf||Sf();Tf||(Rf(),Tf=!0);Uf.add(a,b)};
Sf=function(){if(g.q.Promise&&g.q.Promise.resolve){var a=g.q.Promise.resolve(void 0);Rf=function(){a.then(Wf)}}else Rf=function(){var b=Wf;
"function"!==typeof g.q.setImmediate||g.q.Window&&g.q.Window.prototype&&!ec("Edge")&&g.q.Window.prototype.setImmediate==g.q.setImmediate?(Xf||(Xf=Of()),Xf(b)):g.q.setImmediate(b)}};
Wf=function(){for(var a;a=Uf.remove();){try{a.Eb.call(a.scope)}catch(b){Nf(b)}Mf(Yf,a)}Tf=!1};
g.Zf=function(a){a.prototype.$goog_Thenable=!0};
g.$f=function(a){if(!a)return!1;try{return!!a.$goog_Thenable}catch(b){return!1}};
g.ag=function(a,b){this.Y=0;this.Gb=void 0;this.we=this.jd=this.Ja=null;this.Mg=this.ci=!1;if(a!=g.Ja)try{var c=this;a.call(b,function(d){c.Rb(2,d)},function(d){c.Rb(3,d)})}catch(d){this.Rb(3,d)}};
bg=function(){this.next=this.context=this.onRejected=this.j=this.i=null;this.l=!1};
g.dg=function(a,b,c){var d=cg.get();d.j=a;d.onRejected=b;d.context=c;return d};
g.fg=function(a,b,c){eg(a,b,c,null)||Vf(g.Ua(b,a))};
g.gg=function(a){return new g.ag(function(b,c){var d=a.length,e=[];if(d)for(var f=function(n,t){d--;e[n]=t;0==d&&b(e)},k=function(n){c(n)},l=0,m;l<a.length;l++)m=a[l],g.fg(m,g.Ua(f,l),k);
else b(e)})};
g.ig=function(a,b){return hg(a,null,b,void 0)};
jg=function(a,b){if(0==a.Y)if(a.Ja){var c=a.Ja;if(c.jd){for(var d=0,e=null,f=null,k=c.jd;k&&(k.l||(d++,k.i==a&&(e=k),!(e&&1<d)));k=k.next)e||(f=k);e&&(0==c.Y&&1==d?jg(c,b):(f?(d=f,d.next==c.we&&(c.we=d),d.next=d.next.next):kg(c),lg(c,e,3,b)))}a.Ja=null}else a.Rb(3,b)};
g.ng=function(a,b){a.jd||2!=a.Y&&3!=a.Y||mg(a);a.we?a.we.next=b:a.jd=b;a.we=b};
hg=function(a,b,c,d){var e=g.dg(null,null,null);e.i=new g.ag(function(f,k){e.j=b?function(l){try{var m=b.call(d,l);f(m)}catch(n){k(n)}}:f;
e.onRejected=c?function(l){try{var m=c.call(d,l);void 0===m&&l instanceof g.og?k(l):f(m)}catch(n){k(n)}}:k});
e.i.Ja=a;g.ng(a,e);return e.i};
eg=function(a,b,c,d){if(a instanceof g.ag)return g.ng(a,g.dg(b||g.Ja,c||null,d)),!0;if(g.$f(a))return a.then(b,c,d),!0;if(g.Na(a))try{var e=a.then;if("function"===typeof e)return pg(a,e,b,c,d),!0}catch(f){return c.call(d,f),!0}return!1};
pg=function(a,b,c,d,e){function f(m){l||(l=!0,d.call(e,m))}
function k(m){l||(l=!0,c.call(e,m))}
var l=!1;try{b.call(a,k,f)}catch(m){f(m)}};
mg=function(a){a.ci||(a.ci=!0,Vf(a.kq,a))};
kg=function(a){var b=null;a.jd&&(b=a.jd,a.jd=b.next,b.next=null);a.jd||(a.we=null);return b};
lg=function(a,b,c,d){if(3==c&&b.onRejected&&!b.l)for(;a&&a.Mg;a=a.Ja)a.Mg=!1;if(b.i)b.i.Ja=null,qg(b,c,d);else try{b.l?b.j.call(b.context):qg(b,c,d)}catch(e){rg.call(null,e)}Mf(cg,b)};
qg=function(a,b,c){2==b?a.j.call(a.context,c):a.onRejected&&a.onRejected.call(a.context,c)};
sg=function(a,b){a.Mg=!0;Vf(function(){a.Mg&&rg.call(null,b)})};
g.og=function(a){g.Ya.call(this,a)};
g.tg=function(a,b){g.Jf.call(this);this.l=a||1;this.i=b||g.q;this.j=(0,g.v)(this.Kq,this);this.C=g.Va()};
g.ug=function(a,b,c){if("function"===typeof a)c&&(a=(0,g.v)(a,c));else if(a&&"function"==typeof a.handleEvent)a=(0,g.v)(a.handleEvent,a);else throw Error("Invalid listener argument");return 2147483647<Number(b)?-1:g.q.setTimeout(a,b||0)};
g.vg=function(a){g.q.clearTimeout(a)};
xg=function(){this.D=new wg;this.j=new Map;this.flushInterval=3E4;this.l=new g.tg(this.flushInterval);this.l.L("tick",this.C,!1,this)};
yg=function(a){for(var b=0;b<a.length;b++)a[b].clear()};
zg=function(){this.i=[];this.l=-1};
Bg=function(a){-1==a.l&&(a.l=Ag(a.i,function(b,c,d){return c?b+Math.pow(2,d):b},0));
return a.l};
g.Cg=function(a,b,c){g.B.call(this);this.i=a;this.j=b||0;this.l=c;this.qb=(0,g.v)(this.ql,this)};
Dg=function(){this.l=-1};
Eg=function(){this.l=64;this.i=[];this.H=[];this.F=[];this.C=[];this.C[0]=128;for(var a=1;a<this.l;++a)this.C[a]=0;this.D=this.j=0;this.reset()};
Fg=function(a,b,c){c||(c=0);var d=a.F;if("string"===typeof b)for(var e=0;16>e;e++)d[e]=b.charCodeAt(c)<<24|b.charCodeAt(c+1)<<16|b.charCodeAt(c+2)<<8|b.charCodeAt(c+3),c+=4;else for(e=0;16>e;e++)d[e]=b[c]<<24|b[c+1]<<16|b[c+2]<<8|b[c+3],c+=4;for(e=16;80>e;e++){var f=d[e-3]^d[e-8]^d[e-14]^d[e-16];d[e]=(f<<1|f>>>31)&4294967295}b=a.i[0];c=a.i[1];var k=a.i[2],l=a.i[3],m=a.i[4];for(e=0;80>e;e++){if(40>e)if(20>e){f=l^c&(k^l);var n=1518500249}else f=c^k^l,n=1859775393;else 60>e?(f=c&k|l&(c|k),n=2400959708):
(f=c^k^l,n=3395469782);f=(b<<5|b>>>27)+f+m+n+d[e]&4294967295;m=l;l=k;k=(c<<30|c>>>2)&4294967295;c=b;b=f}a.i[0]=a.i[0]+b&4294967295;a.i[1]=a.i[1]+c&4294967295;a.i[2]=a.i[2]+k&4294967295;a.i[3]=a.i[3]+l&4294967295;a.i[4]=a.i[4]+m&4294967295};
Gg=function(){};
Ig=function(a){if(a instanceof Gg)return a;if("function"==typeof a.dc)return a.dc(!1);if(g.Ma(a)){var b=0,c=new Gg;c.next=function(){for(;;){if(b>=a.length)throw Hg;if(b in a)return a[b++];b++}};
return c}throw Error("Not implemented");};
g.Jg=function(a,b,c){if(g.Ma(a))try{g.A(a,b,c)}catch(d){if(d!==Hg)throw d;}else{a=Ig(a);try{for(;;)b.call(c,a.next(),void 0,a)}catch(d){if(d!==Hg)throw d;}}};
Kg=function(a){if(g.Ma(a))return g.gb(a);a=Ig(a);var b=[];g.Jg(a,function(c){b.push(c)});
return b};
g.Mg=function(a,b){this.la={};this.i=[];this.xd=this.l=0;var c=arguments.length;if(1<c){if(c%2)throw Error("Uneven number of arguments");for(var d=0;d<c;d+=2)this.set(arguments[d],arguments[d+1])}else a&&g.Lg(this,a)};
Ng=function(a,b){return a===b};
g.Pg=function(a){if(a.l!=a.i.length){for(var b=0,c=0;b<a.i.length;){var d=a.i[b];g.Og(a.la,d)&&(a.i[c++]=d);b++}a.i.length=c}if(a.l!=a.i.length){var e={};for(c=b=0;b<a.i.length;)d=a.i[b],g.Og(e,d)||(a.i[c++]=d,e[d]=1),b++;a.i.length=c}};
g.Lg=function(a,b){if(b instanceof g.Mg)for(var c=b.Fb(),d=0;d<c.length;d++)a.set(c[d],b.get(c[d]));else for(c in b)a.set(c,b[c])};
g.Og=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)};
g.Qg=function(a){return"string"==typeof a.className?a.className:a.getAttribute&&a.getAttribute("class")||""};
g.Rg=function(a){return a.classList?a.classList:g.Qg(a).match(/\S+/g)||[]};
g.C=function(a,b){return a.classList?a.classList.contains(b):g.db(g.Rg(a),b)};
g.Ug=function(a){var b=[];Sg(new Tg,a,b);return b.join("")};
Tg=function(){};
Sg=function(a,b,c){if(null==b)c.push("null");else{if("object"==typeof b){if(Array.isArray(b)){var d=b;b=d.length;c.push("[");for(var e="",f=0;f<b;f++)c.push(e),Sg(a,d[f],c),e=",";c.push("]");return}if(b instanceof String||b instanceof Number||b instanceof Boolean)b=b.valueOf();else{c.push("{");e="";for(d in b)Object.prototype.hasOwnProperty.call(b,d)&&(f=b[d],"function"!=typeof f&&(c.push(e),Vg(d,c),c.push(":"),Sg(a,f,c),e=","));c.push("}");return}}switch(typeof b){case "string":Vg(b,c);break;case "number":c.push(isFinite(b)&&
!isNaN(b)?String(b):"null");break;case "boolean":c.push(String(b));break;case "function":c.push("null");break;default:throw Error("Unknown type: "+typeof b);}}};
Vg=function(a,b){b.push('"',a.replace(Wg,function(c){var d=Xg[c];d||(d="\\u"+(c.charCodeAt(0)|65536).toString(16).substr(1),Xg[c]=d);return d}),'"')};
g.Yg=function(a){g.B.call(this);this.D=1;this.j=[];this.C=0;this.i=[];this.l={};this.H=!!a};
g.Zg=function(a,b,c,d){if(b=a.l[b]){var e=a.i;(b=g.bb(b,function(f){return e[f+1]==c&&e[f+2]==d}))&&a.nc(b)}};
$g=function(a,b,c){Vf(function(){a.apply(b,c)})};
g.ah=function(a){this.i=a};
bh=function(a){this.i=a};
ch=function(a){this.data=a};
dh=function(a){return void 0===a||a instanceof ch?a:new ch(a)};
g.eh=function(a){this.i=a};
g.fh=function(a){var b=a.creation;a=a.expiration;return!!a&&a<g.Va()||!!b&&b>g.Va()};
gh=function(){};
hh=function(){};
g.ih=function(a){this.i=a};
g.jh=function(){var a=null;try{a=window.localStorage||null}catch(b){}this.i=a};
lh=function(a,b){this.l=a;this.i=null;if(g.E&&!g.ed(9)){kh||(kh=new g.Mg);this.i=kh.get(a);this.i||(b?this.i=document.getElementById(b):(this.i=document.createElement("userdata"),this.i.addBehavior("#default#userData"),document.body.appendChild(this.i)),kh.set(a,this.i));try{this.i.load(this.l)}catch(c){this.i=null}}};
nh=function(a){return"_"+encodeURIComponent(a).replace(/[.!~*'()%]/g,function(b){return mh[b]})};
oh=function(a){try{a.i.save(a.l)}catch(b){throw"Storage mechanism: Quota exceeded";}};
ph=function(a){return a.i.XMLDocument.documentElement};
qh=function(a,b){this.l=a;this.i=b+"::"};
g.rh=function(a){var b=new g.jh;return b.isAvailable()?a?new qh(b,a):b:null};
sh=function(a,b){1<b.length?a[b[0]]=b[1]:1===b.length&&Object.assign(a,b[0])};
g.uh=function(a){sh(g.th,arguments)};
vh=function(a,b){var c=g.F(a,void 0);c?c.push(b):g.uh(a,[b])};
g.F=function(a,b){return a in g.th?g.th[a]:b};
g.xh=function(a){a=wh(a);return"string"===typeof a&&"false"===a?!1:!!a};
g.yh=function(a,b){var c=wh(a);return void 0===c&&void 0!==b?b:Number(c||0)};
wh=function(a){var b=g.F("EXPERIMENTS_FORCED_FLAGS",{});return void 0!==b[a]?b[a]:g.F("EXPERIMENT_FLAGS",{})[a]};
g.zh=function(){var a=[],b=g.F("EXPERIMENTS_FORCED_FLAGS",{});for(c in b)a.push({key:c,value:String(b[c])});var c=g.F("EXPERIMENT_FLAGS",{});for(var d in c)d.startsWith("force_")&&void 0===b[d]&&a.push({key:d,value:String(c[d])});return a};
g.Bh=function(a){var b=a.__yt_uid_key;b||(b=Ah(),a.__yt_uid_key=b);return b};
g.Dh=function(){var a=document;if("visibilityState"in a)return a.visibilityState;var b=Ch+"VisibilityState";if(b in a)return a[b]};
Fh=function(a){Eh.forEach(function(b){return b(a)})};
Hh=function(a){return a&&window.yterr?function(){try{return a.apply(this,arguments)}catch(b){g.Gh(b),Fh(b)}}:a};
g.Gh=function(a){var b=g.u("yt.logging.errors.log");b?b(a,"ERROR",void 0,void 0,void 0):(b=g.F("ERRORS",[]),b.push([a,"ERROR",void 0,void 0,void 0]),g.uh("ERRORS",b))};
g.Ih=function(a){var b=g.u("yt.logging.errors.log");b?b(a,"WARNING",void 0,void 0,void 0):(b=g.F("ERRORS",[]),b.push([a,"WARNING",void 0,void 0,void 0]),g.uh("ERRORS",b))};
g.Kh=function(a){this.type="";this.state=this.source=this.data=this.currentTarget=this.relatedTarget=this.target=null;this.charCode=this.keyCode=0;this.metaKey=this.shiftKey=this.ctrlKey=this.altKey=!1;this.rotation=this.clientY=this.clientX=0;this.changedTouches=this.touches=null;try{if(a=a||window.event){this.event=a;for(var b in a)b in Jh||(this[b]=a[b]);this.rotation=a.rotation;var c=a.target||a.srcElement;c&&3==c.nodeType&&(c=c.parentNode);this.target=c;var d=a.relatedTarget;if(d)try{d=d.nodeName?
d:null}catch(e){d=null}else"mouseover"==this.type?d=a.fromElement:"mouseout"==this.type&&(d=a.toElement);this.relatedTarget=d;this.clientX=void 0!=a.clientX?a.clientX:a.pageX;this.clientY=void 0!=a.clientY?a.clientY:a.pageY;this.keyCode=a.keyCode?a.keyCode:a.which;this.charCode=a.charCode||("keypress"==this.type?this.keyCode:0);this.altKey=a.altKey;this.ctrlKey=a.ctrlKey;this.shiftKey=a.shiftKey;this.metaKey=a.metaKey;this.i=a.pageX;this.l=a.pageY}}catch(e){}};
Lh=function(a){if(document.body&&document.documentElement){var b=document.body.scrollTop+document.documentElement.scrollTop;a.i=a.clientX+(document.body.scrollLeft+document.documentElement.scrollLeft);a.l=a.clientY+b}};
g.Mh=function(a){void 0===a.i&&Lh(a);return a.i};
g.Nh=function(a){void 0===a.l&&Lh(a);return a.l};
g.Ph=function(a,b,c,d){d=void 0===d?{}:d;a.addEventListener&&("mouseenter"!=b||"onmouseenter"in document?"mouseleave"!=b||"onmouseenter"in document?"mousewheel"==b&&"MozBoxSizing"in document.documentElement.style&&(b="MozMousePixelScroll"):b="mouseout":b="mouseover");return g.kb(Oh,function(e){var f="boolean"===typeof e[4]&&e[4]==!!d,k=g.Na(e[4])&&g.Na(d)&&g.pb(e[4],d);return!!e.length&&e[0]==a&&e[1]==b&&e[2]==c&&(f||k)})};
g.G=function(a,b,c,d){d=void 0===d?{}:d;if(!a||!a.addEventListener&&!a.attachEvent)return"";var e=g.Ph(a,b,c,d);if(e)return e;e=++Qh.count+"";var f=!("mouseenter"!=b&&"mouseleave"!=b||!a.addEventListener||"onmouseenter"in document);var k=f?function(l){l=new g.Kh(l);if(!g.ie(l.relatedTarget,function(m){return m==a},!0))return l.currentTarget=a,l.type=b,c.call(a,l)}:function(l){l=new g.Kh(l);
l.currentTarget=a;return c.call(a,l)};
k=Hh(k);a.addEventListener?("mouseenter"==b&&f?b="mouseover":"mouseleave"==b&&f?b="mouseout":"mousewheel"==b&&"MozBoxSizing"in document.documentElement.style&&(b="MozMousePixelScroll"),Rh()||"boolean"===typeof d?a.addEventListener(b,k,d):a.addEventListener(b,k,!!d.capture)):a.attachEvent("on"+b,k);Oh[e]=[a,b,c,k,d];return e};
g.Sh=function(a){a&&("string"==typeof a&&(a=[a]),g.A(a,function(b){if(b in Oh){var c=Oh[b],d=c[0],e=c[1],f=c[3];c=c[4];d.removeEventListener?Rh()||"boolean"===typeof c?d.removeEventListener(e,f,c):d.removeEventListener(e,f,!!c.capture):d.detachEvent&&d.detachEvent("on"+e,f);delete Oh[b]}}))};
g.Th=function(a){for(var b in Oh)Oh[b][0]==a&&g.Sh(b)};
g.Uh=function(a,b){"function"===typeof a&&(a=Hh(a));return window.setTimeout(a,b)};
g.Vh=function(a,b){"function"===typeof a&&(a=Hh(a));return window.setInterval(a,b)};
g.Wh=function(a){window.clearTimeout(a)};
g.Xh=function(a){window.clearInterval(a)};
g.Yh=function(a){this.qb=a;this.i=null;this.j=0;this.H=null;this.C=0;this.l=[];for(a=0;4>a;a++)this.l.push(0);this.D=0;this.M=g.G(window,"mousemove",(0,g.v)(this.R,this));this.K=g.Vh((0,g.v)(this.F,this),25)};
Zh=function(){};
g.ai=function(a,b){return $h(a,0,b)};
g.bi=function(a,b){return $h(a,1,b)};
g.di=function(a){for(var b=0,c=a.length;b<c;b++)g.ci(a[b])};
ei=function(){Zh.apply(this,arguments)};
g.fi=function(){return!!g.u("yt.scheduler.instance")};
$h=function(a,b,c){isNaN(c)&&(c=void 0);var d=g.u("yt.scheduler.instance.addJob");return d?d(a,b,c):void 0===c?(a(),NaN):g.Uh(a,c||0)};
g.ci=function(a){if(!isNaN(a)){var b=g.u("yt.scheduler.instance.cancelJob");b?b(a):g.Wh(a)}};
g.ii=function(a){var b=void 0===a?{}:a;a=void 0===b.Ft?!0:b.Ft;b=void 0===b.Yz?!1:b.Yz;if(null==g.u("_lact",window)){var c=parseInt(g.F("LACT"),10);c=isFinite(c)?g.Va()-Math.max(c,0):-1;g.r("_lact",c,window);g.r("_fact",c,window);-1==c&&g.gi();g.G(document,"keydown",g.gi);g.G(document,"keyup",g.gi);g.G(document,"mousedown",g.gi);g.G(document,"mouseup",g.gi);a&&(b?g.G(window,"touchmove",function(){hi("touchmove",200)},{passive:!0}):(g.G(window,"resize",function(){hi("resize",200)}),g.G(window,"scroll",
function(){hi("scroll",200)})));
new g.Yh(function(){hi("mouse",100)});
g.G(document,"touchstart",g.gi,{passive:!0});g.G(document,"touchend",g.gi,{passive:!0})}};
hi=function(a,b){ji[a]||(ji[a]=!0,g.bi(function(){g.gi();ji[a]=!1},b))};
g.gi=function(){null==g.u("_lact",window)&&g.ii();var a=g.Va();g.r("_lact",a,window);-1==g.u("_fact",window)&&g.r("_fact",a,window);(a=g.u("ytglobal.ytUtilActivityCallback_"))&&a()};
g.ki=function(){var a=g.u("_lact",window);return null==a?-1:Math.max(g.Va()-a,0)};
g.pi=function(a,b,c){var d=li();if(d&&b){var e=d.subscribe(a,function(){var f=arguments;var k=function(){mi[e]&&b.apply&&"function"==typeof b.apply&&b.apply(c||window,f)};
try{ni[a]?k():g.Uh(k,0)}catch(l){g.Gh(l)}},c);
mi[e]=!0;oi[a]||(oi[a]=[]);oi[a].push(e);return e}return 0};
g.qi=function(a){var b=li();b&&("number"===typeof a?a=[a]:"string"===typeof a&&(a=[parseInt(a,10)]),g.A(a,function(c){b.unsubscribeByKey(c);delete mi[c]}))};
g.H=function(a,b){var c=li();return c?c.publish.apply(c,arguments):!1};
g.ri=function(a,b){ni[a]=!0;var c=li();c=c?c.publish.apply(c,arguments):!1;ni[a]=!1;return c};
li=function(){return g.q.ytPubsubPubsubInstance};
g.yi=function(a){a=void 0===a?!1:a;return new g.ag(function(b){g.Wh(g.si);g.Wh(g.ti);g.ti=0;ui&&ui.isReady()?(vi(b,a),g.wi.clear()):(g.xi(),b())})};
g.xi=function(){g.xh("web_gel_timeout_cap")&&!g.ti&&(g.ti=g.Uh(g.yi,6E4));g.Wh(g.si);var a=g.yh("web_gel_debounce_ms",1E4);a=g.F("LOGGING_BATCH_TIMEOUT",g.zi||a);g.xh("shorten_initial_gel_batch_timeout")&&Ai&&(a=Bi);g.si=g.Uh(g.yi,a)};
vi=function(a,b){var c=ui;b=void 0===b?!1:b;for(var d=Math.round((0,g.Ci)()),e=g.wi.size,f=g.ja(g.wi),k=f.next();!k.done;k=f.next()){var l=g.ja(k.value);k=l.next().value;var m=l.next().value;l=g.rb({context:g.Di(c.i||g.Ei())});l.events=m;(m=Fi[k])&&Gi(l,k,m);delete Fi[k];Hi(l,d);g.Ii(c,"log_event",l,{retry:!0,onSuccess:function(){e--;e||a();Ji=Math.round((0,g.Ci)()-d)},
onError:function(){e--;e||a()},
lB:b});Ai=!1}};
Hi=function(a,b){a.requestTimeMs=String(b);g.xh("unsplit_gel_payloads_in_logs")&&(a.unsplitGelPayloadsInLogs=!0);var c=g.F("EVENT_ID",void 0);if(c){var d=g.F("BATCH_CLIENT_COUNTER",void 0)||0;!d&&g.xh("web_client_counter_random_seed")&&(d=Math.floor(Math.random()*Ki/2));d++;d>Ki&&(d=1);g.uh("BATCH_CLIENT_COUNTER",d);c={serializedEventId:c,clientCounter:String(d)};a.serializedClientEventId=c;Li&&Ji&&g.xh("log_gel_rtt_web")&&(a.previousBatchInfo={serializedClientEventId:Li,roundtripMs:String(Ji)});
Li=c;Ji=0}};
Gi=function(a,b,c){if(c.videoId)var d="VIDEO";else if(c.playlistId)d="PLAYLIST";else return;a.credentialTransferTokenTargetId=c;a.context=a.context||{};a.context.user=a.context.user||{};a.context.user.credentialTransferTokens=[{token:b,scope:d}]};
g.Pi=function(a,b,c,d){d=void 0===d?{}:d;var e={};e.eventTimeMs=Math.round(d.timestamp||(0,g.Ci)());e[a]=b;a=g.ki();e.context={lastActivityMs:String(d.timestamp||!isFinite(a)?-1:a)};g.xh("log_sequence_info_on_gel_web")&&d.vd&&(a=e.context,b=d.vd,Mi[b]=b in Mi?Mi[b]+1:0,a.sequence={index:Mi[b],groupKey:b},d.iq&&delete Mi[d.vd]);d=d.xe;a="";d&&(a={},d.videoId?a.videoId=d.videoId:d.playlistId&&(a.playlistId=d.playlistId),Fi[d.token]=a,a=d.token);d=g.wi.get(a)||[];g.wi.set(a,d);d.push(e);c&&(ui=new c);
c=g.yh("web_logging_max_batch")||100;e=(0,g.Ci)();d.length>=c?g.yi(!0):e-g.Ni>=g.Oi&&(g.xi(),g.Ni=e)};
Ri=function(a){for(var b=a.split("&"),c={},d=0,e=b.length;d<e;d++){var f=b[d].split("=");if(1==f.length&&f[0]||2==f.length)try{var k=g.tc(f[0]||""),l=g.tc(f[1]||"");k in c?Array.isArray(c[k])?g.hb(c[k],l):c[k]=[c[k],l]:c[k]=l}catch(m){m.args=[{key:f[0],value:f[1],query:a}],Qi.hasOwnProperty(f[0])||("ReferenceError"===m.name?g.Ih(m):g.Gh(m))}}return c};
g.Si=function(a){var b=[];g.ib(a,function(c,d){var e=g.sc(d),f;Array.isArray(c)?f=c:f=[c];g.A(f,function(k){""==k?b.push(e):b.push(e+"="+g.sc(k))})});
return b.join("&")};
g.Ti=function(a){"?"==a.charAt(0)&&(a=a.substr(1));return Ri(a)};
g.Vi=function(a,b){return Ui(a,b||{},!0)};
g.Wi=function(a,b){return Ui(a,b||{},!1)};
Ui=function(a,b,c){var d=a.split("#",2);a=d[0];d=1<d.length?"#"+d[1]:"";var e=a.split("?",2);a=e[0];e=g.Ti(e[1]||"");for(var f in b)!c&&null!==e&&f in e||(e[f]=b[f]);return g.Hc(a,e)+d};
g.Xi=function(a){if(!b)var b=window.location.href;var c=g.zc(1,a),d=g.Ac(a);c&&d?(a=a.match(g.yc),b=b.match(g.yc),a=a[3]==b[3]&&a[1]==b[1]&&a[4]==b[4]):a=d?g.Ac(b)==d&&(Number(g.zc(4,b))||null)==(Number(g.zc(4,a))||null):!0;return a};
g.aj=function(a){var b=Yi;a=void 0===a?g.u("yt.ads.biscotti.lastId_")||"":a;var c=Object,d=c.assign,e={};e.dt=Zi;e.flash="0";a:{try{var f=b.i.top.location.href}catch(M){f=2;break a}f=f?f===b.l.location.href?0:1:2}e=(e.frm=f,e);e.u_tz=-(new Date).getTimezoneOffset();var k=void 0===k?$i:k;try{var l=k.history.length}catch(M){l=0}e.u_his=l;e.u_java=!!$i.navigator&&"unknown"!==typeof $i.navigator.javaEnabled&&!!$i.navigator.javaEnabled&&$i.navigator.javaEnabled();$i.screen&&(e.u_h=$i.screen.height,e.u_w=
$i.screen.width,e.u_ah=$i.screen.availHeight,e.u_aw=$i.screen.availWidth,e.u_cd=$i.screen.colorDepth);$i.navigator&&$i.navigator.plugins&&(e.u_nplug=$i.navigator.plugins.length);$i.navigator&&$i.navigator.mimeTypes&&(e.u_nmime=$i.navigator.mimeTypes.length);l=b.i;try{var m=l.screenX;var n=l.screenY}catch(M){}try{var t=l.outerWidth;var w=l.outerHeight}catch(M){}try{var x=l.innerWidth;var D=l.innerHeight}catch(M){}m=[l.screenLeft,l.screenTop,m,n,l.screen?l.screen.availWidth:void 0,l.screen?l.screen.availTop:
void 0,t,w,x,D];try{var O=g.Zd(b.i.top).round()}catch(M){O=new g.Td(-12245933,-12245933)}n=O;O={};t=new zg;g.q.SVGElement&&g.q.document.createElementNS&&t.set(0);w=ne();w["allow-top-navigation-by-user-activation"]&&t.set(1);w["allow-popups-to-escape-sandbox"]&&t.set(2);g.q.crypto&&g.q.crypto.subtle&&t.set(3);g.q.TextDecoder&&g.q.TextEncoder&&t.set(4);t=Bg(t);O.bc=t;O.bih=n.height;O.biw=n.width;O.brdim=m.join();b=b.l;b=(O.vis={visible:1,hidden:2,prerender:3,preview:4,unloaded:5}[b.visibilityState||
b.webkitVisibilityState||b.mozVisibilityState||""]||0,O.wgl=!!$i.WebGLRenderingContext,O);c=d.call(c,e,b);c.ca_type="image";a&&(c.bid=a);return c};
cj=function(){if(!bj)return null;var a=bj();return"open"in a?a:null};
g.dj=function(a){switch(a&&"status"in a?a.status:-1){case 200:case 201:case 202:case 203:case 204:case 205:case 206:case 304:return!0;default:return!1}};
fj=function(a,b){b=void 0===b?{}:b;var c=g.Xi(a),d=g.xh("web_ajax_ignore_global_headers_if_set"),e;for(e in ej){var f=g.F(ej[e]);!f||!c&&g.Ac(a)||d&&void 0!==b[e]||(b[e]=f)}if(c||!g.Ac(a))b["X-YouTube-Utc-Offset"]=String(-(new Date).getTimezoneOffset());(c||!g.Ac(a))&&(d="undefined"!=typeof Intl?(new Intl.DateTimeFormat).resolvedOptions().timeZone:null)&&(b["X-YouTube-Time-Zone"]=d);if(c||!g.Ac(a))b["X-YouTube-Ad-Signals"]=g.Si(g.aj(void 0));return b};
hj=function(a){var b=window.location.search,c=g.Ac(a),d=g.xc(g.zc(5,a));d=(c=c&&(c.endsWith("youtube.com")||c.endsWith("youtube-nocookie.com")))&&d&&d.startsWith("/api/");if(!c||d)return a;var e=g.Ti(b),f={};g.A(gj,function(k){e[k]&&(f[k]=e[k])});
return g.Wi(a,f)};
kj=function(a,b){if(window.fetch&&"XML"!=b.format){var c={method:b.method||"GET",credentials:"same-origin"};b.headers&&(c.headers=b.headers);a=ij(a,b);var d=jj(a,b);d&&(c.body=d);b.withCredentials&&(c.credentials="include");var e=!1,f;fetch(a,c).then(function(k){if(!e){e=!0;f&&g.Wh(f);var l=k.ok,m=function(n){n=n||{};var t=b.context||g.q;l?b.onSuccess&&b.onSuccess.call(t,n,k):b.onError&&b.onError.call(t,n,k);b.sa&&b.sa.call(t,n,k)};
"JSON"==(b.format||"JSON")&&(l||400<=k.status&&500>k.status)?k.json().then(m,function(){m(null)}):m(null)}});
b.cn&&0<b.timeout&&(f=g.Uh(function(){e||(e=!0,g.Wh(f),b.cn.call(b.context||g.q))},b.timeout))}else g.I(a,b)};
g.I=function(a,b){var c=b.format||"JSON";a=ij(a,b);var d=jj(a,b),e=!1,f=lj(a,function(m){if(!e){e=!0;l&&g.Wh(l);var n=g.dj(m),t=null,w=400<=m.status&&500>m.status,x=500<=m.status&&600>m.status;if(n||w||x)t=mj(a,c,m,b.Ga);if(n)a:if(m&&204==m.status)n=!0;else{switch(c){case "XML":n=0==parseInt(t&&t.return_code,10);break a;case "RAW":n=!0;break a}n=!!t}t=t||{};w=b.context||g.q;n?b.onSuccess&&b.onSuccess.call(w,m,t):b.onError&&b.onError.call(w,m,t);b.sa&&b.sa.call(w,m,t)}},b.method,d,b.headers,b.responseType,
b.withCredentials);
if(b.ic&&0<b.timeout){var k=b.ic;var l=g.Uh(function(){e||(e=!0,f.abort(),g.Wh(l),k.call(b.context||g.q,f))},b.timeout)}return f};
ij=function(a,b){b.AJ&&(a=document.location.protocol+"//"+document.location.hostname+(document.location.port?":"+document.location.port:"")+a);var c=g.F("XSRF_FIELD_NAME",void 0),d=b.va;d&&(d[c]&&delete d[c],a=g.Vi(a,d));return a};
jj=function(a,b){var c=g.F("XSRF_FIELD_NAME",void 0),d=g.F("XSRF_TOKEN",void 0),e=b.postBody||"",f=b.ba,k=g.F("XSRF_FIELD_NAME",void 0),l;b.headers&&(l=b.headers["Content-Type"]);b.pJ||g.Ac(a)&&!b.withCredentials&&g.Ac(a)!=document.location.hostname||"POST"!=b.method||l&&"application/x-www-form-urlencoded"!=l||b.ba&&b.ba[k]||(f||(f={}),f[c]=d);f&&"string"===typeof e&&(e=g.Ti(e),g.tb(e,f),e=b.Qn&&"JSON"==b.Qn?JSON.stringify(e):g.Gc(e));f=e||f&&!g.lb(f);!nj&&f&&"POST"!=b.method&&(nj=!0,g.Gh(Error("AJAX request with postData should use POST")));
return e};
mj=function(a,b,c,d){var e=null;switch(b){case "JSON":try{var f=c.responseText}catch(k){throw d=Error("Error reading responseText"),d.params=a,g.Ih(d),k;}a=c.getResponseHeader("Content-Type")||"";f&&0<=a.indexOf("json")&&(")]}'\n"===f.substring(0,5)&&(f=f.substring(5)),e=JSON.parse(f));break;case "XML":if(a=(a=c.responseXML)?oj(a):null)e={},g.A(a.getElementsByTagName("*"),function(k){e[k.tagName]=g.pj(k)})}d&&qj(e);
return e};
qj=function(a){if(g.Na(a))for(var b in a)"html_content"==b||g.Cb(b,"_html")?a[b]=g.pc(g.Ab("HTML that is escaped and sanitized server-side and passed through yt.net.ajax"),a[b]):qj(a[b])};
oj=function(a){return a?(a=("responseXML"in a?a.responseXML:a).getElementsByTagName("root"))&&0<a.length?a[0]:null:null};
g.pj=function(a){var b="";g.A(a.childNodes,function(c){b+=c.nodeValue});
return b};
g.rj=function(a,b){b.method="POST";b.ba||(b.ba={});return g.I(a,b)};
lj=function(a,b,c,d,e,f,k){function l(){4==(m&&"readyState"in m?m.readyState:0)&&b&&Hh(b)(m)}
c=void 0===c?"GET":c;d=void 0===d?"":d;var m=cj();if(!m)return null;"onloadend"in m?m.addEventListener("loadend",l,!1):m.onreadystatechange=l;g.xh("debug_forward_web_query_parameters")&&(a=hj(a));m.open(c,a,!0);f&&(m.responseType=f);k&&(m.withCredentials=!0);c="POST"==c&&(void 0===window.FormData||!(d instanceof FormData));if(e=fj(a,e))for(var n in e)m.setRequestHeader(n,e[n]),"content-type"==n.toLowerCase()&&(c=!1);c&&m.setRequestHeader("Content-Type","application/x-www-form-urlencoded");m.send(d);
return m};
sj=function(){return"INNERTUBE_API_KEY"in g.th&&"INNERTUBE_API_VERSION"in g.th};
g.Ei=function(){return{innertubeApiKey:g.F("INNERTUBE_API_KEY",void 0),innertubeApiVersion:g.F("INNERTUBE_API_VERSION",void 0),It:g.F("INNERTUBE_CONTEXT_CLIENT_CONFIG_INFO"),Jt:g.F("INNERTUBE_CONTEXT_CLIENT_NAME","WEB"),innertubeContextClientVersion:g.F("INNERTUBE_CONTEXT_CLIENT_VERSION",void 0),Lt:g.F("INNERTUBE_CONTEXT_HL",void 0),Kt:g.F("INNERTUBE_CONTEXT_GL",void 0),Mt:g.F("INNERTUBE_HOST_OVERRIDE",void 0)||"",Ot:!!g.F("INNERTUBE_USE_THIRD_PARTY_AUTH",!1),Nt:!!g.F("INNERTUBE_OMIT_API_KEY_WHEN_AUTH_HEADER_IS_PRESENT",
!1),appInstallData:g.F("SERIALIZED_CLIENT_CONFIG_DATA",void 0)}};
g.Di=function(a){var b={client:{hl:a.Lt,gl:a.Kt,clientName:a.Jt,clientVersion:a.innertubeContextClientVersion,configInfo:a.It}},c=window.devicePixelRatio;c&&1!=c&&(b.client.screenDensityFloat=String(c));c=g.F("EXPERIMENTS_TOKEN","");""!==c&&(b.client.experimentsToken=c);c=g.zh();0<c.length&&(b.request={internalExperimentFlags:c});a.appInstallData&&g.xh("web_log_app_install_experiments")&&(b.client.configInfo=b.client.configInfo||{},b.client.configInfo.appInstallData=a.appInstallData);g.F("DELEGATED_SESSION_ID")&&
!g.xh("pageid_as_header_web")&&(b.user={onBehalfOfUser:g.F("DELEGATED_SESSION_ID")});a=Object;c=a.assign;for(var d=b.client,e={},f=g.ja(Object.entries(g.Ti(g.F("DEVICE","")))),k=f.next();!k.done;k=f.next()){var l=g.ja(k.value);k=l.next().value;l=l.next().value;"cbrand"===k?e.deviceMake=l:"cmodel"===k?e.deviceModel=l:"cbr"===k?e.browserName=l:"cbrver"===k?e.browserVersion=l:"cos"===k?e.osName=l:"cosver"===k?e.osVersion=l:"cplatform"===k&&(e.platform=l)}b.client=c.call(a,d,e);return b};
tj=function(a,b,c){c=void 0===c?{}:c;var d={"X-Goog-Visitor-Id":c.visitorData||g.F("VISITOR_DATA","")};if(b&&b.includes("www.youtube-nocookie.com"))return d;(b=c.fJ||g.F("AUTHORIZATION"))||(a?b="Bearer "+g.u("gapi.auth.getToken")().dJ:b=g.He([]));b&&(d.Authorization=b,d["X-Goog-AuthUser"]=g.F("SESSION_INDEX",0),g.xh("pageid_as_header_web")&&(d["X-Goog-PageId"]=g.F("DELEGATED_SESSION_ID")));return d};
uj=function(a){a=Object.assign({},a);delete a.Authorization;var b=g.He();if(b){var c=new Eg;c.update(g.F("INNERTUBE_API_KEY",void 0));c.update(b);a.hash=g.hd(c.digest(),3)}return a};
g.wj=function(a,b,c,d,e){g.vj.set(""+a,b,{tm:c,path:"/",domain:void 0===d?"youtube.com":d,secure:void 0===e?!1:e})};
g.xj=function(a,b){return g.vj.get(""+a,b)};
g.yj=function(a,b,c){g.vj.remove(""+a,void 0===b?"/":b,void 0===c?"youtube.com":c)};
g.zj=function(a){var b;(b=g.rh(a))||(a=new lh(a||"UserDataSharedStore"),b=a.isAvailable()?a:null);this.i=(a=b)?new g.eh(a):null;this.l=document.domain||window.location.hostname};
Bj=function(){Aj||(Aj=new g.zj("yt.innertube"));return Aj};
Cj=function(a,b,c,d){if(d)return null;d=Bj().get("nextId",!0)||1;var e=Bj().get("requests",!0)||{};e[d]={method:a,request:b,authState:uj(c),requestTime:Math.round((0,g.Ci)())};Bj().set("nextId",d+1,86400,!0);Bj().set("requests",e,86400,!0);return d};
Dj=function(a){var b=Bj().get("requests",!0)||{};delete b[a];Bj().set("requests",b,86400,!0)};
Ej=function(a){var b=Bj().get("requests",!0);if(b){for(var c in b){var d=b[c];if(!(6E4>Math.round((0,g.Ci)())-d.requestTime)){var e=d.authState,f=uj(tj(!1));g.pb(e,f)&&(e=d.request,"requestTimeMs"in e&&(e.requestTimeMs=Math.round((0,g.Ci)())),g.Ii(a,d.method,e,{}));delete b[c]}}Bj().set("requests",b,86400,!0)}};
g.Fj=function(a,b){this.version=a;this.args=b};
g.Gj=function(a,b){this.topic=a;this.i=b};
g.Ij=function(a,b){var c=Hj();c&&c.publish.call(c,a.toString(),a,b)};
g.Mj=function(a,b,c){var d=Hj();if(!d)return 0;var e=d.subscribe(a.toString(),function(f,k){var l=g.u("ytPubsub2Pubsub2SkipSubKey");l&&l==e||(l=function(){if(Jj[e])try{if(k&&a instanceof g.Gj&&a!=f)try{var m=a.i,n=k;if(!n.args||!n.version)throw Error("yt.pubsub2.Data.deserialize(): serializedData is incomplete.");try{if(!m.xd){var t=new m;m.xd=t.version}var w=m.xd}catch(x){}if(!w||n.version!=w)throw Error("yt.pubsub2.Data.deserialize(): serializedData version is incompatible.");try{k=Reflect.construct(m,
g.gb(n.args))}catch(x){throw x.message="yt.pubsub2.Data.deserialize(): "+x.message,x;}}catch(x){throw x.message="yt.pubsub2.pubsub2 cross-binary conversion error for "+a.toString()+": "+x.message,x;}b.call(c||window,k)}catch(x){g.Gh(x)}},Kj[a.toString()]?g.fi()?g.bi(l):g.Uh(l,0):l())});
Jj[e]=!0;Lj[a.toString()]||(Lj[a.toString()]=[]);Lj[a.toString()].push(e);return e};
Qj=function(){var a=Nj,b=g.Mj(Oj,function(c){a.apply(void 0,arguments);g.Pj(b)},void 0);
return b};
g.Pj=function(a){var b=Hj();b&&("number"===typeof a&&(a=[a]),g.A(a,function(c){b.unsubscribeByKey(c);delete Jj[c]}))};
Hj=function(){return g.u("ytPubsub2Pubsub2Instance")};
Uj=function(a){Rj||(Sj?Sj.i(a):(Tj.push({type:"ERROR",payload:a}),10<Tj.length&&Tj.shift()))};
Vj=function(a,b){Rj||(Sj?Sj.logEvent(a,b):(Tj.push({type:"EVENT",eventType:a,payload:b}),10<Tj.length&&Tj.shift()))};
Wj=function(a){if(!a)throw Error();throw a;};
Xj=function(a){return a};
Yj=function(a){var b=this;this.l=a;this.state={status:"PENDING"};this.i=[];this.onRejected=[];this.l(function(c){if("PENDING"===b.state.status){b.state={status:"FULFILLED",value:c};c=g.ja(b.i);for(var d=c.next();!d.done;d=c.next())d=d.value,d()}},function(c){if("PENDING"===b.state.status){b.state={status:"REJECTED",
reason:c};c=g.ja(b.onRejected);for(var d=c.next();!d.done;d=c.next())d=d.value,d()}})};
ak=function(a,b,c,d,e){try{if("FULFILLED"!==a.state.status)throw Error("calling handleResolve before the promise is fulfilled.");var f=c(a.state.value);f instanceof Yj?Zj(a,b,f,d,e):d(f)}catch(k){e(k)}};
bk=function(a,b,c,d,e){try{if("REJECTED"!==a.state.status)throw Error("calling handleReject before the promise is rejected.");var f=c(a.state.reason);f instanceof Yj?Zj(a,b,f,d,e):d(f)}catch(k){e(k)}};
Zj=function(a,b,c,d,e){b===c?e(new TypeError("Circular promise chain detected.")):c.then(function(f){f instanceof Yj?Zj(a,b,f,d,e):d(f)},function(f){e(f)})};
ck=function(a,b,c){function d(){c(a.error);f()}
function e(){b(a.result);f()}
function f(){try{a.removeEventListener("success",e),a.removeEventListener("error",d)}catch(k){}}
a.addEventListener("success",e);a.addEventListener("error",d)};
dk=function(a){return new Promise(function(b,c){ck(a,b,c)})};
ek=function(a){return new Yj(function(b,c){ck(a,b,c)})};
fk=function(a,b){return new Yj(function(c,d){function e(){var f=a?b(a):null;f?f.then(function(k){a=k;e()},d):c()}
e()})};
g.gk=function(a,b){for(var c=[],d=1;d<arguments.length;++d)c[d-1]=arguments[d];d=Error.call(this,a);this.message=d.message;"stack"in d&&(this.stack=d.stack);this.args=[].concat(g.ma(c))};
ik=function(a,b,c){b=void 0===b?{}:b;c=void 0===c?hk[a]:c;g.gk.call(this,c,Object.assign({name:"YtIdbKnownError",isSw:void 0===self.document,isIframe:self!==self.top,type:a},b));this.type=a;this.message=c;Object.setPrototypeOf(this,ik.prototype);Uj(this)};
jk=function(a,b,c){ik.call(this,"UNKNOWN_ABORT",{objectStoreNames:a,dbName:b,mode:c});Object.setPrototypeOf(this,jk.prototype)};
kk=function(a){ik.call(this,"MISSING_OBJECT_STORE",{CJ:a},hk.MISSING_OBJECT_STORE);Object.setPrototypeOf(this,kk.prototype)};
lk=function(a,b){this.i=a;this.options=b;this.transactionCount=0;this.j=Math.round((0,g.Ci)());this.l=!1};
qk=function(a,b,c,d){c=void 0===c?"readonly":c;a.transactionCount++;var e=a.i.transaction(b,c);e=new mk(e);d=ok(e,d);pk(a,d,b.join(),c);return d};
pk=function(a,b,c,d){g.Lc(a,function f(){var k,l,m=this,n,t,w;return g.Ba(f,function(x){if(1==x.i)return k=Math.round((0,g.Ci)()),g.ta(x,2),g.sa(x,b,4);if(2!=x.i)l=Math.round((0,g.Ci)()),rk(m,!0,c,l-k),x.i=0,x.C=0;else{n=g.ua(x);t=Math.round((0,g.Ci)());var D=n,O=m.i.name,M=m.transactionCount,la;"QuotaExceededError"===D.name?la=new ik("QUOTA_EXCEEDED",{objectStoreNames:c,dbName:O,mode:d}):"UnknownError"===D.name&&(la=new ik("QUOTA_MAYBE_EXCEEDED",{objectStoreNames:c,dbName:O,mode:d}));la&&Vj("QUOTA_EXCEEDED",
{dbName:O,objectStoreNames:c,transactionCount:M,transactionMode:d});w=t-k;n instanceof jk&&(Vj("TRANSACTION_UNEXPECTEDLY_ABORTED",{objectStoreNames:c,transactionDuration:w,transactionCount:m.transactionCount,dbDuration:t-m.j}),m.l=!0);rk(m,!1,c,w);x.i=0}})})};
rk=function(a,b,c,d){Vj("TRANSACTION_ENDED",{objectStoreNames:c,connectionHasUnknownAbortedTransaction:a.l,duration:d,isSuccessful:b})};
sk=function(a){this.i=a};
uk=function(a,b){return tk(a,{query:b},function(c){return c["delete"]().then(function(){return c["continue"]()})}).then(function(){})};
tk=function(a,b,c){a=a.i.openCursor(b.query,b.direction);return vk(a).then(function(d){return fk(d,c)})};
mk=function(a){var b=this;this.i=a;this.l=new Map;this.aborted=!1;this.done=new Promise(function(c,d){b.i.addEventListener("complete",function(){c()});
b.i.addEventListener("error",function(e){e.currentTarget===e.target&&d(b.i.error)});
b.i.addEventListener("abort",function(){var e=b.i.error;if(e)d(e);else if(!b.aborted){e=jk;for(var f=b.i.objectStoreNames,k=[],l=0;l<f.length;l++){var m=f.item(l);if(null===m)throw Error("Invariant: item in DOMStringList is null");k.push(m)}e=new e(k.join(),b.i.db.name,b.i.mode);d(e)}})})};
ok=function(a,b){var c=new Promise(function(d,e){b(a).then(function(f){a.commit();d(f)})["catch"](e)});
return Promise.all([c,a.done]).then(function(d){return g.ja(d).next().value})};
wk=function(a,b){var c=a.i.objectStore(b),d=a.l.get(c);d||(d=new sk(c),a.l.set(c,d));return d};
xk=function(a){this.i=a};
yk=function(a,b,c){a=a.i.openCursor(void 0===b.query?null:b.query,void 0===b.direction?"next":b.direction);return vk(a).then(function(d){return fk(d,c)})};
zk=function(a,b){this.request=a;this.cursor=b};
vk=function(a){return ek(a).then(function(b){return null===b?null:new zk(a,b)})};
Ak=function(a,b,c){return g.Lc(this,function e(){var f,k,l,m,n,t,w,x,D,O;return g.Ba(e,function(M){if(1==M.i)return f=self.indexedDB.open(a,b),k=c,l=k.blocked,m=k.blocking,n=k.VA,t=k.upgrade,w=k.closed,D=function(){x||(x=new lk(f.result,{closed:w}));return x},f.addEventListener("upgradeneeded",function(la){if(null===la.newVersion)throw Error("Invariant: newVersion on IDbVersionChangeEvent is null");
if(null===f.transaction)throw Error("Invariant: transaction on IDbOpenDbRequest is null");la.dataLoss&&"none"!==la.dataLoss&&Vj("IDB_DATA_CORRUPTED",{reason:la.dataLossMessage||"unknown reason",dbName:a});var ya=D(),Pa=new mk(f.transaction);t&&t(ya,la.oldVersion,la.newVersion,Pa)}),l&&f.addEventListener("blocked",function(){l()}),g.sa(M,dk(f),2);
O=M.l;m&&O.addEventListener("versionchange",function(){m(D())});
O.addEventListener("close",function(){Vj("IDB_UNEXPECTEDLY_CLOSED",{dbName:a,dbVersion:O.version});n&&n()});
return M["return"](D())})})};
Bk=function(a,b,c){c=void 0===c?{}:c;return Ak(a,b,c)};
Ck=function(a,b){b=void 0===b?{}:b;return g.Lc(this,function d(){var e,f,k;return g.Ba(d,function(l){e=self.indexedDB.deleteDatabase(a);f=b;(k=f.blocked)&&e.addEventListener("blocked",function(){k()});
return g.sa(l,dk(e),0)})})};
Dk=function(a){var b=g.dc;return b?0<=b.toLowerCase().indexOf(a):!1};
Ek=function(a,b){this.name=a;this.options=b;this.l=!1};
Gk=function(a){return g.Lc(this,function c(){var d;return g.Ba(c,function(e){if(1==e.i)return g.sa(e,Fk.open(),2);d=e.l;return e["return"](qk(d,["databases"],"readwrite",function(f){var k=wk(f,"databases");return k.get(a.actualName).then(function(l){if(l?a.actualName!==l.actualName||a.publicName!==l.publicName||a.userIdentifier!==l.userIdentifier||a.signedIn!==l.signedIn||a.clearDataOnAuthChange!==l.clearDataOnAuthChange:1)return ek(k.i.put(a,void 0)).then(function(){})})}))})})};
Hk=function(a){return g.Lc(this,function c(){var d;return g.Ba(c,function(e){if(1==e.i)return g.sa(e,Fk.open(),2);d=e.l;return e["return"](d["delete"]("databases",a))})})};
Ik=function(a){return g.Lc(this,function c(){var d,e;return g.Ba(c,function(f){return 1==f.i?(d=[],g.sa(f,Fk.open(),2)):3!=f.i?(e=f.l,g.sa(f,qk(e,["databases"],"readonly",function(k){return tk(wk(k,"databases"),{},function(l){a(l.getValue())&&d.push(l.getValue());return l["continue"]()})}),3)):f["return"](d)})})};
Jk=function(a){return Ik(function(b){return b.publicName===a})};
Kk=function(a){return Ik(function(b){return b.userIdentifier===a})};
Lk=function(a,b){return Ik(function(c){return!!c.clearDataOnAuthChange&&(c.userIdentifier!==a||c.signedIn!==b)})};
Pk=function(){var a=this;Mk&&(Nk||(Nk=g.bi(function(){return g.Lc(a,function c(){var d,e,f,k,l,m;return g.Ba(c,function(n){switch(n.i){case 1:return e=Ok,f=!0,g.ta(n,2),g.sa(n,Lk(null===e||void 0===e?void 0:e.userIdentifier,null!==(d=null===e||void 0===e?void 0:e.signedIn)&&void 0!==d?d:!1),4);case 4:k=n.l;if(!k.length){f=!1;n.mb(5);break}l=k[0];return g.sa(n,Ck(l.actualName),6);case 6:return g.sa(n,Hk(l.actualName),5);case 5:n.i=3;n.C=0;break;case 2:m=g.ua(n),Uj(m),f=!1;case 3:g.ci(Nk),Nk=0,f&&Pk(),
n.i=0}})})})))};
Tk=function(){return g.Lc(this,function b(){var c,d,e;return g.Ba(b,function(f){switch(f.i){case 1:var k;if(k=Qk||Rk)k=/WebKit\/([0-9]+)/.exec(g.dc),k=!!(k&&600<=parseInt(k[1],10));k&&(k=/WebKit\/([0-9]+)/.exec(g.dc),k=!(k&&602<=parseInt(k[1],10)));if(k&&!g.xh("ytidb_allow_on_ios_safari_v8_and_v9")||g.Sk)return f["return"](!1);try{if(c=self,!(c.indexedDB&&c.IDBIndex&&c.IDBKeyRange&&c.IDBObjectStore))return f["return"](!1)}catch(l){return f["return"](!1)}if(!("IDBTransaction"in self&&"objectStoreNames"in
IDBTransaction.prototype))return f["return"](!1);if(!g.xh("ytidb_new_supported_check_with_delete")){f.mb(2);break}g.ta(f,3);return g.sa(f,Hk("yt-idb-test-do-not-use"),5);case 5:return f["return"](!0);case 3:return g.ua(f),f["return"](!1);case 2:if(!g.xh("ytidb_new_supported_check_with_add_and_delete")){f.mb(6);break}g.ta(f,7);d={actualName:"yt-idb-test-do-not-use",publicName:"yt-idb-test-do-not-use",userIdentifier:void 0,signedIn:!1};return g.sa(f,Gk(d),9);case 9:return g.sa(f,Hk("yt-idb-test-do-not-use"),
10);case 10:return f["return"](!0);case 7:return g.ua(f),f["return"](!1);case 6:return g.ta(f,11,12),g.sa(f,Bk("yt-idb-test-do-not-use"),14);case 14:if(e=f.l,!e)return f["return"](!1);case 12:f.R=[f.j];f.C=0;f.H=0;if(e)try{e.close()}catch(l){}k=f.R.splice(0)[0];(k=f.j=f.j||k)?k.nm?f.i=f.C||f.H:void 0!=k.mb&&f.H<k.mb?(f.i=k.mb,f.j=null):f.i=f.H:f.i=13;break;case 11:return g.ua(f),f["return"](!1);case 13:return f["return"](!0)}})})};
Vk=function(){if(void 0!==Uk)return Uk;var a=(0,g.Ci)();Rj=!0;return Uk=Tk().then(function(b){Rj=!1;Vj("IS_SUPPORTED_COMPLETED",{duration:Math.round((0,g.Ci)()-a),isSupported:b});return b})};
Wk=function(a){if(0<=a.indexOf(":"))throw Error("Database name cannot contain ':'");};
Xk=function(a){return{actualName:a,publicName:a,userIdentifier:void 0,signedIn:!1}};
Yk=function(a){if(!Mk)return Xk(a);var b=Ok;if(!b)throw new ik("AUTH_INVALID");return{actualName:a+":"+b.userIdentifier,publicName:a,userIdentifier:b.userIdentifier,signedIn:b.signedIn}};
$k=function(a,b,c,d){var e;return g.Lc(this,function k(){var l,m;return g.Ba(k,function(n){switch(n.i){case 1:return g.sa(n,Zk(),2);case 2:return Wk(a),c?l=Xk(a):l=Yk(a),l.clearDataOnAuthChange=null!==(e=d.clearDataOnAuthChange)&&void 0!==e?e:!1,g.ta(n,3),g.sa(n,Gk(l),5);case 5:return g.sa(n,Bk(l.actualName,b,d),6);case 6:return n["return"](n.l);case 3:return m=g.ua(n),g.ta(n,7),g.sa(n,Hk(l.actualName),9);case 9:n.i=8;n.C=0;break;case 7:g.ua(n);case 8:throw m;}})})};
Zk=function(){return g.Lc(this,function b(){var c;return g.Ba(b,function(d){if(1==d.i)return g.sa(d,Vk(),2);c=d.l;if(!c)throw new ik("IDB_NOT_SUPPORTED");d.i=0})})};
al=function(a,b,c){c=void 0===c?{}:c;return $k(a,b,!1,c)};
bl=function(a,b,c){c=void 0===c?{}:c;return $k(a,b,!0,c)};
cl=function(a,b){b=void 0===b?{}:b;return g.Lc(this,function d(){var e;return g.Ba(d,function(f){return 1==f.i?(Wk(a),g.sa(f,Zk(),2)):3!=f.i?(e=Yk(a),g.sa(f,Ck(e.actualName,b),3)):g.sa(f,Hk(e.actualName),0)})})};
dl=function(a,b){var c=this,d=a.map(function(e){return g.Lc(c,function k(){return g.Ba(k,function(l){return 1==l.i?g.sa(l,Ck(e.actualName,b),2):g.sa(l,Hk(e.actualName),0)})})});
return Promise.all(d).then(function(){})};
el=function(a,b){b=void 0===b?{}:b;return g.Lc(this,function d(){return g.Ba(d,function(e){return 1==e.i?(Wk(a),g.sa(e,Zk(),2)):3!=e.i?g.sa(e,Ck(a,b),3):g.sa(e,Hk(a),0)})})};
fl=function(a,b){Ek.call(this,a,b);this.options=b;Wk(a)};
gl=function(){Yj.call(this,function(){});
throw Error("Not allowed to instantiate the thennable outside of the core library.");};
il=function(){hl||(hl=new g.zj("yt.offline"));return hl};
ml=function(){g.Jf.call(this);this.j=this.C=this.i=!1;this.l=jl();kl(this);ll(this)};
jl=function(){var a=window.navigator.onLine;return void 0===a?!0:a};
ll=function(a){window.addEventListener("online",function(){a.l=!0;a.i&&a.dispatchEvent("ytnetworkstatus-online");nl(a);if(a.j&&g.xh("offline_error_handling")){var b=il().get("errors",!0);if(b){for(var c in b)if(b[c]){var d=new g.gk(c,"sent via offline_errors");d.name=b[c].name;d.stack=b[c].stack;g.Gh(d)}il().set("errors",{},2592E3,!0)}}})};
kl=function(a){window.addEventListener("offline",function(){a.l=!1;a.i&&a.dispatchEvent("ytnetworkstatus-offline");nl(a)})};
nl=function(a){a.C&&(g.Ih(new g.gk("NetworkStatusManager state did not match poll",(0,g.Ci)()-0)),a.C=!1)};
pl=function(a){a=void 0===a?{}:a;g.Jf.call(this);var b=this;this.l=this.C=0;ml.i||(ml.i=new ml);this.i=ml.i;this.i.i=!0;a.Xt&&(this.i.j=!0);a.rh?(this.rh=a.rh,this.i.L("ytnetworkstatus-online",function(){ol(b,"publicytnetworkstatus-online")}),this.i.L("ytnetworkstatus-offline",function(){ol(b,"publicytnetworkstatus-offline")})):(this.i.L("ytnetworkstatus-online",function(){b.dispatchEvent("publicytnetworkstatus-online")}),this.i.L("ytnetworkstatus-offline",function(){b.dispatchEvent("publicytnetworkstatus-offline")}))};
ol=function(a,b){a.rh?a.l?(g.ci(a.C),a.C=g.bi(function(){a.j!==b&&(a.dispatchEvent(b),a.j=b,a.l=(0,g.Ci)())},a.rh-((0,g.Ci)()-a.l))):(a.dispatchEvent(b),a.j=b,a.l=(0,g.Ci)()):a.dispatchEvent(b)};
sl=function(a,b){b=void 0===b?{}:b;(0,g.ql.Sg)().then(function(){rl||(rl=new pl({Xt:!0}));rl.i.l!==jl()&&g.Ih(new g.gk("NetworkStatusManager isOnline does not match window status"));g.I(a,b)})};
tl=function(a,b){b=void 0===b?{}:b;(0,g.ql.Sg)().then(function(){g.I(a,b)})};
g.ul=function(a){var b=this;this.i=null;a?this.i=a:sj()&&(this.i=g.Ei());g.ai(function(){Ej(b)},5E3)};
g.Ii=function(a,b,c,d){!g.F("VISITOR_DATA")&&"visitor_id"!==b&&.01>Math.random()&&g.Ih(new g.gk("Missing VISITOR_DATA when sending innertube request.",b,c,d));if(!a.isReady()){var e=new g.gk("innertube xhrclient not ready",b,c,d);g.Gh(e);e.sampleWeight=0;throw e;}var f={headers:{"Content-Type":"application/json"},method:"POST",ba:c,Qn:"JSON",ic:function(){d.ic()},
cn:d.ic,onSuccess:function(w,x){if(d.onSuccess)d.onSuccess(x)},
an:function(w){if(d.onSuccess)d.onSuccess(w)},
onError:function(w,x){if(d.onError)d.onError(x)},
FJ:function(w){if(d.onError)d.onError(w)},
timeout:d.timeout,withCredentials:!0},k="";(e=a.i.Mt)&&(k=e);var l=a.i.Ot||!1,m=tj(l,k,d);Object.assign(f.headers,m);f.headers.Authorization&&!k&&(f.headers["x-origin"]=window.location.origin);e="/youtubei/"+a.i.innertubeApiVersion+"/"+b;var n={alt:"json"};a.i.Nt&&f.headers.Authorization||(n.key=a.i.innertubeApiKey);var t=g.Vi(""+k+e,n);(0,g.ql.Sg)().then(function(w){if(d.retry&&g.xh("retry_web_logging_batches")&&"www.youtube-nocookie.com"!=k){if(g.xh("networkless_gel")&&!w||!g.xh("networkless_gel"))var x=
Cj(b,c,m,l);if(x){var D=f.onSuccess,O=f.an;f.onSuccess=function(M,la){Dj(x);D(M,la)};
c.an=function(M,la){Dj(x);O(M,la)}}}try{g.xh("use_fetch_for_op_xhr")?kj(t,f):g.xh("networkless_gel")&&d.retry?(f.method="POST",!d.lB&&g.xh("nwl_send_fast_on_unload")?tl(t,f):sl(t,f)):g.rj(t,f)}catch(M){if("InvalidAccessError"==M.name)x&&(Dj(x),x=0),g.Ih(Error("An extension is blocking network request."));
else throw M;}x&&g.ai(function(){Ej(a)},5E3)})};
g.vl=function(a,b,c){c=void 0===c?{}:c;var d=g.ul;g.F("ytLoggingEventsDefaultDisabled",!1)&&g.ul==g.ul&&(d=null);g.Pi(a,b,d,c)};
wl=function(){this.i=[];this.l=[]};
yl=function(a,b,c,d){c+="."+a;a=xl(b);d[c]=a;return c.length+a.length};
xl=function(a){return("string"===typeof a?a:String(JSON.stringify(a))).substr(0,500)};
g.Al=function(a){g.zl(a,"WARNING")};
g.zl=function(a,b,c,d,e,f){f=void 0===f?{}:f;f.name=c||g.F("INNERTUBE_CONTEXT_CLIENT_NAME",1);f.version=d||g.F("INNERTUBE_CONTEXT_CLIENT_VERSION",void 0);c=f||{};b=void 0===b?"ERROR":b;b=void 0===b?"ERROR":b;var k=void 0===k?!1:k;if(a&&(g.xh("console_log_js_exceptions")&&(d=[],d.push("Name: "+a.name),d.push("Message: "+a.message),a.hasOwnProperty("params")&&d.push("Error Params: "+JSON.stringify(a.params)),d.push("File name: "+a.fileName),d.push("Stacktrace: "+a.stack),d=d.join("\n"),window.console.log(d,
a)),(!g.xh("web_yterr_killswitch")||window&&window.yterr||k)&&!(5<=Bl)&&0!==a.sampleWeight)){f=g.Ld(a);k=f.message||"Unknown Error";d=f.name||"UnknownError";var l=f.stack||a.i||"Not available";g.xh("kevlar_js_fixes")&&l.startsWith(d+": "+k)&&(e=l.split("\n"),e.shift(),l=e.join("\n"));e=f.lineNumber||"Not available";f=f.fileName||"Not available";if(a.hasOwnProperty("args")&&a.args&&a.args.length)for(var m=0,n=0;n<a.args.length;n++){var t=a.args[n],w="params."+n;m+=w.length;if(t)if(Array.isArray(t))for(var x=
c,D=0;D<t.length&&!(t[D]&&(m+=yl(D,t[D],w,x),500<m));D++);else if("object"===typeof t)for(x in x=void 0,D=c,t){if(t[x]&&(m+=yl(x,t[x],w,D),500<m))break}else c[w]=xl(t),m+=c[w].length;else c[w]=xl(t),m+=c[w].length;if(500<=m)break}else if(a.hasOwnProperty("params")&&a.params)if(t=a.params,"object"===typeof a.params)for(n in w=0,t){if(t[n]&&(m="params."+n,x=xl(t[n]),c[m]=x,w+=m.length+x.length,500<w))break}else c.params=xl(t);navigator.vendor&&!c.hasOwnProperty("vendor")&&(c.vendor=navigator.vendor);
c={message:k,name:d,lineNumber:e,fileName:f,stack:l,params:c};a=Number(a.columnNumber);isNaN(a)||(c.lineNumber=c.lineNumber+":"+a);a=g.ja(Cl);for(k=a.next();!k.done;k=a.next())if(k=k.value,k.qj[c.name])for(e=g.ja(k.qj[c.name]),d=e.next();!d.done;d=e.next())if(f=d.value,d=c.message.match(f.regexp)){c.params["error.original"]=d[0];e=f.groups;f={};for(n=0;n<e.length;n++)f[e[n]]=d[n+1],c.params["error."+e[n]]=d[n+1];c.message=k.vm(f);break}window.yterr&&"function"===typeof window.yterr&&window.yterr(c);
if(!(Dl.has(c.message)||0<=c.stack.indexOf("/YouTubeCenter.js")||0<=c.stack.indexOf("/mytube.js"))){"ERROR"===b?El.ea("handleError",c):"WARNING"===b&&El.ea("handleWarning",c);if(g.xh("kevlar_gel_error_routing")){a=b;a:{k=g.ja(Fl);for(d=k.next();!d.done;d=k.next())if(Dk(d.value.toLowerCase())){k=!0;break a}k=!1}if(!k){d={stackTrace:c.stack};c.fileName&&(d.filename=c.fileName);k=c.lineNumber&&c.lineNumber.split?c.lineNumber.split(":"):[];0!==k.length&&(1!==k.length||isNaN(Number(k[0]))?2!==k.length||
isNaN(Number(k[0]))||isNaN(Number(k[1]))||(d.lineNumber=Number(k[0]),d.columnNumber=Number(k[1])):d.lineNumber=Number(k[0]));Gl||(Gl=new wl);k=Gl;e=c.message;f=c.name;a:{n=g.ja(k.l);for(l=n.next();!l.done;l=n.next())if(l=l.value,c.message&&c.message.match(l.i)){n=l.weight;break a}n=g.ja(k.i);for(l=n.next();!l.done;l=n.next())if(l=l.value,l.callback(c)){n=l.weight;break a}n=1}e={level:"ERROR_LEVEL_UNKNOWN",message:e,errorClassName:f,sampleWeight:n};"ERROR"===a?e.level="ERROR_LEVEL_ERROR":"WARNING"===
a&&(e.level="ERROR_LEVEL_WARNNING");a={isObfuscated:!0,browserStackInfo:d};d={pageUrl:window.location.href};g.F("FEXP_EXPERIMENTS")&&(d.experimentIds=g.F("FEXP_EXPERIMENTS"));d.kvPairs=[{key:"client.params.errorServiceSignature",value:"msg="+k.l.length+"&cb="+k.i.length},{key:"client.params.serviceWorker",value:"false"}];if(k=c.params)for(f=g.ja(Object.keys(k)),n=f.next();!n.done;n=f.next())n=n.value,d.kvPairs.push({key:"client."+n,value:String(k[n])});k=g.F("SERVER_NAME",void 0);f=g.F("SERVER_VERSION",
void 0);k&&f&&(d.kvPairs.push({key:"server.name",value:k}),d.kvPairs.push({key:"server.version",value:f}));g.vl("clientError",{errorMetadata:d,stackTrace:a,logMessage:e});g.yi()}}if(!g.xh("suppress_error_204_logging")){a=c.params||{};b={va:{a:"logerror",t:"jserror",type:c.name,msg:c.message.substr(0,250),line:c.lineNumber,level:b,"client.name":a.name},ba:{url:g.F("PAGE_NAME",window.location.href),file:c.fileName},method:"POST"};a.version&&(b["client.version"]=a.version);if(b.ba){c.stack&&(b.ba.stack=
c.stack);k=g.ja(Object.keys(a));for(d=k.next();!d.done;d=k.next())d=d.value,b.ba["client."+d]=a[d];if(a=g.F("LATEST_ECATCHER_SERVICE_TRACKING_PARAMS",void 0))for(k=g.ja(Object.keys(a)),d=k.next();!d.done;d=k.next())d=d.value,b.ba[d]=a[d];a=g.F("SERVER_NAME",void 0);k=g.F("SERVER_VERSION",void 0);a&&k&&(b.ba["server.name"]=a,b.ba["server.version"]=k)}g.I(g.F("ECATCHER_REPORT_HOST","")+"/error_204",b)}Dl.add(c.message);Bl++}}};
Il=function(){this.i=Hl();this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/session_partition",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"survival_status"},{ia:2,ha:"partition_min"},{ia:3,ha:"session_type"},{ia:3,ha:"status"})};
wg=function(){};
Hl=function(){Jl||(Jl=new xg);return Jl};
Ml=function(a,b){for(var c=[],d=1;d<arguments.length;++d)c[d-1]=arguments[d];if(!Kl(a)||c.some(function(e){return!Kl(e)}))throw Error("Only objects may be merged.");
c=g.ja(c);for(d=c.next();!d.done;d=c.next())Ll(a,d.value);return a};
Ll=function(a,b){for(var c in b)if(Kl(b[c])){if(c in a&&!Kl(a[c]))throw Error("Cannot merge an object into a non-object.");c in a||(a[c]={});Ll(a[c],b[c])}else if(Nl(b[c])){if(c in a&&!Nl(a[c]))throw Error("Cannot merge an array into a non-array.");c in a||(a[c]=[]);Ol(a[c],b[c])}else a[c]=b[c];return a};
Ol=function(a,b){for(var c=g.ja(b),d=c.next();!d.done;d=c.next())d=d.value,Kl(d)?a.push(Ll({},d)):Nl(d)?a.push(Ol([],d)):a.push(d);return a};
Kl=function(a){return"object"===typeof a&&!Array.isArray(a)};
Nl=function(a){return"object"===typeof a&&Array.isArray(a)};
g.J=function(a,b){return a?a.dataset?a.dataset[g.Pl(b)]:a.getAttribute("data-"+b):null};
g.Pl=function(a){return Ql[a]||(Ql[a]=String(a).replace(/\-([a-z])/g,function(b,c){return c.toUpperCase()}))};
g.Sl=function(a,b){var c=g.Rl(a);spf.script.load(a,c,b)};
g.Rl=function(a){var b="";if(a){var c=a.indexOf("jsbin/"),d=a.lastIndexOf(".js"),e=c+6;-1<c&&-1<d&&d>e&&(b=a.substring(e,d),b=b.replace(Tl,""),b=b.replace(Ul,""),b=b.replace("debug-",""),b=b.replace("tracing-",""))}return b};
Vl=function(){this.l=!1;this.i=null};
Wl=function(a,b,c,d,e){e=e?window.trayride.ad:window.botguard.bg;if(d)try{a.i=new e(b,c?function(){return c(b)}:g.Ja)}catch(f){g.Al(f)}else{try{a.i=new e(b)}catch(f){g.Al(f)}c&&c(b)}};
Xl=function(){return parseInt(g.F("DCLKSTAT",0),10)};
Zl=function(){return!!Yl.i};
$l=function(a){a=void 0===a?{}:a;a=void 0===a?{}:a;return Yl.i?Yl.i.hot?Yl.i.hot(void 0,void 0,a):Yl.i.invoke(void 0,void 0,a):null};
am=function(a){this.l=void 0===a?null:a;this.Y=0;this.i=null};
g.bm=function(a){var b=new am;a=void 0===a?null:a;b.Y=2;b.i=void 0===a?null:a;return b};
g.cm=function(a){var b=new am;a=void 0===a?null:a;b.Y=1;b.i=void 0===a?null:a;return b};
g.em=function(){this.i=g.F("ALT_PREF_COOKIE_NAME","PREF");var a=g.xj(this.i);if(a){a=decodeURIComponent(a).split("&");for(var b=0;b<a.length;b++){var c=a[b].split("="),d=c[0];(c=c[1])&&(g.dm[d]=c.toString())}}};
fm=function(a){if(/^f([1-9][0-9]*)$/.test(a))throw Error("ExpectedRegexMatch: "+a);};
gm=function(a){if(!/^\w+$/.test(a))throw Error("ExpectedRegexMismatch: "+a);};
hm=function(){g.B.call(this);this.i=[]};
im=function(a){a=a||{};var b={},c={};this.url=a.url||"";this.args=a.args||g.qb(b);this.assets=a.assets||{};this.attrs=a.attrs||g.qb(c);this.fallback=a.fallback||null;this.fallbackMessage=a.fallbackMessage||null;this.html5=!!a.html5;this.disable=a.disable||{};this.loaded=!!a.loaded;this.messages=a.messages||{}};
jm=function(a){a instanceof im||(a=new im(a));return a};
lm=function(){a:{if(window.crypto&&window.crypto.getRandomValues)try{var a=Array(16),b=new Uint8Array(16);window.crypto.getRandomValues(b);for(var c=0;c<a.length;c++)a[c]=b[c];var d=a;break a}catch(e){}d=Array(16);for(a=0;16>a;a++){b=g.Va();for(c=0;c<b%23;c++)d[a]=Math.random();d[a]=Math.floor(256*Math.random())}if(km)for(a=1,b=0;b<km.length;b++)d[a%16]=d[a%16]^d[(a-1)%16]/4^km.charCodeAt(b),a++}a=[];for(b=0;b<d.length;b++)a.push("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_".charAt(d[b]&
63));return a.join("")};
g.nm=function(a){a&&g.Pi("foregroundHeartbeatScreenAssociated",{clientDocumentNonce:mm,clientScreenNonce:a},g.ul)};
g.om=function(a){this.i=a};
pm=function(a){a=void 0===a?0:a;return 0==a?"client-screen-nonce":"client-screen-nonce."+a};
qm=function(a){a=void 0===a?0:a;return 0==a?"ROOT_VE_TYPE":"ROOT_VE_TYPE."+a};
rm=function(a){return g.F(qm(void 0===a?0:a),void 0)};
g.sm=function(){var a=rm(0),b;a?b=new g.om({veType:a,youtubeData:void 0}):b=null;return b};
tm=function(){var a=g.F("csn-to-ctt-auth-info");a||(a={},g.uh("csn-to-ctt-auth-info",a));return a};
g.um=function(a){a=void 0===a?0:a;var b=g.F(pm(a));if(!b&&!g.F("USE_CSN_FALLBACK",!0))return null;b||0!=a||(g.xh("kevlar_client_side_screens")||g.xh("c3_client_side_screens")?b="UNDEFINED_CSN":b=g.F("EVENT_ID"));return b?b:null};
vm=function(a,b,c){var d=tm();(c=g.um(c))&&delete d[c];b&&(d[a]=b)};
g.wm=function(a){return tm()[a]};
g.xm=function(a,b,c,d){c=void 0===c?0:c;if(a!==g.F(pm(c))||b!==g.F(qm(c)))if(vm(a,d,c),g.uh(pm(c),a),g.uh(qm(c),b),0==c||g.xh("web_screen_associated_all_layers"))b=function(){setTimeout(function(){g.nm(a)},0)},"requestAnimationFrame"in window?window.requestAnimationFrame(b):b()};
ym=function(a){g.Fj.call(this,1,arguments);this.csn=a};
g.Bm=function(a,b,c){zm.push({payloadName:a,payload:b,options:c});Am||(Am=Qj())};
Nj=function(a){if(zm){for(var b=g.ja(zm),c=b.next();!c.done;c=b.next())c=c.value,c.payload&&(c.payload.csn=a.csn,g.Pi(c.payloadName,c.payload,null,c.options));zm.length=0}Am=0};
g.Dm=function(a,b,c){var d=g.xh("use_default_events_client")?void 0:g.ul;g.A(b,function(e){var f=c,k=(f=void 0===f?!1:f)?16:8;e={csn:a,ve:e.getAsJson(),eventType:k};f={xe:g.wm(a),vd:a,iq:f};"UNDEFINED_CSN"==a?g.Bm("visualElementHidden",e,f):d?g.Pi("visualElementHidden",e,d,f):g.vl("visualElementHidden",e,f)})};
Em=function(){var a=g.sm(),b=g.um();b&&a&&g.Dm(b,[a],!0)};
g.Im=function(a){if("FOREGROUND_HEARTBEAT_TRIGGER_ON_BACKGROUND"==a||"FOREGROUND_HEARTBEAT_TRIGGER_ON_FOREGROUND"==a){if(Fm==a)return;Fm=a}var b=9E4+2E3*Math.random();if("FOREGROUND_HEARTBEAT_TRIGGER_ON_INTERVAL"!=a||!(g.ki()>b)&&"visible"==g.Dh()){b=-1;g.Gm&&(b=Math.round((0,g.Ci)()-g.Gm));var c=g.u("_fact",window);g.Pi("foregroundHeartbeat",{firstActivityMs:String(null==c||-1==c?-1:Math.max(g.Va()-c,0)),clientDocumentNonce:mm,index:String(Hm),lastEventDeltaMs:String(b),trigger:a},g.ul);g.r("_fact",
-1,window);Hm++;g.Gm=(0,g.Ci)()}};
g.Jm=function(a,b,c,d,e){this.name=a;this.deps=b||[];this.page=c||"";this.D=d?Hh(d):null;this.C=e?Hh(e):null;this.j=[];this.Y=this.i=0};
g.Km=function(a){g.ci(a.i);a.i=g.bi((0,g.v)(a.init,a))};
g.Nm=function(a){a.name in Lm&&Mm(a.name);Lm[a.name]={reqs:[],disable:(0,g.v)(a.disable,a)};g.A(a.deps,function(b){if(!(b in Lm))throw Error("Module "+b+" required by "+a.name);Lm[b].reqs.push(a.name)});
a.enable()};
Mm=function(a){if(a in Lm){var b=Lm[a];g.A(b.reqs,function(c){Mm(c)});
try{b.disable()}catch(c){}delete Lm[a]}};
Pm=function(a){sh(g.Om,arguments)};
g.Qm=function(a){return a in g.Om};
Rm=function(a){sh(g.Om,arguments)};
g.Um=function(a,b,c,d,e){e=void 0===e?"":e;if(a)if(c&&!Dk("cobalt"))a&&(a=g.Rb(g.Yb(a)),"about:invalid#zClosurez"===a||a.startsWith("data")?a="":(a=g.mc(g.oc(a)),a=g.sc(g.Ug(a))),g.Db(a)||(a=g.be("IFRAME",{src:'javascript:"<body><img src=\\""+'+a+'+"\\"></body>"',style:"display:none"}),g.he(a).body.appendChild(a)));else if(e)lj(a,b,"POST",e,d);else if(g.F("USE_NET_AJAX_FOR_PING_TRANSPORT",!1)||d)lj(a,b,"GET","",d);else{b:{try{var f=new Za({url:a});if(f.j&&f.l||f.C){var k=g.xc(g.zc(5,a));var l=!(!k||
!k.endsWith("/aclk")||"1"!==g.Kc(a,"ri"));break b}}catch(m){}l=!1}l?Sm(a)?(b&&b(),c=!0):c=!1:c=!1;c||Tm(a,b)}};
Sm=function(a,b){try{if(window.navigator&&window.navigator.sendBeacon&&window.navigator.sendBeacon(a,void 0===b?"":b))return!0}catch(c){}return!1};
Tm=function(a,b){var c=new Image,d=""+Vm++;Wm[d]=c;c.onload=c.onerror=function(){b&&Wm[d]&&b();delete Wm[d]};
c.src=a};
g.Ym=function(a,b){a=a||"";var c=a.match(Xm);spf.style.load(a,c?c[1]:"",b)};
g.Zm=function(a,b,c){var d=void 0===d?!0:d;var e=g.F("VALID_SESSION_TEMPDATA_DOMAINS",[]),f=g.Ac(window.location.href);f&&e.push(f);f=g.Ac(a);if(g.db(e,f)||!f&&g.Bb(a,"/"))if(g.xh("autoescape_tempdata_url")&&(e=document.createElement("a"),g.qc(e,a),a=e.href),a&&(a=g.Bc(a),e=a.indexOf("#"),a=0>e?a:a.substr(0,e)))d&&!b.csn&&(b.itct||b.ved)&&(b=Object.assign({csn:g.um()},b)),c?(c=parseInt(c,10),isFinite(c)&&0<c&&(d="ST-"+g.vc(a).toString(36),b=b?g.Gc(b):"",g.wj(d,b,c||5))):(c=b,b="ST-"+g.vc(a).toString(36),
c=c?g.Gc(c):"",g.wj(b,c,5))};
g.$m=function(a){var b=void 0===b?{}:b;var c=void 0===c?"":c;var d=void 0===d?window:d;g.rc(d.location,g.Hc(a,b)+c)};
g.an=function(a,b){b&&g.Zm(a,b);(window.ytspf||{}).enabled?spf.navigate(a):g.$m(a)};
g.bn=function(a,b,c){b=void 0===b?{}:b;c=void 0===c?!1:c;var d=g.F("EVENT_ID");d&&(b.ei||(b.ei=d));b&&g.Zm(a,b);if(c)return!1;g.an(a);return!0};
fn=function(a,b,c,d){g.B.call(this);var e=this;this.H=this.Pa=a;this.N=b;this.F=!1;this.api={};this.wa=this.K=null;this.R=new g.Yg;g.qe(this,this.R);this.C={};this.aa=this.Ka=this.j=this.pb=this.i=null;this.V=!1;this.D=this.M=null;this.ub={};this.Qd=["onReady"];this.bb=null;this.xb=NaN;this.ka={};this.l=d;cn(this);this.Xf("WATCH_LATER_VIDEO_ADDED",this.qy.bind(this));this.Xf("WATCH_LATER_VIDEO_REMOVED",this.ry.bind(this));this.Xf("onAdAnnounce",this.vp.bind(this));this.Xb=new hm(this);g.qe(this,this.Xb);
this.ra=0;c?this.ra=g.Uh(function(){e.loadNewVideoConfig(c)},0):d&&(dn(this),en(this))};
dn=function(a){var b;a.l?b=a.l.rootElementId:b=a.i.attrs.id;a.j=b||a.j;"video-player"==a.j&&(a.j=a.N,a.l?a.l.rootElementId=a.N:a.i.attrs.id=a.N);a.H.id==a.j&&(a.j+="-player",a.l?a.l.rootElementId=a.j:a.i.attrs.id=a.j)};
gn=function(a){a.i&&!a.i.loaded&&(a.i.loaded=!0,"0"!=a.i.args.autoplay?a.api.loadVideoByPlayerVars(a.i.args):a.api.cueVideoByPlayerVars(a.i.args))};
kn=function(a){var b=!0,c=hn(a);c&&a.i&&(a=jn(a),b=g.J(c,"version")===a);return b&&!!g.u("yt.player.Application.create")};
en=function(a){if(!a.Ha()&&!a.V){var b=kn(a);if(b&&"html5"==(hn(a)?"html5":null))a.aa="html5",a.F||ln(a);else if(mn(a),a.aa="html5",b&&a.D)a.Pa.appendChild(a.D),ln(a);else{a.i&&(a.i.loaded=!0);var c=!1;a.M=function(){c=!0;var d=nn(a,"player_bootstrap_method")?g.u("yt.player.Application.createAlternate")||g.u("yt.player.Application.create"):g.u("yt.player.Application.create");var e=a.i?a.i.clone():void 0;d(a.Pa,e,a.l);ln(a)};
a.V=!0;b?a.M():(g.Sl(jn(a),a.M),(b=a.l?a.l.cssUrl:a.i.assets.css)&&g.Ym(b),on(a)&&!c&&g.r("yt.player.Application.create",null,void 0))}}};
hn=function(a){var b=g.z(a.j);!b&&a.H&&a.H.querySelector&&(b=a.H.querySelector("#"+a.j));return b};
ln=function(a){if(!a.Ha()){var b=hn(a),c=!1;b&&b.getApiInterface&&b.getApiInterface()&&(c=!0);c?(a.V=!1,!nn(a,"html5_remove_not_servable_check_killswitch")&&b.isNotServable&&a.i&&b.isNotServable(a.i.args.video_id)||pn(a)):a.xb=g.Uh(function(){ln(a)},50)}};
pn=function(a){cn(a);a.F=!0;var b=hn(a);b.addEventListener&&(a.K=qn(a,b,"addEventListener"));b.removeEventListener&&(a.wa=qn(a,b,"removeEventListener"));var c=b.getApiInterface();c=c.concat(b.getInternalApiInterface());for(var d=0;d<c.length;d++){var e=c[d];a.api[e]||(a.api[e]=qn(a,b,e))}for(var f in a.C)a.K(f,a.C[f]);gn(a);a.Ka&&a.Ka(a.api);a.R.ea("onReady",a.api)};

/**
 * Rehike-specific change!
 * 
 * Patch player loader to use the globally defined function names. Recently (as
 * of 2023/12/13), a legacy event mechanism which Hitchhiker used was removed
 * from the player source code.
 * 
 * Hitchhiker's JS sends a string as argument b here, but the player now expects
 * a function. It used to grab window[arguments[1]] previously and put it in a
 * wrapper, but now it just fails altogether and breaks the player completely.
 */
qn=function(a,b,c)
{
    var d = b[c];
    return function()
    {
        try
        {
            // Grab the global object for the second argument, if necessary.
            if (
                window.yt && 
                window.yt.config_ && 
                window.yt.config_.REHIKE_LATEST_PLAYER == true
            )
            {
                if (typeof arguments[1] == "string")
                {
                    if (window[arguments[1]])
                    {
                        arguments[1] = window[arguments[1]];
                    }
                }
            }

            return a.bb = null, d.apply(b, arguments)
        }
        catch (e)
        {
            "sendAbandonmentPing" != c && (e.params = c, a.bb = e, g.Ih(e))
        }
    }
};

cn=function(a){a.F=!1;if(a.wa)for(var b in a.C)a.wa(b,a.C[b]);for(var c in a.ka)g.Wh(parseInt(c,10));a.ka={};a.K=null;a.wa=null;for(var d in a.api)a.api[d]=null;a.api.addEventListener=a.Xf.bind(a);a.api.removeEventListener=a.eA.bind(a);a.api.destroy=a.dispose.bind(a);a.api.getLastError=a.Aq.bind(a);a.api.getPlayerType=a.Fq.bind(a);a.api.getCurrentVideoConfig=a.xq.bind(a);a.api.loadNewVideoConfig=a.loadNewVideoConfig.bind(a);a.api.isReady=a.Ou.bind(a)};
rn=function(a,b){var c=b;if("string"==typeof b){if(a.ub[b])return a.ub[b];c=function(){var d=g.u(b);d&&d.apply(g.q,arguments)};
a.ub[b]=c}return c?c:null};
sn=function(a,b){var c="ytPlayer"+b+a.N;a.C[b]=c;g.q[c]=function(d){var e=g.Uh(function(){a.Ha()||(a.R.ea(b,d),g.nb(a.ka,String(e)))},0);
g.ob(a.ka,String(e),!0)};
return c};
mn=function(a){a.cancel();cn(a);a.aa=null;a.i&&(a.i.loaded=!1);var b=hn(a);b&&(kn(a)||!on(a)?a.D=b:(b&&b.destroy&&b.destroy(),a.D=null));g.ge(a.Pa)};
on=function(a){return a.i&&a.i.args&&a.i.args.fflags?-1!=a.i.args.fflags.indexOf("player_destroy_old_version=true"):!1};
jn=function(a){return a.l?a.l.jsUrl:a.i.assets.js};
nn=function(a,b){if(a.l)var c=a.l.serializedExperimentFlags;else a.i&&a.i.args&&(c=a.i.args.fflags);return"true"==Ri(c||"")[b]};
g.wn=function(a,b){var c=void 0===c?!0:c;a="string"===typeof a?g.Vd(a):a;var d=g.tn+"_"+g.Ra(a),e=g.un[d];if(e&&c)return b&&b.args&&b.args.fflags&&b.args.fflags.includes("web_player_remove_playerproxy=true")?e.api.loadVideoByPlayerVars(b.args||null):e.loadNewVideoConfig(b),e.api;e=new fn(a,d,b,void 0);g.un[d]=e;g.H("player-added",e.api);g.oe(e,g.Ua(vn,e));return e.api};
vn=function(a){delete g.un[a.getId()]};
g.xn=function(a){if(!a)return null;var b=g.tn+"_"+g.Ra(a),c=g.un[b];c||(c=new fn(a,b),g.un[b]=c);return c.api};
g.yn=function(a){return g.xn(document.getElementById(a))};
zn=function(a,b){g.Fj.call(this,1,arguments)};
An=function(a,b){g.Fj.call(this,1,arguments)};
Bn=function(){this.timing={};this.clearResourceTimings=function(){};
this.webkitClearResourceTimings=function(){};
this.mozClearResourceTimings=function(){};
this.msClearResourceTimings=function(){};
this.oClearResourceTimings=function(){}};
Dn=function(a){var b=Cn(a);if(b.aft)return b.aft;a=g.F((a||"")+"TIMING_AFT_KEYS",["ol"]);for(var c=a.length,d=0;d<c;d++){var e=b[a[d]];if(e)return e}return NaN};
Gn=function(){var a;if(g.xh("csi_use_performance_navigation_timing")){var b,c,d,e=null===(d=null===(c=null===(b=null===(a=null===En||void 0===En?void 0:En.getEntriesByType)||void 0===a?void 0:a.call(En,"navigation"))||void 0===b?void 0:b[0])||void 0===c?void 0:c.toJSON)||void 0===d?void 0:d.call(c);e?(e.requestStart=Fn(e.requestStart),e.responseEnd=Fn(e.responseEnd),e.redirectStart=Fn(e.redirectStart),e.redirectEnd=Fn(e.redirectEnd),e.domainLookupEnd=Fn(e.domainLookupEnd),e.connectStart=Fn(e.connectStart),
e.connectEnd=Fn(e.connectEnd),e.responseStart=Fn(e.responseStart),e.secureConnectionStart=Fn(e.secureConnectionStart),e.domainLookupStart=Fn(e.domainLookupStart),e.isPerformanceNavigationTiming=!0,a=e):a=En.timing}else a=En.timing;return a};
Hn=function(){return g.xh("csi_use_time_origin")&&En.timeOrigin?Math.floor(En.timeOrigin):En.timing.navigationStart};
Fn=function(a){return Math.round(Hn()+a)};
In=function(a){g.r("ytglobal.timingready_",a,void 0)};
Jn=function(a){return!!g.u("yt.timing."+(a||"")+"pingSent_")};
Kn=function(a,b){g.r("yt.timing."+(b||"")+"pingSent_",a,void 0)};
Mn=function(a){return g.u("ytcsi."+(a||"")+"data_")||Ln(a)};
Nn=function(a){a=Mn(a);a.info||(a.info={});return a.info};
Cn=function(a){a=Mn(a);a.tick||(a.tick={});return a.tick};
On=function(a){var b=Mn(a).nonce;b||(b=lm(),Mn(a).nonce=b);return b};
Ln=function(a){var b={tick:{},info:{}};g.r("ytcsi."+(a||"")+"data_",b,void 0);return b};
Rn=function(a){var b=Cn(a||""),c=Dn(a);c&&!Pn&&(g.Ij(Qn,new zn(Math.round(c-b._start),a)),Pn=!0)};
Sn=function(){if(En.getEntriesByType){var a=En.getEntriesByType("paint");if(a=g.bb(a,function(b){return"first-paint"===b.name}))return Fn(a.startTime)}a=En.timing;
return a.Qu?Math.max(0,a.Qu):0};
Tn=function(){var a=g.u("ytcsi.debug");a||(a=[],g.r("ytcsi.debug",a,void 0),g.r("ytcsi.reference",{},void 0));return a};
Vn=function(a){a=a||"";var b=Un();if(b[a])return b[a];var c=Tn(),d={timerName:a,info:{},tick:{},span:{}};c.push(d);return b[a]=d};
Un=function(){var a=g.u("ytcsi.reference");if(a)return a;Tn();return g.u("ytcsi.reference")};
Wn=function(){this.i=0};
Xn=function(){Wn.i||(Wn.i=new Wn);return Wn.i};
Zn=function(a,b){Yn[b]=Yn[b]||{count:0};var c=Yn[b];c.count++;c.time=(0,g.Ci)();a.i||(a.i=g.ai(function(){var d=(0,g.Ci)(),e;for(e in Yn)Yn[e]&&6E4<d-Yn[e].time&&delete Yn[e];a&&(a.i=0)},5E3));
return 5<c.count?(6===c.count&&1>1E5*Math.random()&&(c=new g.gk("CSI data exceeded logging limit with key",b.split("_")),0<=b.indexOf("plev")||g.Al(c)),!0):!1};
$n=function(a){return!!g.F("FORCE_CSI_ON_GEL",!1)||g.xh("csi_on_gel")||!!Mn(a).useGel};
bo=function(a,b,c){var d=ao(c);d.gelTicks&&(d.gelTicks["tick_"+a]=!0);c||b||(0,g.Ci)();return $n(c)?(Vn(c||"").tick[a]=b||(0,g.Ci)(),d=On(c),"_start"===a?(a=Xn(),Zn(a,"baseline_"+d)||g.vl("latencyActionBaselined",{clientActionNonce:d},{timestamp:b})):Xn().tick(a,d,b),Rn(c),!0):!1};
ho=function(a,b,c){c=ao(c);if(c.gelInfos)c.gelInfos["info_"+a]=!0;else{var d={};c.gelInfos=(d["info_"+a]=!0,d)}if(a.match("_rid")){var e=a.split("_rid")[0];a="REQUEST_ID"}if(a in co){c=co[a];g.db(eo,c)&&(b=!!b);a in fo&&"string"===typeof b&&(b=fo[a]+b.toUpperCase());a=b;b=c.split(".");for(var f=d={},k=0;k<b.length-1;k++){var l=b[k];f[l]={};f=f[l]}f[b[b.length-1]]="requestIds"===c?[{id:a,endpoint:e}]:a;return Ml({},d)}g.db(go,a)||g.Al(new g.gk("Unknown label logged with GEL CSI",a))};
ao=function(a){a=Mn(a);if(!("gel"in a))a.gel={gelTicks:{},gelInfos:{}};else if(a.gel){var b=a.gel;b.gelInfos||(b.gelInfos={});b.gelTicks||(b.gelTicks={})}return a.gel};
io=function(a){a=ao(a);"gelInfos"in a||(a.gelInfos={});return a.gelInfos};
ko=function(){Ln(void 0);jo();Kn(!1,void 0);g.F("TIMING_ACTION")&&g.uh("PREVIOUS_ACTION",g.F("TIMING_ACTION"));g.uh("TIMING_ACTION","")};
vo=function(){var a=g.F("TIMING_ACTION",void 0),b=g.F("TIMING_AFT_KEYS");Vn("").info.actionType=a;b&&g.uh("TIMING_AFT_KEYS",b);g.uh("TIMING_ACTION",a);a=g.F("TIMING_INFO",{});for(var c in a)a.hasOwnProperty(c)&&lo(c,a[c]);lo("is_nav",1);(c=g.um())&&lo("csn",c);(c=g.F("PREVIOUS_ACTION",void 0))&&!$n()&&lo("pa",c);c=Nn();a=g.F("CLIENT_PROTOCOL");b=g.F("CLIENT_TRANSPORT");a&&lo("p",a);b&&lo("t",b);lo("yt_vis",mo());if("cold"===c.yt_lt){lo("yt_lt","cold");a=Gn();if(b=Hn())g.no("srt",a.responseStart),
1!==c.prerender&&oo("n",b);c=Sn();0<c&&g.no("fpt",c);po();En&&En.getEntriesByType&&qo();c=[];if(document.querySelector&&En&&En.getEntriesByName)for(var d in ro)ro.hasOwnProperty(d)&&(a=ro[d],so(d,a)&&c.push(a));c.length&&lo("rc",c.join(","))}if($n(void 0)){d={actionType:to[g.F("TIMING_ACTION",void 0)]||"LATENCY_ACTION_UNKNOWN",previousAction:to[g.F("PREVIOUS_ACTION",void 0)]||"LATENCY_ACTION_UNKNOWN"};if(c=g.um())d.clientScreenNonce=c;c=On(void 0);Xn().info(d,c)}d=Nn();a=Cn();if("cold"===d.yt_lt&&
(c=ao(),b=c.gelTicks?c.gelTicks:c.gelTicks={},c=c.gelInfos?c.gelInfos:c.gelInfos={},$n())){for(var e in a)"tick_"+e in b||bo(e,a[e]);e=io();a=On();b={};for(var f in d)if(!("info_"+f in c)){var k=ho(f,d[f]);k&&(Ml(e,k),Ml(b,k))}Xn().info(b,a)}In(!0);g.uo(!1)};
oo=function(a,b){lo("yt_sts",a,void 0);g.no("_start",b,void 0)};
lo=function(a,b,c){null!==b&&(Nn(c)[a]=b,$n(c)?(a=ho(a,b,c))&&$n(c)&&(b=Vn(c||""),Ml(b.info,a),Ml(io(c),a),c=On(c),Xn().info(a,c)):Vn(c||"").info[a]=b)};
g.no=function(a,b,c){var d=Cn(c);if(g.xh("use_first_tick")&&wo(a,c))return d[a];b||"_"===a[0]||xo(a,c);var e=b||(0,g.Ci)();d[a]=e;bo(a,b,c)||(g.uo(!1,c),Vn(c||"").tick[a]=b||(0,g.Ci)());return d[a]};
yo=function(a,b){if($n(void 0)){var c=ao(void 0);if(c.gelSpans)c.gelSpans[a]=!0;else{var d={};c.gelSpans=(d[a]=!0,d)}c={spanName:a,spanLengthUsec:String(Math.round(1E3*b))};Vn("").span[String(c.spanName)]=c;d=ao(void 0);"gelSpans"in d||(d.gelSpans={});Ml(d.gelSpans,c);d=On(void 0);Xn().span(c,d)}};
xo=function(a,b){En.mark&&(g.Bb(a,"mark_")||(a="mark_"+a),b&&(a+=" ("+b+")"),En.mark(a))};
wo=function(a,b){var c=Cn(b);return a in c};
g.uo=function(a,b){if(!Jn(b)){var c=g.F((b||"")+"TIMING_ACTION",void 0),d=Cn(b);if(g.u("ytglobal.timing"+(b||"")+"ready_")&&c&&wo("_start")&&Dn(b))if(Rn(b),a||b)zo(b);else{c=!0;var e=g.F("TIMING_WAIT",[]);if(e.length)for(var f=0,k=e.length;f<k;++f)if(!(e[f]in d)){c=!1;break}c&&zo(b)}}};
mo=function(){switch(g.Dh()){case "hidden":return 0;case "visible":return 1;case "prerender":return 2;case "unloaded":return 3;default:return-1}};
zo=function(a){if(!$n(a)){var b=Cn(a),c=Nn(a),d=b._start,e=g.F("CSI_SERVICE_NAME","youtube"),f={v:2,s:e,action:g.F((a||"")+"TIMING_ACTION",void 0)},k=c.srt;void 0!==b.srt&&delete c.srt;b.aft=Dn(a);var l=Cn(a),m=l.pbr,n=l.vc;l=l.pbs;m&&n&&l&&m<n&&n<l&&Nn(a).yt_pvis&&"youtube"===e&&(lo("yt_lt","hot_bg",a),e=b.vc,m=b.pbs,delete b.aft,c.aft=Math.round(m-e));for(var t in c)"_"!==t.charAt(0)&&(f[t]=c[t]);b.ps=(0,g.Ci)();t={};e=[];for(var w in b)"_"!==w.charAt(0)&&(m=Math.round(b[w]-d),t[w]=m,e.push(w+"."+
m));f.rt=e.join(",");b=!!c.ap;g.xh("debug_csi_data")&&(c=g.u("yt.timing.csiData"),c||(c=[],g.r("yt.timing.csiData",c,void 0)),c.push({page:location.href,time:new Date,args:f}));c="";for(var x in f)f.hasOwnProperty(x)&&(c+="&"+x+"="+f[x]);f="/csi_204?"+c.substring(1);if(window.navigator&&window.navigator.sendBeacon&&b){var D=void 0===D?"":D;Sm(f,D)||g.Um(f,void 0,void 0,void 0,D)}else g.Um(f);Kn(!0,a);g.Ij(Ao,new An(t.aft+(Number(k)||0),a))}};
Bo=function(a){if($n(void 0))wo("_start",void 0)&&g.no("aa",void 0,void 0);else if(!Jn(void 0)){var b=g.F("CSI_SERVICE_NAME","youtube");g.F("TIMING_ACTION",void 0)&&b&&(g.no("aa",void 0,void 0),lo("ap",1,void 0),lo("yt_fss",a,void 0),zo(void 0))}};
so=function(a,b){var c=document.querySelector(a);if(!c)return!1;var d="",e=c.nodeName;"SCRIPT"===e?(d=c.src,d||(d=c.getAttribute("data-timing-href"))&&(d=window.location.protocol+d)):"LINK"===e&&(d=c.href);g.Ha()&&c.setAttribute("nonce",g.Ha());return d?(c=En.getEntriesByName(d))&&c[0]&&(c=c[0],d=Hn(),g.no("rsf_"+b,d+Math.round(c.fetchStart)),g.no("rse_"+b,d+Math.round(c.responseEnd)),void 0!==c.transferSize&&(d=Nn(void 0),e=io(void 0),"rc"in d||"rc"in e||lo("rc",""),0===c.transferSize))?!0:!1:!1};
po=function(){if(!g.xh("log_deltas_killswitch")){var a,b,c,d;if(En&&En.timing){En.timeOrigin&&En.timing.navigationStart&&yo("startTimeDelta",Math.floor(En.timeOrigin)-En.timing.navigationStart);var e=null===(d=null===(c=null===(b=null===(a=En.getEntriesByType)||void 0===a?void 0:a.call(En,"navigation"))||void 0===b?void 0:b[0])||void 0===c?void 0:c.toJSON)||void 0===d?void 0:d.call(c);e&&e.responseEnd&&En.timing.responseEnd&&yo("responseEndDelta",Fn(e.responseEnd)-En.timing.responseEnd)}}a=Gn();a.isPerformanceNavigationTiming&&
lo("pnt",1,void 0);g.no("nreqs",a.requestStart,void 0);g.no("nress",a.responseStart,void 0);g.no("nrese",a.responseEnd,void 0);0<a.redirectEnd-a.redirectStart&&(g.no("nrs",a.redirectStart,void 0),g.no("nre",a.redirectEnd,void 0));0<a.domainLookupEnd-a.domainLookupStart&&(g.no("ndnss",a.domainLookupStart,void 0),g.no("ndnse",a.domainLookupEnd,void 0));0<a.connectEnd-a.connectStart&&(g.no("ntcps",a.connectStart,void 0),g.no("ntcpe",a.connectEnd,void 0));a.secureConnectionStart>=Hn()&&0<a.connectEnd-
a.secureConnectionStart&&(g.no("nstcps",a.secureConnectionStart,void 0),g.no("ntcpe",a.connectEnd,void 0))};
qo=function(){var a=window.location.protocol,b=En.getEntriesByType("resource");b=g.Co(b,function(c){return 0===c.name.indexOf(a+"//fonts.gstatic.com/s/")});
(b=Ag(b,function(c,d){return d.duration>c.duration?d:c},{duration:0}))&&0<b.startTime&&0<b.responseEnd&&(g.no("wffs",Fn(b.startTime)),g.no("wffe",Fn(b.responseEnd)))};
Mo=function(){Do++;var a=g.Zd(),b=new g.re(0,0,a.width,a.height);lo("vph",a.height);lo("vpw",a.width);g.no("vpc");a=document.querySelectorAll(".yt-lockup-thumbnail img[data-ytimg]");var c=a.length,d=[];Eo.start();for(var e=0;e<c;e++){var f=a[e];Fo(f,b)&&(f=Go(f),f.then(Ho),d.push(f),Io.push(f))}g.no("vpcc");b=g.gg(d).then(Jo);g.ig(b,Ko);b.then(Lo);Io.push(b);return b};
Lo=function(){Eo.stop()};
Ko=function(){g.no("vpr")};
Fo=function(a,b){for(var c=a,d=[];c!=document.body;){var e=g.Bh(c);if(e in No)return!0;if(e in Oo)return!1;var f=window.getComputedStyle(c);if("none"==f.display||"hidden"==f.visibility)return Oo[e]=!0,!1;f=c.getBoundingClientRect();if(!(b.left<=f.left+f.width&&f.left<=b.left+b.width&&b.top<=f.top+f.height&&f.top<=b.top+b.height))return Oo[e]=!0,!1;d.push(e);c=c.parentElement}for(c=0;c<d.length;c++)No[d[c]]=!0;return!0};
Jo=function(a){var b=g.Zd();b=new g.re(0,0,b.width,b.height);for(var c=0,d=0,e=a.length;d<e;d++){var f=a[d].time;Fo(a[d].Et,b)&&c<f&&(c=f)}return c};
Go=function(a){var b=Do;return new g.ag(function(c,d){var e={Et:a,time:0};a.loadTime?(e.time=parseInt(a.loadTime,10),c(e)):(a.slt=function(){Do!=b?d():(e.time=parseInt(a.loadTime,10),c(e),a.slt=void 0)},Po.push(a))})};
Ho=function(a){Eo.start();a=a.time;Qo<a&&(Qo=a,g.no("lim",a))};
Ro=function(){g.no("vptl",Qo);g.no("vpl",Qo)};
So=function(){Io.forEach(function(a){a.cancel()});
Qo=Io.length=0;No={};Oo={};Po.forEach(function(a){a.slt=void 0});
Po.length=0};
Uo=function(a){if(null!=a){var b=[];To.forEach(function(c){c in a&&(delete a[c],b.push(c))});
a.cached_load="1"}};
g.Vo=function(){return g.C(g.z("page-container"),"remote-connected")};
Zo=function(){Wo=g.Vh(Xo,5E3);var a=g.Yo();a&&(a.addEventListener("onReady",Xo),a.addEventListener("onStateChange",Xo))};
$o=function(a){for(var b in g.un){var c=g.un[b];c&&c.cancel()}if(a=a||null)g.wn("player-api",a),a=jm(a),a.loaded=!0;Xo();g.r("ytplayer.config",a,void 0)};
g.Yo=function(){return g.yn("player-api")};
Xo=function(){var a=g.Yo();if(a){var b=1==(a&&a.isReady()?a.getPlayerState():void 0),c="watch"==g.F("PAGE_NAME"),d=g.Vo();!b||c||d||a.pauseVideo()}};
cp=function(){g.no("ol");window.requestAnimationFrame&&!document.hidden?window.requestAnimationFrame(function(){setTimeout(function(){g.no("cpt");g.H("on_cpt_tick",(new Date).getTime())},0)}):document.hidden?(g.no("cpt"),g.H("on_cpt_tick",(new Date).getTime())):setTimeout(function(){g.no("cpt");
g.H("on_cpt_tick",(new Date).getTime())},0);
ap();g.F("CSI_VIEWPORT")&&(bp=Mo(),bp.then(function(a){g.no("vpl",a);bp=null},function(){}))};
ap=function(){dp("init");var a=g.F("PAGE_NAME",void 0);a&&dp("init-"+a)};
dp=function(a){g.fi()?ep.push(g.bi(g.Ua(g.ri,a),0)):g.H(a)};
fp=function(){g.di(ep);ep.length=0;So();bp&&(bp.cancel(),bp=null);var a=g.F("PAGE_NAME",void 0);a&&g.ri("dispose-"+a);g.ri("dispose")};
gp=function(){cp()};
hp=function(){g.F("TIMING_REPORT_ON_UNLOAD")&&g.uo(!0);Bo("u");g.Im("FOREGROUND_HEARTBEAT_TRIGGER_ON_BACKGROUND");Em();g.yi();fp();g.ri("pageunload")};
ip=function(){g.gi()};
jp=function(){window.yt_spf_loaded_history=!0;g.gi()};
pp=function(){kp=1;lp=mp=0;g.F("TIMING_REPORT_ON_UNLOAD")&&g.uo(!0);if(g.xh("warm_load_nav_start_web")){Bo("n");var a=Un();a[""]&&delete a[""];var b={timerName:"",info:{},tick:{},span:{}};Tn().push(b);a[""]=b;ko();In(!1);g.uh("TIMING_AFT_KEYS",[]);lo("yt_lt","warm");g.uh("TIMING_ACTION","");g.uh("TIMING_WAIT",[]);delete g.F("TIMING_INFO",{}).yt_lt;oo("n",void 0)}else Bo("n"),ko(),oo("n");xo("nr");np(op);Em();g.ri("navigate")};
up=function(a){a=a.detail.part||a.detail.partial;g.no("nc"+mp);++mp;if(a&&a.data&&a.data.deferDispose)"watch"==a.name&&g.no("bc");else{var b=1==kp;kp=2;b?(np(qp),rp()):np(sp);if(b=a&&a.data&&a.data.swfcfg)tp(a.timing,b.args),wo("cfg")||g.no("cfg"),$o(b)}};
vp=function(){g.no("np"+lp);++lp};
xp=function(a){a=a.detail.response;var b=1==kp;kp=3;b&&(np(wp),rp());if(b=a.data&&a.data.swfcfg)tp(a.timing,b.args),wo("cfg")||g.no("cfg"),$o(b)};
Bp=function(a){g.no("nd");a=a.detail.response;g.yp=a.cacheKey;a=a.timing;var b=window._spf_state;g.zp.navigationCount=b&&b["nav-counter"]||0;g.xh("warm_load_nav_start_web")?g.no("srt",a.responseStart):(oo("ne",a.responseStart),b=Gn().responseStart,lo("srt",Math.max(0,b-Hn())));lo("yt_lt",a.spfCached?"hot":"warm");g.no("pfs",a.fetchStart);g.no("pfrs",a.responseStart);"redirectStart"in a&&po();np(Ap);document.getElementById("content").style.height="";cp();kp=0};
Cp=function(a){var b=a.detail.url,c=a.detail.err;c&&(a=a.detail.xhr,a&&!a.responseText||a&&a.responseText&&a.responseText.startsWith("<")||(c.params=b,g.Ih(c)))};
Ep=function(){Dp();window.yt_spf_loaded_history=!1};
Fp=function(){};
rp=function(){var a=document.getElementById("content");-1<a.className.indexOf("spf-animate")&&(a.style.height=a.clientHeight+"px");fp();a=g.F("PREVIOUS_ACTION");for(var b in g.th)delete g.th[b];g.uh("PREVIOUS_ACTION",a);g.uh("SERVED_VIA_SPF_HISTORY",!!window.yt_spf_loaded_history);g.r("ytplayer.config",null,void 0);(b=g.Yo())&&b.stopVideo&&(b.stopVideo(),b.getLastError()&&(b=document.getElementById("movie_player"))&&b.stopVideo&&b.stopVideo());Xo()};
np=function(a){var b=document.getElementById("progress");b||(b=document.createElement("div"),b.id="progress",b.appendChild(document.createElement("dt")),b.appendChild(document.createElement("dd")),document.body.appendChild(b));g.ci(Gp);Gp=g.bi(function(){var c=a[0],d=a[1],e=a[2];b.className="";var f=b.style;f.transitionDuration=f.webkitTransitionDuration=c+"ms";f.width=d+"%";g.Wh(Hp);Hp=g.Uh(function(){b.className=e},c)},0)};
Dp=function(){var a=Ap[0]+50;g.Wh(Hp);Hp=g.Uh(function(){var b=document.getElementById("progress");b&&b.parentNode.removeChild(b)},a)};
tp=function(a,b){var c=a&&a.spfPrefetched;a&&a.spfCached&&!c&&null!=b&&Uo(b)};
Ip=function(a,b,c,d,e){b=void 0===b?"Unknown file":b;c=void 0===c?0:c;var f=!1,k;if((k=(k=g.th.EXPERIMENT_FLAGS)?k.log_window_onerror_fraction:void 0)&&Math.random()<k)f=!0;else{k=document.getElementsByTagName("script");for(var l=0,m=k.length;l<m;l++)if(0<k[l].src.indexOf("/debug-")){f=!0;break}}f&&(f=!1,e?f=!0:("string"===typeof a?k=a:ErrorEvent&&a instanceof ErrorEvent?(f=!0,k=a.message,b=a.filename,c=a.lineno,d=a.colno):(k="Unknown error",b="Unknown file",c=0),e=new g.gk(k),e.name="UnhandledWindowError",
e.message=k,e.fileName=b,e.lineNumber=c,isNaN(d)?delete e.columnNumber:e.columnNumber=d),f?g.zl(e):g.Al(e))};
Jp=function(){g.Jm.call(this,"www/base");this.l=0};
Kp=function(a){(a=a.detail.name)&&Mm(a)};
g.aa=[];fa="function"==typeof Object.defineProperties?Object.defineProperty:function(a,b,c){if(a==Array.prototype||a==Object.prototype)return a;a[b]=c.value;return a};
ea=da(this);ha("Symbol",function(a){function b(e){if(this instanceof b)throw new TypeError("Symbol is not a constructor");return new c("jscomp_symbol_"+(e||"")+"_"+d++,e)}
function c(e,f){this.i=e;fa(this,"description",{configurable:!0,writable:!0,value:f})}
if(a)return a;c.prototype.toString=function(){return this.i};
var d=0;return b});
ha("Symbol.iterator",function(a){if(a)return a;a=Symbol("Symbol.iterator");for(var b="Array Int8Array Uint8Array Uint8ClampedArray Int16Array Uint16Array Int32Array Uint32Array Float32Array Float64Array".split(" "),c=0;c<b.length;c++){var d=ea[b[c]];"function"===typeof d&&"function"!=typeof d.prototype[a]&&fa(d.prototype,a,{configurable:!0,writable:!0,value:function(){return ia(ca(this))}})}return a});
var na="function"==typeof Object.create?Object.create:function(a){function b(){}
b.prototype=a;return new b},Lp=function(){function a(){function c(){}
new c;Reflect.construct(c,[],function(){});
return new c instanceof c}
if("undefined"!=typeof Reflect&&Reflect.construct){if(a())return Reflect.construct;var b=Reflect.construct;return function(c,d,e){c=b(c,d);e&&Reflect.setPrototypeOf(c,e.prototype);return c}}return function(c,d,e){void 0===e&&(e=c);
e=na(e.prototype||Object.prototype);return Function.prototype.apply.call(c,e,d)||e}}(),Mp;
if("function"==typeof Object.setPrototypeOf)Mp=Object.setPrototypeOf;else{var Np;a:{var Op={a:!0},Pp={};try{Pp.__proto__=Op;Np=Pp.a;break a}catch(a){}Np=!1}Mp=Np?function(a,b){a.__proto__=b;if(a.__proto__!==b)throw new TypeError(a+" is not extensible");return a}:null}var oa=Mp;
pa.prototype.M=function(a){this.l=a};
pa.prototype["return"]=function(a){this.j={"return":a};this.i=this.H};
pa.prototype.mb=function(a){this.i=a};
ha("Reflect",function(a){return a?a:{}});
ha("Reflect.construct",function(){return Lp});
ha("Reflect.setPrototypeOf",function(a){return a?a:oa?function(b,c){try{return oa(b,c),!0}catch(d){return!1}}:null});
ha("Promise",function(a){function b(k){this.Y=0;this.Gb=void 0;this.i=[];this.D=!1;var l=this.j();try{k(l.resolve,l.reject)}catch(m){l.reject(m)}}
function c(){this.i=null}
function d(k){return k instanceof b?k:new b(function(l){l(k)})}
if(a)return a;c.prototype.l=function(k){if(null==this.i){this.i=[];var l=this;this.j(function(){l.D()})}this.i.push(k)};
var e=ea.setTimeout;c.prototype.j=function(k){e(k,0)};
c.prototype.D=function(){for(;this.i&&this.i.length;){var k=this.i;this.i=[];for(var l=0;l<k.length;++l){var m=k[l];k[l]=null;try{m()}catch(n){this.C(n)}}}this.i=null};
c.prototype.C=function(k){this.j(function(){throw k;})};
b.prototype.j=function(){function k(n){return function(t){m||(m=!0,n.call(l,t))}}
var l=this,m=!1;return{resolve:k(this.K),reject:k(this.l)}};
b.prototype.K=function(k){if(k===this)this.l(new TypeError("A Promise cannot resolve to itself"));else if(k instanceof b)this.V(k);else{a:switch(typeof k){case "object":var l=null!=k;break a;case "function":l=!0;break a;default:l=!1}l?this.R(k):this.C(k)}};
b.prototype.R=function(k){var l=void 0;try{l=k.then}catch(m){this.l(m);return}"function"==typeof l?this.za(l,k):this.C(k)};
b.prototype.l=function(k){this.H(2,k)};
b.prototype.C=function(k){this.H(1,k)};
b.prototype.H=function(k,l){if(0!=this.Y)throw Error("Cannot settle("+k+", "+l+"): Promise already settled in state"+this.Y);this.Y=k;this.Gb=l;2===this.Y&&this.N();this.F()};
b.prototype.N=function(){var k=this;e(function(){if(k.M()){var l=ea.console;"undefined"!==typeof l&&l.error(k.Gb)}},1)};
b.prototype.M=function(){if(this.D)return!1;var k=ea.CustomEvent,l=ea.Event,m=ea.dispatchEvent;if("undefined"===typeof m)return!0;"function"===typeof k?k=new k("unhandledrejection",{cancelable:!0}):"function"===typeof l?k=new l("unhandledrejection",{cancelable:!0}):(k=ea.document.createEvent("CustomEvent"),k.initCustomEvent("unhandledrejection",!1,!0,k));k.promise=this;k.reason=this.Gb;return m(k)};
b.prototype.F=function(){if(null!=this.i){for(var k=0;k<this.i.length;++k)f.l(this.i[k]);this.i=null}};
var f=new c;b.prototype.V=function(k){var l=this.j();k.fg(l.resolve,l.reject)};
b.prototype.za=function(k,l){var m=this.j();try{k.call(l,m.resolve,m.reject)}catch(n){m.reject(n)}};
b.prototype.then=function(k,l){function m(x,D){return"function"==typeof x?function(O){try{n(x(O))}catch(M){t(M)}}:D}
var n,t,w=new b(function(x,D){n=x;t=D});
this.fg(m(k,n),m(l,t));return w};
b.prototype["catch"]=function(k){return this.then(void 0,k)};
b.prototype.fg=function(k,l){function m(){switch(n.Y){case 1:k(n.Gb);break;case 2:l(n.Gb);break;default:throw Error("Unexpected state: "+n.Y);}}
var n=this;null==this.i?f.l(m):this.i.push(m);this.D=!0};
b.resolve=d;b.reject=function(k){return new b(function(l,m){m(k)})};
b.race=function(k){return new b(function(l,m){for(var n=g.ja(k),t=n.next();!t.done;t=n.next())d(t.value).fg(l,m)})};
b.all=function(k){var l=g.ja(k),m=l.next();return m.done?d([]):new b(function(n,t){function w(O){return function(M){x[O]=M;D--;0==D&&n(x)}}
var x=[],D=0;do x.push(void 0),D++,d(m.value).fg(w(x.length-1),t),m=l.next();while(!m.done)})};
return b});
ha("String.prototype.endsWith",function(a){return a?a:function(b,c){var d=Ca(this,b,"endsWith");b+="";void 0===c&&(c=d.length);for(var e=Math.max(0,Math.min(c|0,d.length)),f=b.length;0<f&&0<e;)if(d[--e]!=b[--f])return!1;return 0>=f}});
ha("String.prototype.startsWith",function(a){return a?a:function(b,c){var d=Ca(this,b,"startsWith");b+="";for(var e=d.length,f=b.length,k=Math.max(0,Math.min(c|0,d.length)),l=0;l<f&&k<e;)if(d[k++]!=b[l++])return!1;return l>=f}});
ha("Object.setPrototypeOf",function(a){return a||oa});
var Qp="function"==typeof Object.assign?Object.assign:function(a,b){for(var c=1;c<arguments.length;c++){var d=arguments[c];if(d)for(var e in d)Da(d,e)&&(a[e]=d[e])}return a};
ha("Object.assign",function(a){return a||Qp});
ha("Array.prototype.entries",function(a){return a?a:function(){return Ea(this,function(b,c){return[b,c]})}});
ha("Array.prototype.keys",function(a){return a?a:function(){return Ea(this,function(b){return b})}});
ha("Array.prototype.values",function(a){return a?a:function(){return Ea(this,function(b,c){return c})}});
ha("Object.is",function(a){return a?a:function(b,c){return b===c?0!==b||1/b===1/c:b!==b&&c!==c}});
ha("Array.prototype.includes",function(a){return a?a:function(b,c){var d=this;d instanceof String&&(d=String(d));var e=d.length,f=c||0;for(0>f&&(f=Math.max(f+e,0));f<e;f++){var k=d[f];if(k===b||Object.is(k,b))return!0}return!1}});
ha("String.prototype.includes",function(a){return a?a:function(b,c){return-1!==Ca(this,b,"includes").indexOf(b,c||0)}});
ha("Object.entries",function(a){return a?a:function(b){var c=[],d;for(d in b)Da(b,d)&&c.push([d,b[d]]);return c}});
ha("WeakMap",function(a){function b(m){this.Oa=(l+=Math.random()+1).toString();if(m){m=g.ja(m);for(var n;!(n=m.next()).done;)n=n.value,this.set(n[0],n[1])}}
function c(){}
function d(m){var n=typeof m;return"object"===n&&null!==m||"function"===n}
function e(m){if(!Da(m,k)){var n=new c;fa(m,k,{value:n})}}
function f(m){var n=Object[m];n&&(Object[m]=function(t){if(t instanceof c)return t;Object.isExtensible(t)&&e(t);return n(t)})}
if(function(){if(!a||!Object.seal)return!1;try{var m=Object.seal({}),n=Object.seal({}),t=new a([[m,2],[n,3]]);if(2!=t.get(m)||3!=t.get(n))return!1;t["delete"](m);t.set(n,4);return!t.has(m)&&4==t.get(n)}catch(w){return!1}}())return a;
var k="$jscomp_hidden_"+Math.random();f("freeze");f("preventExtensions");f("seal");var l=0;b.prototype.set=function(m,n){if(!d(m))throw Error("Invalid WeakMap key");e(m);if(!Da(m,k))throw Error("WeakMap key fail: "+m);m[k][this.Oa]=n;return this};
b.prototype.get=function(m){return d(m)&&Da(m,k)?m[k][this.Oa]:void 0};
b.prototype.has=function(m){return d(m)&&Da(m,k)&&Da(m[k],this.Oa)};
b.prototype["delete"]=function(m){return d(m)&&Da(m,k)&&Da(m[k],this.Oa)?delete m[k][this.Oa]:!1};
return b});
ha("Map",function(a){function b(){var l={};return l.previous=l.next=l.head=l}
function c(l,m){var n=l.l;return ia(function(){if(n){for(;n.head!=l.l;)n=n.previous;for(;n.next!=n.head;)return n=n.next,{done:!1,value:m(n)};n=null}return{done:!0,value:void 0}})}
function d(l,m){var n=m&&typeof m;"object"==n||"function"==n?f.has(m)?n=f.get(m):(n=""+ ++k,f.set(m,n)):n="p_"+m;var t=l.i[n];if(t&&Da(l.i,n))for(var w=0;w<t.length;w++){var x=t[w];if(m!==m&&x.key!==x.key||m===x.key)return{id:n,list:t,index:w,tb:x}}return{id:n,list:t,index:-1,tb:void 0}}
function e(l){this.i={};this.l=b();this.size=0;if(l){l=g.ja(l);for(var m;!(m=l.next()).done;)m=m.value,this.set(m[0],m[1])}}
if(function(){if(!a||"function"!=typeof a||!a.prototype.entries||"function"!=typeof Object.seal)return!1;try{var l=Object.seal({x:4}),m=new a(g.ja([[l,"s"]]));if("s"!=m.get(l)||1!=m.size||m.get({x:4})||m.set({x:4},"t")!=m||2!=m.size)return!1;var n=m.entries(),t=n.next();if(t.done||t.value[0]!=l||"s"!=t.value[1])return!1;t=n.next();return t.done||4!=t.value[0].x||"t"!=t.value[1]||!n.next().done?!1:!0}catch(w){return!1}}())return a;
var f=new WeakMap;e.prototype.set=function(l,m){l=0===l?0:l;var n=d(this,l);n.list||(n.list=this.i[n.id]=[]);n.tb?n.tb.value=m:(n.tb={next:this.l,previous:this.l.previous,head:this.l,key:l,value:m},n.list.push(n.tb),this.l.previous.next=n.tb,this.l.previous=n.tb,this.size++);return this};
e.prototype["delete"]=function(l){l=d(this,l);return l.tb&&l.list?(l.list.splice(l.index,1),l.list.length||delete this.i[l.id],l.tb.previous.next=l.tb.next,l.tb.next.previous=l.tb.previous,l.tb.head=null,this.size--,!0):!1};
e.prototype.clear=function(){this.i={};this.l=this.l.previous=b();this.size=0};
e.prototype.has=function(l){return!!d(this,l).tb};
e.prototype.get=function(l){return(l=d(this,l).tb)&&l.value};
e.prototype.entries=function(){return c(this,function(l){return[l.key,l.value]})};
e.prototype.keys=function(){return c(this,function(l){return l.key})};
e.prototype.values=function(){return c(this,function(l){return l.value})};
e.prototype.forEach=function(l,m){for(var n=this.entries(),t;!(t=n.next()).done;)t=t.value,l.call(m,t[1],t[0],this)};
e.prototype[Symbol.iterator]=e.prototype.entries;var k=0;return e});
ha("Set",function(a){function b(c){this.la=new Map;if(c){c=g.ja(c);for(var d;!(d=c.next()).done;)this.add(d.value)}this.size=this.la.size}
if(function(){if(!a||"function"!=typeof a||!a.prototype.entries||"function"!=typeof Object.seal)return!1;try{var c=Object.seal({x:4}),d=new a(g.ja([c]));if(!d.has(c)||1!=d.size||d.add(c)!=d||1!=d.size||d.add({x:4})!=d||2!=d.size)return!1;var e=d.entries(),f=e.next();if(f.done||f.value[0]!=c||f.value[1]!=c)return!1;f=e.next();return f.done||f.value[0]==c||4!=f.value[0].x||f.value[1]!=f.value[0]?!1:e.next().done}catch(k){return!1}}())return a;
b.prototype.add=function(c){c=0===c?0:c;this.la.set(c,c);this.size=this.la.size;return this};
b.prototype["delete"]=function(c){c=this.la["delete"](c);this.size=this.la.size;return c};
b.prototype.clear=function(){this.la.clear();this.size=0};
b.prototype.has=function(c){return this.la.has(c)};
b.prototype.entries=function(){return this.la.entries()};
b.prototype.values=function(){return this.la.values()};
b.prototype.keys=b.prototype.values;b.prototype[Symbol.iterator]=b.prototype.values;b.prototype.forEach=function(c,d){var e=this;this.la.forEach(function(f){return c.call(d,f,f,e)})};
return b});
ha("Object.values",function(a){return a?a:function(b){var c=[],d;for(d in b)Da(b,d)&&c.push(b[d]);return c}});
ha("Array.from",function(a){return a?a:function(b,c,d){c=null!=c?c:function(l){return l};
var e=[],f="undefined"!=typeof Symbol&&Symbol.iterator&&b[Symbol.iterator];if("function"==typeof f){b=f.call(b);for(var k=0;!(f=b.next()).done;)e.push(c.call(d,f.value,k++))}else for(f=b.length,k=0;k<f;k++)e.push(c.call(d,b[k],k));return e}});
ha("Promise.prototype.finally",function(a){return a?a:function(b){return this.then(function(c){return Promise.resolve(b()).then(function(){return c})},function(c){return Promise.resolve(b()).then(function(){throw c;
})})}});
g.Rp=g.Rp||{};g.q=this||self;Ia=/^[\w+/_-]+[=]{0,2}$/;Ga=null;Oa="closure_uid_"+(1E9*Math.random()>>>0);Qa=0;g.y(g.Ya,Error);g.Ya.prototype.name="CustomError";var Ag;g.cb=Array.prototype.indexOf?function(a,b){return Array.prototype.indexOf.call(a,b,void 0)}:function(a,b){if("string"===typeof a)return"string"!==typeof b||1!=b.length?-1:a.indexOf(b,0);
for(var c=0;c<a.length;c++)if(c in a&&a[c]===b)return c;return-1};
g.Sp=Array.prototype.lastIndexOf?function(a,b){return Array.prototype.lastIndexOf.call(a,b,a.length-1)}:function(a,b){var c=a.length-1;
0>c&&(c=Math.max(0,a.length+c));if("string"===typeof a)return"string"!==typeof b||1!=b.length?-1:a.lastIndexOf(b,c);for(;0<=c;c--)if(c in a&&a[c]===b)return c;return-1};
g.A=Array.prototype.forEach?function(a,b,c){Array.prototype.forEach.call(a,b,c)}:function(a,b,c){for(var d=a.length,e="string"===typeof a?a.split(""):a,f=0;f<d;f++)f in e&&b.call(c,e[f],f,a)};
g.Co=Array.prototype.filter?function(a,b,c){return Array.prototype.filter.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=[],f=0,k="string"===typeof a?a.split(""):a,l=0;l<d;l++)if(l in k){var m=k[l];
b.call(c,m,l,a)&&(e[f++]=m)}return e};
g.Tp=Array.prototype.map?function(a,b,c){return Array.prototype.map.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=Array(d),f="string"===typeof a?a.split(""):a,k=0;k<d;k++)k in f&&(e[k]=b.call(c,f[k],k,a));
return e};
Ag=Array.prototype.reduce?function(a,b,c){return Array.prototype.reduce.call(a,b,c)}:function(a,b,c){var d=c;
(0,g.A)(a,function(e,f){d=b.call(void 0,d,e,f,a)});
return d};
g.Up=Array.prototype.some?function(a,b,c){return Array.prototype.some.call(a,b,c)}:function(a,b,c){for(var d=a.length,e="string"===typeof a?a.split(""):a,f=0;f<d;f++)if(f in e&&b.call(c,e[f],f,a))return!0;
return!1};
g.Vp=Array.prototype.every?function(a,b,c){return Array.prototype.every.call(a,b,c)}:function(a,b,c){for(var d=a.length,e="string"===typeof a?a.split(""):a,f=0;f<d;f++)if(f in e&&!b.call(c,e[f],f,a))return!1;
return!0};var sb="constructor hasOwnProperty isPrototypeOf propertyIsEnumerable toLocaleString toString valueOf".split(" ");var ub;g.yb.prototype.Gc=!0;g.yb.prototype.Vb=function(){return this.i};
var xb={},wb={};g.Wp=RegExp("^[^\u0591-\u06ef\u06fa-\u08ff\u200f\ud802-\ud803\ud83a-\ud83b\ufb1d-\ufdff\ufe70-\ufefc]*[A-Za-z\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u02b8\u0300-\u0590\u0900-\u1fff\u200e\u2c00-\ud801\ud804-\ud839\ud83c-\udbff\uf900-\ufb1c\ufe00-\ufe6f\ufefd-\uffff]");g.Xp=RegExp("^[^A-Za-z\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u02b8\u0300-\u0590\u0900-\u1fff\u200e\u2c00-\ud801\ud804-\ud839\ud83c-\udbff\uf900-\ufb1c\ufe00-\ufe6f\ufefd-\uffff]*[\u0591-\u06ef\u06fa-\u08ff\u200f\ud802-\ud803\ud83a-\ud83b\ufb1d-\ufdff\ufe70-\ufefc]");var Eb,Fb,Gb,Hb,Ib,Jb,Kb;g.Mb=String.prototype.trim?function(a){return a.trim()}:function(a){return/^[\s\xa0]*([\s\S]*?)[\s\xa0]*$/.exec(a)[1]};
Eb=/&/g;Fb=/</g;Gb=/>/g;Hb=/"/g;Ib=/'/g;Jb=/\x00/g;Kb=/[\x00&<>"']/;var Tb,Sb,Wb,Pb;g.Qb.prototype.Gc=!0;g.Qb.prototype.Vb=function(){return this.j.toString()};
g.Qb.prototype.l=!0;g.Qb.prototype.i=function(){return 1};
Tb=/^(?:audio\/(?:3gpp2|3gpp|aac|L16|midi|mp3|mp4|mpeg|oga|ogg|opus|x-m4a|x-matroska|x-wav|wav|webm)|font\/\w+|image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp|x-icon)|video\/(?:mpeg|mp4|ogg|webm|quicktime|x-matroska))(?:;\w+=(?:\w+|"[\w;,= ]+"))*$/i;Sb=/^data:(.*);base64,[a-z0-9+\/]+=*$/i;Wb=/^(?:(?:https?|mailto|ftp):|[^:/?#]*(?:[/?#]|$))/i;Pb={};g.Xb=g.Ub("about:invalid#zClosurez");g.ac.prototype.Gc=!0;g.ac.prototype.Vb=function(){return this.i};
g.$b={};g.Yp=new g.ac("",g.$b);g.Zp=RegExp("\\b(url\\([ \t\n]*)('[ -&(-\\[\\]-~]*'|\"[ !#-\\[\\]-~]*\"|[!#-&*-\\[\\]-~]*)([ \t\n]*\\))","g");g.$p=RegExp("\\b(calc|cubic-bezier|fit-content|hsl|hsla|linear-gradient|matrix|minmax|repeat|rgb|rgba|(rotate|scale|translate)(X|Y|Z|3d)?)\\([-+*/0-9a-z.%\\[\\], ]+\\)","g");g.bc={};g.cc.prototype.Vb=function(){return this.i};
g.aq=new g.cc("",g.bc);a:{var bq=g.q.navigator;if(bq){var cq=bq.userAgent;if(cq){g.dc=cq;break a}}g.dc=""};var jc;g.kc.prototype.l=!0;g.kc.prototype.i=function(){return this.C};
g.kc.prototype.Gc=!0;g.kc.prototype.Vb=function(){return this.j.toString()};
jc={};g.dq=new g.kc(g.q.trustedTypes&&g.q.trustedTypes.emptyHTML||"",0,jc);g.eq=g.nc("<br>",0);g.fq=g.$a(function(){var a=document.createElement("div"),b=document.createElement("div");b.appendChild(document.createElement("div"));a.appendChild(b);b=a.firstChild.firstChild;a.innerHTML=g.lc(g.dq);return!b.parentElement});g.gq=String.prototype.repeat?function(a,b){return a.repeat(b)}:function(a,b){return Array(b+1).join(a)};g.yc=/^(?:([^:/?#.]+):)?(?:\/\/(?:([^\\/?#]*)@)?([^\\/?#]*?)(?::([0-9]+))?(?=[\\/?#]|$))?([^?#]+)?(?:\?([^#]*))?(?:#([\s\S]*))?$/;g.Jc=/#|$/;var Mc=0,Nc=0;Qc.prototype.length=function(){return this.i.length};
Qc.prototype.end=function(){var a=this.i;this.i=[];return a};Xc[" "]=g.Ja;var tq,ad,xq;g.hq=ec("Opera");g.E=ec("Trident")||ec("MSIE");g.Sk=ec("Edge");g.iq=g.Sk||g.E;g.jq=ec("Gecko")&&!(-1!=g.dc.toLowerCase().indexOf("webkit")&&!ec("Edge"))&&!(ec("Trident")||ec("MSIE"))&&!ec("Edge");g.kq=-1!=g.dc.toLowerCase().indexOf("webkit")&&!ec("Edge");g.lq=ec("Macintosh");g.mq=ec("Windows");g.nq=ec("Linux")||ec("CrOS");g.oq=ec("Android");g.pq=Vc();g.qq=ec("iPad");g.rq=ec("iPod");g.sq=g.Wc();
a:{var uq="",vq=function(){var a=g.dc;if(g.jq)return/rv:([^\);]+)(\)|;)/.exec(a);if(g.Sk)return/Edge\/([\d\.]+)/.exec(a);if(g.E)return/\b(?:MSIE|rv)[: ]([^\);]+)(\)|;)/.exec(a);if(g.kq)return/WebKit\/(\S+)/.exec(a);if(g.hq)return/(?:Version)[ \/]?(\S+)/.exec(a)}();
vq&&(uq=vq?vq[1]:"");if(g.E){var wq=$c();if(null!=wq&&wq>parseFloat(uq)){tq=String(wq);break a}}tq=uq}g.bd=tq;ad={};if(g.q.document&&g.E){var yq=$c();xq=yq?yq:parseInt(g.bd,10)||void 0}else xq=void 0;g.dd=xq;g.zq=fc();g.Aq=Vc()||ec("iPod");g.Bq=ec("iPad");g.Cq=g.ic();g.Dq=gc();g.Eq=g.hc()&&!g.Wc();var gd;gd={};g.id=null;jd.prototype.reset=function(){this.j=[];this.i.end();this.l=0};g.sd.prototype.next=function(){return this.i<this.l.length?{done:!1,value:this.l[this.i++]}:{done:!0,value:void 0}};
"undefined"!=typeof Symbol&&"undefined"!=typeof Symbol.iterator&&(g.sd.prototype[Symbol.iterator]=function(){return this});var ud="function"==typeof Uint8Array,vd=[];g.td.prototype.hc=function(){if(this.i)for(var a in this.i){var b=this.i[a];if(Array.isArray(b))for(var c=0;c<b.length;c++)b[c]&&b[c].hc();else b&&b.hc()}return this.vb};
g.td.prototype.l=ud?function(){var a=Uint8Array.prototype.toJSON;Uint8Array.prototype.toJSON=function(){return g.hd(this)};
try{return JSON.stringify(this.vb&&this.hc(),Hd)}finally{Uint8Array.prototype.toJSON=a}}:function(){return JSON.stringify(this.vb&&this.hc(),Hd)};
g.td.prototype.toString=function(){return this.hc().toString()};
g.td.prototype.clone=function(){return new this.constructor(Id(this.hc()))};var $i=window;var Nd={};g.h=g.Od.prototype;g.h.isEnabled=function(){return navigator.cookieEnabled};
g.h.set=function(a,b,c){var d=!1;if("object"===typeof c){var e=c.MJ;d=c.secure||!1;var f=c.domain||void 0;var k=c.path||void 0;var l=c.tm}if(/[;=\s]/.test(a))throw Error('Invalid cookie name "'+a+'"');if(/[;\r\n]/.test(b))throw Error('Invalid cookie value "'+b+'"');void 0===l&&(l=-1);this.i.cookie=a+"="+b+(f?";domain="+f:"")+(k?";path="+k:"")+(0>l?"":0==l?";expires="+(new Date(1970,1,1)).toUTCString():";expires="+(new Date(Date.now()+1E3*l)).toUTCString())+(d?";secure":"")+(null!=e?";samesite="+e:
"")};
g.h.get=function(a,b){for(var c=a+"=",d=(this.i.cookie||"").split(";"),e=0,f;e<d.length;e++){f=(0,g.Mb)(d[e]);if(0==f.lastIndexOf(c,0))return f.substr(c.length);if(f==a)return""}return b};
g.h.remove=function(a,b,c){var d=void 0!==this.get(a);this.set(a,"",{tm:0,path:b,domain:c});return d};
g.h.Fb=function(){return g.Pd(this).keys};
g.h.Za=g.ba(1);g.h.isEmpty=function(){return!this.i.cookie};
g.h.Va=function(){return this.i.cookie?(this.i.cookie||"").split(";").length:0};
g.h.sc=g.ba(5);g.h.clear=function(){for(var a=g.Pd(this).keys,b=a.length-1;0<=b;b--)this.remove(a[b])};
g.vj=new g.Od("undefined"==typeof document?null:document);var ce;ce=!g.E||g.ed(9);g.Fq=!g.jq&&!g.E||g.E&&g.ed(9)||g.jq&&g.cd("1.9.1");g.Gq=g.E&&!g.cd("9");g.Hq=g.E||g.hq||g.kq;g.h=g.Qd.prototype;g.h.clone=function(){return new g.Qd(this.x,this.y)};
g.h.equals=function(a){return a instanceof g.Qd&&g.Rd(this,a)};
g.h.ceil=function(){this.x=Math.ceil(this.x);this.y=Math.ceil(this.y);return this};
g.h.floor=function(){this.x=Math.floor(this.x);this.y=Math.floor(this.y);return this};
g.h.round=function(){this.x=Math.round(this.x);this.y=Math.round(this.y);return this};g.h=g.Td.prototype;g.h.clone=function(){return new g.Td(this.width,this.height)};
g.h.aspectRatio=function(){return this.width/this.height};
g.h.isEmpty=function(){return!(this.width*this.height)};
g.h.ceil=function(){this.width=Math.ceil(this.width);this.height=Math.ceil(this.height);return this};
g.h.floor=function(){this.width=Math.floor(this.width);this.height=Math.floor(this.height);return this};
g.h.round=function(){this.width=Math.round(this.width);this.height=Math.round(this.height);return this};var Wd={cellpadding:"cellPadding",cellspacing:"cellSpacing",colspan:"colSpan",frameborder:"frameBorder",height:"height",maxlength:"maxLength",nonce:"nonce",role:"role",rowspan:"rowSpan",type:"type",usemap:"useMap",valign:"vAlign",width:"width"};var je={GC:"allow-forms",HC:"allow-modals",IC:"allow-orientation-lock",JC:"allow-pointer-lock",KC:"allow-popups",LC:"allow-popups-to-escape-sandbox",MC:"allow-presentation",NC:"allow-same-origin",OC:"allow-scripts",PC:"allow-top-navigation",QC:"allow-top-navigation-by-user-activation"},me=g.$a(function(){return le()});g.B.prototype.Ma=!1;g.B.prototype.Ha=function(){return this.Ma};
g.B.prototype.dispose=function(){this.Ma||(this.Ma=!0,this.O())};
g.B.prototype.O=function(){if(this.za)for(;this.za.length;)this.za.shift()()};g.h=g.re.prototype;g.h.clone=function(){return new g.re(this.left,this.top,this.width,this.height)};
g.h.contains=function(a){return a instanceof g.Qd?a.x>=this.left&&a.x<=this.left+this.width&&a.y>=this.top&&a.y<=this.top+this.height:this.left<=a.left&&this.left+this.width>=a.left+a.width&&this.top<=a.top&&this.top+this.height>=a.top+a.height};
g.h.ceil=function(){this.left=Math.ceil(this.left);this.top=Math.ceil(this.top);this.width=Math.ceil(this.width);this.height=Math.ceil(this.height);return this};
g.h.floor=function(){this.left=Math.floor(this.left);this.top=Math.floor(this.top);this.width=Math.floor(this.width);this.height=Math.floor(this.height);return this};
g.h.round=function(){this.left=Math.round(this.left);this.top=Math.round(this.top);this.width=Math.round(this.width);this.height=Math.round(this.height);return this};var se={};var Iq;g.p(we,ve);we.prototype.toString=function(){return this.i.toString()};
var Jq=null===(Iq=ue())||void 0===Iq?void 0:Iq.emptyHTML;new we(null!==Jq&&void 0!==Jq?Jq:"",se);var Kq;g.p(ye,xe);ye.prototype.toString=function(){return this.i.toString()};
var Lq=null===(Kq=ue())||void 0===Kq?void 0:Kq.emptyScript;new ye(null!==Lq&&void 0!==Lq?Lq:"",se);g.p(Ae,ze);Ae.prototype.toString=function(){return this.i};new Ae("about:blank",se);new Ae("about:invalid#zTSz",se);g.Mq=g.jq?"MozUserSelect":g.kq||g.Sk?"WebkitUserSelect":null;var Zi=(new Date).getTime();Ie.prototype.j=function(a){for(var b=[],c=0;c<arguments.length;++c)b[c-0]=arguments[c];b=this.C(b);return this.hf.has(b)?this.hf.get(b):void 0};
Ie.prototype.clear=function(){this.hf.clear()};
Ie.prototype.C=function(a){for(var b=[],c=0;c<arguments.length;++c)b[c-0]=arguments[c];return b?b.join(","):"key"};g.y(Ke,g.td);g.y(Le,g.td);var Je=[1];Le.prototype.Va=function(){return g.Ad(this,2)};g.y(Pe,g.td);g.y(Re,g.td);g.y(Se,g.td);g.y(Te,g.td);g.y(Ve,g.td);g.y(Xe,g.td);g.y(Ze,g.td);var Oe=[3,6,4],Qe=[[1,2]],Ue=[1],We=[[1,2,3]],Ye=[[1,2,3]];g.p(ff,Ie);g.gf.prototype.stopPropagation=function(){this.i=!0};
g.gf.prototype.preventDefault=function(){this.defaultPrevented=!0};var Cf,Oq,yf;g.Nq=!g.E||g.ed(9);Cf=!g.E||g.ed(9);Oq=g.E&&!g.cd("9");yf=function(){if(!g.q.addEventListener||!Object.defineProperty)return!1;var a=!1,b=Object.defineProperty({},"passive",{get:function(){a=!0}});
try{g.q.addEventListener("test",g.Ja,b),g.q.removeEventListener("test",g.Ja,b)}catch(c){}return a}();g.Pq=g.kq?"webkitTransitionEnd":g.hq?"otransitionend":"transitionend";g.y(g.hf,g.gf);var Qq={2:"touch",3:"pen",4:"mouse"};
g.hf.prototype.init=function(a,b){var c=this.type=a.type,d=a.changedTouches&&a.changedTouches.length?a.changedTouches[0]:null;this.target=a.target||a.srcElement;this.currentTarget=b;var e=a.relatedTarget;e?g.jq&&(g.Yc(e,"nodeName")||(e=null)):"mouseover"==c?e=a.fromElement:"mouseout"==c&&(e=a.toElement);this.relatedTarget=e;d?(this.clientX=void 0!==d.clientX?d.clientX:d.pageX,this.clientY=void 0!==d.clientY?d.clientY:d.pageY,this.screenX=d.screenX||0,this.screenY=d.screenY||0):(this.clientX=void 0!==
a.clientX?a.clientX:a.pageX,this.clientY=void 0!==a.clientY?a.clientY:a.pageY,this.screenX=a.screenX||0,this.screenY=a.screenY||0);this.button=a.button;this.keyCode=a.keyCode||0;this.key=a.key||"";this.charCode=a.charCode||("keypress"==c?a.keyCode:0);this.ctrlKey=a.ctrlKey;this.altKey=a.altKey;this.shiftKey=a.shiftKey;this.metaKey=a.metaKey;this.l=g.lq?a.metaKey:a.ctrlKey;this.pointerId=a.pointerId||0;this.pointerType="string"===typeof a.pointerType?a.pointerType:Qq[a.pointerType]||"";this.state=
a.state;this.yb=a;a.defaultPrevented&&this.preventDefault()};
g.hf.prototype.stopPropagation=function(){g.hf.T.stopPropagation.call(this);this.yb.stopPropagation?this.yb.stopPropagation():this.yb.cancelBubble=!0};
g.hf.prototype.preventDefault=function(){g.hf.T.preventDefault.call(this);var a=this.yb;if(a.preventDefault)a.preventDefault();else if(a.returnValue=!1,Oq)try{if(a.ctrlKey||112<=a.keyCode&&123>=a.keyCode)a.keyCode=-1}catch(b){}};var jf="closure_listenable_"+(1E6*Math.random()|0),lf=0;g.h=of.prototype;g.h.add=function(a,b,c,d,e){var f=a.toString();a=this.listeners[f];a||(a=this.listeners[f]=[],this.i++);var k=qf(a,b,d,e);-1<k?(b=a[k],c||(b.eg=!1)):(b=new mf(b,this.src,f,!!d,e),b.eg=c,a.push(b));return b};
g.h.remove=function(a,b,c,d){a=a.toString();if(!(a in this.listeners))return!1;var e=this.listeners[a];b=qf(e,b,c,d);return-1<b?(nf(e[b]),g.eb(e,b),0==e.length&&(delete this.listeners[a],this.i--),!0):!1};
g.h.removeAll=function(a){a=a&&a.toString();var b=0,c;for(c in this.listeners)if(!a||c==a){for(var d=this.listeners[c],e=0;e<d.length;e++)++b,nf(d[e]);delete this.listeners[c];this.i--}return b};
g.h.Id=function(a,b,c,d){a=this.listeners[a.toString()];var e=-1;a&&(e=qf(a,b,c,d));return-1<e?a[e]:null};
g.h.hasListener=function(a,b){var c=void 0!==a,d=c?a.toString():"",e=void 0!==b;return jb(this.listeners,function(f){for(var k=0;k<f.length;++k)if(!(c&&f[k].type!=d||e&&f[k].capture!=b))return!0;return!1})};var wf="closure_lm_"+(1E6*Math.random()|0),Ff={},Af=0,If="__closure_events_fn_"+(1E9*Math.random()>>>0);g.y(g.Jf,g.B);g.Jf.prototype[jf]=!0;g.h=g.Jf.prototype;g.h.vg=function(){return this.oj};
g.h.Kf=g.ba(6);g.h.addEventListener=function(a,b,c,d){g.sf(this,a,b,c,d)};
g.h.removeEventListener=function(a,b,c,d){g.Df(this,a,b,c,d)};
g.h.dispatchEvent=function(a){var b=this.vg();if(b){var c=[];for(var d=1;b;b=b.vg())c.push(b),++d}b=this.wp;d=a.type||a;if("string"===typeof a)a=new g.gf(a,b);else if(a instanceof g.gf)a.target=a.target||b;else{var e=a;a=new g.gf(d,b);g.tb(a,e)}e=!0;if(c)for(var f=c.length-1;!a.i&&0<=f;f--){var k=a.currentTarget=c[f];e=Kf(k,d,!0,a)&&e}a.i||(k=a.currentTarget=b,e=Kf(k,d,!0,a)&&e,a.i||(e=Kf(k,d,!1,a)&&e));if(c)for(f=0;!a.i&&f<c.length;f++)k=a.currentTarget=c[f],e=Kf(k,d,!1,a)&&e;return e};
g.h.O=function(){g.Jf.T.O.call(this);this.removeAllListeners();this.oj=null};
g.h.L=function(a,b,c,d){return this.Bc.add(String(a),b,!1,c,d)};
g.h.Yd=function(a,b,c,d){return this.Bc.add(String(a),b,!0,c,d)};
g.h.ua=function(a,b,c,d){return this.Bc.remove(String(a),b,c,d)};
g.h.removeAllListeners=function(a){return this.Bc?this.Bc.removeAll(a):0};
g.h.Id=function(a,b,c,d){return this.Bc.Id(String(a),b,c,d)};
g.h.hasListener=function(a,b){return this.Bc.hasListener(void 0!==a?String(a):void 0,b)};Lf.prototype.get=function(){if(0<this.l){this.l--;var a=this.i;this.i=a.next;a.next=null}else a=this.j();return a};var Xf;var Yf=new Lf(function(){return new Qf},function(a){a.reset()});
Pf.prototype.add=function(a,b){var c=Yf.get();c.set(a,b);this.l?this.l.next=c:this.i=c;this.l=c};
Pf.prototype.remove=function(){var a=null;this.i&&(a=this.i,this.i=this.i.next,this.i||(this.l=null),a.next=null);return a};
Qf.prototype.set=function(a,b){this.Eb=a;this.scope=b;this.next=null};
Qf.prototype.reset=function(){this.next=this.scope=this.Eb=null};var Rf,Tf=!1,Uf=new Pf;bg.prototype.reset=function(){this.context=this.onRejected=this.j=this.i=null;this.l=!1};
var cg=new Lf(function(){return new bg},function(a){a.reset()});
g.ag.prototype.then=function(a,b,c){return hg(this,"function"===typeof a?a:null,"function"===typeof b?b:null,c)};
g.Zf(g.ag);g.h=g.ag.prototype;g.h.cancel=function(a){if(0==this.Y){var b=new g.og(a);Vf(function(){jg(this,b)},this)}};
g.h.ZA=function(a){this.Y=0;this.Rb(2,a)};
g.h.aB=function(a){this.Y=0;this.Rb(3,a)};
g.h.Rb=function(a,b){0==this.Y&&(this===b&&(a=3,b=new TypeError("Promise cannot resolve to itself")),this.Y=1,eg(b,this.ZA,this.aB,this)||(this.Gb=b,this.Y=a,this.Ja=null,mg(this),3!=a||b instanceof g.og||sg(this,b)))};
g.h.kq=function(){for(var a;a=kg(this);)lg(this,a,this.Y,this.Gb);this.ci=!1};
var rg=Nf;g.y(g.og,g.Ya);g.og.prototype.name="cancel";g.y(g.tg,g.Jf);g.h=g.tg.prototype;g.h.enabled=!1;g.h.tc=null;g.h.setInterval=function(a){this.l=a;this.tc&&this.enabled?(this.stop(),this.start()):this.tc&&this.stop()};
g.h.Kq=function(){if(this.enabled){var a=g.Va()-this.C;0<a&&a<.8*this.l?this.tc=this.i.setTimeout(this.j,this.l-a):(this.tc&&(this.i.clearTimeout(this.tc),this.tc=null),this.dispatchEvent("tick"),this.enabled&&(this.stop(),this.start()))}};
g.h.start=function(){this.enabled=!0;this.tc||(this.tc=this.i.setTimeout(this.j,this.l),this.C=g.Va())};
g.h.stop=function(){this.enabled=!1;this.tc&&(this.i.clearTimeout(this.tc),this.tc=null)};
g.h.O=function(){g.tg.T.O.call(this);this.stop();delete this.i};xg.prototype.C=function(){var a=this.j.values();a=[].concat(g.ma(a)).filter(function(b){return b.hf.size});
a.length&&this.D.flush(a);yg(a);this.l.enabled&&this.l.stop()};
xg.prototype.i=function(a,b){for(var c=[],d=1;d<arguments.length;++d)c[d-1]=arguments[d];this.j.has(a)||this.j.set(a,new ff(a,c))};zg.prototype.set=function(a,b){b=void 0===b?!0:b;0<=a&&52>a&&0===a%1&&this.i[a]!=b&&(this.i[a]=b,this.l=-1)};
zg.prototype.get=function(a){return!!this.i[a]};g.y(g.Cg,g.B);g.h=g.Cg.prototype;g.h.Oa=0;g.h.O=function(){g.Cg.T.O.call(this);this.stop();delete this.i;delete this.l};
g.h.start=function(a){this.stop();this.Oa=g.ug(this.qb,void 0!==a?a:this.j)};
g.h.stop=function(){this.isActive()&&g.vg(this.Oa);this.Oa=0};
g.h.isActive=function(){return 0!=this.Oa};
g.h.ql=function(){this.Oa=0;this.i&&this.i.call(this.l)};g.y(Eg,Dg);Eg.prototype.reset=function(){this.i[0]=1732584193;this.i[1]=4023233417;this.i[2]=2562383102;this.i[3]=271733878;this.i[4]=3285377520;this.D=this.j=0};
Eg.prototype.update=function(a,b){if(null!=a){void 0===b&&(b=a.length);for(var c=b-this.l,d=0,e=this.H,f=this.j;d<b;){if(0==f)for(;d<=c;)Fg(this,a,d),d+=this.l;if("string"===typeof a)for(;d<b;){if(e[f]=a.charCodeAt(d),++f,++d,f==this.l){Fg(this,e);f=0;break}}else for(;d<b;)if(e[f]=a[d],++f,++d,f==this.l){Fg(this,e);f=0;break}}this.j=f;this.D+=b}};
Eg.prototype.digest=function(){var a=[],b=8*this.D;56>this.j?this.update(this.C,56-this.j):this.update(this.C,this.l-(this.j-56));for(var c=this.l-1;56<=c;c--)this.H[c]=b&255,b/=256;Fg(this,this.H);for(c=b=0;5>c;c++)for(var d=24;0<=d;d-=8)a[b]=this.i[c]>>d&255,++b;return a};var Hg="StopIteration"in g.q?g.q.StopIteration:{message:"StopIteration",stack:""};Gg.prototype.next=function(){throw Hg;};
Gg.prototype.dc=function(){return this};g.h=g.Mg.prototype;g.h.Va=function(){return this.l};
g.h.Za=g.ba(0);g.h.Fb=function(){g.Pg(this);return this.i.concat()};
g.h.sc=g.ba(4);g.h.equals=function(a,b){if(this===a)return!0;if(this.l!=a.Va())return!1;var c=b||Ng;g.Pg(this);for(var d,e=0;d=this.i[e];e++)if(!c(this.get(d),a.get(d)))return!1;return!0};
g.h.isEmpty=function(){return 0==this.l};
g.h.clear=function(){this.la={};this.xd=this.l=this.i.length=0};
g.h.remove=function(a){return g.Og(this.la,a)?(delete this.la[a],this.l--,this.xd++,this.i.length>2*this.l&&g.Pg(this),!0):!1};
g.h.get=function(a,b){return g.Og(this.la,a)?this.la[a]:b};
g.h.set=function(a,b){g.Og(this.la,a)||(this.l++,this.i.push(a),this.xd++);this.la[a]=b};
g.h.forEach=function(a,b){for(var c=this.Fb(),d=0;d<c.length;d++){var e=c[d],f=this.get(e);a.call(b,f,e,this)}};
g.h.clone=function(){return new g.Mg(this)};
g.h.dc=function(a){g.Pg(this);var b=0,c=this.xd,d=this,e=new Gg;e.next=function(){if(c!=d.xd)throw Error("The map has changed since the iterator was created");if(b>=d.i.length)throw Hg;var f=d.i[b++];return a?f:d.la[f]};
return e};var Xg={'"':'\\"',"\\":"\\\\","/":"\\/","\b":"\\b","\f":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\x0B":"\\u000b"},Wg=/\uffff/.test("\uffff")?/[\\"\x00-\x1f\x7f-\uffff]/g:/[\\"\x00-\x1f\x7f-\xff]/g;g.y(g.Yg,g.B);g.h=g.Yg.prototype;g.h.subscribe=function(a,b,c){var d=this.l[a];d||(d=this.l[a]=[]);var e=this.D;this.i[e]=a;this.i[e+1]=b;this.i[e+2]=c;this.D=e+3;d.push(e);return e};
g.h.nc=function(a){var b=this.i[a];if(b){var c=this.l[b];0!=this.C?(this.j.push(a),this.i[a+1]=g.Ja):(c&&g.fb(c,a),delete this.i[a],delete this.i[a+1],delete this.i[a+2])}return!!b};
g.h.ea=function(a,b){var c=this.l[a];if(c){for(var d=Array(arguments.length-1),e=1,f=arguments.length;e<f;e++)d[e-1]=arguments[e];if(this.H)for(e=0;e<c.length;e++){var k=c[e];$g(this.i[k+1],this.i[k+2],d)}else{this.C++;try{for(e=0,f=c.length;e<f;e++)k=c[e],this.i[k+1].apply(this.i[k+2],d)}finally{if(this.C--,0<this.j.length&&0==this.C)for(;c=this.j.pop();)this.nc(c)}}return 0!=e}return!1};
g.h.clear=function(a){if(a){var b=this.l[a];b&&(g.A(b,this.nc,this),delete this.l[a])}else this.i.length=0,this.l={}};
g.h.Va=function(a){if(a){var b=this.l[a];return b?b.length:0}a=0;for(b in this.l)a+=this.Va(b);return a};
g.h.O=function(){g.Yg.T.O.call(this);this.clear();this.j.length=0};g.ah.prototype.set=function(a,b){void 0===b?this.i.remove(a):this.i.set(a,g.Ug(b))};
g.ah.prototype.get=function(a){try{var b=this.i.get(a)}catch(c){return}if(null!==b)try{return JSON.parse(b)}catch(c){throw"Storage: Invalid value was encountered";}};
g.ah.prototype.remove=function(a){this.i.remove(a)};g.y(bh,g.ah);bh.prototype.set=function(a,b){bh.T.set.call(this,a,dh(b))};
bh.prototype.l=function(a){a=bh.T.get.call(this,a);if(void 0===a||a instanceof Object)return a;throw"Storage: Invalid value was encountered";};
bh.prototype.get=function(a){if(a=this.l(a)){if(a=a.data,void 0===a)throw"Storage: Invalid value was encountered";}else a=void 0;return a};g.y(g.eh,bh);g.eh.prototype.set=function(a,b,c){if(b=dh(b)){if(c){if(c<g.Va()){g.eh.prototype.remove.call(this,a);return}b.expiration=c}b.creation=g.Va()}g.eh.T.set.call(this,a,b)};
g.eh.prototype.l=function(a,b){var c=g.eh.T.l.call(this,a);if(c)if(!b&&g.fh(c))g.eh.prototype.remove.call(this,a);else return c};g.y(hh,gh);hh.prototype.Va=function(){var a=0;g.Jg(this.dc(!0),function(){a++});
return a};
hh.prototype.clear=function(){var a=Kg(this.dc(!0)),b=this;g.A(a,function(c){b.remove(c)})};g.y(g.ih,hh);g.h=g.ih.prototype;g.h.isAvailable=function(){if(!this.i)return!1;try{return this.i.setItem("__sak","1"),this.i.removeItem("__sak"),!0}catch(a){return!1}};
g.h.set=function(a,b){try{this.i.setItem(a,b)}catch(c){if(0==this.i.length)throw"Storage mechanism: Storage disabled";throw"Storage mechanism: Quota exceeded";}};
g.h.get=function(a){a=this.i.getItem(a);if("string"!==typeof a&&null!==a)throw"Storage mechanism: Invalid value was encountered";return a};
g.h.remove=function(a){this.i.removeItem(a)};
g.h.Va=function(){return this.i.length};
g.h.dc=function(a){var b=0,c=this.i,d=new Gg;d.next=function(){if(b>=c.length)throw Hg;var e=c.key(b++);if(a)return e;e=c.getItem(e);if("string"!==typeof e)throw"Storage mechanism: Invalid value was encountered";return e};
return d};
g.h.clear=function(){this.i.clear()};
g.h.key=function(a){return this.i.key(a)};g.y(g.jh,g.ih);g.y(lh,hh);var mh={".":".2E","!":".21","~":".7E","*":".2A","'":".27","(":".28",")":".29","%":"."},kh=null;g.h=lh.prototype;g.h.isAvailable=function(){return!!this.i};
g.h.set=function(a,b){this.i.setAttribute(nh(a),b);oh(this)};
g.h.get=function(a){a=this.i.getAttribute(nh(a));if("string"!==typeof a&&null!==a)throw"Storage mechanism: Invalid value was encountered";return a};
g.h.remove=function(a){this.i.removeAttribute(nh(a));oh(this)};
g.h.Va=function(){return ph(this).attributes.length};
g.h.dc=function(a){var b=0,c=ph(this).attributes,d=new Gg;d.next=function(){if(b>=c.length)throw Hg;var e=c[b++];if(a)return decodeURIComponent(e.nodeName.replace(/\./g,"%")).substr(1);e=e.nodeValue;if("string"!==typeof e)throw"Storage mechanism: Invalid value was encountered";return e};
return d};
g.h.clear=function(){for(var a=ph(this),b=a.attributes.length;0<b;b--)a.removeAttribute(a.attributes[b-1].nodeName);oh(this)};g.y(qh,hh);qh.prototype.set=function(a,b){this.l.set(this.i+a,b)};
qh.prototype.get=function(a){return this.l.get(this.i+a)};
qh.prototype.remove=function(a){this.l.remove(this.i+a)};
qh.prototype.dc=function(a){var b=this.l.dc(!0),c=this,d=new Gg;d.next=function(){for(var e=b.next();e.substr(0,c.i.length)!=c.i;)e=b.next();return a?e.substr(c.i.length):c.l.get(e)};
return d};g.th=window.yt&&window.yt.config_||window.ytcfg&&window.ytcfg.data_||{};g.r("yt.config_",g.th,void 0);var Rq=0,Ch=g.kq?"webkit":g.jq?"moz":g.E?"ms":g.hq?"o":"",Ah=g.u("ytDomDomGetNextId")||function(){return++Rq};
g.r("ytDomDomGetNextId",Ah,void 0);var Eh=[];var Jh={stopImmediatePropagation:1,stopPropagation:1,preventMouseEvent:1,preventManipulation:1,preventDefault:1,layerX:1,layerY:1,screenX:1,screenY:1,scale:1,rotation:1,webkitMovementX:1,webkitMovementY:1};g.Kh.prototype.preventDefault=function(){this.event&&(this.event.returnValue=!1,this.event.preventDefault&&this.event.preventDefault())};
g.Kh.prototype.stopPropagation=function(){this.event&&(this.event.cancelBubble=!0,this.event.stopPropagation&&this.event.stopPropagation())};
g.Kh.prototype.stopImmediatePropagation=function(){this.event&&(this.event.cancelBubble=!0,this.event.stopImmediatePropagation&&this.event.stopImmediatePropagation())};var Oh=g.q.ytEventsEventsListeners||{};g.r("ytEventsEventsListeners",Oh,void 0);var Qh=g.q.ytEventsEventsCounter||{count:0};g.r("ytEventsEventsCounter",Qh,void 0);var Rh=g.$a(function(){var a=!1;try{var b=Object.defineProperty({},"capture",{get:function(){a=!0}});
window.addEventListener("test",null,b)}catch(c){}return a});g.Sq=window.ytcsi&&window.ytcsi.now?window.ytcsi.now:window.performance&&window.performance.timing&&window.performance.now&&window.performance.timing.navigationStart?function(){return window.performance.timing.navigationStart+window.performance.now()}:function(){return(new Date).getTime()};
g.Tq="Microsoft Internet Explorer"==navigator.appName;g.y(g.Yh,g.B);g.Yh.prototype.R=function(a){this.i=new g.Qd(g.Mh(a),g.Nh(a))};
g.Yh.prototype.F=function(){if(this.i){var a=g.Sq();if(0!=this.j){var b=g.Sd(this.H,this.i)/(a-this.j);this.l[this.D]=.5<Math.abs((b-this.C)/this.C)?1:0;for(var c=0,d=0;4>d;d++)c+=this.l[d]||0;3<=c&&this.qb();this.C=b}this.j=a;this.H=this.i;this.D=(this.D+1)%4}};
g.Yh.prototype.O=function(){g.Xh(this.K);g.Sh(this.M)};g.p(ei,Zh);ei.prototype.start=function(){var a=g.u("yt.scheduler.instance.start");a&&a()};
ei.prototype.pause=function(){var a=g.u("yt.scheduler.instance.pause");a&&a()};
g.Ka(ei);ei.getInstance();var ji={};var Uq=g.q.ytPubsubPubsubInstance||new g.Yg,mi=g.q.ytPubsubPubsubSubscribedKeys||{},oi=g.q.ytPubsubPubsubTopicToKeys||{},ni=g.q.ytPubsubPubsubIsSynchronous||{};g.Yg.prototype.subscribe=g.Yg.prototype.subscribe;g.Yg.prototype.unsubscribeByKey=g.Yg.prototype.nc;g.Yg.prototype.publish=g.Yg.prototype.ea;g.Yg.prototype.clear=g.Yg.prototype.clear;g.r("ytPubsubPubsubInstance",Uq,void 0);g.r("ytPubsubPubsubTopicToKeys",oi,void 0);g.r("ytPubsubPubsubIsSynchronous",ni,void 0);
g.r("ytPubsubPubsubSubscribedKeys",mi,void 0);var Vq;Vq=window;g.Ci=Vq.ytcsi&&Vq.ytcsi.now?Vq.ytcsi.now:Vq.performance&&Vq.performance.timing&&Vq.performance.now&&Vq.performance.timing.navigationStart?function(){return Vq.performance.timing.navigationStart+Vq.performance.now()}:function(){return(new Date).getTime()};var Bi,Ki,Li,Ji,ui,Ai;Bi=g.yh("initial_gel_batch_timeout",1E3);Ki=Math.pow(2,16)-1;g.Oi=10;Li=null;Ji=0;ui=void 0;g.si=0;g.ti=0;g.Ni=0;Ai=!0;g.wi=g.q.ytLoggingTransportGELQueue_||new Map;g.r("ytLoggingTransportGELQueue_",g.wi,void 0);var Fi=g.q.ytLoggingTransportTokensToCttTargetIds_||{};g.r("ytLoggingTransportTokensToCttTargetIds_",Fi,void 0);var Mi=g.q.ytLoggingGelSequenceIdObj_||{};g.r("ytLoggingGelSequenceIdObj_",Mi,void 0);var Qi={q:!0,search_query:!0};var Yi=new function(){var a=window.document;this.i=window;this.l=a};
g.r("yt.ads_.signals_.getAdSignalsString",function(a){return g.Si(g.aj(a))},void 0);var bj="XMLHttpRequest"in g.q?function(){return new XMLHttpRequest}:null;var ej,gj,nj;ej={Authorization:"AUTHORIZATION","X-Goog-Visitor-Id":"SANDBOXED_VISITOR_ID","X-YouTube-Client-Name":"INNERTUBE_CONTEXT_CLIENT_NAME","X-YouTube-Client-Version":"INNERTUBE_CONTEXT_CLIENT_VERSION","X-YouTube-Delegation-Context":"INNERTUBE_CONTEXT_SERIALIZED_DELEGATION_CONTEXT","X-YouTube-Device":"DEVICE","X-Youtube-Identity-Token":"ID_TOKEN","X-YouTube-Page-CL":"PAGE_CL","X-YouTube-Page-Label":"PAGE_BUILD_LABEL","X-YouTube-Variants-Checksum":"VARIANTS_CHECKSUM"};gj="app debugcss debugjs expflag force_ad_params force_viral_ad_response_params forced_experiments innertube_snapshots innertube_goldens internalcountrycode internalipoverride absolute_experiments conditional_experiments sbb sr_bns_address client_dev_root_url".split(" ");
nj=!1;g.Wq=oj;g.zj.prototype.set=function(a,b,c,d){c=c||31104E3;this.remove(a);if(this.i)try{this.i.set(a,b,g.Va()+1E3*c);return}catch(f){}var e="";if(d)try{e=escape(g.Ug(b))}catch(f){return}else e=escape(b);g.wj(a,e,c,this.l)};
g.zj.prototype.get=function(a,b){var c=void 0,d=!this.i;if(!d)try{c=this.i.get(a)}catch(e){d=!0}if(d&&(c=g.xj(a))&&(c=unescape(c),b))try{c=JSON.parse(c)}catch(e){this.remove(a),c=void 0}return c};
g.zj.prototype.remove=function(a){this.i&&this.i.remove(a);g.yj(a,"/",this.l)};var Aj;g.Gj.prototype.toString=function(){return this.topic};var Xq=g.u("ytPubsub2Pubsub2Instance")||new g.Yg;g.Yg.prototype.subscribe=g.Yg.prototype.subscribe;g.Yg.prototype.unsubscribeByKey=g.Yg.prototype.nc;g.Yg.prototype.publish=g.Yg.prototype.ea;g.Yg.prototype.clear=g.Yg.prototype.clear;g.r("ytPubsub2Pubsub2Instance",Xq,void 0);var Jj=g.u("ytPubsub2Pubsub2SubscribedKeys")||{};g.r("ytPubsub2Pubsub2SubscribedKeys",Jj,void 0);var Lj=g.u("ytPubsub2Pubsub2TopicToKeys")||{};g.r("ytPubsub2Pubsub2TopicToKeys",Lj,void 0);
var Kj=g.u("ytPubsub2Pubsub2IsAsync")||{};g.r("ytPubsub2Pubsub2IsAsync",Kj,void 0);g.r("ytPubsub2Pubsub2SkipSubKey",null,void 0);var Tj=[],Sj,Rj=!1;Yj.all=function(a){return new Yj(function(b,c){var d=[],e=a.length;0===e&&b(d);for(var f={me:0};f.me<a.length;f={me:f.me},++f.me)Yj.resolve(a[f.me]).then(function(k){return function(l){d[k.me]=l;e--;0===e&&b(d)}}(f))["catch"](function(k){c(k)})})};
Yj.resolve=function(a){return new Yj(function(b,c){a instanceof Yj?a.then(b,c):b(a)})};
Yj.reject=function(a){return new Yj(function(b,c){c(a)})};
Yj.prototype.then=function(a,b){var c=this,d=null!==a&&void 0!==a?a:Xj,e=null!==b&&void 0!==b?b:Wj;return new Yj(function(f,k){"PENDING"===c.state.status?(c.i.push(function(){ak(c,c,d,f,k)}),c.onRejected.push(function(){bk(c,c,e,f,k)})):"FULFILLED"===c.state.status?ak(c,c,d,f,k):"REJECTED"===c.state.status&&bk(c,c,e,f,k)})};
Yj.prototype["catch"]=function(a){return this.then(void 0,a)};g.p(g.gk,Error);var Yq={},hk=(Yq.AUTH_INVALID="No user identifier specified.",Yq.EXPLICIT_ABORT="Transaction was explicitly aborted.",Yq.IDB_NOT_SUPPORTED="IndexedDB is not supported.",Yq.MISSING_OBJECT_STORE="Object store not created.",Yq.UNKNOWN_ABORT="Transaction was aborted for unknown reasons.",Yq.QUOTA_EXCEEDED="The current transaction exceeded its quota limitations.",Yq.QUOTA_MAYBE_EXCEEDED="The current transaction may have failed because of exceeding quota limitations.",Yq);g.p(ik,g.gk);g.p(jk,ik);
g.p(kk,ik);g.h=lk.prototype;g.h.add=function(a,b,c){return qk(this,[a],"readwrite",function(d){return wk(d,a).add(b,c)})};
g.h.clear=function(a){return qk(this,[a],"readwrite",function(b){return wk(b,a).clear()})};
g.h.close=function(){var a;this.i.close();(null===(a=this.options)||void 0===a?0:a.closed)&&this.options.closed()};
g.h.count=function(a,b){return qk(this,[a],"readonly",function(c){return wk(c,a).count(b)})};
g.h["delete"]=function(a,b){return qk(this,[a],"readwrite",function(c){return wk(c,a)["delete"](b)})};
g.h.get=function(a,b){return qk(this,[a],"readwrite",function(c){return wk(c,a).get(b)})};
g.h.getVersion=function(){return this.i.version};
g.h=sk.prototype;g.h.add=function(a,b){return ek(this.i.add(a,b))};
g.h.clear=function(){return ek(this.i.clear()).then(function(){})};
g.h.count=function(a){return ek(this.i.count(a))};
g.h["delete"]=function(a){return a instanceof IDBKeyRange?uk(this,a):ek(this.i["delete"](a))};
g.h.get=function(a){return ek(this.i.get(a))};
g.h.index=function(a){return new xk(this.i.index(a))};
g.h.getName=function(){return this.i.name};
mk.prototype.abort=function(){this.i.abort();this.aborted=!0;throw new ik("EXPLICIT_ABORT");};
mk.prototype.commit=function(){var a=this.i;a.commit&&!this.aborted&&a.commit()};
xk.prototype.count=function(a){return ek(this.i.count(a))};
xk.prototype["delete"]=function(a){return yk(this,{query:a},function(b){return b["delete"]().then(function(){return b["continue"]()})})};
xk.prototype.get=function(a){return ek(this.i.get(a))};
xk.prototype.getKey=function(a){return ek(this.i.getKey(a))};
g.h=zk.prototype;g.h.advance=function(a){this.cursor.advance(a);return vk(this.request)};
g.h["continue"]=function(a){this.cursor["continue"](a);return vk(this.request)};
g.h["delete"]=function(){return ek(this.cursor["delete"]()).then(function(){})};
g.h.getKey=function(){return this.cursor.key};
g.h.getValue=function(){return this.cursor.value};
g.h.update=function(a){return ek(this.cursor.update(a))};var Rk=g.Aq||g.Bq;g.h=Ek.prototype;g.h.oh=function(a,b,c){c=void 0===c?{}:c;return Bk(a,b,c)};
g.h["delete"]=function(a){a=void 0===a?{}:a;return Ck(this.name,a)};
g.h.Lk=function(){};
g.h.Mk=function(){};
g.h.open=function(){var a=this;if(!this.i){var b=function(){a.i===e&&(a.i=void 0,a.Mk(b))},c={blocking:function(f){f.close()},
closed:b,VA:b,upgrade:this.options.upgrade},d=function(){return g.Lc(a,function k(){var l=this,m,n,t;return g.Ba(k,function(w){switch(w.i){case 1:return g.ta(w,2),g.sa(w,l.oh(l.name,l.options.version,c),4);case 4:m=w.l;if(!g.zq){w.mb(5);break}a:{var x=g.ja(Object.keys(l.options.Wu));for(var D=x.next();!D.done;D=x.next())if(D=D.value,!m.i.objectStoreNames.contains(D)){x=D;break a}x=void 0}n=x;if(void 0===n){w.mb(5);break}if(!g.zq||l.l){w.mb(7);break}l.l=!0;return g.sa(w,l["delete"](),8);case 8:return w["return"](d());
case 7:throw new kk(n);case 5:return w["return"](m);case 2:t=g.ua(w);if(t instanceof DOMException?"VersionError"===t.name:"DOMError"in self&&t instanceof DOMError?"VersionError"===t.name:t instanceof Object&&"message"in t&&"An attempt was made to open a database using a lower version than the existing version."===t.message)return w["return"](l.oh(l.name,void 0,Object.assign(Object.assign({},c),{upgrade:void 0})));b();throw t;}})})};
var e=d();this.Lk(b);this.i=e}return this.i};var Fk=new Ek("YtIdbMeta",{Wu:{databases:!0},upgrade:function(a,b){1>b&&a.i.createObjectStore("databases",{keyPath:"actualName"})}});var Mk=!1,Ok,Zq=new g.Jf,Nk=0;var Uk,$q,ar=["getAll","getAllKeys","getKey","openKeyCursor"],br=["getAll","getAllKeys","getKey","openKeyCursor"],Qk=!1;g.p(fl,Ek);fl.prototype.oh=function(a,b,c){c=void 0===c?{}:c;return(this.options.CA?bl:al)(a,b,Object.assign(Object.assign({},c),{clearDataOnAuthChange:this.options.clearDataOnAuthChange}))};
fl.prototype.Lk=function(a){Zq.Yd.call(Zq,"authchanged",a)};
fl.prototype.Mk=function(a){Zq.ua("authchanged",a)};
fl.prototype["delete"]=function(a){a=void 0===a?{}:a;return(this.options.CA?el:cl)(this.name,a)};g.p(gl,Yj);gl.reject=Yj.reject;gl.resolve=Yj.resolve;gl.all=Yj.all;g.ql={};g.ql.Sg=Vk;g.ql.Qt=function(){return void 0!==$q?$q:$q=Vk().then(function(a){Rj=!0;if(!a)return!1;var b=g.ja(ar);for(a=b.next();!a.done;a=b.next())if(!IDBObjectStore.prototype[a.value])return!1;b=g.ja(br);for(a=b.next();!a.done;a=b.next())if(!IDBIndex.prototype[a.value])return!1;return IDBObjectStore.prototype.getKey?!0:!1}).then(function(a){Rj=!1;
return a})};
g.ql.hH="yt-idb-test-do-not-use";g.ql.TC=!1;g.ql.iH={NJ:function(a){Qk=a},
LJ:function(){$q=Uk=void 0}};
g.ql.rD=lk;g.ql.kE=function(){};
g.ql.cG=sk;g.ql.qH=mk;g.ql.jE=xk;g.ql.sD=zk;g.ql.WF={rJ:function(){return!1}};
g.ql.oh=al;g.ql.GJ=bl;g.ql.oJ=cl;g.ql.mJ=function(a,b){b=void 0===b?{}:b;return g.Lc(this,function d(){var e;return g.Ba(d,function(f){if(1==f.i)return Wk(a),g.sa(f,Zk(),2);if(3!=f.i)return g.sa(f,Jk(a),3);e=f.l;return g.sa(f,dl(e,b),0)})})};
g.ql.kJ=function(a,b){b=void 0===b?{}:b;return g.Lc(this,function d(){var e;return g.Ba(d,function(f){if(1==f.i)return g.sa(f,Zk(),2);if(3!=f.i)return g.sa(f,Kk(a),3);e=f.l;return g.sa(f,dl(e,b),0)})})};
g.ql.nJ=el;g.ql.lJ=cl;g.ql.pH=gl;g.ql.OJ=function(a,b){Mk=!0;Ok={userIdentifier:a,signedIn:b};Zq.dispatchEvent("authchanged");Pk()};
g.ql.iJ=function(){Mk=!0;Ok=void 0;Zq.dispatchEvent("authchanged");Pk()};
g.ql.Database=fl;g.ql.tJ=function(a,b){var c;return function(){c||(c=new fl(a,b));return c}};
g.ql.cJ=ik;g.ql.ErrorType={UC:"AUTH_INVALID",yD:"EXPLICIT_ABORT",WD:"IDB_NOT_SUPPORTED",MF:"MISSING_OBJECT_STORE",yI:"UNKNOWN_ABORT",qp:"QUOTA_EXCEEDED",IG:"QUOTA_MAYBE_EXCEEDED"};g.ql.EventType={VD:"IDB_DATA_CORRUPTED",XD:"IDB_UNEXPECTEDLY_CLOSED",ZD:"IS_SUPPORTED_COMPLETED",qp:"QUOTA_EXCEEDED",jH:"TRANSACTION_ENDED",kH:"TRANSACTION_UNEXPECTEDLY_ABORTED"};
g.ql.JJ=function(a){for(Sj=a;0<Tj.length;)switch(a=Tj.shift(),a.type){case "ERROR":Sj.i(a.payload);break;case "EVENT":Sj.logEvent(a.eventType,a.payload)}};var hl;g.p(ml,g.Jf);g.p(pl,g.Jf);var rl;g.ul.prototype.isReady=function(){!this.i&&sj()&&(this.i=g.Ei());return!!this.i};var Cl=[{vm:function(a){return"Cannot read property '"+a.key+"'"},
qj:{TypeError:[{regexp:/Cannot read property '([^']+)' of (null|undefined)/,groups:["key","value"]},{regexp:/\u65e0\u6cd5\u83b7\u53d6\u672a\u5b9a\u4e49\u6216 (null|undefined) \u5f15\u7528\u7684\u5c5e\u6027\u201c([^\u201d]+)\u201d/,groups:["value","key"]},{regexp:/\uc815\uc758\ub418\uc9c0 \uc54a\uc74c \ub610\ub294 (null|undefined) \ucc38\uc870\uc778 '([^']+)' \uc18d\uc131\uc744 \uac00\uc838\uc62c \uc218 \uc5c6\uc2b5\ub2c8\ub2e4./,groups:["value","key"]},{regexp:/No se puede obtener la propiedad '([^']+)' de referencia nula o sin definir/,
groups:["key"]},{regexp:/Unable to get property '([^']+)' of (undefined or null) reference/,groups:["key","value"]}],Error:[{regexp:/(Permission denied) to access property "([^']+)"/,groups:["reason","key"]}]}},{vm:function(a){return"Cannot call '"+a.key+"'"},
qj:{TypeError:[{regexp:/(?:([^ ]+)?\.)?([^ ]+) is not a function/,groups:["base","key"]},{regexp:/([^ ]+) called on (null or undefined)/,groups:["key","value"]},{regexp:/Object (.*) has no method '([^ ]+)'/,groups:["base","key"]},{regexp:/Object doesn't support property or method '([^ ]+)'/,groups:["key"]},{regexp:/\u30aa\u30d6\u30b8\u30a7\u30af\u30c8\u306f '([^']+)' \u30d7\u30ed\u30d1\u30c6\u30a3\u307e\u305f\u306f\u30e1\u30bd\u30c3\u30c9\u3092\u30b5\u30dd\u30fc\u30c8\u3057\u3066\u3044\u307e\u305b\u3093/,
groups:["key"]},{regexp:/\uac1c\uccb4\uac00 '([^']+)' \uc18d\uc131\uc774\ub098 \uba54\uc11c\ub4dc\ub97c \uc9c0\uc6d0\ud558\uc9c0 \uc54a\uc2b5\ub2c8\ub2e4./,groups:["key"]}]}}];var Gl;var El=new g.Yg;var Dl=new Set,Bl=0,Fl=["PhantomJS","Googlebot","TO STOP THIS SECURITY SCAN go/scan"];wg.prototype.flush=function(a){a=void 0===a?[]:a;if(g.xh("enable_client_streamz_web")){a=g.ja(a);for(var b=a.next();!b.done;b=a.next()){b=b.value;var c=new Pe;c=g.Bd(c,1,b.D);for(var d=b,e=[],f=0;f<d.i.length;f++)e.push(d.i[f].ha);c=g.Bd(c,3,e||[]);d=[];e=[];f=g.ja(b.hf.keys());for(var k=f.next();!k.done;k=f.next())e.push(k.value.split(","));for(f=0;f<e.length;f++){k=e[f];var l=b.l;for(var m=b.j(k)||[],n=[],t=0;t<m.length;t++){var w=m[t];w=w&&w.sJ();var x=new Ze;switch(l){case 3:g.Cd(x,1,Ye[0],Number(w));
break;case 2:g.Cd(x,2,Ye[0],Number(w))}n.push(x)}l=n;for(m=0;m<l.length;m++){n=l[m];t=new Ve;n=g.Fd(t,2,n);t=k;w=[];x=b;for(var D=[],O=0;O<x.i.length;O++)D.push(x.i[O].ia);x=D;for(D=0;D<x.length;D++){O=x[D];var M=t[D],la=new Xe;switch(O){case 3:g.Cd(la,1,We[0],String(M));break;case 2:g.Cd(la,2,We[0],Number(M));break;case 1:g.Cd(la,3,We[0],"true"==M)}w.push(la)}g.Gd(n,1,w);d.push(n)}}g.Gd(c,4,d);d=b=new jd;e=g.Ad(c,1);null!=e&&pd(d,1,e);e=g.Ad(c,5);null!=e&&nd(d,5,e);e=g.Dd(c,Re,2);null!=e&&qd(d,2,
e,bf);e=g.Ad(c,3);if(0<e.length&&null!=e)for(f=0;f<e.length;f++)pd(d,3,e[f]);e=g.Ad(c,6);if(0<e.length&&null!=e)for(f=0;f<e.length;f++)nd(d,6,e[f]);e=g.Ed(c,Ve,4);0<e.length&&rd(d,4,e,ef);c=new Uint8Array(b.l+b.i.length());e=b.j;f=e.length;for(k=d=0;k<f;k++)l=e[k],c.set(l,d),d+=l.length;e=b.i.end();c.set(e,d);b.j=[c];b={serializedIncrementBatch:g.hd(c)};g.vl("streamzIncremented",b)}}};var Jl;new function(){new Il};
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/app_boots",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/app_boots_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/component_registration",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/component_registration_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/network_request",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"path"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/network_request_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"path"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/warm_page_navigation",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"page_type"},{ia:3,ha:"request_type"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/warm_page_navigation_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"page_type"},{ia:3,ha:"request_type"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/service_worker_registration",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/service_worker_registration_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/yt_initial_data_present",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/yt_initial_data_present_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/yt_guide_data_present",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/yt_guide_data_present_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/stfe_greater_than_one_minute",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/stfe_greater_than_one_minute_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/stfe_greater_than_ten_minutes",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/stfe_greater_than_ten_minutes_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/one_minute_success",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/one_minute_success_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/ten_minute_success",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"},{ia:3,ha:"status"})}(Hl());
new function(a){this.i=a;this.i.i("/client_streamz/youtube/web_client_sli/youtube_web/ten_minute_success_start",{ia:3,ha:"browser"},{ia:1,ha:"is_shell_load"},{ia:3,ha:"canary_state"})}(Hl());var Ql={};var Tl=/\.vflset|-vfl[a-zA-Z0-9_+=-]+/,Ul=/-[a-zA-Z]{2,3}_[a-zA-Z]{2,3}(?=(\/|$))/;Vl.prototype.initialize=function(a,b,c,d,e,f){var k=this;f=void 0===f?!1:f;b?(this.l=!0,g.Sl(b,function(){k.l=!1;var l=0<=b.indexOf("/th/");(l?window.trayride:window.botguard)?Wl(k,c,d,f,l):(l=g.Rl(b),spf.script.unload(l),g.Al(new g.gk("Unable to load Botguard","from "+b)))})):a&&(e=g.fe("SCRIPT"),e.textContent=a,e.nonce=g.Ha(),document.head.appendChild(e),document.head.removeChild(e),((a=a.includes("trayride"))?window.trayride:window.botguard)?Wl(this,c,d,f,a):g.Al(Error("Unable to load Botguard from JS")))};
Vl.prototype.dispose=function(){this.i=null};var Yl=new Vl;am.prototype.then=function(a,b,c){return this.l?this.l.then(a,b,c):1===this.Y&&a?(a=a.call(c,this.i),g.$f(a)?a:g.cm(a)):2===this.Y&&b?(a=b.call(c,this.i),g.$f(a)?a:g.bm(a)):this};
am.prototype.getValue=function(){return this.i};
g.Zf(am);g.dm=g.u("ytglobal.prefsUserPrefsPrefs_")||{};g.r("ytglobal.prefsUserPrefsPrefs_",g.dm,void 0);g.h=g.em.prototype;g.h.get=function(a,b){gm(a);fm(a);var c=void 0!==g.dm[a]?g.dm[a].toString():null;return null!=c?c:b?b:""};
g.h.set=function(a,b){gm(a);fm(a);if(null==b)throw Error("ExpectedNotNull");g.dm[a]=b.toString()};
g.h.remove=function(a){gm(a);fm(a);delete g.dm[a]};
g.h.save=function(){g.wj(this.i,this.dump(),63072E3)};
g.h.clear=function(){g.mb(g.dm)};
g.h.dump=function(){var a=[],b;for(b in g.dm)a.push(b+"="+encodeURIComponent(String(g.dm[b])));return a.join("&")};
g.Ka(g.em);g.cr=new Map([["dark","USER_INTERFACE_THEME_DARK"],["light","USER_INTERFACE_THEME_LIGHT"]]);g.p(hm,g.B);hm.prototype.ua=function(a){for(var b=0;b<this.i.length;b++)if(this.i[b]==a){this.i.splice(b,1);a.target.removeEventListener(a.name,a.callback);break}};
hm.prototype.O=function(){for(;this.i.length;){var a=this.i.pop();a.target.removeEventListener(a.name,a.callback)}g.B.prototype.O.call(this)};im.prototype.clone=function(){var a=new im,b;for(b in this)if(this.hasOwnProperty(b)){var c=this[b];"object"==g.La(c)?a[b]=g.qb(c):a[b]=c}return a};var km=g.Va().toString();var mm=g.q.ytLoggingDocDocumentNonce_||lm();g.r("ytLoggingDocDocumentNonce_",mm,void 0);g.om.prototype.getAsJson=function(){var a={};void 0!==this.i.trackingParams?a.trackingParams=this.i.trackingParams:(a.veType=this.i.veType,void 0!==this.i.veCounter&&(a.veCounter=this.i.veCounter),void 0!==this.i.elementIndex&&(a.elementIndex=this.i.elementIndex));void 0!==this.i.dataElement&&(a.dataElement=this.i.dataElement.getAsJson());void 0!==this.i.youtubeData&&(a.youtubeData=this.i.youtubeData);return a};
g.om.prototype.toString=function(){return JSON.stringify(this.getAsJson())};
g.om.prototype.isClientVe=function(){return!this.i.trackingParams&&!!this.i.veType};g.r("yt_logging_screen.getRootVeType",rm,void 0);g.r("yt_logging_screen.getCurrentCsn",g.um,void 0);g.r("yt_logging_screen.getCttAuthInfo",g.wm,void 0);g.r("yt_logging_screen.setCurrentScreen",g.xm,void 0);g.p(ym,g.Fj);var Oj=new g.Gj("screen-created",ym),zm=[],Am=0;var Hm,Fm;Hm=0;g.Gm=null;Fm=null;g.h=g.Jm.prototype;g.h.mA=function(){2==this.Y||this.init()};
g.h.enable=function(){this.Y=1;g.A("string"==typeof this.page?[this.page]:this.page,function(a){a&&(this.subscribe("init-"+a,this.mA,this),this.subscribe("dispose-"+a,this.dispose,this),g.F("PAGE_NAME")==a&&g.Km(this))},this)};
g.h.init=function(){g.ci(this.i);this.Y=2;this.D&&this.D()};
g.h.dispose=function(){this.Y=3;g.ci(this.i);this.C&&this.C()};
g.h.disable=function(){this.Y=4;this.clear();try{this.dispose()}catch(a){g.Gh(a)}};
g.h.subscribe=function(a,b,c){a=g.pi(a,b,c);this.j.push(a);return a};
g.h.clear=function(){g.qi(this.j);this.j=[]};var Lm=g.u("yt.modules.registry_")||{};g.r("yt.modules.registry_",Lm,void 0);g.Om=window.yt&&window.yt.msgs_||window.ytcfg&&window.ytcfg.msgs||{};g.r("yt.msgs_",g.Om,void 0);var Wm={},Vm=0;var Xm=/cssbin\/(?:debug-)?([a-zA-Z0-9_-]+?)(?:-2x|-web|-rtl|-vfl|.css)/;g.p(fn,g.B);g.h=fn.prototype;g.h.getId=function(){return this.N};
g.h.loadNewVideoConfig=function(a){if(!this.Ha()){this.ra&&(g.Wh(this.ra),this.ra=0);this.pb=a=jm(a);this.i=a.clone();dn(this);this.Ka||(this.Ka=rn(this,this.i.args.jsapicallback||"onYouTubePlayerReady"));this.i.args.jsapicallback=null;(a=this.i.attrs.width)&&g.Ce(this.H,Number(a)||String(a));if(a=this.i.attrs.height)this.H.style.height=g.Be(Number(a)||String(a),!0);en(this);this.F&&gn(this)}};
g.h.xq=function(){return this.pb};
g.h.Ou=function(){return this.F};
g.h.Xf=function(a,b){var c=this,d=rn(this,b);if(d){if(!g.db(this.Qd,a)&&!this.C[a]){var e=sn(this,a);this.K&&this.K(a,e)}this.R.subscribe(a,d);"onReady"==a&&this.F&&g.Uh(function(){d(c.api)},0)}};
g.h.eA=function(a,b){if(!this.Ha()){var c=rn(this,b);c&&g.Zg(this.R,a,c)}};
g.h.vp=function(a){g.H("a11y-announce",a)};
g.h.qy=function(a){g.H("WATCH_LATER_VIDEO_ADDED",a)};
g.h.ry=function(a){g.H("WATCH_LATER_VIDEO_REMOVED",a)};
g.h.Fq=function(){return this.aa||(hn(this)?"html5":null)};
g.h.Aq=function(){return this.bb};
g.h.cancel=function(){if(this.M){var a=jn(this),b=this.M;a=g.Rl(a);spf.script.ignore(a,b)}g.Wh(this.xb);this.V=!1};
g.h.O=function(){mn(this);if(this.D&&this.i&&this.D.destroy)try{this.D.destroy()}catch(b){g.Gh(b)}this.ub=null;for(var a in this.C)g.q[this.C[a]]=null;this.pb=this.i=this.api=null;delete this.Pa;delete this.H;g.B.prototype.O.call(this)};g.un={};g.tn="player_uid_"+(1E9*Math.random()>>>0);g.dr=window.performance&&window.performance.memory;g.zp={};g.p(zn,g.Fj);g.p(An,g.Fj);var Qn=new g.Gj("aft-recorded",zn),Ao=new g.Gj("timing-sent",An);var er=window,En=er.performance||er.mozPerformance||er.msPerformance||er.webkitPerformance||new Bn;var Pn=!1,ro={'script[name="scheduler/scheduler"]':"sj",'script[name="player/base"]':"pj",'link[rel="stylesheet"][name="www-player"]':"pc",'link[rel="stylesheet"][name="player/www-player"]':"pc",'script[name="desktop_polymer/desktop_polymer"]':"dpj",'link[rel="import"][name="desktop_polymer"]':"dph",'script[name="mobile-c3/mobile-c3"]':"mcj",'link[rel="stylesheet"][name="mobile-c3"]':"mcc",'script[name="player-plasma-ias-phone/base"]':"mcppj",'script[name="player-plasma-ias-tablet/base"]':"mcptj",
'link[rel="stylesheet"][name="mobile-polymer-player-ias"]':"mcpc",'link[rel="stylesheet"][name="mobile-polymer-player-svg-ias"]':"mcpsc",'script[name="mobile_blazer_core_mod"]':"mbcj",'link[rel="stylesheet"][name="mobile_blazer_css"]':"mbc",'script[name="mobile_blazer_logged_in_users_mod"]':"mbliuj",'script[name="mobile_blazer_logged_out_users_mod"]':"mblouj",'script[name="mobile_blazer_noncore_mod"]':"mbnj","#player_css":"mbpc",'script[name="mobile_blazer_desktopplayer_mod"]':"mbpj",'link[rel="stylesheet"][name="mobile_blazer_tablet_css"]':"mbtc",
'script[name="mobile_blazer_watch_mod"]':"mbwj"},jo=(0,g.v)(En.clearResourceTimings||En.webkitClearResourceTimings||En.mozClearResourceTimings||En.msClearResourceTimings||En.oClearResourceTimings||g.Ja,En);var Yn=g.q.ytLoggingLatencyUsageStats_||{};g.r("ytLoggingLatencyUsageStats_",Yn,void 0);Wn.prototype.tick=function(a,b,c){Zn(this,"tick_"+a+"_"+b)||g.vl("latencyActionTicked",{tickName:a,clientActionNonce:b},{timestamp:c})};
Wn.prototype.info=function(a,b){var c=Object.keys(a).join("");Zn(this,"info_"+c+"_"+b)||(c=Object.assign({},a),c.clientActionNonce=b,g.vl("latencyActionInfo",c))};
Wn.prototype.span=function(a,b){var c=Object.keys(a).join("");Zn(this,"span_"+c+"_"+b)||(a.clientActionNonce=b,g.vl("latencyActionSpan",a))};var fr={},to=(fr.ad_to_ad="LATENCY_ACTION_AD_TO_AD",fr.ad_to_video="LATENCY_ACTION_AD_TO_VIDEO",fr.app_startup="LATENCY_ACTION_APP_STARTUP",fr["artist.analytics"]="LATENCY_ACTION_CREATOR_ARTIST_ANALYTICS",fr["artist.events"]="LATENCY_ACTION_CREATOR_ARTIST_CONCERTS",fr["artist.presskit"]="LATENCY_ACTION_CREATOR_ARTIST_PROFILE",fr.browse="LATENCY_ACTION_BROWSE",fr.channels="LATENCY_ACTION_CHANNELS",fr.creator_channel_dashboard="LATENCY_ACTION_CREATOR_CHANNEL_DASHBOARD",fr["channel.analytics"]="LATENCY_ACTION_CREATOR_CHANNEL_ANALYTICS",
fr["channel.comments"]="LATENCY_ACTION_CREATOR_CHANNEL_COMMENTS",fr["channel.content"]="LATENCY_ACTION_CREATOR_POST_LIST",fr["channel.copyright"]="LATENCY_ACTION_CREATOR_CHANNEL_COPYRIGHT",fr["channel.editing"]="LATENCY_ACTION_CREATOR_CHANNEL_EDITING",fr["channel.monetization"]="LATENCY_ACTION_CREATOR_CHANNEL_MONETIZATION",fr["channel.music"]="LATENCY_ACTION_CREATOR_CHANNEL_MUSIC",fr["channel.translations"]="LATENCY_ACTION_CREATOR_CHANNEL_TRANSLATIONS",fr["channel.videos"]="LATENCY_ACTION_CREATOR_CHANNEL_VIDEOS",
fr["channel.live_streaming"]="LATENCY_ACTION_CREATOR_LIVE_STREAMING",fr.chips="LATENCY_ACTION_CHIPS",fr["dialog.copyright_strikes"]="LATENCY_ACTION_CREATOR_DIALOG_COPYRIGHT_STRIKES",fr["dialog.uploads"]="LATENCY_ACTION_CREATOR_DIALOG_UPLOADS",fr.embed="LATENCY_ACTION_EMBED",fr.home="LATENCY_ACTION_HOME",fr.library="LATENCY_ACTION_LIBRARY",fr.live="LATENCY_ACTION_LIVE",fr.live_pagination="LATENCY_ACTION_LIVE_PAGINATION",fr.onboarding="LATENCY_ACTION_KIDS_ONBOARDING",fr.parent_profile_settings="LATENCY_ACTION_KIDS_PARENT_PROFILE_SETTINGS",
fr.parent_tools_collection="LATENCY_ACTION_PARENT_TOOLS_COLLECTION",fr.parent_tools_dashboard="LATENCY_ACTION_PARENT_TOOLS_DASHBOARD",fr.player_att="LATENCY_ACTION_PLAYER_ATTESTATION",fr["post.comments"]="LATENCY_ACTION_CREATOR_POST_COMMENTS",fr["post.edit"]="LATENCY_ACTION_CREATOR_POST_EDIT",fr.prebuffer="LATENCY_ACTION_PREBUFFER",fr.prefetch="LATENCY_ACTION_PREFETCH",fr.profile_settings="LATENCY_ACTION_KIDS_PROFILE_SETTINGS",fr.profile_switcher="LATENCY_ACTION_KIDS_PROFILE_SWITCHER",fr.results=
"LATENCY_ACTION_RESULTS",fr.search_ui="LATENCY_ACTION_SEARCH_UI",fr.search_zero_state="LATENCY_ACTION_SEARCH_ZERO_STATE",fr.secret_code="LATENCY_ACTION_KIDS_SECRET_CODE",fr.settings="LATENCY_ACTION_SETTINGS",fr.tenx="LATENCY_ACTION_TENX",fr.video_to_ad="LATENCY_ACTION_VIDEO_TO_AD",fr.watch="LATENCY_ACTION_WATCH",fr.watch_it_again="LATENCY_ACTION_KIDS_WATCH_IT_AGAIN",fr["watch,watch7"]="LATENCY_ACTION_WATCH",fr["watch,watch7_html5"]="LATENCY_ACTION_WATCH",fr["watch,watch7ad"]="LATENCY_ACTION_WATCH",
fr["watch,watch7ad_html5"]="LATENCY_ACTION_WATCH",fr.wn_comments="LATENCY_ACTION_LOAD_COMMENTS",fr.ww_rqs="LATENCY_ACTION_WHO_IS_WATCHING",fr["video.analytics"]="LATENCY_ACTION_CREATOR_VIDEO_ANALYTICS",fr["video.comments"]="LATENCY_ACTION_CREATOR_VIDEO_COMMENTS",fr["video.edit"]="LATENCY_ACTION_CREATOR_VIDEO_EDIT",fr["video.translations"]="LATENCY_ACTION_CREATOR_VIDEO_TRANSLATIONS",fr["video.video_editor"]="LATENCY_ACTION_CREATOR_VIDEO_VIDEO_EDITOR",fr["video.video_editor_async"]="LATENCY_ACTION_CREATOR_VIDEO_VIDEO_EDITOR_ASYNC",
fr["video.monetization"]="LATENCY_ACTION_CREATOR_VIDEO_MONETIZATION",fr.voice_assistant="LATENCY_ACTION_VOICE_ASSISTANT",fr.cast_load_by_entity_to_watch="LATENCY_ACTION_CAST_LOAD_BY_ENTITY_TO_WATCH",fr.networkless_performance="LATENCY_ACTION_NETWORKLESS_PERFORMANCE",fr),gr={},co=(gr.ad_allowed="adTypesAllowed",gr.yt_abt="adBreakType",gr.ad_cpn="adClientPlaybackNonce",gr.ad_docid="adVideoId",gr.yt_ad_an="adNetworks",gr.ad_at="adType",gr.aida="appInstallDataAgeMs",gr.browse_id="browseId",gr.p="httpProtocol",
gr.t="transportProtocol",gr.cpn="clientPlaybackNonce",gr.ccs="creatorInfo.creatorCanaryState",gr.cseg="creatorInfo.creatorSegment",gr.csn="clientScreenNonce",gr.docid="videoId",gr.GetHome_rid="requestIds",gr.GetSearch_rid="requestIds",gr.GetPlayer_rid="requestIds",gr.GetWatchNext_rid="requestIds",gr.GetBrowse_rid="requestIds",gr.GetLibrary_rid="requestIds",gr.is_continuation="isContinuation",gr.is_nav="isNavigation",gr.b_p="kabukiInfo.browseParams",gr.is_prefetch="kabukiInfo.isPrefetch",gr.is_secondary_nav=
"kabukiInfo.isSecondaryNav",gr.prev_browse_id="kabukiInfo.prevBrowseId",gr.query_source="kabukiInfo.querySource",gr.voz_type="kabukiInfo.vozType",gr.yt_lt="loadType",gr.mver="creatorInfo.measurementVersion",gr.yt_ad="isMonetized",gr.nr="webInfo.navigationReason",gr.nrsu="navigationRequestedSameUrl",gr.ncnp="webInfo.nonPreloadedNodeCount",gr.pnt="performanceNavigationTiming",gr.prt="playbackRequiresTap",gr.plt="playerInfo.playbackType",gr.pis="playerInfo.playerInitializedState",gr.paused="playerInfo.isPausedOnLoad",
gr.yt_pt="playerType",gr.fmt="playerInfo.itag",gr.yt_pl="watchInfo.isPlaylist",gr.yt_pre="playerInfo.preloadType",gr.yt_ad_pr="prerollAllowed",gr.pa="previousAction",gr.yt_red="isRedSubscriber",gr.rce="mwebInfo.responseContentEncoding",gr.scrh="screenHeight",gr.scrw="screenWidth",gr.st="serverTimeMs",gr.ssdm="shellStartupDurationMs",gr.br_trs="tvInfo.bedrockTriggerState",gr.kebqat="kabukiInfo.earlyBrowseRequestInfo.abandonmentType",gr.kebqa="kabukiInfo.earlyBrowseRequestInfo.adopted",gr.label="tvInfo.label",
gr.is_mdx="tvInfo.isMdx",gr.preloaded="tvInfo.isPreloaded",gr.upg_player_vis="playerInfo.visibilityState",gr.query="unpluggedInfo.query",gr.upg_chip_ids_string="unpluggedInfo.upgChipIdsString",gr.yt_vst="videoStreamType",gr.vph="viewportHeight",gr.vpw="viewportWidth",gr.yt_vis="isVisible",gr.rcl="mwebInfo.responseContentLength",gr.GetSettings_rid="requestIds",gr.GetTrending_rid="requestIds",gr.GetMusicSearchSuggestions_rid="requestIds",gr.REQUEST_ID="requestIds",gr),eo="isContinuation isNavigation kabukiInfo.earlyBrowseRequestInfo.adopted kabukiInfo.isPrefetch kabukiInfo.isSecondaryNav isMonetized navigationRequestedSameUrl performanceNavigationTiming playerInfo.isPausedOnLoad prerollAllowed isRedSubscriber tvInfo.isMdx tvInfo.isPreloaded isVisible watchInfo.isPlaylist playbackRequiresTap".split(" "),
hr={},fo=(hr.ccs="CANARY_STATE_",hr.mver="MEASUREMENT_VERSION_",hr.pis="PLAYER_INITIALIZED_STATE_",hr.yt_pt="LATENCY_PLAYER_",hr.pa="LATENCY_ACTION_",hr.yt_vst="VIDEO_STREAM_TYPE_",hr),go="all_vc ap aq c cver cbrand cmodel cplatform ctheme ei l_an l_mm plid srt yt_fss yt_li vpst vpni2 vpil2 icrc icrt pa GetAccountOverview_rid GetHistory_rid cmt d_vpct d_vpnfi d_vpni nsru pc pfa pfeh pftr pnc prerender psc rc start tcrt tcrc ssr vpr vps yt_abt yt_fn yt_fs yt_pft yt_pre yt_pt yt_pvis ytu_pvis yt_ref yt_sts tds".split(" ");var ir=window;ir.ytcsi&&(ir.ytcsi.info=lo,ir.ytcsi.tick=g.no);var Qo=0,Io=[],Po=[],Do=0,No={},Oo={},Eo=new g.Cg(Ro,1E3);var To=["server_prefetched_vast","vmap"];var Wo;var bp=null,ep=[];var kp,Hp,Gp,mp,lp,op,wp,qp,sp,Ap;mp=0;lp=0;op=[900,60,"waiting"];wp=[500,99,"waiting"];qp=[300,60,"waiting"];sp=[400,99,"waiting"];Ap=[300,101,"done"];window.yt=window.yt||{};g.r("yt.setConfig",g.uh,void 0);g.r("yt.pushConfigArray",vh,void 0);g.r("yt.getConfig",g.F,void 0);g.r("yt.config.set",g.uh,void 0);g.r("yt.config.pushArray",vh,void 0);g.r("yt.config.get",g.F,void 0);g.r("yt.hasMsg",g.Qm,void 0);g.r("yt.setMsg",Pm,void 0);g.r("yt.setGoogMsg",Rm,void 0);g.r("yt.msgs.has",g.Qm,void 0);g.r("yt.msgs.set",Pm,void 0);g.r("yt.msgs.setGoog",Rm,void 0);g.r("yt.pubsub.publish",g.H,void 0);g.r("yt.pubsub.subscribe",g.pi,void 0);
g.r("ytcsi.tick",g.no,void 0);g.p(Jp,g.Jm);
Jp.prototype.enable=function(){window.onload=gp;window.onunload=hp;window.onerror=Ip;var a=window.ytspf||{};a.enabled?(window.addEventListener&&(window.addEventListener("spfclick",ip),window.addEventListener("spfhistory",jp),window.addEventListener("spfrequest",pp),window.addEventListener("spfpartprocess",up),window.addEventListener("spfpartdone",vp),window.addEventListener("spfprocess",xp),window.addEventListener("spfdone",Bp),window.addEventListener("spferror",Cp),window.addEventListener("spfjsbeforeunload",
Kp)),a.config=a.config||{},window.ytdepmap?spf.script.ready("spf",function(){a.enabled=spf.init(a.config)}):a.enabled=spf.init(a.config),this.subscribe("init",Ep),this.subscribe("dispose",Fp)):spf.dispose();
this.subscribe("init",this.init,this);this.subscribe("dispose",this.dispose,this)};
Jp.prototype.init=function(){g.Jm.prototype.init.call(this);(window.ytspf||{}).enabled||spf.dispose();var a=window.ytPageFrameLoaded||!1;if(!a&&g.F("PAGEFRAME_JS")){var b=g.F("PAGEFRAME_JS",void 0);var c=function(){var e=g.u("ytbin.www.pageframe.setup");e&&(window.ytPageFrameLoaded=!0,e())}}else a&&(a=g.u("yt.www.masthead.loadSearchbox"))&&a();
a=g.F("JS_COMMON_MODULE");var d=g.F("JS_PAGE_MODULES");d||(d=[a]);a=g.F("JS_DELAY_LOAD",0);0<a?(g.Wh(this.l),this.l=g.Uh(function(){b&&g.Sl(b,c);spf.script.require(d)},a)):(b&&g.Sl(b,c),spf.script.require(d));
g.r("yt.abuse.player.botguardInitialized",Zl,void 0);g.r("yt.abuse.player.invokeBotguard",$l,void 0);g.r("yt.abuse.dclkstatus.checkDclkStatus",Xl,void 0);g.r("yt.player.exports.navigate",g.bn,void 0);g.r("yt.util.activity.init",g.ii,void 0);g.r("yt.util.activity.getTimeSinceActive",g.ki,void 0);g.r("yt.util.activity.setTimestamp",g.gi,void 0);$o(g.u("ytplayer.config"));g.u("ytspf.enabled")&&Zo();vo()};
Jp.prototype.dispose=function(){g.Wh(this.l);var a=g.u("ytbin.www.pageframe.cancelSetup");a&&a();g.Xh(Wo);if(a=g.Yo())a.removeEventListener("onReady",Xo),a.removeEventListener("onStateChange",Xo);In(!1);(a=(a=(a=document.getElementById("ticker"))&&"TRIGGER_CONDITION_ENABLE_NOTIFICATIONS_PROMPT"==g.J(a,"trigger-condition")?a:null)?a.querySelector(".yt-uix-button-alert-info"):null)&&g.Th(a);g.Jm.prototype.dispose.call(this)};
Jp.prototype.disable=function(){g.Jm.prototype.disable.call(this);window.removeEventListener&&(window.removeEventListener("spfclick",ip),window.removeEventListener("spfhistory",jp),window.removeEventListener("spfrequest",pp),window.removeEventListener("spfpartprocess",up),window.removeEventListener("spfpartdone",vp),window.removeEventListener("spfprocess",xp),window.removeEventListener("spfdone",Bp),window.removeEventListener("spferror",Cp),window.removeEventListener("spfjsbeforeunload",Kp));window.onload=
null;window.onunload=null;window.onerror=null};
g.Nm(new Jp);})(_yt_www);
