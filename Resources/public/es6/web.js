//
// Copyright 2019
//

//
// Register global
//

window.web = new FoundationAntD({
    rsaPublicKey: ``,
}, jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    web.vue('.bsw-body').template(web.config.template || null).data(Object.assign({

        web,
        locale: web.d.locales[web.lang.i18n_ant],
        no_loading_once: false,
        spinning: false,
        configure: {},  // from v-init
        message: {},  // from v-init
        tips: {}, // from v-init

    }, web.config.data)).computed(Object.assign({}, web.config.computed || {})).method(Object.assign({}, web.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                vnode.context[binding.arg] = (binding.value || binding.expression);
            }
        },

    }, web.config.directive || {})).watch(Object.assign({}, web.config.watch || {})).component(Object.assign({

        // component
        'b-icon': web.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
        }),

    }, web.config.component || {})).init(function (v) {

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            let src = $(this).attr('src');
            src = web.setParams({t: web.timestamp()}, src);
            $(this).attr('src', src);
        });

        v.$nextTick(function () {
            // logic
            for (let fn in web.config.logic || []) {
                if (!web.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                web.config.logic[fn](v);
            }
        });

        setTimeout(function () {
            if (typeof v.message.content !== 'undefined') {
                // notification message confirm
                let duration = web.isNull(v.message.duration) ? undefined : v.message.duration;
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