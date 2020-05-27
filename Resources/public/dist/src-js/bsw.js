'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
// Copyright 2019
//

//
// Register global
//

window.bsw = new FoundationAntD({
    rsaPublicKey: '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----'
}, jQuery, Vue, antd, window.lang || {});

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
        formUrl: null,
        formMethod: null,

        theme: 'light',
        themeMap: { dark: 'light', light: 'dark' },
        weak: 'no',
        third_message: 'yes',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true,
        ckEditor: {},

        no_loading_once: false,
        spinning: false,
        configure: {}, // from v-init
        message: {}, // from v-init
        tips: {}, // from v-init
        modal: {
            visible: false
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
        dispatcher: function dispatcher(data, element) {
            var that = this;
            var action = function action() {
                var fn = data.function || 'console.log';
                that[fn](data, element);
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
            this.formUrl = data.location;
            this.formMethod = $(element).attr('bsw-method');
        },
        pagination: function pagination(url, page) {
            var that = this;
            if (page) {
                url = bsw.setParams({ page: page }, url);
            }
            if (that.preview_list.length === 0) {
                return location.href = url;
            }
            bsw.request(url).then(function (res) {
                bsw.response(res).then(function () {
                    that.preview_list = res.sets.preview.list;
                    that.preview_page_number = page;
                    that.preview_url = url;
                    that.preview_pagination_data = res.sets.preview.page;
                    that.preview_image_change();
                    history.replaceState({}, "", url);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        filter: function filter(event) {
            var _this = this;

            var jump = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

            var that = this;
            event.preventDefault();
            that.filter_form.validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        var format = values[field]._f || that.filter_format[field];
                        values[field] = values[field].format(format);
                        jump = true; // fix bug for ant-d
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                var _format = values[field][i]._f || that.filter_format[field];
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
                return _this[_this.formMethod + 'FilterForm'](_values, jump);
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
            if (jump) {
                return location.href = url;
            }
            this.pagination(url);
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
                        height: 800
                    };
                    data.location = bsw.setParams(res.sets, _this2.api_export);
                    _this2.showIFrame(data, $('body')[0]);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        persistence: function persistence(event) {
            var _this3 = this;

            var that = this;
            event.preventDefault();
            that.persistence_form.validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        var format = values[field]._f || that.persistence_format[field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                var _format2 = values[field][i]._f || that.persistence_format[field];
                                values[field][i] = values[field][i].format(_format2);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, field + '.fileList')) {
                        delete values[field];
                    }
                }
                return _this3[_this3.formMethod + 'PersistenceForm'](values);
            });
        },
        submitPersistenceForm: function submitPersistenceForm(values) {
            bsw.request(this.formUrl, { submit: values }).then(function (res) {
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

            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            var field = this.persistence_upload_field;
            var collect = this.persistence_file_list_key_collect[field];

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
                        if (this.persistence_form) {
                            this.persistence_form.setFieldsValue(_defineProperty({}, key, sets[map[key]]));
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
            var field = this.persistence_switch_field;
            var now = this.persistence_field_shape_now;
            var collect = this.persistence_field_shape_collect[field];
            for (var f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = collect[f].includes(value);
            }
        },
        showModal: function showModal(options) {
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
                        centered: true,
                        width: logic.width || data.width || bsw.popupCosySize().width,
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
                        that.preview_pagination_refresh();
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
            for (var i = 0; i < this.preview_selected_row.length; i++) {
                if (bsw.isString(this.preview_selected_row[i])) {
                    rows[i] = bsw.evalExpr(this.preview_selected_row[i]);
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
                visible: true,
                width: data.width || size.width,
                title: data.title === false ? data.title : data.title || bsw.lang.please_select,
                centered: true,
                wrapClassName: 'bsw-iframe-container',
                content: '<iframe id="bsw-iframe" src="' + data.location + '"></iframe>'
            };
            this.showModal(options);
            this.$nextTick(function () {
                $("#bsw-iframe").height(data.height || size.height);
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
            var json = this.persistence_form.getFieldValue(data.field);
            var url = bsw.setParams(_defineProperty({}, data.key, json), data.url);
            window.open(url);
        },
        initCkEditor: function initCkEditor() {
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
                        return new FileUploadAdapter(editor, loader, that.api_upload);
                    };
                    that.ckEditor[id].model.document.on('change:data', function () {
                        if (that.persistence_form) {
                            that.persistence_form.setFieldsValue(_defineProperty({}, id, that.ckEditor[id].getData()));
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
            this.modal.visible = false;
            if (this.persistence_form && data.repair) {
                this.persistence_form.setFieldsValue(_defineProperty({}, data.repair, data.ids));
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
        }
    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                vnode.context[binding.arg] = binding.value || binding.expression;
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
