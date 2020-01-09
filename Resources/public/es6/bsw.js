//
// Copyright 2019
//

//
// Register global
//

window.bsw = FoundationAntD;
window.app = new FoundationAntD({
    rsaPublicKey: `-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc
B4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5
IZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH
NydhxUEs0y8aMzWbGwIDAQAB
-----END PUBLIC KEY-----`,
}, jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    app.vue('.bsw-body').template(app.config.template || null).data(Object.assign({

        bsw,
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        opposeMap: {yes: 'no', no: 'yes'},
        formUrl: null,
        formMethod: null,

        theme: 'light',
        themeMap: {dark: 'light', light: 'dark'},
        weak: 'no',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true,
        ckEditor: {},

        no_loading_once: false,
        spinning: false,
        message: null,  // from v-init
        configure: {},  // from v-init
        modal: {
            visible: false,
        },

    }, app.config.data)).computed(Object.assign({}, app.config.computed || {})).method(Object.assign({

        moment,

        redirect(data) {
            let url = data.location;
            if (bsw.isMobile() && this.mobileDefaultCollapsed) {
                bsw.cookie().set('bsw_menu_collapsed', 'yes');
            }
            if (url.startsWith('http') || url.startsWith('/')) {
                return location.href = url;
            }
        },

        getBswData(object) {
            return bsw.evalExpr(object.attr('bsw-data'));
        },

        redirectByVue(event) {
            this.redirect(this.getBswData($(event.item.$el).find('span')));
        },

        dispatcher(data, element) {
            let that = this;
            let action = function () {
                let fn = data.function || 'console.log';
                that[fn](data, element);
            };
            if (typeof data.confirm === 'undefined') {
                action();
            } else {
                app.showConfirm(data.confirm, app.lang.confirm_title, {onOk: () => action()});
            }
        },

        dispatcherByNative(element) {
            this.dispatcher(this.getBswData($(element)), element);
        },

        dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0])
        },

        setUrlToForm(data, element) {
            this.formUrl = data.location;
            this.formMethod = $(element).attr('bsw-method');
        },

        pagination(url, page, uuid) {
            let that = this;
            if (page) {
                url = bsw.setParams({page}, url);
            }
            app.request(url).then((res) => {
                app.response(res).then(() => {
                    that[`list_${uuid}`] = res.sets.preview.list;
                    that[`page_${uuid}`] = page;
                    that[`url_${uuid}`] = url;
                    that[`page_data_${uuid}`] = res.sets.preview.page;
                    that[`image_change_table_${uuid}`]();
                    history.replaceState({}, "", bsw.unsetParams(['uuid'], url));
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        filter(event, uuid, jump = false) {
            let that = this;
            let formatKey = `date_format_${uuid}`;
            event.preventDefault();
            that[`form_filter_${uuid}`].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[formatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[formatKey][field]);
                            }
                        }
                    }
                }
                return this[`${this.formMethod}FilterForm`](values, uuid, jump);
            });
        },

        submitFilterForm(values, uuid, jump = false) {
            let _values = {};
            let number = 0;
            for (let field in values) {
                if (!values.hasOwnProperty(field)) {
                    continue;
                }
                if (typeof values[field] === 'undefined') {
                    continue;
                }
                if (values[field] == null) {
                    continue;
                }
                if (values[field].length === 0) {
                    continue;
                }
                _values[field] = values[field];
                number += 1;
            }

            let url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.setParams({filter: _values}, url);

            if (jump) {
                location.href = url;
            } else {
                this.pagination(url, null, uuid);
            }
        },

        persistence(event, uuid) {
            let that = this;
            let formatKey = `date_format_${uuid}`;
            event.preventDefault();
            that[`form_persistence_${uuid}`].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[formatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[formatKey][field]);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, `${field}.fileList`)) {
                        delete values[field];
                    }
                }
                return this[`${this.formMethod}PersistenceForm`](values, uuid);
            });
        },

        submitPersistenceForm(values, uuid) {
            let data = {submit: values};
            app.request(this.formUrl, data).then((res) => {
                app.response(res).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        uploaderChange({file, fileList}) {
            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            let keyMd5 = this.key_for_file_md5;
            let keySha1 = this.key_for_file_sha1;
            let keyList = this.key_for_file_list;

            if (!file.response) {
                this[keyList] = fileList;
                return;
            }
            if (file.response.error) {
                this[keyList] = fileList.slice(0, -1);
            }

            let files = this[keyList].slice(-1);
            if (files.length) {
                let sets = files[0].response.sets;
                let map = {
                    [keyMd5]: 'attachment_md5',
                    [keySha1]: 'attachment_sha1',
                    [keyList]: 'attachment_id',
                };
                for (let key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        let field = `${key.split('_')[0]}`;
                        if ($(`#${field}`).length === 0) {
                            continue;
                        }
                        this[this.key_for_form].setFieldsValue({[field]: sets[map[key]]});
                    }
                }
            }
            app.response(file.response).catch((reason => {
                console.warn(reason);
            }));
        },

        showModal(options) {
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = app.popupCosySize().width;
            }
            this.modal = Object.assign(this.modal, options);
        },

        showModalAfterRequest(data, element) {
            app.request(data.location).then((res) => {
                app.response(res).then(() => {
                    let sets = res.sets;
                    let logic = sets.logic || sets;
                    this.showModal({
                        width: logic.width || data.width || app.popupCosySize().width,
                        title: logic.title || data.title || app.lang.modal_title,
                        content: sets.content,
                    });
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        requestByAjax(data, element) {
            let that = this;
            app.request(data.location).then((res) => {
                app.response(res, () => {
                    that[`pagination_refresh_${data.uuid}`]();
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        multipleAction(data, element) {
            let ids = this[`selected_row_keys_${data.uuid}`];
            if (ids.length === 0) {
                return app.warning(app.lang.select_item_first);
            }
            app.request(data.location, {ids: ids}).then((res) => {
                app.response(res, () => {
                    console.log(res);
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        showIFrameByVue(event) {
            let size = app.popupCosySize();
            let object = $(event.target);
            let data = this.getBswData(object);
            data.location = bsw.setParams({iframe: true, fill: object.prev().attr('id')}, data.location);

            let options = {
                visible: true,
                width: size.width,
                title: app.lang.please_select,
                centered: true,
                wrapClassName: 'bsw-preview-iframe',
                content: `<iframe id="bsw-preview-iframe" src="${data.location}"></iframe>`,
            };
            this.showModal(options);
            this.$nextTick(function () {
                $("#bsw-preview-iframe").height(size.height);
            });
        },

        fillParentForm(data, element) {
            data.ids = this[`selected_row_keys_${data.uuid}`];
            if (data.ids.length === 0) {
                return app.warning(app.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },

        fillParentFormInParent(data, element) {
            this.modal.visible = false;
            this[this.key_for_form].setFieldsValue({[data.fill]: data.ids.join(',')});
        },

        initCkEditor() {
            let that = this;
            $('.bsw-persistence .bsw-ck-editor').each(function () {
                let id = $(this).attr('id');
                ClassicEditor.create(this, {}).then(editor => {
                    that.ckEditor[id] = editor;
                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new FileUploadAdapter(editor, loader, that.api_upload);
                    };
                    that.ckEditor[id].model.document.on('change:data', function () {
                        that[that.key_for_form].setFieldsValue({[id]: that.ckEditor[id].getData()});
                    });
                }).catch(err => {
                    console.log(err.stack);
                });
            });
        },

    }, app.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                vnode.context[binding.arg] = (binding.value || binding.expression);
            }
        },

    }, app.config.directive || {})).component(Object.assign({

        // component
        'b-icon': app.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
        }),

    }, app.config.component || {})).init(function (v) {

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            let src = $(this).attr('src');
            src = bsw.setParams({t: bsw.timestamp()}, src);
            $(this).attr('src', src);
        });

        let duration = 100;
        setTimeout(function () {
            // logic
            for (let fn in app.config.logic || []) {
                if (!app.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                app.config.logic[fn](v);
            }
        }, duration);

        // page loading
        setTimeout(function () {
            // message
            $('.bsw-page-loading').fadeOut(200, function () {
                if (typeof v.message.content !== 'undefined') {
                    // notification message confirm
                    let duration = v.message.duration ? v.message.duration : undefined;
                    try {
                        app[v.message.classify](v.message.content, duration, null, v.message.type);
                    } catch (e) {
                        console.warn(app.lang.message_data_error);
                        console.warn(v.message);
                    }
                }
                // tips
                if (typeof v.tips.content !== 'undefined') {
                    v.showModal(v.tips);
                }
            });
        }, duration + 400);
    });

    window.addEventListener('message', function (event) {
        event.data.function += 'InParent';
        app.cnf.v.dispatcher(event.data, $('body')[0]);
    }, false)
});

// -- eof --