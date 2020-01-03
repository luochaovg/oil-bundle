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
}, jQuery, Vue, antd);

//
// Init
//

$(function () {

    // vue
    app.vue('.app-body').template(app.config.template || null).data(Object.assign({

        bsw: bsw,
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        opposeMap: { 'yes': 'no', 'no': 'yes' },
        formUrl: null,
        formMethod: null,

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
        dispatcherByNative: function dispatcherByNative(element) {
            var data = this.getBswData($(element));
            var fn = data.function || 'console.log';
            this[fn](data, element);
        },
        dispatcherByVue: function dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0]);
        },
        setUrlToForm: function setUrlToForm(data, element) {
            this.formUrl = data.location;
            this.formMethod = $(element).attr('bsw-method');
        },
        pagination: function pagination(api, pageNumber, dataListKey, imageChangeTable) {
            var that = this;
            var uuid = bsw.parseQueryString(api).uuid;
            if (pageNumber) {
                api = bsw.setParams({ page: pageNumber }, api);
            }
            app.request(api).then(function (res) {
                app.response(res).then(function () {
                    that[dataListKey] = res.sets.preview.list;
                    that['page_' + uuid] = pageNumber;
                    imageChangeTable && that[imageChangeTable]();
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        filter: function filter(event, formFilterKey, dateFormatKey) {
            var _this = this;

            var that = this;
            event.preventDefault();
            that[formFilterKey].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[dateFormatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[dateFormatKey][field]);
                            }
                        }
                    }
                }
                return _this[_this.formMethod + 'FilterForm'](values);
            });
        },
        submitFilterForm: function submitFilterForm(values) {
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
            location.href = bsw.setParams({ filter: _values }, url, false);
        },
        persistence: function persistence(event, formPersistenceKey, dateFormatKey) {
            var _this2 = this;

            var that = this;
            event.preventDefault();
            that[formPersistenceKey].validateFields(function (err, values) {
                if (err) {
                    return false;
                }
                // logic
                for (var field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        values[field] = values[field].format(values[field]._f || that[dateFormatKey][field]);
                    }
                    if (bsw.isArray(values[field])) {
                        for (var i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                values[field][i] = values[field][i].format(values[field][i]._f || that[dateFormatKey][field]);
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

            var keyMd5 = this.keyForFileMd5;
            var keySha1 = this.keyForFileSha1;
            var keyList = this.keyForFileList;
            var form = this.keyForForm;

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
                        this[form].setFieldsValue(_defineProperty({}, field, sets[map[key]]));
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

            app.request(data.api).then(function (res) {
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
        showIFrameByVue: function showIFrameByVue(event) {
            var element = event.target;
            var url = $(element).attr('bsw-url');

            var options = {
                visible: true,
                width: 1200,
                title: '请选择',
                content: '123<iframe src="' + url + '"></iframe>456'
            };

            console.log(options);
            this.showModal(options);
        },
        multipleAction: function multipleAction(event) {
            console.log(event);
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
});

// -- eof --
