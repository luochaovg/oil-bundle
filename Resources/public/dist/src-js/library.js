'use strict';

//
// Copyright 2018
//

//
// Generic prototype
//

// Array.prototype.contains
Array.prototype.contains = function (element) {
    var self = this;
    for (var i = 0; i < self.length; i++) {
        if (self[i] === element) {
            return true;
        }
    }
    return false;
};

// Array.prototype.unique
Array.prototype.unique = function () {
    var ra = [];
    for (var i = 0; i < this.length; i++) {
        if (!ra.contains(this[i])) {
            ra.push(this[i]);
        }
    }
    return ra;
};

// Array.prototype.remove
Array.prototype.remove = function (val) {
    // 删除指定值
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
    return this;
};

// Array.prototype.swap
Array.prototype.swap = function (first, last) {
    this[first] = this.splice(last, 1, this[first])[0];
    return this;
};

// Array.prototype.up
Array.prototype.up = function (index) {
    if (index === 0) {
        return this;
    }
    return this.swap(index, index - 1);
};

// Array.prototype.down
Array.prototype.down = function (index) {
    if (index === this.length - 1) {
        return this;
    }
    return this.swap(index, index + 1);
};

// String.prototype.trim
String.prototype.trim = function (str) {
    str = str ? '\\s' + str : '\\s';
    return this.replace(new RegExp('(^[' + str + ']*)|([' + str + ']*$)', 'g'), '');
};

// String.prototype.repeat
String.prototype.repeat = function (num) {
    num = isNaN(num) || num < 1 ? 1 : num + 1;
    return new Array(num).join(this);
};

//
// Library
//

var library = {

    /**
     * Logger
     *
     * @param obj mixed
     */
    log: function log() {
        var _console;

        (_console = console).log.apply(_console, arguments);
    },


    /**
     * Get timestamp
     *
     * @param sec boolean
     * @return {number}
     */
    time: function time(sec) {
        var time = new Date().getTime();
        return sec ? Math.ceil(time / 1000) : time;
    },


    /**
     * Parse query string
     *
     * @param url       string
     * @param hostPart  boolean
     *
     * @returns array
     */
    parseQueryString: function parseQueryString() {
        var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var hostPart = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

        url = decodeURIComponent(url || location.href);
        if (url.indexOf('?') === -1) {
            url = url + '?';
        }

        var items = {};
        var urlArr = url.split('?');
        if (hostPart) {
            items['hostPart'] = urlArr[0];
        }

        url = urlArr[1];
        if (url.length === 0) {
            return items;
        }

        if (url.indexOf('#')) {
            url = url.split('#')[0];
        }

        url = url.split('&');
        $.each(url, function (key, item) {
            item = item.split('=');
            items[item[0]] = item[1];
        });

        return items;
    },


    /**
     * Build query with json object
     *
     * @param obj
     * @return {string}
     */
    jsonBuildQuery: function jsonBuildQuery(obj) {
        var that = this;
        var query = '',
            name = void 0,
            value = void 0,
            fullSubName = void 0,
            subName = void 0,
            subValue = void 0,
            innerObj = void 0,
            i = void 0;
        for (name in obj) {
            if (!obj.hasOwnProperty(name)) {
                continue;
            }
            value = obj[name];

            if (value instanceof Array) {
                for (i = 0; i < value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += that.jsonBuildQuery(innerObj) + '&';
                }
            } else if (value instanceof Object) {
                for (subName in value) {
                    if (!value.hasOwnProperty(subName)) {
                        continue;
                    }
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += that.jsonBuildQuery(innerObj) + '&';
                }
            } else if (value !== undefined && value !== null) {
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }
        }

        return query.length ? query.substr(0, query.length - 1) : query;
    },


    /**
     * Set params to url
     *
     * @param params
     * @param url
     * @return {*}
     */
    setParams: function setParams(params, url) {
        var queryParams = this.parseQueryString(url, true);
        var host = queryParams.hostPart;
        delete queryParams.hostPart;

        params = Object.assign(queryParams, params);
        var queryString = this.jsonBuildQuery(params);

        return host + '?' + queryString;
    },


    /**
     * Unset params from url
     *
     * @param params
     * @param url
     * @return {*}
     */
    unsetParams: function unsetParams(params, url) {
        url = url || location.href;
        var queryParams = this.parseQueryString(url, true);

        $.each(params || [], function (k, v) {
            if (typeof queryParams[v] !== 'undefined') {
                delete queryParams[v];
            }
        });

        var host = queryParams.hostPart;
        delete queryParams.hostPart;

        url = host + '?' + decodeURI(this.jsonBuildQuery(queryParams));

        return url.trim('?');
    },


    /**
     * Get device version
     */
    device: function () {
        var u = navigator.userAgent;
        return {
            ie: u.indexOf('Trident') > -1,
            opera: u.indexOf('Presto') > -1,
            chrome: u.indexOf('AppleWebKit') > -1,
            firefox: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') === -1,
            mobile: !!u.match(/AppleWebKit.*Mobile.*/),
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
            android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,
            iPhone: u.indexOf('iPhone') > -1,
            iPad: u.indexOf('iPad') > -1,
            webApp: u.indexOf('Safari') === -1,
            version: navigator.appVersion
        };
    }(),

    /**
     * Is mobile
     *
     * @returns boolean
     */
    isMobile: function isMobile() {
        return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
        );
    },


    // Cookie
    cookie: {
        set: function set(name, value) {
            var time = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 86400 * 365;
            var domain = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

            var expires = new Date();
            expires.setTime(expires.getTime() + time * 1000);
            if (!domain) {
                domain = '';
            } else {
                domain = '; domain=' + domain;
            }
            document.cookie = name + '=' + escape(value) + '; expires=' + expires.toGMTString() + '; path=/' + domain;
        },
        get: function get(name) {
            var cookieArray = document.cookie.split('; ');
            for (var i = 0; i < cookieArray.length; i++) {
                var arr = cookieArray[i].split('=');
                if (arr[0] === name) {
                    return unescape(arr[1]);
                }
            }
            return false;
        },
        delete: function _delete(name) {
            var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

            if (!domain) {
                domain = '';
            } else {
                domain = '; domain=' + domain;
            }
            document.cookie = name + '=; expires=' + new Date(0).toGMTString() + '; path=/' + domain;
        }
    },

    /**
     * Eval expression
     *
     * @param expr string
     * @param def mixed
     * @returns mixed
     */
    evalExpr: function evalExpr(expr) {
        var def = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

        var data = null;
        try {
            data = eval('(' + expr + ')');
        } catch (e) {
            library.log('Error: you expression has syntax error -> ' + expr);
        }

        return data ? data : def;
    }
};
