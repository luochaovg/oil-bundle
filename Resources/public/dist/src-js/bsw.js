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
    bsw.vue().template(bsw.config.template || null).data(Object.assign({

        bsw: bsw,
        locale: bsw.d.locales[bsw.lang.i18n_ant],
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        submitFormUrl: null,
        submitFormMethod: null,
        ckEditor: {},
        loadTimes: 1,
        noLoadingOnce: false,
        spinning: false,
        vConsole: null,
        init: { // from v-init
            configure: {},
            message: {},
            modal: {},
            result: {}
        },
        footer: 'footer',
        modal: {},
        modalMeta: {
            visible: true,
            centered: true
        },
        drawer: {},
        drawerMeta: {
            visible: true
        },
        result: {},
        resultMeta: {
            visible: true
        }

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        moment: moment,

        redirectByVue: function redirectByVue(event) {
            bsw.redirect(bsw.getBswData($(event.item.$el).find('span')));
        },
        dispatcherByNative: function dispatcherByNative(element) {
            bsw.dispatcherByBswData(bsw.getBswData($(element)), element);
        },
        dispatcherByVue: function dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0]);
        },
        showIFrameByNative: function showIFrameByNative(element) {
            bsw.showIFrame(bsw.getBswData($(element)), element);
        },
        showIFrameByVue: function showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0]);
        },
        tabsLinksSwitch: function tabsLinksSwitch(key) {
            bsw.redirect(bsw.getBswData($('#tabs_link_' + key)));
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
            var that = this;
            var ids = this.selectedRowHandler();
            if (ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            bsw.request(data.location, { ids: ids }).then(function (res) {
                bsw.response(res).then(function () {
                    bsw.responseLogic(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
                if (typeof data.refresh !== 'undefined' && data.refresh) {
                    that.previewPaginationRefresh(false);
                }
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        showIFrameWithChecked: function showIFrameWithChecked(data, element) {
            var ids = this.selectedRowHandler(data.selector).join(',');
            var args = { ids: ids };
            if (typeof data.form !== 'undefined') {
                var key = 'fill[' + data.form + ']';
                args = _defineProperty({}, key, ids);
            }
            data.location = bsw.setParams(args, data.location);
            bsw.showIFrame(data, element);
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
            if (that.previewColumns.length === 0) {
                return;
            }
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
                bsw.response(res).then(function (a) {
                    that.previewList = res.sets.preview.list;
                    that.previewPageNumber = page;
                    that.previewUrl = url;
                    that.previewPaginationData = res.sets.preview.page;
                    that.previewImageChange();
                    bsw.responseLogic(res);
                    history.replaceState({}, '', url);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        filterFormAction: function filterFormAction(event) {
            var jump = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
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
                return that[that.submitFormMethod + 'FilterForm'](_values, jump);
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
            var that = this;
            var url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url);
            url = bsw.setParams({ filter: values, scene: 'export' }, url);

            bsw.request(url).then(function (res) {
                bsw.response(res).then(function () {
                    var data = {
                        title: bsw.lang.export_mission,
                        width: 500,
                        height: 385
                    };
                    data.location = bsw.setParams(res.sets, that.init.exportApiUrl, true);
                    bsw.showIFrame(data, $('body')[0]);
                    bsw.responseLogic(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        submitFormAction: function submitFormAction(event, form, dateFormat) {
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
                return that[that.submitFormMethod + 'PersistenceForm'](values);
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
                    bsw.response(res).then(function () {
                        bsw.responseLogic(res);
                    }).catch(function (reason) {
                        console.warn(reason);
                    });
                }
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        uploaderChange: function uploaderChange(_ref, field) {
            var file = _ref.file,
                fileList = _ref.fileList;
            var form = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'persistenceForm';

            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

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
                bsw.response(file.response).then(function () {
                    bsw.responseLogic(file.response);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }
        },
        changeTriggerHideForInput: function changeTriggerHideForInput(event, field) {
            this.changeTriggerHide(event.target.value, {}, field);
        },
        changeTriggerHideForSelect: function changeTriggerHideForSelect(value, option, field) {
            this.changeTriggerHide(value, field);
        },
        changeTriggerHide: function changeTriggerHide(value, field) {
            var now = this.persistenceFieldHideNow;
            var collect = this.persistenceFieldHideCollect[field];
            for (var f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = collect[f].includes(value.toString());
            }
        },
        changeTriggerDisabledForInput: function changeTriggerDisabledForInput(event, field) {
            this.changeTriggerDisabled(event.target.value, {}, field);
        },
        changeTriggerDisabledForSelect: function changeTriggerDisabledForSelect(value, option, field) {
            this.changeTriggerDisabled(value, field);
        },
        changeTriggerDisabled: function changeTriggerDisabled(value, field) {
            var now = this.persistenceFieldDisabledNow;
            var collect = this.persistenceFieldDisabledCollect[field];
            for (var f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = collect[f].includes(value.toString());
            }
        },
        requestByAjax: function requestByAjax(data, element) {
            var that = this;
            bsw.request(data.location).then(function (res) {
                bsw.response(res).then(function () {
                    bsw.responseLogic(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
                if (typeof data.refresh !== 'undefined' && data.refresh) {
                    that.previewPaginationRefresh(false);
                }
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        dynamicDataSource: function dynamicDataSource() {
            var that = this;
            if (typeof this.ddsCore === 'undefined') {
                this.ddsCore = bsw.debounce(200, function (args) {
                    var item = $('#' + args[args.length - 1]);
                    var ddsApi = item.data('dds-api');
                    var ddsMeta = item.data('dds-meta');
                    that.noLoadingOnce = true;
                    bsw.request(ddsApi, args).then(function (res) {
                        bsw.response(res).then(function () {
                            if (ddsMeta && res.sets) {
                                bsw.setJsonDeep(that, ddsMeta, res.sets);
                            }
                            bsw.responseLogic(res);
                        }).catch(function (reason) {
                            console.warn(reason);
                        });
                    }).catch(function (reason) {
                        console.warn(reason);
                    });
                });
            }
            this.ddsCore(arguments);
        },
        copyFileLink: function copyFileLink(data, element) {
            this.copy = data.link;
        },
        getFormDataByEvent: function getFormDataByEvent(event) {
            var field = event.target.id;
            var value = event.target.value;
            var data = bsw.getBswData($(event.target));
            var form = data.form || 'persistenceForm';

            return { field: field, value: value, data: data, form: form };
        },
        refreshPreviewInParent: function refreshPreviewInParent(data, element) {
            bsw.handleResponseInParent(data, element);
            this.previewPaginationRefresh(false);
        }
    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                var keyFlag = '';
                if (binding.arg.startsWith('data-')) {
                    keyFlag = 'data';
                    binding.arg = binding.arg.substr(5);
                }
                var key = bsw.smallHump(binding.arg, '-');
                var value = binding.value || binding.expression;
                if (key === 'configure') {
                    bsw.cnf = Object.assign(bsw.cnf, value);
                } else if (keyFlag === 'data') {
                    vnode.context[key] = value;
                } else {
                    vnode.context.init[key] = value;
                }
            }
        }

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        })

    }, bsw.config.component || {})).init(function (v) {

        var change = false;
        if (v.scaffoldInit) {
            change = v.scaffoldInit();
        }

        bsw.initClipboard();
        bsw.initUpwardInfect();
        bsw.initHighlight();
        bsw.initVConsole();

        var timeout = change ? 1200 : 600;
        setTimeout(function () {
            bsw.initScrollX();
            $(window).resize();
            var operates = function operates() {
                bsw.messageAutoDiscovery(v.init);
                bsw.autoIFrameHeight();
                bsw.prominentAnchor();
                v.loadTimes += 1;
            };
            var loadingDiv = $('.bsw-page-loading');
            if (loadingDiv.length) {
                loadingDiv.fadeOut(300, function () {
                    return operates();
                });
            } else {
                operates();
            }
        }, timeout);
    });

    window.addEventListener('message', function (event) {
        event.data.function += 'InParent';
        bsw.dispatcherByBswData(event.data, $('body')[0]);
    }, false);
});

// -- eof --
