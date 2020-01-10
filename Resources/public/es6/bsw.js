//
// Copyright 2019
//

//
// Register global
//

window.bsw = new FoundationAntD({
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
    bsw.vue('.bsw-body').template(bsw.config.template || null).data(Object.assign({

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

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

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
                bsw.showConfirm(data.confirm, bsw.lang.confirm_title, {onOk: () => action()});
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

        pagination(url, page) {
            let that = this;
            if (page) {
                url = bsw.setParams({page}, url);
            }
            bsw.request(url).then((res) => {
                bsw.response(res).then(() => {
                    that.preview_list = res.sets.preview.list;
                    that.preview_page_number = page;
                    that.preview_url = url;
                    that.preview_pagination_data = res.sets.preview.page;
                    that.preview_image_change();
                    history.replaceState({}, "", bsw.unsetParams(['uuid'], url));
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        filter(event, jump = false) {
            let that = this;
            event.preventDefault();
            that.filter_form.validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that.filter_format[field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that.filter_format[field];
                                values[field][i] = values[field][i].format(format);
                            }
                        }
                    }
                }
                return this[`${this.formMethod}FilterForm`](values, jump);
            });
        },

        submitFilterForm(values, jump = false) {
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
                this.pagination(url);
            }
        },

        persistence(event) {
            let that = this;
            event.preventDefault();
            that.persistence_form.validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that.persistence_format[field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that.persistence_format[field];
                                values[field][i] = values[field][i].format(format);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, `${field}.fileList`)) {
                        delete values[field];
                    }
                }
                return this[`${this.formMethod}PersistenceForm`](values);
            });
        },

        submitPersistenceForm(values) {
            let data = {submit: values};
            bsw.request(this.formUrl, data).then((res) => {
                let params = bsw.parseQueryString();
                if (params.iframe) {
                    parent.postMessage({response: res, function: 'handleResponse'}, '*');
                } else {
                    bsw.response(res).catch((reason => {
                        console.warn(reason);
                    }));
                }
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

            let keyMd5 = this.persistence_file_md5;
            let keySha1 = this.persistence_file_sha1;
            let keyList = this.persistence_file_list;

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
                        this.persistence_form.setFieldsValue({[field]: sets[map[key]]});
                    }
                }
            }

            if (typeof file.response.code === 'undefined' || file.response.code === 500) {
                this.spinning = false;
            }
            bsw.response(file.response).catch((reason => {
                console.warn(reason);
            }));
        },

        showModal(options) {
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            this.modal = Object.assign(this.modal, options);
        },

        showModalAfterRequest(data, element) {
            bsw.request(data.location).then((res) => {
                bsw.response(res).then(() => {
                    let sets = res.sets;
                    let logic = sets.logic || sets;
                    this.showModal({
                        centered: true,
                        width: logic.width || data.width || bsw.popupCosySize().width,
                        title: logic.title || data.title || bsw.lang.modal_title,
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
            bsw.request(data.location).then((res) => {
                bsw.response(res).then(() => {
                    that.preview_pagination_refresh();
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        multipleAction(data, element) {
            let ids = this.preview_selected_row;
            if (ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            bsw.request(data.location, {ids: ids}).then((res) => {
                bsw.response(res).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        showIFrame(data, element) {
            let size = bsw.popupCosySize();
            let fill = $(element).prev().attr('id');
            data.location = bsw.setParams({iframe: true, fill}, data.location);

            let options = {
                visible: true,
                width: data.width || size.width,
                title: data.title || bsw.lang.please_select,
                centered: true,
                wrapClassName: 'bsw-preview-iframe',
                content: `<iframe id="bsw-preview-iframe" src="${data.location}"></iframe>`,
            };
            this.showModal(options);
            this.$nextTick(function () {
                $("#bsw-preview-iframe").height(data.height || size.height);
            });
        },

        showIFrameByNative(element) {
            this.showIFrame(this.getBswData($(element)), element);
        },

        showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0])
        },

        fillParentForm(data, element) {
            data.ids = this.preview_selected_row;
            if (data.ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            parent.postMessage(data, '*');
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
                        that.persistence_form.setFieldsValue({[id]: that.ckEditor[id].getData()});
                    });
                }).catch(err => {
                    console.warn(err.stack);
                });
            });
        },

        //
        // for iframe exec in parent
        //

        fillParentFormInParent(data, element) {
            this.modal.visible = false;
            this.persistence_form.setFieldsValue({[data.fill]: data.ids.join(',')});
        },

        handleResponseInParent(data, element) {
            this.modal.visible = false;
            bsw.response(data.response).catch((reason => {
                console.warn(reason);
            }));
        },

    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                vnode.context[binding.arg] = (binding.value || binding.expression);
            }
        },

    }, bsw.config.directive || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
        }),

    }, bsw.config.component || {})).init(function (v) {

        let change = false;
        if (v.scaffoldInit) {
            change = v.scaffoldInit();
        }

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            let src = $(this).attr('src');
            src = bsw.setParams({t: bsw.timestamp()}, src);
            $(this).attr('src', src);
        });

        v.$nextTick(function () {
            // logic
            for (let fn in bsw.config.logic || []) {
                if (!bsw.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                bsw.config.logic[fn](v);
            }
        });

        let timeout = change ? 800 : 100;
        setTimeout(function () {
            $('.bsw-page-loading').fadeOut(200, function () {
                if (typeof v.message.content !== 'undefined') {
                    // notification message confirm
                    let duration = bsw.isNull(v.message.duration) ? undefined : v.message.duration;
                    try {
                        bsw[v.message.classify](v.message.content, duration, null, v.message.type);
                    } catch (e) {
                        console.warn(bsw.lang.message_data_error);
                        console.warn(v.message);
                    }
                }
                // tips
                if (typeof v.tips.content !== 'undefined') {
                    v.showModal(v.tips);
                }
            });
        }, timeout);
    });

    window.addEventListener('message', function (event) {
        event.data.function += 'InParent';
        bsw.cnf.v.dispatcher(event.data, $('body')[0]);
    }, false)
});

// -- eof --