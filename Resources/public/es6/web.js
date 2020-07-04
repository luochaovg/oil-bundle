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

        web,
        locale: web.d.locales[web.lang.i18n_ant],
        noLoadingOnce: false,
        spinning: false,
        configure: {},  // from v-init
        message: {},  // from v-init
        tips: {}, // from v-init

    }, web.config.data)).computed(Object.assign({}, web.config.computed || {})).method(Object.assign({

        redirect(data) {
            if (data.function && data.function !== 'redirect') {
                return this.dispatcher(data, $('body'));
            }
            let url = data.location;
            if (url.startsWith('http') || url.startsWith('/')) {
                if (typeof data.window === 'undefined') {
                    return location.href = url;
                } else {
                    return window.open(url);
                }
            }
        },

        getBswData(object) {
            return object[0].dataBsw || object.data('bsw') || {};
        },

        redirectByVue(event) {
            this.redirect(this.getBswData($(event.item.$el).find('span')));
        },

        dispatcher(data, element) {
            let that = this;
            let action = function () {
                if (!data.function || data.function.length === 0) {
                    return console.error(`Attribute function should be configure in options.`, data);
                }
                if (typeof that[data.function] === 'undefined') {
                    return console.error(`Method ${data.function} is undefined.`, data);
                }
                that[data.function](data, element);
            };
            if (typeof data.confirm === 'undefined') {
                action();
            } else {
                bsw.showConfirm(data.confirm, bsw.lang.confirm_title, {onOk: () => action()});
            }
        },

        dispatcherByNative(element) {
            this.dispatcher(this.getBswData($(element)), element);
        },

        dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0])
        },

    }, web.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                let key = web.smallHump(binding.arg);
                vnode.context[key] = (binding.value || binding.expression);
            }
        },

    }, web.config.directive || {})).watch(Object.assign({}, web.config.watch || {})).component(Object.assign({

        // component
        'b-icon': web.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
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
                let duration = bsw.isNull(v.message.duration) ? undefined : v.message.duration;
                try {
                    bsw[v.message.classify](Base64.decode(v.message.content), duration, null, v.message.type);
                } catch (e) {
                    console.warn(bsw.lang.message_data_error);
                    console.warn(v.message);
                }
            }
            // tips
            if (typeof v.tips.content !== 'undefined') {
                let map = ['title', 'content'];
                for (let i = 0; i < map.length; i++) {
                    if (typeof v.tips[map[i]] !== 'undefined') {
                        v.tips[map[i]] = Base64.decode(v.tips[map[i]]);
                    }
                }
                v.showModal(v.tips);
            }
        }, 100);
    });
});

// -- eof --