'use strict';

//
// Copyright 2019
//

//
// Register global
//

window.bsw = new FoundationAntD(jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    bsw.vue().template(bsw.config.template || null).data(Object.assign({

        bsw: bsw,
        locale: bsw.d.locales[bsw.lang.i18n_ant],
        noLoadingOnce: false,
        spinning: false,
        ckEditor: {},
        init: { // from v-init
            configure: {},
            message: {},
            modal: {},
            result: {}
        }

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({
        redirectByVue: function redirectByVue(event) {
            bsw.redirect(bsw.getBswData($(event.item.$el).find('span')));
        },
        dispatcherByNative: function dispatcherByNative(element) {
            bsw.dispatcherByBswData(bsw.getBswData($(element)), element);
        },
        dispatcherByVue: function dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0]);
        },
        showIFrameByNative: function showIFrameByNative(element) {
            bsw.showIFrame(bsw.getBswData($(element)), element);
        },
        showIFrameByVue: function showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0]);
        },
        tabsLinksSwitch: function tabsLinksSwitch(key) {
            bsw.redirect(bsw.getBswData($('#tabs_link_' + key)));
        }
    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                var key = bsw.smallHump(binding.arg);
                vnode.context.init[key] = binding.value || binding.expression;
            }
        }

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        })

    }, bsw.config.component || {})).init(function (v) {

        setTimeout(function () {
            bsw.messageAutoDiscovery(v.init);
        }, 100);
    });
});

// -- eof --
