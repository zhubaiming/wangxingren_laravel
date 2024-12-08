"use strict";
var _baseComponent = _interopRequireDefault(require("../helpers/baseComponent")),
_classNames = _interopRequireDefault(require("../helpers/libs/classNames")),
_warning = _interopRequireDefault(require("../helpers/libs/warning")),
_props = require("./props"),
_useFieldForm = _interopRequireWildcard(require("../helpers/hooks/useFieldForm")),
_constants = require("../helpers/shared/constants"),
_util = require("../helpers/shared/util"),
_asyncValidator = _interopRequireDefault(require("../helpers/libs/async-validator"));
function _interopRequireWildcard(e) {
    if (e && e.__esModule) return e;
    var t = {};
    if (null != e) for (var i in e) if (Object.prototype.hasOwnProperty.call(e, i)) {
        var r = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(e, i) : {};
        r.get || r.set ? Object.defineProperty(t, i, r) : t[i] = e[i]
    }
    return t.
default = e,
    t
}
function _interopRequireDefault(e) {
    return e && e.__esModule ? e: {
    default:
        e
    }
}
function _objectWithoutProperties(e, t) {
    if (null == e) return {};
    var i, r, l = _objectWithoutPropertiesLoose(e, t);
    if (Object.getOwnPropertySymbols) {
        var s = Object.getOwnPropertySymbols(e);
        for (r = 0; r < s.length; r++) i = s[r],
        0 <= t.indexOf(i) || Object.prototype.propertyIsEnumerable.call(e, i) && (l[i] = e[i])
    }
    return l
}
function _objectWithoutPropertiesLoose(e, t) {
    if (null == e) return {};
    var i, r, l = {},
    s = Object.keys(e);
    for (r = 0; r < s.length; r++) i = s[r],
    0 <= t.indexOf(i) || (l[i] = e[i]);
    return l
}
function _typeof(e) {
    return (_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ?
    function(e) {
        return typeof e
    }: function(e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol": typeof e
    })(e)
}
function _toConsumableArray(e) {
    return _arrayWithoutHoles(e) || _iterableToArray(e) || _nonIterableSpread()
}
function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance")
}
function _iterableToArray(e) {
    if (Symbol.iterator in Object(e) || "[object Arguments]" === Object.prototype.toString.call(e)) return Array.from(e)
}
function _arrayWithoutHoles(e) {
    if (Array.isArray(e)) {
        for (var t = 0,
        i = new Array(e.length); t < e.length; t++) i[t] = e[t];
        return i
    }
}
function ownKeys(t, e) {
    var i = Object.keys(t);
    if (Object.getOwnPropertySymbols) {
        var r = Object.getOwnPropertySymbols(t);
        e && (r = r.filter(function(e) {
            return Object.getOwnPropertyDescriptor(t, e).enumerable
        })),
        i.push.apply(i, r)
    }
    return i
}
function _objectSpread(t) {
    for (var e = 1; e < arguments.length; e++) {
        var i = null != arguments[e] ? arguments[e] : {};
        e % 2 ? ownKeys(i, !0).forEach(function(e) {
            _defineProperty(t, e, i[e])
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(i)) : ownKeys(i).forEach(function(e) {
            Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(i, e))
        })
    }
    return t
}
function _defineProperty(e, t, i) {
    return t in e ? Object.defineProperty(e, t, {
        value: i,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = i,
    e
} (0, _baseComponent.
default)({
        useExport:
        !0,
        relations: {
            "../field/index": {
                type: "descendant",
                observer: function(e, t) {
                    var i = t.unlinked;
                    this.renderFields[e.data.name] = !1 === i,
                    this.callDebounceFn(this.changeValue)
                }
            }
        },
        properties: _props.props,
        observers: _defineProperty({},
        "layout, validateMessages, requiredMarkStyle, asteriskText, requiredText, optionalText, disabled, readOnly",
        function() {
            this.changeFieldElem(this.data)
        }),
        computed: {
            classes: ["prefixCls",
            function(e) {
                return {
                    wrap: (0, _classNames.
                default)(e),
                    footer: "".concat(e, "__footer")
                }
            }]
        },
        methods: {
            changeFieldElem: function(e) {
                var l = e.layout,
                s = e.validateMessages,
                a = e.requiredMarkStyle,
                n = e.asteriskText,
                o = e.requiredText,
                d = e.optionalText,
                u = e.disabled,
                c = e.readOnly,
                t = this.getRelationsByName("../field/index");
                if (0 < t.length) {
                    var f = t.length - 1;
                    t.forEach(function(e, t) {
                        var i = t === f,
                        r = {
                            layout: l,
                            validateMessages: s,
                            requiredMarkStyle: a,
                            asteriskText: n,
                            requiredText: o,
                            optionalText: d,
                            disabled: u,
                            readOnly: c
                        };
                        e.changeContext(t, i, r)
                    })
                }
            },
            changeValue: function() {
                this.changeFieldElem(this.data),
                this.clearUnlinkedFields()
            },
            saveRef: function(e, t, i) {
                if (i) this.recoverClearedField(e),
                this.instances[e] = i;
                else {
                    var r = this.fieldsStore.getFieldMeta(e);
                    r.preserve || (this.clearedFieldMetaCache[e] = {
                        field: this.fieldsStore.getField(e),
                        meta: r
                    },
                    this.clearField(e))
                }
            },
            recoverClearedField: function(e) {
                this.clearedFieldMetaCache[e] && (this.fieldsStore.setFields(_defineProperty({},
                e, this.clearedFieldMetaCache[e].field)), this.fieldsStore.setFieldMeta(e, this.clearedFieldMetaCache[e].meta), delete this.clearedFieldMetaCache[e])
            },
            setFieldsAsErrors: function(e, t) {
                if (this.fieldsStore.getValidFieldsFullName(e).includes(e) && !(0, _useFieldForm.hasRules)(t.validate)) {
                    var i = this.fieldsStore.getField(e);
                    i.errors && this.fieldsStore.setFields(_defineProperty({},
                    e, _objectSpread({},
                    i, {
                        errors: void 0
                    })))
                }
            },
            clearUnlinkedFields: function() {
                var i = this,
                e = this.fieldsStore.getAllFieldsName().filter(function(e) {
                    var t = i.fieldsStore.getFieldMeta(e);
                    return ! i.renderFields[e] && !t.preserve
                });
                0 < e.length && e.forEach(function(e) {
                    return i.clearField(e)
                })
            },
            clearField: function(e) {
                this.fieldsStore.clearField(e),
                delete this.renderFields[e],
                delete this.instances[e],
                delete this.cachedBind[e]
            },
            setFields: function(e, l) {
                var s = this,
                a = this.fieldsStore.flattenRegisteredFields(e);
                Object.keys(a).forEach(function(e) {
                    s.fieldsStore.setFields(_defineProperty({},
                    e, a[e]));
                    var t = s.fieldsStore.getFieldMeta(e);
                    if (t) {
                        var i = t.fieldElem,
                        r = t.inputElem;
                        i.forceUpdate(r, l)
                    }
                });
                var t = Object.keys(a).reduce(function(e, t) {
                    return (0, _util.set)(e, t, s.fieldsStore.getField(t))
                },
                {}),
                i = this.fieldsStore.getNestedAllFields();
                this.onFieldsChange(t, i)
            },
            onCollectValidate: function(e, t) {
                for (var i = arguments.length,
                r = new Array(2 < i ? i - 2 : 0), l = 2; l < i; l++) r[l - 2] = arguments[l];
                var s = this.onCollectCommon(e, t, r),
                a = s.field,
                n = s.fieldMeta,
                o = _objectSpread({},
                a, {
                    dirty: !0
                });
                this.fieldsStore.setFieldsAsDirty(),
                this.validateFieldsInternal([o], {
                    action: t,
                    options: {
                        firstFields: !!n.validateFirst
                    }
                })
            },
            onCollectCommon: function(e, t, i) {
                var r = this.fieldsStore.getField(e),
                l = this.fieldsStore.getFieldMeta(e),
                s = l.oriInputEvents,
                a = l.fieldElem;
                s && s[t] && s[t].apply(s, _toConsumableArray(i));
                var n = _useFieldForm.getValueFromEvent.apply(void 0, _toConsumableArray(i));
                if (n !== this.fieldsStore.getFieldValue(e)) {
                    a.data.value !== n && a.setData({
                        value: n
                    });
                    var o = _defineProperty({},
                    e, n),
                    d = this.fieldsStore.getAllValues();
                    this.onValuesChange(o, _objectSpread({},
                    d, {},
                    o))
                }
                return {
                    name: e,
                    field: _objectSpread({},
                    r, {
                        value: n,
                        touched: !0
                    }),
                    fieldMeta: l
                }
            },
            onCollect: function(e, t) {
                for (var i = arguments.length,
                r = new Array(2 < i ? i - 2 : 0), l = 2; l < i; l++) r[l - 2] = arguments[l];
                var s = this.onCollectCommon(e, t, r),
                a = s.name,
                n = s.field,
                o = s.fieldMeta.validate;
                this.fieldsStore.setFieldsAsDirty();
                var d = _objectSpread({},
                n, {
                    dirty: (0, _useFieldForm.hasRules)(o)
                });
                this.setFields(_defineProperty({},
                a, d))
            },
            setFieldsValue: function(e, t) {
                var r = this.fieldsStore.fieldsMeta,
                l = this.fieldsStore.flattenRegisteredFields(e),
                i = Object.keys(l).reduce(function(e, t) {
                    if (r[t]) {
                        var i = l[t];
                        e[t] = {
                            value: i
                        }
                    }
                    return e
                },
                {});
                this.setFields(i, t);
                var s = this.fieldsStore.getAllValues();
                this.onValuesChange(e, s)
            },
            resetFields: function(e) {
                var t = this,
                i = void 0 === e ? e: Array.isArray(e) ? e: [e],
                r = this.fieldsStore.resetFields(i); (0 < Object.keys(r).length && this.setFields(r), e) ? (Array.isArray(e) ? e: [e]).forEach(function(e) {
                    return delete t.clearedFieldMetaCache[e]
                }) : this.clearedFieldMetaCache = {}
            },
            validateFieldsInternal: function(e, t, i) {
                var a = this,
                n = t.fieldNames,
                l = t.action,
                r = t.options,
                s = void 0 === r ? {}: r,
                o = {},
                d = {},
                u = {},
                c = {};
                if (e.forEach(function(e) {
                    var t = e.name;
                    if (!0 === s.force || !1 !== e.dirty) {
                        var i = a.fieldsStore.getFieldMeta(t),
                        r = _objectSpread({},
                        e);
                        r.errors = void 0,
                        r.validating = !0,
                        r.dirty = !0,
                        o[t] = a.getRules(i, l),
                        d[t] = r.value,
                        u[t] = r
                    } else e.errors && (0, _util.set)(c, t, {
                        errors: e.errors
                    })
                }), this.setFields(u), Object.keys(d).forEach(function(e) {
                    d[e] = a.fieldsStore.getFieldValue(e)
                }), i && (0, _useFieldForm.isEmptyObject)(u)) i((0, _useFieldForm.isEmptyObject)(c) ? null: c, this.fieldsStore.getFieldsValue(n));
                else {
                    var f = new _asyncValidator.
                default(o),
                    h = this.data.validateMessages;
                    h && f.messages(h),
                    f.validate(d, s,
                    function(e) {
                        var s = _objectSpread({},
                        c);
                        e && e.length && e.forEach(function(e) {
                            var r = e.field,
                            l = r;
                            Object.keys(o).some(function(e) {
                                var t = o[e] || [];
                                if (e === r) return l = e,
                                !0;
                                if (t.every(function(e) {
                                    return "array" !== e.type
                                }) || 0 !== r.indexOf("".concat(e, "."))) return ! 1;
                                var i = r.slice(e.length + 1);
                                return !! /^\d+$/.test(i) && (l = e, !0)
                            });
                            var t = (0, _util.get)(s, l);
                            "object" === _typeof(t) && !Array.isArray(t) || (0, _util.set)(s, l, {
                                errors: []
                            }),
                            (0, _util.get)(s, l.concat(".errors")).push(e)
                        });
                        var r = [],
                        l = {};
                        Object.keys(o).forEach(function(e) {
                            var t = (0, _util.get)(s, e),
                            i = a.fieldsStore.getField(e); (0, _util.eq)(i.value, d[e]) ? (i.errors = t && t.errors, i.value = d[e], i.validating = !1, i.dirty = !1, l[e] = i) : r.push({
                                name: e
                            })
                        }),
                        a.setFields(l),
                        i && (r.length && r.forEach(function(e) {
                            var t = e.name,
                            i = [{
                                message: "".concat(t, " need to revalidate"),
                                field: t
                            }]; (0, _util.set)(s, t, {
                                expired: !0,
                                errors: i
                            })
                        }), i((0, _useFieldForm.isEmptyObject)(s) ? null: s, a.fieldsStore.getFieldsValue(n)))
                    })
                }
            },
            validateFields: function(d, u, c) {
                var f = this,
                e = new Promise(function(i, r) {
                    var e = (0, _useFieldForm.getParams)(d, u, c),
                    t = e.names,
                    l = e.options,
                    s = (0, _useFieldForm.getParams)(d, u, c).callback;
                    if (!s || "function" == typeof s) {
                        var a = s;
                        s = function(e, t) {
                            a && a(e, t),
                            e ? r({
                                errors: e,
                                values: t
                            }) : i(t)
                        }
                    }
                    var n = t ? f.fieldsStore.getValidFieldsFullName(t) : f.fieldsStore.getValidFieldsName(),
                    o = n.filter(function(e) {
                        var t = f.fieldsStore.getFieldMeta(e);
                        return (0, _useFieldForm.hasRules)(t.validate)
                    }).map(function(e) {
                        var t = f.fieldsStore.getField(e);
                        return t.value = f.fieldsStore.getFieldValue(e),
                        t
                    });
                    o.length ? ("firstFields" in l || (l.firstFields = n.filter(function(e) {
                        return !! f.fieldsStore.getFieldMeta(e).validateFirst
                    })), f.validateFieldsInternal(o, {
                        fieldNames: n,
                        options: l
                    },
                    s)) : s(null, f.fieldsStore.getFieldsValue(n))
                });
                return e.
                catch(function(e) {
                    return console.error && console.error(e),
                    e
                }),
                e
            },
            getRules: function(e, t) {
                var i = e.validate.filter(function(e) {
                    return ! t || 0 <= e.trigger.indexOf(t)
                }).map(function(e) {
                    return e.rules
                });
                return (0, _useFieldForm.flattenArray)(i)
            },
            getFieldInstance: function(e) {
                return this.instances[e]
            },
            getCacheBind: function(e, t, i) {
                this.cachedBind[e] || (this.cachedBind[e] = {});
                var r = this.cachedBind[e];
                return r[t] && r[t].oriFn === i || (r[t] = {
                    fn: i.bind(this, e, t),
                    oriFn: i
                }),
                r[t].fn
            },
            getFieldDecorator: function(r, e, t) {
                var l = this,
                s = this.getFieldProps(r, e, t);
                return function(e) {
                    l.renderFields[r] = !0;
                    var t = l.fieldsStore.getFieldMeta(r),
                    i = e.data;
                    return t.inputElem = e,
                    i.oriInputEvents || (s.oriInputEvents = _objectSpread({},
                    i.inputEvents), t.oriInputEvents = _objectSpread({},
                    i.inputEvents)),
                    _objectSpread({},
                    s, {},
                    l.fieldsStore.getFieldValuePropValue(t))
                }
            },
            getFieldProps: function(t, e, i) {
                var r = this;
                delete this.clearedFieldMetaCache[t];
                var l = (0, _useFieldForm.transformRules)(e.rules),
                s = e.initialValue,
                a = e.trigger,
                n = void 0 === a ? _constants.DEFAULT_TRIGGER: a,
                o = e.valuePropName,
                d = e.validate,
                u = void 0 === d ? [] : d,
                c = e.validateTrigger,
                f = void 0 === c ? [_constants.DEFAULT_TRIGGER] : c,
                h = e.preserve,
                F = e.validateFirst,
                p = e.hidden,
                g = {
                    name: t,
                    trigger: n,
                    valuePropName: o,
                    validate: u,
                    validateTrigger: f,
                    preserve: h,
                    rules: l,
                    validateFirst: F,
                    hidden: p
                }; (0, _useFieldForm.isNullValue)(s) || (g.initialValue = s);
                var v = _objectSpread({},
                this.fieldsStore.getFieldValuePropValue(g), {
                    inputEvents: {}
                }),
                y = this.fieldsStore.getFieldMeta(t);
                "initialValue" in g && (y.initialValue = g.initialValue),
                y.fieldElem = i;
                var m = (0, _useFieldForm.normalizeValidateRules)(u, l, f),
                _ = (0, _useFieldForm.getValidateTriggers)(m);
                _.forEach(function(e) {
                    v.inputEvents[e] || (v.inputEvents[e] = r.getCacheBind(t, e, r.onCollectValidate))
                }),
                -1 === _.indexOf(n) && (v.inputEvents[n] = this.getCacheBind(t, n, this.onCollect));
                var b = _objectSpread({},
                y, {},
                g, {
                    validate: m
                });
                this.fieldsStore.setFieldMeta(t, b),
                this.setFieldsAsErrors(t, b);
                b.fieldElem,
                b.inputElem,
                b.oriInputEvents;
                var S = _objectWithoutProperties(b, ["fieldElem", "inputElem", "oriInputEvents"]);
                return v[_constants.FIELD_META_PROP] = S,
                v[_constants.FIELD_DATA_PROP] = this.fieldsStore.getField(t),
                v
            },
            registerField: function(e, t) {
                var i = "".concat(e, "__ref"),
                r = this.getCacheBind(e, i, this.saveRef);
                return r(t),
                function() {
                    r(null)
                }
            },
            getInternalHooks: function(e) {
                return "FORM_HOOK_MARK" === e ? {
                    registerField: this.registerField.bind(this),
                    getFieldDecorator: this.getFieldDecorator.bind(this)
                }: ((0, _warning.
            default)(!1, "`getInternalHooks` is internal usage of the <form />. Should not call directly."), null)
            },
            expose: function() {
                return {
                    getFieldsValue: this.getFieldsValue,
                    getFieldValue: this.getFieldValue,
                    setFieldsInitialValue: this.setFieldsInitialValue,
                    getFieldsError: this.getFieldsError,
                    getFieldError: this.getFieldError,
                    isFieldValidating: this.isFieldValidating,
                    isFieldsValidating: this.isFieldsValidating,
                    isFieldsTouched: this.isFieldsTouched,
                    isFieldTouched: this.isFieldTouched,
                    setFieldsValue: this.setFieldsValue.bind(this),
                    setFields: this.setFields.bind(this),
                    resetFields: this.resetFields.bind(this),
                    validateFields: this.validateFields.bind(this),
                    getFieldInstance: this.getFieldInstance.bind(this),
                    getInternalHooks: this.getInternalHooks.bind(this)
                }
            },
            onValuesChange: function(e, t) {
                this.triggerEvent("change", {
                    form: this.expose(),
                    changedValues: e,
                    allValues: t
                })
            },
            onFieldsChange: function(e, t) {
                this.triggerEvent("fieldsChange", {
                    form: this.expose(),
                    changedFields: e,
                    allFields: t
                })
            }
        },
        created: function() {
            var i = this;
            this.fieldsStore = (0, _useFieldForm.
        default)(),
            this.renderFields = {},
            this.cachedBind = {},
            this.clearedFieldMetaCache = {},
            this.instances = {},
            ["getFieldsValue", "getFieldValue", "setFieldsInitialValue", "getFieldsError", "getFieldError", "isFieldValidating", "isFieldsValidating", "isFieldsTouched", "isFieldTouched"].forEach(function(t) {
                i[t] = function() {
                    var e;
                    return (e = i.fieldsStore)[t].apply(e, arguments)
                }
            })
        }
    });