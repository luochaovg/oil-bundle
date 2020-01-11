'use strict';

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
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        opposeMap: { yes: 'no', no: 'yes' },
        formUrl: null,
        formMethod: null,

        theme: 'light',
        themeMap: { dark: 'light', light: 'dark' },
        weak: 'no',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true,
        ckEditor: {},

        no_loading_once: false,
        spinning: false,
        message: null, // from v-init
        configure: {}, // from v-init
        modal: {
            visible: false
        }

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        moment: moment,

        redirect: function redirect(data) {
            var url = data.location;
            if (bsw.isMobile() && this.mobileDefaultCollapsed) {
                bsw.cookie().set('bsw_menu_collapsed', 'yes');
            }
            if (url.startsWith('http') || url.startsWith('/')) {
                return location.href = url;
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
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                var _format = values[field][i]._f || that.filter_format[field];
                                values[field][i] = values[field][i].format(_format);
                            }
                        }
                    }
                }
                return _this[_this.formMethod + 'FilterForm'](values, jump);
            });
        },
        submitFilterForm: function submitFilterForm(values) {
            var jump = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

            var _values = {};
            var number = 0;
            for (var field in values) {
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

            var url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.setParams({ filter: _values }, url);

            if (jump) {
                location.href = url;
            } else {
                this.pagination(url);
            }
        },
        persistence: function persistence(event) {
            var _this2 = this;

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
                return _this2[_this2.formMethod + 'PersistenceForm'](values);
            });
        },
        submitPersistenceForm: function submitPersistenceForm(values) {
            var data = { submit: values };
            bsw.request(this.formUrl, data).then(function (res) {
                var params = bsw.parseQueryString();
                if (params.iframe) {
                    parent.postMessage({ response: res, function: 'handleResponse' }, '*');
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
                var map = (_map = {}, _defineProperty(_map, collect.field, 'attachment_id'), _defineProperty(_map, collect.md5, 'attachment_md5'), _defineProperty(_map, collect.sha1, 'attachment_sha1'), _map);
                for (var key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        if ($('#' + key).length === 0) {
                            continue;
                        }
                        this.persistence_form.setFieldsValue(_defineProperty({}, key, sets[map[key]]));
                    }
                }
            }

            if (typeof file.response.code === 'undefined' || file.response.code === 500) {
                this.spinning = false;
            }
            bsw.response(file.response).catch(function (reason) {
                console.warn(reason);
            });
        },
        showModal: function showModal(options) {
            options.visible = true;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            this.modal = Object.assign(this.modal, options);
        },
        showModalAfterRequest: function showModalAfterRequest(data, element) {
            var _this3 = this;

            bsw.request(data.location).then(function (res) {
                bsw.response(res).then(function () {
                    var sets = res.sets;
                    var logic = sets.logic || sets;
                    _this3.showModal({
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
                    that.preview_pagination_refresh();
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        multipleAction: function multipleAction(data, element) {
            var ids = this.preview_selected_row;
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
            var fill = $(element).prev().attr('id');
            data.location = bsw.setParams({ iframe: true, fill: fill }, data.location);

            var options = {
                visible: true,
                width: data.width || size.width,
                title: data.title || bsw.lang.please_select,
                centered: true,
                wrapClassName: 'bsw-preview-iframe',
                content: '<iframe id="bsw-preview-iframe" src="' + data.location + '"></iframe>'
            };
            this.showModal(options);
            this.$nextTick(function () {
                $("#bsw-preview-iframe").height(data.height || size.height);
            });
        },
        showIFrameByNative: function showIFrameByNative(element) {
            this.showIFrame(this.getBswData($(element)), element);
        },
        showIFrameByVue: function showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0]);
        },
        fillParentForm: function fillParentForm(data, element) {
            data.ids = this.preview_selected_row;
            if (data.ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },
        initCkEditor: function initCkEditor() {
            var that = this;
            $('.bsw-persistence .bsw-ck-editor').each(function () {
                var id = $(this).attr('id');
                ClassicEditor.create(this, {}).then(function (editor) {
                    that.ckEditor[id] = editor;
                    editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                        return new FileUploadAdapter(editor, loader, that.api_upload);
                    };
                    that.ckEditor[id].model.document.on('change:data', function () {
                        that.persistence_form.setFieldsValue(_defineProperty({}, id, that.ckEditor[id].getData()));
                    });
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
            this.persistence_form.setFieldsValue(_defineProperty({}, data.fill, data.ids.join(',')));
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

    }, bsw.config.directive || {})).component(Object.assign({

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

        // change captcha
        $('img.bsw-captcha').off('click').on('click', function () {
            var src = $(this).attr('src');
            src = bsw.setParams({ t: bsw.timestamp() }, src);
            $(this).attr('src', src);
        });

        v.$nextTick(function () {
            // logic
            for (var fn in bsw.config.logic || []) {
                if (!bsw.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                bsw.config.logic[fn](v);
            }
        });

        var timeout = change ? 800 : 100;
        setTimeout(function () {
            $('.bsw-page-loading').fadeOut(200, function () {
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
