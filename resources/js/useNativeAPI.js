"use strict";
Object.defineProperty(exports, "__esModule", {
    value: !0
}),
exports.chooseMedia = chooseMedia,
exports.uploadFile = uploadFile,
exports.getSystemInfoSync = getSystemInfoSync,
exports.vibrateShort = vibrateShort,
exports.getMenuButtonBoundingClientRectSync = getMenuButtonBoundingClientRectSync,
exports.nextTick = nextTick;
var _global = require("../internals/global");
function ownKeys(t, e) {
    var o = Object.keys(t);
    if (Object.getOwnPropertySymbols) {
        var i = Object.getOwnPropertySymbols(t);
        e && (i = i.filter(function(e) {
            return Object.getOwnPropertyDescriptor(t, e).enumerable
        })),
        o.push.apply(o, i)
    }
    return o
}
function _objectSpread(t) {
    for (var e = 1; e < arguments.length; e++) {
        var o = null != arguments[e] ? arguments[e] : {};
        e % 2 ? ownKeys(o, !0).forEach(function(e) {
            _defineProperty(t, e, o[e])
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(o)) : ownKeys(o).forEach(function(e) {
            Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(o, e))
        })
    }
    return t
}
function _defineProperty(e, t, o) {
    return t in e ? Object.defineProperty(e, t, {
        value: o,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = o,
    e
}
function _objectWithoutProperties(e, t) {
    if (null == e) return {};
    var o, i, r = _objectWithoutPropertiesLoose(e, t);
    if (Object.getOwnPropertySymbols) {
        var n = Object.getOwnPropertySymbols(e);
        for (i = 0; i < n.length; i++) o = n[i],
        0 <= t.indexOf(o) || Object.prototype.propertyIsEnumerable.call(e, o) && (r[o] = e[o])
    }
    return r
}
function _objectWithoutPropertiesLoose(e, t) {
    if (null == e) return {};
    var o, i, r = {},
    n = Object.keys(e);
    for (i = 0; i < n.length; i++) o = n[i],
    0 <= t.indexOf(o) || (r[o] = e[o]);
    return r
}
var fakeMediaResult = function(e, t) {
    if ("type" in t) return t;
    if (e.mediaType.includes("video")) return {
        tempFiles: [{
            tempFilePath: t.tempFilePath,
            size: t.size,
            duration: t.duration,
            height: t.height,
            width: t.width,
            thumbTempFilePath: t.tempFilePath,
            fileType: "video"
        }],
        type: "video"
    };
    var o = t.tempFilePaths,
    i = void 0 === o ? [] : o,
    r = t.tempFiles,
    n = void 0 === r ? [] : r;
    return {
        tempFiles: i.map(function(e, t) {
            return {
                tempFilePath: n[t].path || e,
                size: n[t].size,
                fileType: "image"
            }
        }),
        type: "image"
    }
};
function chooseMedia(t) {
    function e(e) {
        h.success && h.success(fakeMediaResult(t, e))
    }
    var o = t.count,
    i = void 0 === o ? 9 : o,
    r = t.mediaType,
    n = void 0 === r ? ["image", "video"] : r,
    a = t.sourceType,
    s = void 0 === a ? ["album", "camera"] : a,
    u = t.maxDuration,
    c = void 0 === u ? 10 : u,
    l = t.sizeType,
    p = void 0 === l ? ["original", "compressed"] : l,
    d = t.camera,
    m = void 0 === d ? "back": d,
    f = t.compressed,
    g = void 0 === f || f,
    h = _objectWithoutProperties(t, ["count", "mediaType", "sourceType", "maxDuration", "sizeType", "camera", "compressed"]);
    if ("function" == typeof _global.miniprogramThis.chooseMedia) return _global.miniprogramThis.chooseMedia(_objectSpread({},
    t, {
        success: e
    }));
    if (n.includes("video")) {
        var b = _objectSpread({
            sourceType: s,
            compressed: g,
            maxDuration: void 0 === t.maxDuration ? 60 : c,
            camera: m
        },
        h, {
            success: e
        });
        return _global.miniprogramThis.chooseVideo(b)
    }
    var y = _objectSpread({
        count: i,
        sizeType: p,
        sourceType: s
    },
    h, {
        success: e
    });
    return _global.miniprogramThis.chooseImage(y)
}
function uploadFile(e) {
    var t = e.url,
    o = e.filePath,
    i = e.name,
    r = void 0 === i ? "file": i,
    n = e.header,
    a = void 0 === n ? {}: n,
    s = e.formData,
    u = void 0 === s ? {}: s,
    c = e.timeout,
    l = void 0 === c ? 20 : c,
    p = e.enableProfile,
    d = void 0 === p || p,
    m = e.enableHttp2,
    f = void 0 !== m && m,
    g = _objectWithoutProperties(e, ["url", "filePath", "name", "header", "formData", "timeout", "enableProfile", "enableHttp2"]);

    console.error(e, e.timeout, c, l);

    return _global.miniprogramThis.uploadFile(_objectSpread({
        url: t,
        filePath: o,
        name: r,
        header: a,
        formData: u,
//        timeout: l,
        enableProfile: d,
        enableHttp2: f
    },
    g))
}
function getSystemInfoSync() {
    var e = 0 < arguments.length && void 0 !== arguments[0] ? arguments[0] : ["window", "device", "appBase"];
    return "function" == typeof _global.miniprogramThis.getWindowInfo ? e.reduce(function(e, t) {
        return _objectSpread({},
        e, {},
        _global.miniprogramThis["get".concat(t.charAt(0).toUpperCase() + t.substring(1), "Info")]())
    },
    {}) : _global.miniprogramThis.getSystemInfoSync()
}
function vibrateShort(e) {
    if ("devtools" !== getSystemInfoSync(["window", "device"]).platform) return _global.miniprogramThis.vibrateShort(e)
}
function getMenuButtonBoundingClientRectSync() {
    var t;
    try {
        if (null === (t = _global.miniprogramThis.getMenuButtonBoundingClientRect ? _global.miniprogramThis.getMenuButtonBoundingClientRect() : null)) throw "getMenuButtonBoundingClientRect error";
        if (! (t.width && t.top && t.left && t.height)) throw "getMenuButtonBoundingClientRect error"
    } catch(e) {
        var o = getSystemInfoSync(["window", "device"]),
        i = !!(o.system.toLowerCase().search("ios") + 1),
        r = 88,
        n = 4;
        "android" === o.platform ? (n = 8, r = 96) : "devtools" === o.platform && (n = i ? 5.5 : 7.5),
        o.statusBarHeight || (o.statusBarHeight = o.screenHeight - o.windowHeight - 20),
        t = {
            bottom: o.statusBarHeight + n + 32,
            height: 32,
            left: o.windowWidth - r - 10,
            right: o.windowWidth - 10,
            top: o.statusBarHeight + n,
            width: r
        }
    }
    return t
}
function nextTick(e) {
    return "function" == typeof _global.miniprogramThis.nextTick ? _global.miniprogramThis.nextTick(e) : "undefined" != typeof Promise ? Promise.resolve().then(e) : void setTimeout(function() {
        return e()
    },
    0)
}