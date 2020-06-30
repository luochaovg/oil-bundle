'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

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

        bsw: bsw,
        locale: bsw.d.locales[bsw.lang.i18n_ant],
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        opposeMap: { yes: 'no', no: 'yes' },
        submitFormUrl: null,
        submitFormMethod: null,

        theme: 'light',
        themeMap: { dark: 'light', light: 'dark' },
        weak: 'no',
        thirdMessage: 'yes',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true,
        ckEditor: {},

        noLoadingOnce: false,
        spinning: false,
        configure: {}, // from v-init
        message: {}, // from v-init
        tips: {}, // from v-init
        modal: {
            visible: false,
            centered: true
        }

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        moment: moment,

        redirect: function redirect(data) {
            if (data.function && data.function !== 'redirect') {
                return this.dispatcher(data, $('body'));
            }
            var url = data.location;
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
        getBswData: function getBswData(object) {
            return bsw.evalExpr(object.attr('bsw-data'));
        },
        redirectByVue: function redirectByVue(event) {
            this.redirect(this.getBswData($(event.item.$el).find('span')));
        },
        tabsLinksSwitch: function tabsLinksSwitch(key) {
            this.redirect(this.getBswData($('#tabs_link_' + key)));
        },
        dispatcher: function dispatcher(data, element) {
            var that = this;
            var action = function action() {
                if (data.function.length === 0) {
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
        },
        setUrlToForm: function setUrlToForm(data, element) {
            this.submitFormUrl = data.location;
            this.submitFormMethod = $(element).attr('bsw-method');
        },
        previewGetUrl: function previewGetUrl(url) {
            var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

            url = url || this.previewUrl;
            return bsw.setParams(Object.assign({ page: this.previewPageNumber }, params), url);
        },
        previewPaginationRefresh: function previewPaginationRefresh(jump) {
            this.noLoadingOnce = true;
            this.pagination(this.previewGetUrl(), null, jump);
        },
        previewImageChange: function previewImageChange() {
            var that = this;
            var doChecker = setInterval(function () {
                return checker();
            }, 50);
            var checker = function checker() {
                var img = $('img');
                var done = 0;
                img.each(function () {
                    done += this.complete ? 1 : 0;
                });
                var tmp = that.previewColumns[0].fixed;
                that.previewColumns[0].fixed = !tmp;
                that.previewColumns[0].fixed = tmp;
                if (done >= img.length || img.length === 0) {
                    clearInterval(doChecker);
                }
            };
        },
        pagination: function pagination(url, page) {
            var jump = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var that = this;
            if (page) {
                url = bsw.setParams({ page: page }, url);
            }
            if (jump || typeof that.previewList === 'undefined' || that.previewList.length === 0) {
                return location.href = url;
            }
            bsw.request(url).then(function (res) {
                bsw.response(res).then(function () {
                    that.previewList = res.sets.preview.list;
                    that.previewPageNumber = page;
                    that.previewUrl = url;
                    that.previewPaginationData = res.sets.preview.page;
                    that.previewImageChange();
                    history.replaceState({}, "", url);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        filterFormAction: function filterFormAction(event) {
            var jump = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

            var _this = this;

            var form = arguments[2];
            var dateFormat = arguments[3];

            var that = this;
            event.preventDefault();
            that[form].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        var format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                        jump = true; // fix bug for ant-d
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                var _format = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(_format);
                                jump = true; // fix bug for ant-d
                            }
                        }
                    }
                }
                var _values = {};
                for (var _field in values) {
                    if (!values.hasOwnProperty(_field)) {
                        continue;
                    }
                    if (typeof values[_field] === 'undefined') {
                        continue;
                    }
                    if (values[_field] == null) {
                        continue;
                    }
                    if (values[_field].length === 0) {
                        continue;
                    }
                    _values[_field] = values[_field];
                }
                return _this[_this.submitFormMethod + 'FilterForm'](_values, jump);
            });
        },
        searchFilterForm: function searchFilterForm(values) {
            var jump = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

            var effect = {};
            var url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url, false, effect);
            url = bsw.setParams({ filter: values }, url);
            if (_typeof(effect.page) && effect.page > 1) {
                jump = true;
            }
            this.pagination(url, null, jump);
        },
        exportFilterForm: function exportFilterForm(values) {
            var _this2 = this;

            var url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url);
            url = bsw.setParams({ filter: values, scene: 'export' }, url);

            bsw.request(url).then(function (res) {
                bsw.response(res).then(function () {
                    var data = {
                        title: bsw.lang.export_mission,
                        width: 768,
                        height: 700
                    };
                    data.location = bsw.setParams(res.sets, _this2.exportApiUrl, true);
                    _this2.showIFrame(data, $('body')[0]);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        submitFormAction: function submitFormAction(event, form, dateFormat) {
            var _this3 = this;

            var that = this;
            event.preventDefault();
            that[form].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        var format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                var _format2 = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(_format2);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, field + '.fileList')) {
                        delete values[field];
                    }
                }
                return _this3[_this3.submitFormMethod + 'PersistenceForm'](values);
            });
        },
        submitPersistenceForm: function submitPersistenceForm(values) {
            bsw.request(this.submitFormUrl, { submit: values }).then(function (res) {
                var params = bsw.parseQueryString();
                if (params.iframe) {
                    res.sets.arguments = bsw.parseQueryString();
                    var fn = res.sets.function || 'handleResponse';
                    parent.postMessage({ response: res, function: fn }, '*');
                } else {
                    bsw.response(res).catch(function (reason) {
                        console.warn(reason);
                    });
                }
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        uploaderChange: function uploaderChange(_ref) {
            var file = _ref.file,
                fileList = _ref.fileList,
                event = _ref.event;
            var form = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'persistenceForm';

            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            var field = this.persistenceUploadField;
            var collect = this.persistenceFileListKeyCollect[field];

            if (!file.response) {
                collect.list = fileList;
                return;
            }
            if (file.response.error) {
                collect.list = fileList.slice(0, -1);
            }

            var files = collect.list.slice(-1);
            if (files.length) {
                var _map;

                var sets = files[0].response.sets;
                var map = (_map = {}, _defineProperty(_map, collect.id, 'attachment_id'), _defineProperty(_map, collect.md5, 'attachment_md5'), _defineProperty(_map, collect.sha1, 'attachment_sha1'), _defineProperty(_map, collect.url, 'attachment_url'), _map);
                for (var key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        if ($('#' + key).length === 0) {
                            continue;
                        }
                        if (this[form]) {
                            this[form].setFieldsValue(_defineProperty({}, key, sets[map[key]]));
                        }
                    }
                }
            }

            if (typeof file.response.code === 'undefined' || file.response.code === 500) {
                this.spinning = false;
            }

            if (file.response.sets.href) {
                var fn = file.response.sets.function || 'handleResponse';
                parent.postMessage({ response: file.response, function: fn }, '*');
            } else {
                bsw.response(file.response).catch(function (reason) {
                    console.warn(reason);
                });
            }
        },
        switchFieldShapeWithSelect: function switchFieldShapeWithSelect(value, option) {
            var field = this.persistenceSwitchField;
            var now = this.persistenceFieldShapeNow;
            var collect = this.persistenceFieldShapeCollect[field];
            for (var f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = collect[f].includes(value);
            }
        },
        formItemFilterOption: function formItemFilterOption(input, option) {
            return option.componentOptions.children[0].text.toUpperCase().indexOf(input.toUpperCase()) >= 0;
        },
        showModal: function showModal(options) {
            this.modal.visible = false;
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            this.modal = Object.assign(this.modal, options);
        },
        showModalAfterRequest: function showModalAfterRequest(data, element) {
            var _this4 = this;

            bsw.request(data.location).then(function (res) {
                bsw.response(res).then(function () {
                    var sets = res.sets;
                    var logic = sets.logic || sets;
                    _this4.showModal({
                        width: logic.width || data.width || undefined,
                        title: logic.title || data.title || bsw.lang.modal_title,
                        content: sets.content
                    });
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        requestByAjax: function requestByAjax(data, element) {
            var that = this;
            bsw.request(data.location).then(function (res) {
                bsw.response(res).then(function () {
                    if (typeof data.refresh !== 'undefined' && data.refresh) {
                        that.previewPaginationRefresh(false);
                    }
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        selectedRowHandler: function selectedRowHandler(field) {
            var rows = [];
            for (var i = 0; i < this.previewSelectedRow.length; i++) {
                if (bsw.isString(this.previewSelectedRow[i])) {
                    rows[i] = bsw.evalExpr(this.previewSelectedRow[i]);
                    if (field) {
                        rows[i] = rows[i][field] || null;
                    }
                }
            }
            return rows;
        },
        multipleAction: function multipleAction(data, element) {
            var ids = this.selectedRowHandler();
            if (ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            bsw.request(data.location, { ids: ids }).then(function (res) {
                bsw.response(res).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        showIFrame: function showIFrame(data, element) {
            var size = bsw.popupCosySize();
            var repair = $(element).prev().attr('id');
            data.location = bsw.setParams({ iframe: true, repair: repair }, data.location);

            var options = {
                width: data.width || size.width,
                title: data.title === false ? data.title : data.title || bsw.lang.please_select,
                content: '<iframe id="bsw-iframe" src="' + data.location + '"></iframe>'
            };
            this.showModal(options);
            this.$nextTick(function () {
                var iframe = $("#bsw-iframe");
                iframe.height(data.height || size.height);
                iframe.parents("div.ant-modal-body").css({ margin: 0, padding: 0 });
            });
        },
        showIFrameWithChecked: function showIFrameWithChecked(data, element) {
            var ids = this.selectedRowHandler(data.selector).join(',');
            var args = { ids: ids };
            if (typeof data.form !== "undefined") {
                var key = 'fill[' + data.form + ']';
                args = _defineProperty({}, key, ids);
            }
            data.location = bsw.setParams(args, data.location);
            this.showIFrame(data, element);
        },
        showIFrameByNative: function showIFrameByNative(element) {
            this.showIFrame(this.getBswData($(element)), element);
        },
        showIFrameByVue: function showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0]);
        },
        fillParentForm: function fillParentForm(data, element) {
            data.ids = this.selectedRowHandler(data.selector).join(',');
            if (data.ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },
        verifyJsonFormat: function verifyJsonFormat(data, element) {
            var form = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'persistenceForm';

            var json = this[form].getFieldValue(data.field);
            var url = bsw.setParams(_defineProperty({}, data.key, json), data.url);
            window.open(url);
        },
        initCkEditor: function initCkEditor() {
            var form = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'persistenceForm';

            var that = this;
            $('.bsw-persistence .bsw-ck').each(function () {
                var em = this;
                var id = $(em).prev('textarea').attr('id');
                var container = $(em).find('.bsw-ck-editor');
                DecoupledEditor.create(container[0], {
                    language: bsw.lang.i18n_editor,
                    placeholder: $(em).attr('placeholder')
                }).then(function (editor) {
                    that.ckEditor[id] = editor;
                    editor.isReadOnly = $(em).attr('disabled') === 'disabled';
                    editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                        return new FileUploadAdapter(editor, loader, that.uploadApiUrl);
                    };
                    that.ckEditor[id].model.document.on('change:data', function () {
                        if (that[form]) {
                            that[form].setFieldsValue(_defineProperty({}, id, that.ckEditor[id].getData()));
                        }
                    });
                    $(em).find('.bsw-ck-toolbar').append(editor.ui.view.toolbar.element);
                }).catch(function (err) {
                    console.warn(err.stack);
                });
            });
        },


        //
        // for iframe exec in parent
        //

        fillParentFormInParent: function fillParentFormInParent(data, element) {
            var form = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'persistenceForm';

            this.modal.visible = false;
            if (this[form] && data.repair) {
                this[form].setFieldsValue(_defineProperty({}, data.repair, data.ids));
            }
        },
        fillParentFormAfterAjaxInParent: function fillParentFormAfterAjaxInParent(res, element) {
            var data = res.response.sets;
            data.repair = data.arguments.repair;
            this.fillParentFormInParent(data, element);
        },
        handleResponseInParent: function handleResponseInParent(data, element) {
            this.modal.visible = false;
            bsw.response(data.response).catch(function (reason) {
                console.warn(reason);
            });
        },
        showIFrameInParent: function showIFrameInParent(data, element) {
            this.showIFrame(data.response.sets, element);
        },
        refreshPreviewInParent: function refreshPreviewInParent(data, element) {
            this.handleResponseInParent(data, element);
            this.previewPaginationRefresh(false);
        }
    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                var key = bsw.smallHump(binding.arg);
                vnode.context[key] = binding.value || binding.expression;
            }
        }

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
        })

    }, bsw.config.component || {})).init(function (v) {

        var change = false;
        if (v.scaffoldInit) {
            change = v.scaffoldInit();
        }

        v.$nextTick(function () {
            // logic
            for (var fn in bsw.config.logic || []) {
                if (!bsw.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                bsw.config.logic[fn](v);
            }
        });

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            var src = $(this).attr('src');
            src = bsw.setParams({ t: bsw.timestamp() }, src);
            $(this).attr('src', src);
        });

        var timeout = change ? 1000 : 400;
        setTimeout(function () {
            // resize
            $(window).resize();
            $('.bsw-page-loading').fadeOut(300, function () {
                if (typeof v.message.content !== 'undefined') {
                    // notification message confirm
                    var duration = bsw.isNull(v.message.duration) ? undefined : v.message.duration;
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
    }, false);
});

// -- eof --
