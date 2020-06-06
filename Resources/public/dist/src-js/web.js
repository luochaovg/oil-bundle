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
        configure: {}, // from v-init
        message: {}, // from v-init
        tips: {} // from v-init

    }, web.config.data)).computed(Object.assign({}, web.config.computed || {})).method(Object.assign({
        getBswData: function getBswData(object) {
            return web.evalExpr(object.attr('bsw-data'));
        },
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
        redirectByVue: function redirectByVue(event) {
            this.redirect(this.getBswData($(event.item.$el).find('span')));
        },
        dispatcher: function dispatcher(data, element) {
            var that = this;
            var action = function action() {
                var fn = data.function || 'console.log';
                that[fn](data, element);
            };
            if (typeof data.confirm === 'undefined') {
                action();
            } else {
                web.showConfirm(data.confirm, web.lang.confirm_title, { onOk: function onOk() {
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
                vnode.context[key] = binding.value || binding.expression;
            }
        }

    }, web.config.directive || {})).watch(Object.assign({}, web.config.watch || {})).component(Object.assign({

        // component
        'b-icon': web.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
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
            if (typeof v.message.content !== 'undefined') {
                // notification message confirm
                var duration = web.isNull(v.message.duration) ? undefined : v.message.duration;
                try {
                    web[v.message.classify](v.message.content, duration, null, v.message.type);
                } catch (e) {
                    console.warn(web.lang.message_data_error);
                    console.warn(v.message);
                }
            }
            // tips
            if (typeof v.tips.content !== 'undefined') {
                v.showModal(v.tips);
            }
        }, 100);
    });
});

// -- eof --
