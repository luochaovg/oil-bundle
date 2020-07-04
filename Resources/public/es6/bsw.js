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
    bsw.vue('.bsw-body').template(bsw.config.template || null).data(Object.assign({

        bsw,
        locale: bsw.d.locales[bsw.lang.i18n_ant],
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        opposeMap: {yes: 'no', no: 'yes'},
        submitFormUrl: null,
        submitFormMethod: null,

        theme: 'light',
        themeMap: {dark: 'light', light: 'dark'},
        weak: 'no',
        thirdMessage: 'yes',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true,
        ckEditor: {},

        noLoadingOnce: false,
        spinning: false,
        configure: {},  // from v-init
        message: {},  // from v-init
        tips: {}, // from v-init
        modal: {
            visible: false,
            centered: true,
        },
        footer: 'footer',
        drawer: {
            visible: false,
        },

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        moment,

        redirect(data) {
            if (data.function && data.function !== 'redirect') {
                return this.dispatcher(data, $('body'));
            }
            let url = data.location;
            if (bsw.isMobile() && this.mobileDefaultCollapsed) {
                bsw.cookie().set('bsw_menu_collapsed', 'yes');
            }
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

        tabsLinksSwitch(key) {
            this.redirect(this.getBswData($(`#tabs_link_${key}`)));
        },

        dispatcher(data, element) {
            let that = this;
            if (data.iframe) {
                delete data.iframe;
                parent.postMessage({data, function: 'dispatcher'}, '*');
                return;
            }
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

        setUrlToForm(data, element) {
            this.submitFormUrl = data.location;
            this.submitFormMethod = $(element).attr('bsw-method');
        },

        previewGetUrl(url, params = {}) {
            url = url || this.previewUrl;
            return bsw.setParams(Object.assign({page: this.previewPageNumber}, params), url);
        },

        previewPaginationRefresh(jump) {
            this.noLoadingOnce = true;
            this.pagination(this.previewGetUrl(), null, jump);
        },

        previewImageChange() {
            let that = this;
            let doChecker = setInterval(() => checker(), 50);
            let checker = function () {
                let img = $('img');
                let done = 0;
                img.each(function () {
                    done += (this.complete ? 1 : 0);
                });
                let tmp = that.previewColumns[0].fixed;
                that.previewColumns[0].fixed = !tmp;
                that.previewColumns[0].fixed = tmp;
                if ((done >= img.length) || img.length === 0) {
                    clearInterval(doChecker);
                }
            }
        },

        pagination(url, page, jump = false) {
            let that = this;
            if (page) {
                url = bsw.setParams({page}, url);
            }
            if (jump || typeof that.previewList === 'undefined' || that.previewList.length === 0) {
                return location.href = url;
            }
            bsw.request(url).then((res) => {
                bsw.response(res).then(() => {
                    that.previewList = res.sets.preview.list;
                    that.previewPageNumber = page;
                    that.previewUrl = url;
                    that.previewPaginationData = res.sets.preview.page;
                    that.previewImageChange();
                    history.replaceState({}, "", url);
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        filterFormAction(event, jump = false, form, dateFormat) {
            let that = this;
            event.preventDefault();
            that[form].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                        jump = true; // fix bug for ant-d
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(format);
                                jump = true; // fix bug for ant-d
                            }
                        }
                    }
                }
                let _values = {};
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
                }
                return this[`${this.submitFormMethod}FilterForm`](_values, jump);
            });
        },

        searchFilterForm(values, jump = false) {
            let effect = {};
            let url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url, false, effect);
            url = bsw.setParams({filter: values}, url);
            if (typeof effect.page && effect.page > 1) {
                jump = true;
            }
            this.pagination(url, null, jump);
        },

        exportFilterForm(values) {
            let url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url);
            url = bsw.setParams({filter: values, scene: 'export'}, url);

            bsw.request(url).then((res) => {
                bsw.response(res).then(() => {
                    let data = {
                        title: bsw.lang.export_mission,
                        width: 768,
                        height: 700,
                    };
                    data.location = bsw.setParams(res.sets, this.exportApiUrl, true);
                    this.showIFrame(data, $('body')[0]);
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        submitFormAction(event, form, dateFormat) {
            let that = this;
            event.preventDefault();
            that[form].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(format);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, `${field}.fileList`)) {
                        delete values[field];
                    }
                }
                return this[`${this.submitFormMethod}PersistenceForm`](values);
            });
        },

        submitPersistenceForm(values) {
            bsw.request(this.submitFormUrl, {submit: values}).then((res) => {
                let params = bsw.parseQueryString();
                if (params.iframe) {
                    res.sets.arguments = bsw.parseQueryString();
                    let fn = res.sets.function || 'handleResponse';
                    parent.postMessage({response: res, function: fn}, '*');
                } else {
                    bsw.response(res).catch((reason => {
                        console.warn(reason);
                    }));
                }
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        uploaderChange({file, fileList, event}, form = 'persistenceForm') {
            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            let field = this.persistenceUploadField;
            let collect = this.persistenceFileListKeyCollect[field];

            if (!file.response) {
                collect.list = fileList;
                return;
            }
            if (file.response.error) {
                collect.list = fileList.slice(0, -1);
            }

            let files = collect.list.slice(-1);
            if (files.length) {
                let sets = files[0].response.sets;
                let map = {
                    [collect.id]: 'attachment_id',
                    [collect.md5]: 'attachment_md5',
                    [collect.sha1]: 'attachment_sha1',
                    [collect.url]: 'attachment_url',
                };
                for (let key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        if ($(`#${key}`).length === 0) {
                            continue;
                        }
                        if (this[form]) {
                            this[form].setFieldsValue({[key]: sets[map[key]]});
                        }
                    }
                }
            }

            if (typeof file.response.code === 'undefined' || file.response.code === 500) {
                this.spinning = false;
            }

            if (file.response.sets.href) {
                let fn = file.response.sets.function || 'handleResponse';
                parent.postMessage({response: file.response, function: fn}, '*');
            } else {
                bsw.response(file.response).catch((reason => {
                    console.warn(reason);
                }));
            }
        },

        switchFieldShapeWithSelect(value, option) {
            let field = this.persistenceSwitchField;
            let now = this.persistenceFieldShapeNow;
            let collect = this.persistenceFieldShapeCollect[field];
            for (let f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = (collect[f].includes(value));
            }
        },

        formItemFilterOption(input, option) {
            return option.componentOptions.children[0].text.toUpperCase().indexOf(input.toUpperCase()) >= 0;
        },

        showModal(options) {
            this.modal.visible = false;
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            options = Object.assign(this.modal, options);
            if (options.footer) {
                this.footer = '_footer';
            } else {
                this.footer = 'footer';
            }
            this.modal = options;
        },

        showDrawer(options) {
            this.drawer.visible = false;
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            options = Object.assign(this.drawer, options);
            this.drawer = options;
        },

        executeMethod(element, fn) {
            if (!element.length) {
                return;
            }
            let data = element[0].dataBsw;
            if (data[fn]) {
                if (typeof this[data[fn]] === 'undefined') {
                    return console.error(`Method ${data[fn]} is undefined.`, data);
                }
                this[data[fn]](data, event);
            }
        },

        modalOnOk(event) {
            this.modal.visible = false;
            if (!event) {
                return;
            }
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            this.executeMethod(element, 'ok');
        },

        modalOnCancel(event) {
            this.modal.visible = false;
            if (!event) {
                return;
            }
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            this.executeMethod(element, 'cancel');
        },

        drawerOnOk(event) {
            this.drawer.visible = false;
            if (!event) {
                return;
            }
            let element = $(event.target).parents('.bsw-footer-bar');
            this.executeMethod(element, 'ok');
        },

        drawerOnCancel(event) {
            this.drawer.visible = false;
            if (!event) {
                return;
            }
            let element = $(event.target).parents('.bsw-footer-bar');
            this.executeMethod(element, 'cancel');
        },

        showModalAfterRequest(data, element) {
            bsw.request(data.location).then((res) => {
                bsw.response(res).then(() => {
                    let options = bsw.jsonFilter(Object.assign(data, {
                        width: res.sets.width || data.width || undefined,
                        title: res.sets.title || data.title || bsw.lang.modal_title,
                        content: res.sets.content,
                    }));
                    this.showModal(options);
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
                    if (typeof data.refresh !== 'undefined' && data.refresh) {
                        that.previewPaginationRefresh(false);
                    }
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        selectedRowHandler(field) {
            let rows = [];
            for (let i = 0; i < this.previewSelectedRow.length; i++) {
                if (bsw.isString(this.previewSelectedRow[i])) {
                    rows[i] = bsw.evalExpr(this.previewSelectedRow[i]);
                    if (field) {
                        rows[i] = rows[i][field] || null;
                    }
                }
            }
            return rows;
        },

        multipleAction(data, element) {
            let ids = this.selectedRowHandler();
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
            let repair = $(element).prev().attr('id');
            data.location = bsw.setParams({iframe: true, repair}, data.location);

            let options = bsw.jsonFilter(Object.assign(data, {
                width: data.width || size.width,
                title: data.title === false ? data.title : (data.title || bsw.lang.please_select),
                content: `<iframe id="bsw-iframe" src="${data.location}"></iframe>`,
            }));

            let mode = data.shape || 'modal';
            if (mode === 'drawer') {
                this.showDrawer(options);
                this.$nextTick(function () {
                    let iframe = $("#bsw-iframe");
                    let footerHeight = options.footer ? 73 : 0;
                    iframe.height(bsw.popupCosySize(true).height - footerHeight - 55);
                    iframe.parents("div.ant-drawer-body").css({margin: 0, padding: 0});
                });
            } else {
                this.showModal(options);
                this.$nextTick(function () {
                    let iframe = $("#bsw-iframe");
                    iframe.height(data.height || size.height);
                    iframe.parents("div.ant-modal-body").css({margin: 0, padding: 0});
                });
            }
        },

        showIFrameWithChecked(data, element) {
            let ids = this.selectedRowHandler(data.selector).join(',');
            let args = {ids};
            if (typeof data.form !== "undefined") {
                let key = `fill[${data.form}]`;
                args = {[key]: ids};
            }
            data.location = bsw.setParams(args, data.location);
            this.showIFrame(data, element);
        },

        showIFrameByNative(element) {
            this.showIFrame(this.getBswData($(element)), element);
        },

        showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0])
        },

        fillParentForm(data, element) {
            data.ids = this.selectedRowHandler(data.selector).join(',');
            if (data.ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },

        verifyJsonFormat(data, element, form = 'persistenceForm') {
            let json = this[form].getFieldValue(data.field);
            let url = bsw.setParams({[data.key]: json}, data.url);
            window.open(url);
        },

        initCkEditor(form = 'persistenceForm') {
            let that = this;
            $('.bsw-persistence .bsw-ck').each(function () {
                let em = this;
                let id = $(em).prev('textarea').attr('id');
                let container = $(em).find('.bsw-ck-editor');
                DecoupledEditor.create(container[0], {
                    language: bsw.lang.i18n_editor,
                    placeholder: $(em).attr('placeholder'),
                }).then(editor => {
                    that.ckEditor[id] = editor;
                    editor.isReadOnly = $(em).attr('disabled') === 'disabled';
                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new FileUploadAdapter(editor, loader, that.uploadApiUrl);
                    };
                    that.ckEditor[id].model.document.on('change:data', function () {
                        if (that[form]) {
                            that[form].setFieldsValue({[id]: that.ckEditor[id].getData()});
                        }
                    });
                    $(em).find('.bsw-ck-toolbar').append(editor.ui.view.toolbar.element);
                }).catch(err => {
                    console.warn(err.stack);
                });
            });
        },

        //
        // for iframe exec in parent
        //

        dispatcherInParent(data, element) {
            this.modalOnCancel();
            this.drawerOnCancel();
            this.$nextTick(function () {
                if (typeof data.data.location !== 'undefined') {
                    data.data.location = bsw.unsetParams(['iframe'], data.data.location);
                }
                this.dispatcher(data.data, element);
            });
        },

        fillParentFormInParent(data, element, form = 'persistenceForm') {
            this.modalOnCancel();
            this.drawerOnCancel();
            this.$nextTick(function () {
                if (this[form] && data.repair) {
                    this[form].setFieldsValue({[data.repair]: data.ids});
                }
            });
        },

        fillParentFormAfterAjaxInParent(res, element) {
            let data = res.response.sets;
            data.repair = data.arguments.repair;
            this.fillParentFormInParent(data, element);
        },

        handleResponseInParent(data, element) {
            this.modalOnCancel();
            this.drawerOnCancel();
            this.$nextTick(function () {
                bsw.response(data.response).catch((reason => {
                    console.warn(reason);
                }));
            });
        },

        showIFrameInParent(data, element) {
            this.showIFrame(data.response.sets, element);
        },

        refreshPreviewInParent(data, element) {
            this.handleResponseInParent(data, element);
            this.previewPaginationRefresh(false);
        },

    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                let key = bsw.smallHump(binding.arg);
                vnode.context[key] = (binding.value || binding.expression);
            }
        },

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        }),

    }, bsw.config.component || {})).init(function (v) {

        let change = false;
        if (v.scaffoldInit) {
            change = v.scaffoldInit();
        }

        v.$nextTick(function () {
            // logic
            for (let fn in bsw.config.logic || []) {
                if (!bsw.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                bsw.config.logic[fn](v);
            }
        });

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            let src = $(this).attr('src');
            src = bsw.setParams({t: bsw.timestamp()}, src);
            $(this).attr('src', src);
        });

        let timeout = change ? 1000 : 400;
        setTimeout(function () {
            // resize
            $(window).resize();
            $('.bsw-page-loading').fadeOut(300, function () {
                if (typeof v.message.content !== 'undefined') {
                    // notification message confirm
                    let duration = bsw.isNull(v.message.duration) ? undefined : v.message.duration;
                    try {
                        let a = v.message.content;
                        bsw[v.message.classify](bsw.base64Decode(v.message.content), duration, null, v.message.type);
                    } catch (e) {
                        console.warn(bsw.lang.message_data_error, v.message);
                        console.warn(e);
                    }
                }
                // tips
                if (typeof v.tips.content !== 'undefined') {
                    let map = ['title', 'content'];
                    for (let i = 0; i < map.length; i++) {
                        if (typeof v.tips[map[i]] !== 'undefined') {
                            v.tips[map[i]] = bsw.base64Decode(v.tips[map[i]]);
                        }
                    }
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