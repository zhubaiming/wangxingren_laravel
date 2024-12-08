"use strict";
var _baseComponent = _interopRequireDefault(require("../helpers/baseComponent")),
_fieldBehavior = _interopRequireDefault(require("../helpers/mixins/fieldBehavior")),
_classNames3 = _interopRequireDefault(require("../helpers/libs/classNames")),
_warning = _interopRequireDefault(require("../helpers/libs/warning")),
_useNativeAPI = require("../helpers/hooks/useNativeAPI"),
_getDefaultContext = require("../helpers/shared/getDefaultContext"),
_useDOM = require("../helpers/hooks/useDOM"),
_props = require("../form/props"),
_props2 = require("./props");
function _interopRequireDefault(e) {
    return e && e.__esModule ? e: {
    default:
        e
    }
}
function _slicedToArray(e, t) {
    return _arrayWithHoles(e) || _iterableToArrayLimit(e, t) || _nonIterableRest()
}
function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance")
}
function _iterableToArrayLimit(e, t) {
    var r = [],
    a = !0,
    n = !1,
    i = void 0;
    try {
        for (var o, s = e[Symbol.iterator](); ! (a = (o = s.next()).done) && (r.push(o.value), !t || r.length !== t); a = !0);
    } catch(e) {
        n = !0,
        i = e
    } finally {
        try {
            a || null == s.
            return || s.
            return ()
        } finally {
            if (n) throw i
        }
    }
    return r
}
function _arrayWithHoles(e) {
    if (Array.isArray(e)) return e
}
function ownKeys(t, e) {
    var r = Object.keys(t);
    if (Object.getOwnPropertySymbols) {
        var a = Object.getOwnPropertySymbols(t);
        e && (a = a.filter(function(e) {
            return Object.getOwnPropertyDescriptor(t, e).enumerable
        })),
        r.push.apply(r, a)
    }
    return r
}
function _objectSpread(t) {
    for (var e = 1; e < arguments.length; e++) {
        var r = null != arguments[e] ? arguments[e] : {};
        e % 2 ? ownKeys(r, !0).forEach(function(e) {
            _defineProperty(t, e, r[e])
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(r)) : ownKeys(r).forEach(function(e) {
            Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(r, e))
        })
    }
    return t
}
function _defineProperty(e, t, r) {
    return t in e ? Object.defineProperty(e, t, {
        value: r,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = r,
    e
}
var defaultContext = (0, _getDefaultContext.getDefaultContext)(_props.props, ["layout", "validateMessages", "requiredMarkStyle", "asteriskText", "requiredText", "optionalText", "disabled", "readOnly"]),
children = ["picker", "date-picker", "popup-select", "radio-group", "checkbox-group", "selectable", "selector-group", "switch", "input", "input-number", "rater", "slider", "textarea"],
relations = children.map(function(e) {
    return "../".concat(e, "/index")
}).reduce(function(e, t) {
    return _objectSpread({},
    e, _defineProperty({},
    t, {
        type: "descendant",
        observer: function() {
            this.callDebounceFn(this.changeValue)
        }
    }))
},
{}); (0, _baseComponent.
default)({
        useField:
        !0,
        useExport: !0,
        behaviors: [_fieldBehavior.
    default],
        relations: _objectSpread({
            "../form/index": {
                type: "ancestor"
            }
        },
        relations),
        properties: _props2.props,
        data: {
            index: 0,
            isLast: !1,
            context: defaultContext,
            popoverVisible: !1,
            slotRect: null,
            relativeRect: null
        },
        observers: _defineProperty({
            initialValue: function(e) {
                this.changeValue(e)
            }
        },
        "valuePropName, valueNameFromEvent, trigger, validate, validateTrigger, preserve, rules, validateFirst, hidden",
        function() {
            this.changeValue()
        }),
        computed: {
            classes: ["prefixCls, childElementPosition, labelWrap",
            function(e, t, r) {
                return {
                    wrap: (0, _classNames3.
                default)(e),
                    child: (0, _classNames3.
                default)("".concat(e, "__child"), _defineProperty({},
                    "".concat(e, "__child--position-").concat(t), t)),
                    label: (0, _classNames3.
                default)("".concat(e, "__label"), _defineProperty({},
                    "".concat(e, "__label--wrap"), r)),
                    extra: "".concat(e, "__extra"),
                    arrow: "".concat(e, "__arrow"),
                    asterisk: "".concat(e, "__required-asterisk"),
                    text: "".concat(e, "__required-text"),
                    feedback: "".concat(e, "__feedback-message"),
                    labelHelp: "".concat(e, "__label-help")
                }
            }]
        },
        methods: {
            getFormContext: function() {
                return this.getRelationsByName("../form/index")[0]
            },
            getChildNodes: function() {
                return this.getRelationsByType("descendant")
            },
            changeContext: function(e, t, r) {
                var a = 0 < arguments.length && void 0 !== e ? e: 0,
                n = 1 < arguments.length && void 0 !== t && t,
                i = 2 < arguments.length && void 0 !== r ? r: defaultContext;
                this.setData({
                    index: a,
                    isLast: n,
                    context: i
                })
            },
            setPopoverVisible: function() {
                var n = this,
                i = !this.data.popoverVisible; (i ? this.getPopoverRects() : Promise.resolve([])).then(function(e) {
                    var t = _slicedToArray(e, 2),
                    r = t[0],
                    a = t[1];
                    r && a ? n.setData({
                        slotRect: r,
                        relativeRect: a,
                        popoverVisible: i
                    }) : n.setData({
                        popoverVisible: i
                    })
                })
            },
            getPopoverRects: function() {
                var t = this,
                e = this.data.prefixCls;
                return Promise.all([(0, _useDOM.useRect)(".".concat(e, "__label-help"), t), new Promise(function(r) {
                    var e = t.querySelector("#wux-cell");
                    e && e.getBoundingClientRect(function(e, t) {
                        r(t)
                    })
                })])
            },
            changeValue: function(e) {
                var n = this,
                t = 0 < arguments.length && void 0 !== e ? e: this.data.value;
                this.data.value !== t && this.setData({
                    value: t
                });
                var r = this.getChildNodes();
                0 < r.length && r.forEach(function(e) {
                    Object.defineProperty(e, "hasFieldDecorator", {
                        value: !0,
                        enumerable: !1,
                        writable: !0,
                        configurable: !0
                    }),
                    function(e, t, r) {
                        var a = {
                            hasFieldDecorator: !0
                        };
                        r.data[e] !== t && (a[e] = t),
                        r.setData(_objectSpread({},
                        a)),
                        (0, _useNativeAPI.nextTick)(function() {
                            n.forceUpdate(r)
                        })
                    } (n.data.valuePropName, t, e)
                })
            },
            forceUpdate: function(e, t) {
                var r = this,
                a = this.getFormContext();
                if (a) {
                    var n = a.getInternalHooks("FORM_HOOK_MARK").getFieldDecorator,
                    i = this.data,
                    o = n(i.name, i, this)(e);
                    e.setData(o,
                    function() {
                        t && t(),
                        r.reRender(r.data)
                    })
                } else(0, _warning.
            default)(!1, "Field<".concat(this.data.name, "> instance is not connected to any Form element.Forgot to use <wux-form />?"))
            },
            expose: function() {
                return {
                    changeContext: this.changeContext.bind(this),
                    changeValue: this.changeValue.bind(this),
                    forceUpdate: this.forceUpdate.bind(this)
                }
            }
        },
        ready: function() {
            var e = this.getFormContext();
            if (e) {
                var t = e.getInternalHooks("FORM_HOOK_MARK").registerField,
                r = this.data.name;
                this.cancelRegister = t(r, this)
            }
        },
        detached: function() {
            this.cancelRegister && (this.cancelRegister(), this.cancelRegister = null)
        }
    });