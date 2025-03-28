"use strict";
Object.defineProperty(exports, "__esModule", {
    value: !0
}),
exports.getSafeAreaInset = getSafeAreaInset,
exports.checkIPhoneX = void 0;
var _useNativeAPI = require("./useNativeAPI");
function getSafeAreaInset() {
    var e = ["default", "navBar"].includes(0 < arguments.length && void 0 !== arguments[0] ? arguments[0] : "default"),
    t = {
        top: e ? 88 : 44,
        left: 0,
        right: 0,
        bottom: 34
    };
    try {
        var o = (0, _useNativeAPI.getMenuButtonBoundingClientRectSync)(),
        a = (0, _useNativeAPI.getSystemInfoSync)(["window", "device"]),
        r = a.safeArea,
        n = a.screenHeight,
        s = a.windowHeight,
        i = !!(a.system.toLowerCase().search("ios") + 1),
        h = a.statusBarHeight ? a.statusBarHeight: n - s - 20,
        c = 2 * (o.top - h) + o.height,
        u = a.statusBarHeight && i ? 4 : 0;
        t.top = e ? h + c + u: Math.max(h, t.top),
        t.bottom = n - r.bottom
    } catch(e) {}
    return t
}
var checkIPhoneX = function(e) {
    var t = e.model,
    o = e.windowHeight,
    a = e.windowWidth;
    return /iphone (x|12|13|14)/.test(t.toLowerCase()) || 812 <= o && 2 < o / a
};
exports.checkIPhoneX = checkIPhoneX;