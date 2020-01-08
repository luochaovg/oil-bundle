'use strict';

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
// Copyright 2019
//

//
// Register global
//

window.bsw = FoundationAntD;
window.app = new FoundationAntD({
    rsaPublicKey: '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----'
}, jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    app.vue('.app-body').template(app.config.template || null).data(Object.assign({

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

        no_loading_once: false,
        spinning: false,
        message: null, // from v-init
        configure: {}, // from v-init
        modal: {
            visible: false
        }

    }, app.config.data)).computed(Object.assign({}, app.config.computed || {})).method(Object.assign({

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
                return action();
            }
            app.cnf.v.$confirm({
                title: app.lang.confirm_title || 'Operation confirmation',
                content: data.confirm,
                cancelText: app.lang.cancel || 'Cancel',
                okText: app.lang.confirm || 'Confirm',
                width: 320,
                keyboard: false,
                onOk: function onOk() {
                    return action();
                }
            });
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
        pagination: function pagination(url, page, uuid) {
            var that = this;
            if (page) {
                url = bsw.setParams({ page: page }, url);
            }
            app.request(url).then(function (res) {
                app.response(res).then(function () {
                    that['list_' + uuid] = res.sets.preview.list;
                    that['page_' + uuid] = page;
                    that['url_' + uuid] = url;
                    that['page_data_' + uuid] = res.sets.preview.page;
                    that['image_change_table_' + uuid]();
                    history.replaceState({}, "", bsw.unsetParams(['uuid'], url));
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        filter: function filter(event, uuid) {
            var _this = this;

            var jump = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var that = this;
            var formatKey = 'date_format_' + uuid;
            event.preventDefault();
            that['form_filter_' + uuid].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[formatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[formatKey][field]);
                            }
                        }
                    }
                }
                return _this[_this.formMethod + 'FilterForm'](values, uuid, jump);
            });
        },
        submitFilterForm: function submitFilterForm(values, uuid) {
            var jump = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

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
                this.pagination(url, null, uuid);
            }
        },
        persistence: function persistence(event, uuid) {
            var _this2 = this;

            var that = this;
            var formatKey = 'date_format_' + uuid;
            event.preventDefault();
            that['form_persistence_' + uuid].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[formatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[formatKey][field]);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, field + '.fileList')) {
                        delete values[field];
                    }
                }
                return _this2[_this2.formMethod + 'PersistenceForm'](values, uuid);
            });
        },
        submitPersistenceForm: function submitPersistenceForm(values, uuid) {
            var data = { submit: values };
            app.request(this.formUrl, data).then(function (res) {
                app.response(res).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        uploaderChange: function uploaderChange(_ref) {
            var file = _ref.file,
                fileList = _ref.fileList;

            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            var keyMd5 = this.key_for_file_md5;
            var keySha1 = this.key_for_file_sha1;
            var keyList = this.key_for_file_list;

            if (!file.response) {
                this[keyList] = fileList;
                return;
            }
            if (file.response.error) {
                this[keyList] = fileList.slice(0, -1);
            }

            var files = this[keyList].slice(-1);
            if (files.length) {
                var _map;

                var sets = files[0].response.sets;
                var map = (_map = {}, _defineProperty(_map, keyMd5, 'attachment_md5'), _defineProperty(_map, keySha1, 'attachment_sha1'), _defineProperty(_map, keyList, 'attachment_id'), _map);
                for (var key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        var field = '' + key.split('_')[0];
                        if ($('#' + field).length === 0) {
                            continue;
                        }
                        this[this.key_for_form].setFieldsValue(_defineProperty({}, field, sets[map[key]]));
                    }
                }
            }
            app.response(file.response).catch(function (reason) {
                console.warn(reason);
            });
        },
        showModal: function showModal(options) {
            options.visible = true;
            this.modal = Object.assign(this.modal, options);
        },
        showModalAfterRequest: function showModalAfterRequest(data, element) {
            var _this3 = this;

            app.request(data.location).then(function (res) {
                app.response(res).then(function () {
                    var sets = res.sets;
                    var logic = sets.logic || sets;
                    _this3.showModal({
                        width: logic.width || 1000,
                        title: logic.title || 'Modal page',
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
            app.request(data.location).then(function (res) {
                app.response(res, function () {
                    that['pagination_refresh_' + data.uuid]();
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        multipleAction: function multipleAction(data, element) {
            var ids = this['selected_row_keys_' + data.uuid];
            if (ids.length === 0) {
                return app.warning(app.lang.select_item_first);
            }
            app.request(data.location, { ids: ids }).then(function (res) {
                app.response(res, function () {
                    console.log(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        showIFrameByVue: function showIFrameByVue(event) {
            var width = document.body.clientWidth;
            var height = document.body.clientHeight;
            width *= width < 1285 ? 1 : .7;
            height *= height < 666 ? .9 : .75;

            var object = $(event.target);
            var data = this.getBswData(object);
            data.location = bsw.setParams({ iframe: true, fill: object.prev().attr('id') }, data.location);

            var options = {
                visible: true,
                width: width,
                title: app.lang.please_select,
                centered: true,
                wrapClassName: 'app-preview-iframe',
                content: '<iframe id="app-preview-iframe" src="' + data.location + '"></iframe>'
            };
            this.showModal(options);
            this.$nextTick(function () {
                $("#app-preview-iframe").height(height);
            });
        },
        fillParentForm: function fillParentForm(data, element) {
            data.ids = this['selected_row_keys_' + data.uuid];
            if (data.ids.length === 0) {
                return app.warning(app.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },
        fillParentFormInParent: function fillParentFormInParent(data, element) {
            this.modal.visible = false;
            this[this.key_for_form].setFieldsValue(_defineProperty({}, data.fill, data.ids.join(',')));
        },
        initCkEditor: function initCkEditor() {
            $('.app-persistence .bsw-ck-editor').each(function () {
                ClassicEditor.create(this, {}).then(function (editor) {
                    window.editor = editor;
                    editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                        return new FileUploadAdapter(loader);
                    };
                }).catch(function (err) {
                    bsw.log(err.stack);
                });
            });
        }
    }, app.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                vnode.context[binding.arg] = binding.value || binding.expression;
            }
        }

    }, app.config.directive || {})).component(Object.assign({

        // component
        'b-icon': app.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').attr('bsw-value')
        })

    }, app.config.component || {})).init(function (v) {

        // change captcha
        $('img.app-captcha').off('click').on('click', function () {
            var src = $(this).attr('src');
            src = bsw.setParams({ t: bsw.timestamp() }, src);
            $(this).attr('src', src);
        });

        var duration = 100;
        setTimeout(function () {
            // logic
            for (var fn in app.config.logic || []) {
                if (!app.config.logic.hasOwnProperty(fn)) {
                    continue;
                }
                app.config.logic[fn](v);
            }
        }, duration);

        // page loading
        setTimeout(function () {
            // message
            $('.app-page-loading').fadeOut(200, function () {
                if (typeof v.message.content !== 'undefined') {
                    // notification message confirm
                    var _duration = v.message.duration ? v.message.duration : undefined;
                    try {
                        app[v.message.classify](v.message.content, _duration, null, v.message.type);
                    } catch (e) {
                        console.warn('Some error happen in source data of message');
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
    }, false);
});

// -- eof --
