'use strict';

//
// Copyright 2019
//

//
// Register global
//

window.web = new FoundationAntD(jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    web.vue('.bsw-body').template(web.config.template || null).data(Object.assign({

        web: web,
        locale: web.d.locales[web.lang.i18n_ant],
        noLoadingOnce: false,
        spinning: false,
        init: { // from v-init
            configure: {},
            message: {},
            modal: {},
            result: {}
        }

    }, web.config.data)).computed(Object.assign({}, web.config.computed || {})).method(Object.assign({
        redirect: function redirect(data) {
            if (data.function && data.function !== 'redirect') {
                return this.dispatcher(data, $('body'));
            }
            var url = data.location;
            if (url.startsWith('http') || url.startsWith('/')) {
                if (typeof data.window === 'undefined') {
                    return location.href = url;
                } else {
                    return window.open(url);
                }
            }
        },
        getBswData: function getBswData(object) {
            return object[0].dataBsw || object.data('bsw') || {};
        },
        redirectByVue: function redirectByVue(event) {
            this.redirect(this.getBswData($(event.item.$el).find('span')));
        },
        dispatcher: function dispatcher(data, element) {
            var that = this;
            var action = function action() {
                if (!data.function || data.function.length === 0) {
                    return console.error('Attribute function should be configure in options.', data);
                }
                if (typeof that[data.function] === 'undefined') {
                    return console.error('Method ' + data.function + ' is undefined.', data);
                }
                that[data.function](data, element);
            };
            if (typeof data.confirm === 'undefined') {
                action();
            } else {
                bsw.showConfirm(data.confirm, bsw.lang.confirm_title, { onOk: function onOk() {
                        return action();
                    } });
            }
        },
        dispatcherByNative: function dispatcherByNative(element) {
            this.dispatcher(this.getBswData($(element)), element);
        },
        dispatcherByVue: function dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0]);
        }
    }, web.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                var key = web.smallHump(binding.arg);
                vnode.context.init[key] = binding.value || binding.expression;
            }
        }

    }, web.config.directive || {})).watch(Object.assign({}, web.config.watch || {})).component(Object.assign({

        // component
        'b-icon': web.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        })

    }, web.config.component || {})).init(function (v) {

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            var src = $(this).attr('src');
            src = web.setParams({ t: web.timestamp() }, src);
            $(this).attr('src', src);
        });

        v.$nextTick(function () {
            // logic
            for (var fn in web.config.logic || []) {
                if (!web.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                web.config.logic[fn](v);
            }
        });

        setTimeout(function () {
            // message
            var message = v.init.message;
            if (typeof message.content !== 'undefined') {
                // notification message confirm
                message = bsw.arrayBase64Decode(message);
                var duration = bsw.isNull(message.duration) ? undefined : message.duration;
                try {
                    bsw[message.classify](message.content, duration, null, message.type);
                } catch (e) {
                    console.warn(bsw.lang.message_data_error, message);
                    console.warn(e);
                }
            }
            // modal
            if (typeof v.init.modal.content !== 'undefined') {
                v.showModal(bsw.arrayBase64Decode(v.init.modal));
            }
            // result
            if (typeof v.init.result.title !== 'undefined') {
                v.showResult(bsw.arrayBase64Decode(v.init.result));
            }
        }, 100);
    });
});

// -- eof --
