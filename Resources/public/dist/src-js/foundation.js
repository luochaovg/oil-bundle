'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

//
// Copyright 2019
//

//
// Foundation for prototype
//

var FoundationPrototype = function () {
    function FoundationPrototype() {
        _classCallCheck(this, FoundationPrototype);
    }

    _createClass(FoundationPrototype, [{
        key: 'trim',


        /**
         * String trim
         *
         * @param source string
         * @param haystack string
         *
         * @return {string}
         */
        value: function trim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
            return source.replace(new RegExp('(^[' + haystack + ']*)|([' + haystack + ']*$)', 'g'), '');
        }

        /**
         * String left trim
         *
         * @param source string
         * @param haystack string
         *
         * @return {string}
         */

    }, {
        key: 'leftTrim',
        value: function leftTrim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
            return source.replace(new RegExp('(^[' + haystack + ']*)', 'g'), '');
        }

        /**
         * String right trim
         *
         * @param source string
         * @param haystack string
         *
         * @return {string}
         */

    }, {
        key: 'rightTrim',
        value: function rightTrim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
            return source.replace(new RegExp('([' + haystack + ']*$)', 'g'), '');
        }

        /**
         * String pad
         *
         * @param target string
         * @param padStr string
         * @param length int
         * @param type string
         * @return {*}
         */

    }, {
        key: 'pad',
        value: function pad(target, padStr, length, type) {
            padStr = padStr.toString();
            type = type || 'left';

            if (target.length >= length || !['left', 'right', 'both'].contains(type)) {
                return target;
            }
            var last = (length - target.length) % padStr.length;

            var padNum = void 0,
                _padNum = void 0;
            padNum = _padNum = Math.floor((length - target.length) / padStr.length);

            if (last > 0) {
                padNum += 1;
            }

            var _that = target;
            for (var i = 0; i < padNum; i++) {
                if (i === _padNum) {
                    padStr = padStr.substr(0, last);
                }
                switch (type) {
                    case 'left':
                        _that = padStr + _that;
                        break;
                    case 'right':
                        _that += padStr;
                        break;
                    case 'both':
                        _that = 0 === i % 2 ? padStr + _that : _that + padStr;
                        break;
                }
            }

            return _that;
        }
    }, {
        key: 'fill',


        /**
         * String fill
         *
         * @param target string
         * @param fillStr string
         * @param length int
         * @param type string
         * @return {*}
         */
        value: function fill(target, fillStr, length, type) {
            fillStr = fillStr.toString();
            type = type || 'left';

            if (length < 1 || !['left', 'right', 'both'].contains(type)) {
                return target;
            }

            var _that = target;
            for (var i = 0; i < length; i++) {
                switch (type) {
                    case 'left':
                        _that = fillStr + _that;
                        break;
                    case 'right':
                        _that += fillStr;
                        break;
                    case 'both':
                        _that = 0 === i % 2 ? fillStr + _that : _that + fillStr;
                        break;
                }
            }

            return _that;
        }
    }, {
        key: 'repeat',


        /**
         * String repeat
         *
         * @param target string
         * @param num int
         * @return {string}
         */
        value: function repeat(target, num) {
            num = isNaN(num) || num < 1 ? 1 : num + 1;
            return new Array(num).join(target);
        }
    }, {
        key: 'ucWords',


        /**
         * String upper first char of words
         *
         * @param target string
         * @return {*}
         */
        value: function ucWords(target) {
            return target.replace(/\b(\w)+\b/g, function (word) {
                return word.replace(word.charAt(0), word.charAt(0).toUpperCase());
            });
        }
    }, {
        key: 'ucFirst',


        /**
         * String upper first char
         *
         * @param target string
         * @return {*}
         */
        value: function ucFirst(target) {
            return target.replace(target.charAt(0), target.charAt(0).toUpperCase());
        }
    }, {
        key: 'lcFirst',


        /**
         * String lower first char
         *
         * @param target string
         * @return {*}
         */
        value: function lcFirst(target) {
            return target.replace(target.charAt(0), target.charAt(0).toLowerCase());
        }
    }, {
        key: 'bigHump',


        /**
         * String big hump style
         *
         * @param target string
         * @param split string
         * @return {*}
         */
        value: function bigHump(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            var reg = new RegExp(split, 'g');
            return target.replace(reg, ' ').ucWords().replace(/ /g, '');
        }
    }, {
        key: 'smallHump',


        /**
         * String small hump style
         *
         * @param target string
         * @param split string
         * @return {*}
         */
        value: function smallHump(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            return this.lcFirst(this.bigHump(target, split));
        }
    }, {
        key: 'humpToUnder',


        /**
         * String hump to under
         *
         * @param target
         * @param split
         * @returns {void | string | *}
         */
        value: function humpToUnder(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            return this.leftTrim(target.replace(/([A-Z])/g, split + '$1').toLowerCase(), split);
        }
    }, {
        key: 'format',


        /**
         * Date format
         *
         * @param target string
         * @param fmt string
         * @return {*}
         */
        value: function format(target) {
            var fmt = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'yyyy-MM-dd hh:mm:ss';

            var o = {
                'M+': target.getMonth() + 1,
                'd+': target.getDate(),
                'h+': target.getHours(),
                'm+': target.getMinutes(),
                's+': target.getSeconds(),
                'q+': Math.floor((target.getMonth() + 3) / 3),
                'S': target.getMilliseconds()
            };

            if (/(y+)/.test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (target.getFullYear() + '').substr(4 - RegExp.$1.length));
            }

            for (var k in o) {
                if (new RegExp('(' + k + ')').test(fmt)) {
                    fmt = fmt.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ('00' + o[k]).substr(('' + o[k]).length));
                }
            }

            return fmt;
        }
    }, {
        key: 'arrayUnique',


        /**
         * Array unique
         *
         * @param target string
         * @return {any[]}
         */
        value: function arrayUnique(target) {
            return Array.from(new Set(target));
        }

        /**
         * Array remove by value
         *
         * @param target array
         * @param value mixed
         * @return {*}
         */

    }, {
        key: 'arrayRemoveValue',
        value: function arrayRemoveValue(target, value) {
            var index = target.indexOf(value);
            if (index > -1) {
                target.splice(index, 1);
            }
            return target;
        }

        /**
         * Array intersect
         *
         * @param first array
         * @param second array
         * @return {*}
         */

    }, {
        key: 'arrayIntersect',
        value: function arrayIntersect(first, second) {
            return first.filter(function (v) {
                return second.indexOf(v) > -1;
            });
        }

        /**
         * Array difference
         *
         * @param first array
         * @param second array
         * @return {*}
         */

    }, {
        key: 'arrayDifference',
        value: function arrayDifference(first, second) {
            return first.filter(function (v) {
                return second.indexOf(v) === -1;
            });
        }

        /**
         * Array complement
         *
         * @param first array
         * @param second array
         * @return {*}
         */

    }, {
        key: 'arrayComplement',
        value: function arrayComplement(first, second) {
            return first.filter(function (v) {
                return !(second.indexOf(v) > -1);
            }).concat(second.filter(function (v) {
                return !(first.indexOf(v) > -1);
            }));
        }

        /**
         * Array union
         *
         * @param first array
         * @param second array
         * @return {*}
         */

    }, {
        key: 'arrayUnion',
        value: function arrayUnion(first, second) {
            return first.concat(second.filter(function (v) {
                return !(first.indexOf(v) > -1);
            }));
        }

        /**
         * Array swap
         *
         * @param source array
         * @param first int
         * @param last int
         *
         * @return {array}
         */

    }, {
        key: 'swap',
        value: function swap(source, first, last) {
            source[first] = source.splice(last, 1, source[first])[0];
            return source;
        }

        /**
         * Array up
         *
         * @param source array
         * @param index int
         *
         * @return {array}
         */

    }, {
        key: 'up',
        value: function up(source, index) {
            if (index === 0) {
                return source;
            }
            return this.swap(source, index, index - 1);
        }

        /**
         * Array down
         *
         * @param source array
         * @param index int
         *
         * @return {array}
         */

    }, {
        key: 'down',
        value: function down(source, index) {
            if (index === source.length - 1) {
                return source;
            }
            return this.swap(source, index, index + 1);
        }
    }]);

    return FoundationPrototype;
}();

//
// Foundation for tools
//

var FoundationTools = function (_FoundationPrototype) {
    _inherits(FoundationTools, _FoundationPrototype);

    function FoundationTools() {
        _classCallCheck(this, FoundationTools);

        return _possibleConstructorReturn(this, (FoundationTools.__proto__ || Object.getPrototypeOf(FoundationTools)).apply(this, arguments));
    }

    _createClass(FoundationTools, [{
        key: 'blank',


        /**
         * Blank fn
         */
        value: function blank() {}

        /**
         * Is array
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isArray',
        value: function isArray(val) {
            if (null === val) {
                return false;
            }
            return (typeof val === 'undefined' ? 'undefined' : _typeof(val)) === 'object' && val.constructor === Array;
        }

        /**
         * Is object
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isObject',
        value: function isObject(val) {
            if (null === val) {
                return false;
            }
            return (typeof val === 'undefined' ? 'undefined' : _typeof(val)) === 'object' && val.constructor === Object;
        }

        /**
         * Is null
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isNull',
        value: function isNull(val) {
            if (val) {
                return false;
            }
            return typeof val !== 'undefined' && val !== 0;
        }

        /**
         * Is json
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isJson',
        value: function isJson(val) {
            if (null === val) {
                return false;
            }
            return (typeof val === 'undefined' ? 'undefined' : _typeof(val)) === 'object' && Object.prototype.toString.call(val).toLowerCase() === '[object object]';
        }

        /**
         * Is string
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isString',
        value: function isString(val) {
            if (null === val) {
                return false;
            }
            return typeof val === 'string' && val.constructor === String;
        }

        /**
         * Is numeric
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isNumeric',
        value: function isNumeric(val) {
            if (null === val || '' === val) {
                return false;
            }
            return !isNaN(val);
        }

        /**
         * Is boolean
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isBoolean',
        value: function isBoolean(val) {
            if (null === val) {
                return false;
            }
            return typeof val === 'boolean' && val.constructor === Boolean;
        }

        /**
         * Is function
         *
         * @param val mixed
         * @return {boolean}
         */

    }, {
        key: 'isFunction',
        value: function isFunction(val) {
            if (null === val) {
                return false;
            }
            return typeof val === 'function' && Object.prototype.toString.call(val).toLowerCase() === '[object function]';
        }

        /**
         * Get json length
         *
         * @param json object
         * @return {number}
         */

    }, {
        key: 'jsonLength',
        value: function jsonLength(json) {
            var length = 0;
            var i = void 0;
            for (i in json) {
                length++;
            }
            return length;
        }

        /**
         * Get element offset
         *
         * @param obj join
         * @return {{left: *, top: *, width: number, height: number}}
         */

    }, {
        key: 'offset',
        value: function offset(obj) {
            obj = obj.jquery ? obj : $(obj);
            var pos = obj.offset();
            return {
                left: pos.left,
                top: pos.top,
                width: obj[0].offsetWidth,
                height: obj[0].offsetHeight
            };
        }

        /**
         * Timestamp
         *
         * @param second boolean
         *
         * @return {int}
         */

    }, {
        key: 'timestamp',
        value: function timestamp() {
            var second = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

            var time = new Date().getTime();
            return second ? Math.ceil(time / 1000) : time;
        }

        /**
         * Parse query string
         *
         * @param url string
         * @param hostPart boolean
         *
         * @returns {array}
         */

    }, {
        key: 'parseQueryString',
        value: function parseQueryString() {
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
            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = url[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var item = _step.value;

                    item = item.split('=');
                    items[item[0]] = item[1];
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }

            return items;
        }

        /**
         * Json to query string
         *
         * @param source json
         * @param returnObject bool
         * @param needEncode bool
         *
         * @return {string}
         */

    }, {
        key: 'jsonBuildQuery',
        value: function jsonBuildQuery(source) {
            var returnObject = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;


            var query = '',
                _query = {},
                name = void 0,
                value = void 0,
                fullSubName = void 0,
                subName = void 0,
                subValue = void 0,
                innerObject = void 0,
                i = void 0;
            for (name in source) {
                if (!source.hasOwnProperty(name)) {
                    continue;
                }
                value = source[name];

                if (this.isArray(value)) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObject = {};
                        innerObject[fullSubName] = subValue;
                        query += this.jsonBuildQuery(innerObject, returnObject, needEncode) + '&';
                        _query = Object.assign(_query, this.jsonBuildQuery(innerObject, returnObject, needEncode));
                    }
                } else if (this.isObject(value)) {
                    for (subName in value) {
                        if (!value.hasOwnProperty(subName)) {
                            continue;
                        }
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObject = {};
                        innerObject[fullSubName] = subValue;
                        query += this.jsonBuildQuery(innerObject, returnObject, needEncode) + '&';
                        _query = Object.assign(_query, this.jsonBuildQuery(innerObject, returnObject, needEncode));
                    }
                } else if (value !== undefined && value !== null) {
                    if (needEncode) {
                        name = encodeURIComponent(name);
                        value = encodeURIComponent(value);
                    }
                    query += name + '=' + value + '&';
                    _query[name] = value;
                }
            }

            if (returnObject) {
                return _query;
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        }

        /**
         * Url add items
         *
         * @param items json
         * @param url string
         * @param needEncode bool
         *
         * @return {string}
         */

    }, {
        key: 'setParams',
        value: function setParams(items) {
            var url = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;


            var queryParams = this.parseQueryString(url, true);
            var host = queryParams.hostPart;
            delete queryParams.hostPart;

            items = Object.assign(queryParams, this.jsonBuildQuery(items, true, needEncode));
            var queryString = this.jsonBuildQuery(items);
            url = host + '?' + queryString;

            return this.trim(url, '?');
        }

        /**
         * Url remove items
         *
         * @param items json
         * @param url string
         * @param needEncode bool
         * @param effect json
         *
         * @return {string}
         */

    }, {
        key: 'unsetParams',
        value: function unsetParams(items, url) {
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
            var effect = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};


            url = url || location.href;
            var queryParams = this.parseQueryString(url, true);

            var _iteratorNormalCompletion2 = true;
            var _didIteratorError2 = false;
            var _iteratorError2 = undefined;

            try {
                for (var _iterator2 = (items || [])[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                    var v = _step2.value;

                    if (typeof queryParams[v] !== 'undefined') {
                        effect[v] = queryParams[v];
                        delete queryParams[v];
                    }
                }
            } catch (err) {
                _didIteratorError2 = true;
                _iteratorError2 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion2 && _iterator2.return) {
                        _iterator2.return();
                    }
                } finally {
                    if (_didIteratorError2) {
                        throw _iteratorError2;
                    }
                }
            }

            var host = queryParams.hostPart;
            delete queryParams.hostPart;

            url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
            return this.trim(url, '?');
        }

        /**
         * Url remove items
         *
         * @param items json
         * @param url string
         * @param needEncode bool
         * @param effect json
         *
         * @return {string}
         */

    }, {
        key: 'unsetParamsBeginWith',
        value: function unsetParamsBeginWith(items, url) {
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
            var effect = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};

            url = url || location.href;
            var queryParams = this.parseQueryString(url, true);

            var _iteratorNormalCompletion3 = true;
            var _didIteratorError3 = false;
            var _iteratorError3 = undefined;

            try {
                for (var _iterator3 = (items || [])[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                    var v = _step3.value;

                    for (var w in queryParams) {
                        if (!queryParams.hasOwnProperty(w)) {
                            continue;
                        }
                        if (w.startsWith(v)) {
                            effect[w] = queryParams[w];
                            delete queryParams[w];
                        }
                    }
                }
            } catch (err) {
                _didIteratorError3 = true;
                _iteratorError3 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion3 && _iterator3.return) {
                        _iterator3.return();
                    }
                } finally {
                    if (_didIteratorError3) {
                        throw _iteratorError3;
                    }
                }
            }

            var host = queryParams.hostPart;
            delete queryParams.hostPart;

            url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
            return this.trim(url, '?');
        }

        /**
         * Count px of padding and margin
         *
         * @param obj json
         * @param length int
         * @param type string
         * @param pos string
         * @return {number}
         */

    }, {
        key: 'pam',
        value: function pam(obj, length, type, pos) {
            length = length || 1;
            type = type || ['margin', 'padding'];
            pos = pos || ['left', 'right'];

            var px = 0;

            type.each(function (m) {
                pos.each(function (n) {
                    px += parseInt(obj.css(m + '-' + n)) * length;
                });
            });

            return px;
        }

        /**
         * Device checker
         *
         * @return {{}}
         */

    }, {
        key: 'device',
        value: function device() {
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
        }

        /**
         * Mobile check
         *
         * @returns boolean
         */

    }, {
        key: 'isMobile',
        value: function isMobile() {
            return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
            );
        }

        /**
         * Rand a number
         *
         * @param end int
         * @param begin int
         * @return {*}
         */

    }, {
        key: 'rand',
        value: function rand(end, begin) {
            begin = begin || 0;

            var rank = begin;
            var _end = end - rank;

            return parseInt(Number(Math.random() * _end).toFixed(0)) + rank;
        }

        /**
         * Bind key down
         *
         * @param num int
         * @param callback callable
         * @param obj object
         * @param ctrl bool
         */

    }, {
        key: 'keyBind',
        value: function keyBind(num, callback, obj, ctrl) {
            obj = obj || $(document);
            obj.unbind('keydown').bind('keydown', function (event) {
                if (ctrl) {
                    if (event.keyCode === num && event.ctrlKey && callback) {
                        callback();
                    }
                } else {
                    if (event.keyCode === num && callback) {
                        callback();
                    }
                }
            });
        }

        /**
         * Device media
         *
         * @return {{}}
         */

    }, {
        key: 'media',
        value: function media() {
            var width = document.body.clientWidth;
            return {
                'xs': width < 576,
                'sm': width >= 576,
                'md': width >= 768,
                'lg': width >= 992,
                'xl': width >= 1200,
                'xxl': width >= 1600
            };
        }

        /**
         * Int value
         *
         * @param value mixed
         *
         * @return int
         */

    }, {
        key: 'parseInt',
        value: function (_parseInt) {
            function parseInt(_x5) {
                return _parseInt.apply(this, arguments);
            }

            parseInt.toString = function () {
                return _parseInt.toString();
            };

            return parseInt;
        }(function (value) {
            value = parseInt(value);
            return isNaN(value) ? 0 : value;
        })

        /**
         * Cookie tools
         *
         * @return {{}}
         */

    }, {
        key: 'cookie',
        value: function cookie() {
            return {
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
                    document.cookie = name + '=' + encodeURI(value) + '; expires=' + expires.toUTCString() + '; path=/' + domain;
                    return value;
                },
                get: function get(name) {
                    var def = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

                    var cookieArray = document.cookie.split('; ');
                    for (var i = 0; i < cookieArray.length; i++) {
                        var arr = cookieArray[i].split('=');
                        if (arr[0] === name) {
                            return unescape(arr[1]);
                        }
                    }
                    return def;
                },
                delete: function _delete(name) {
                    var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

                    if (!domain) {
                        domain = '';
                    } else {
                        domain = '; domain=' + domain;
                    }
                    document.cookie = name + '=; expires=' + new Date(0).toUTCString() + '; path=/' + domain;
                }
            };
        }

        /**
         * Get next value with map use cookie
         *
         * @param name string
         * @param map json
         * @param def mixed
         * @param set bool
         * @param tips string
         *
         * @return mixed
         */

    }, {
        key: 'cookieMapNext',
        value: function cookieMapNext(name, map, def) {
            var set = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
            var tips = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;

            var ck = this.cookie();
            var current = ck.get(name, def);
            var next = map[current] || def;

            if (tips) {
                var _next = this.ucFirst(next);
                this.message('success', tips + ': ' + _next);
            }

            return set ? ck.set(name, next) : next;
        }

        /**
         * Get current value with map use cookie
         *
         * @param name string
         * @param map json
         * @param def mixed
         *
         * @return mixed
         */

    }, {
        key: 'cookieMapCurrent',
        value: function cookieMapCurrent(name, map, def) {
            var current = this.cookie().get(name, def);

            return map[current] ? current : def;
        }

        /**
         * Eval expression
         *
         * @param expression string
         * @param def mixed
         *
         * @returns {{}}
         */

    }, {
        key: 'evalExpr',
        value: function evalExpr(expression) {
            var def = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

            var data = null;
            try {
                data = window.eval('(' + expression + ')');
            } catch (e) {
                console.warn('Expression has syntax error: ' + expression);
                console.warn(e);
            }

            return data ? data : def;
        }

        /**
         * Switch class name
         *
         * @param cls string
         * @param add bool
         * @param element string
         */

    }, {
        key: 'switchClass',
        value: function switchClass(cls, add) {
            var element = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'html';

            var container = $(element);
            container.removeClass(cls);
            add === 'yes' && container.addClass(cls);
        }

        /**
         * Check json keys exists
         *
         * @param data object
         * @param keys string
         *
         * @returns boolean
         */

    }, {
        key: 'checkJsonDeep',
        value: function checkJsonDeep(data, keys) {
            var origin = data;
            keys = keys.split('.');
            var _iteratorNormalCompletion4 = true;
            var _didIteratorError4 = false;
            var _iteratorError4 = undefined;

            try {
                for (var _iterator4 = keys[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
                    var key = _step4.value;

                    if (typeof origin[key] === 'undefined' || !origin[key]) {
                        return false;
                    }
                    origin = origin[key];
                }
            } catch (err) {
                _didIteratorError4 = true;
                _iteratorError4 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion4 && _iterator4.return) {
                        _iterator4.return();
                    }
                } finally {
                    if (_didIteratorError4) {
                        throw _iteratorError4;
                    }
                }
            }

            return true;
        }
    }]);

    return FoundationTools;
}(FoundationPrototype);

//
// Foundation for AntD
//

var FoundationAntD = function (_FoundationTools) {
    _inherits(FoundationAntD, _FoundationTools);

    /**
     * Constructor
     *
     * @param cnf json
     * @param jQuery object
     * @param Vue object
     * @param AntD object
     * @param lang object
     */
    function FoundationAntD(cnf, jQuery, Vue, AntD) {
        var lang = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};

        _classCallCheck(this, FoundationAntD);

        var _this2 = _possibleConstructorReturn(this, (FoundationAntD.__proto__ || Object.getPrototypeOf(FoundationAntD)).call(this));

        _this2.v = Vue;
        _this2.d = AntD;
        _this2.config = {};
        _this2.lang = lang;
        _this2.cnf = Object.assign({
            rsaPublicKey: null,
            marginTop: '150px',
            loadingMarginTop: '250px',
            shade: .1,
            zIndex: 9999,
            requestTimeout: 30,
            notificationDuration: 5,
            messageDuration: 5,
            confirmDuration: 5,
            alertType: 'message',
            notificationPlacement: 'topRight',
            v: null,
            method: {
                get: 'GET',
                post: 'POST'
            }
        }, cnf);
        return _this2;
    }

    /**
     * Page configure
     *
     * @param config json
     */


    _createClass(FoundationAntD, [{
        key: 'configure',
        value: function configure(config) {
            for (var key in config) {
                if (!config.hasOwnProperty(key)) {
                    continue;
                }
                this.config[key] = Object.assign(this.config[key] || {}, config[key]);
            }
        }

        /**
         * Init with VUE
         *
         * @param selector string
         *
         * @return {*}
         */

    }, {
        key: 'vue',
        value: function vue() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.bsw-content';

            var that = this;
            var conf = {};
            return {
                template: function template(item) {
                    item && (conf.template = item);
                    return this;
                },
                data: function data() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.data = function () {
                        return item;
                    };
                    return this;
                },
                computed: function computed() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.computed = item;
                    return this;
                },
                method: function method() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.methods = item;
                    return this;
                },
                component: function component() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.components = item;
                    return this;
                },
                directive: function directive() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.directives = item;
                    return this;
                },
                init: function init() {
                    var logic = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : self.blank;

                    //that.cnf.v = (new (that.v.extend(conf))()).$mount(selector);
                    conf.el = selector;
                    that.cnf.v = new that.v(conf);
                    logic(that.cnf.v);
                }
            };
        }

        /**
         * Show use notification
         *
         * @param type string
         * @param description string
         * @param duration float
         * @param onClose callable
         *
         * @returns {*}
         */

    }, {
        key: 'notification',
        value: function notification(type, description, duration) {
            var onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : self.blank;


            if (typeof duration === 'undefined') {
                duration = this.cnf.notificationDuration;
            }

            var message = {
                success: this.lang.success,
                info: this.lang.info,
                warning: this.lang.warning,
                error: this.lang.error
            }[type];

            return this.cnf.v.$notification[type]({
                placement: this.cnf.notificationPlacement,
                message: message,
                description: description,
                duration: duration,
                onClose: onClose
            });
        }

        /**
         * Show use message
         *
         * @param type string
         * @param description string
         * @param duration float
         * @param onClose callable
         *
         * @returns {*}
         */

    }, {
        key: 'message',
        value: function message(type, description, duration) {
            var onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : self.blank;


            if (typeof duration === 'undefined') {
                duration = this.cnf.messageDuration;
            }

            return this.cnf.v.$message[type](description, duration, onClose);
        }

        /**
         * Show use confirm
         *
         * @param type string
         * @param description string
         * @param duration float
         * @param onClose callable
         * @param options json
         *
         * @returns {*}
         */

    }, {
        key: 'confirm',
        value: function confirm(type, description, duration) {
            var onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : self.blank;
            var options = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};


            var title = options.title || {
                success: this.lang.success,
                info: this.lang.info,
                warning: this.lang.warning,
                error: this.lang.error
            }[type];

            if (type === 'confirm' && typeof options.width === 'undefined') {
                options.width = this.popupCosySize().width;
            }

            var modal = this.cnf.v['$' + type](Object.assign({
                title: title,
                content: description,
                okText: this.lang.i_got_it,
                onOk: options.onOk || onClose,
                onCancel: onClose
            }, options));

            if (typeof duration === 'undefined') {
                duration = this.cnf.confirmDuration;
            }

            if (duration) {
                setTimeout(function () {
                    modal.destroy();
                }, duration * 1000);
            }

            return modal;
        }

        /**
         * Show success
         *
         * @param description string
         * @param duration float
         * @param onClose callable
         * @param type string
         *
         * @returns {*}
         */

    }, {
        key: 'success',
        value: function success(description, duration, onClose, type) {
            return this[type || this.cnf.alertType]('success', description, duration, onClose);
        }

        /**
         * Show info
         *
         * @param description string
         * @param duration float
         * @param onClose callable
         * @param type string
         *
         * @returns {*}
         */

    }, {
        key: 'info',
        value: function info(description, duration, onClose, type) {
            return this[type || this.cnf.alertType]('info', description, duration, onClose);
        }

        /**
         * Show warning
         *
         * @param description string
         * @param duration float
         * @param onClose callable
         * @param type string
         *
         * @returns {*}
         */

    }, {
        key: 'warning',
        value: function warning(description, duration, onClose, type) {
            return this[type || this.cnf.alertType]('warning', description, duration, onClose);
        }

        /**
         * Show error
         *
         * @param description string
         * @param duration float
         * @param onClose callable
         * @param type string
         *
         * @returns {*}
         */

    }, {
        key: 'error',
        value: function error(description, duration, onClose, type) {
            return this[type || this.cnf.alertType]('error', description, duration, onClose);
        }

        /**
         * Show confirm
         *
         * @param content string
         * @param title string
         * @param options json
         *
         * @return {*}
         */

    }, {
        key: 'showConfirm',
        value: function showConfirm(content, title) {
            var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

            return this.cnf.v.$confirm(Object.assign({
                title: title,
                content: content,
                keyboard: false,
                width: 320,
                okText: this.lang.confirm,
                cancelText: this.lang.cancel
            }, options));
        }

        /**
         * Request
         *
         * @param url string
         * @param data json
         * @param type string
         * @param upload boolean
         * @param times integer
         *
         * @returns {Promise}
         */

    }, {
        key: 'request',
        value: function request(url) {
            var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.cnf.method.post;
            var upload = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
            var times = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 1;

            var that = this;
            return new Promise(function (resolve, reject) {
                $.ajax({
                    type: upload ? that.cnf.method.post : type,
                    data: data,
                    url: url,
                    processData: !upload,
                    contentType: upload ? false : 'application/x-www-form-urlencoded',
                    timeout: that.cnf.requestTimeout * 1000 * (upload ? 10 : 1),
                    beforeSend: function beforeSend() {
                        if (that.cnf.v.no_loading_once) {
                            that.cnf.v.no_loading_once = false;
                        } else {
                            that.cnf.v.spinning = true;
                        }
                    },
                    success: function success(data) {
                        that.cnf.v.spinning = false;
                        resolve(data);
                    },
                    error: function error(obj) {
                        that.cnf.v.spinning = false;
                        if (obj.responseJSON) {
                            var result = obj.responseJSON;
                            var message = '[' + result.code + '] ' + result.message;
                            return that.confirm(result.classify, message, 0);
                        }

                        if (obj.responseText) {
                            var _message = '[' + obj.status + '] ' + obj.statusText;
                            return that.confirm('error', _message, 0);
                        }

                        if (obj.statusText === 'timeout') {
                            console.warn('Client request timeout: ', obj);
                            console.warn('Retry current request in times ' + times);

                            if (times <= 3) {
                                return that.request(url, data, type, upload, ++times);
                            }
                        }
                        reject(obj);
                    }
                });
            });
        }

        /**
         * Handler for response
         *
         * @param result json
         * @param successSameHandler callable
         * @param failedSameHandler callable
         * @param duration int
         */

    }, {
        key: 'response',
        value: function response(result, successSameHandler, failedSameHandler, duration) {

            if (typeof result.code === 'undefined') {
                return this.error(this.lang.response_error_message);
            }

            var that = this;
            return new Promise(function (resolve, reject) {

                var failedHandler = function failedHandler(result) {
                    reject(result);
                    if (typeof result.sets.href !== 'undefined') {
                        location.href = result.sets.href || location.href;
                    }
                };

                var successHandler = function successHandler(result) {
                    resolve(result);
                    if (typeof result.sets.href !== 'undefined') {
                        location.href = result.sets.href || location.href;
                    }
                };

                if (result.error) {
                    if (result.message) {
                        var _duration = that.isNull(result.duration) ? undefined : result.duration;
                        that[result.classify](result.message, _duration, null, result.type).then(function () {
                            failedHandler(result);
                        }).catch(function (reason) {
                            console.warn(reason);
                        });
                    } else {
                        failedHandler(result);
                    }
                    failedSameHandler && failedSameHandler(result);
                } else {

                    if (result.message) {
                        var _duration2 = that.isNull(result.duration) ? undefined : result.duration;
                        that[result.classify](result.message, _duration2, null, result.type).then(function () {
                            successHandler(result);
                        }).catch(function (reason) {
                            console.warn(reason);
                        });
                    } else {
                        successHandler(result);
                    }
                    successSameHandler && successSameHandler(result);
                }
            });
        }

        /**
         * Cosy size for popup
         *
         * @returns {{width: number, height: number}}
         */

    }, {
        key: 'popupCosySize',
        value: function popupCosySize() {
            var width = document.body.clientWidth;
            var height = document.body.clientHeight;
            width *= width < 1285 ? 1 : .7;
            height *= height < 666 ? .9 : .75;
            return { width: width, height: height };
        }

        /**
         * Encrypt by rsa public key
         *
         * @param text string
         *
         * @returns string
         */

    }, {
        key: 'rsaEncrypt',
        value: function rsaEncrypt(text) {
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey(this.cnf.rsaPublicKey);

            return encrypt.encrypt(text);
        }

        /**
         * Create chart
         *
         * @param option object
         *
         * @returns void
         */

    }, {
        key: 'chart',
        value: function chart(option) {
            var chart = echarts.init(document.getElementById('chart-' + option.id), option.theme);
            var o = option.option;

            if (this.checkJsonDeep(o, 'tooltip.formatter')) {
                if (o.tooltip.formatter === ':stackBar') {
                    o.tooltip.formatter = function (params) {
                        var total = 0;
                        var _iteratorNormalCompletion5 = true;
                        var _didIteratorError5 = false;
                        var _iteratorError5 = undefined;

                        try {
                            for (var _iterator5 = params[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
                                var item = _step5.value;

                                total += Math.floor(Number.parseFloat(item.data) * 100);
                            }
                        } catch (err) {
                            _didIteratorError5 = true;
                            _iteratorError5 = err;
                        } finally {
                            try {
                                if (!_iteratorNormalCompletion5 && _iterator5.return) {
                                    _iterator5.return();
                                }
                            } finally {
                                if (_didIteratorError5) {
                                    throw _iteratorError5;
                                }
                            }
                        }

                        total /= 100;

                        var tpl = params[0].name + ' (' + total + ')<br>';
                        var _iteratorNormalCompletion6 = true;
                        var _didIteratorError6 = false;
                        var _iteratorError6 = undefined;

                        try {
                            for (var _iterator6 = params[Symbol.iterator](), _step6; !(_iteratorNormalCompletion6 = (_step6 = _iterator6.next()).done); _iteratorNormalCompletion6 = true) {
                                var _item = _step6.value;

                                var percent = (Number.parseFloat(_item.data) / total || 0) * 100;
                                percent = percent.toFixed(2);
                                tpl += _item.marker + ' ' + _item.seriesName + ': ' + _item.data + ' (' + percent + '%)<br>';
                            }
                        } catch (err) {
                            _didIteratorError6 = true;
                            _iteratorError6 = err;
                        } finally {
                            try {
                                if (!_iteratorNormalCompletion6 && _iterator6.return) {
                                    _iterator6.return();
                                }
                            } finally {
                                if (_didIteratorError6) {
                                    throw _iteratorError6;
                                }
                            }
                        }

                        return tpl;
                    };
                } else if (o.tooltip.formatter === ':pictorialBar') {
                    o.tooltip.formatter = function (params) {
                        return params[0].name + ': ' + params[0].value;
                    };
                }
            }

            chart.setOption(o);
        }

        /**
         * WeChat pay by js api
         *
         * @param config object
         *
         * @returns void
         */

    }, {
        key: 'wxJsApiPay',
        value: function wxJsApiPay(config) {
            if (!window.WeixinJSBridge) {
                console.log("Js api just work in WeiXin browser");
                return;
            }
            WeixinJSBridge.invoke('getBrandWCPayRequest', config, function (result) {
                console.log(result);
                if (result.err_msg === "get_brand_wcpay_request:ok") {
                    console.log('success');
                }
            });
        }
    }]);

    return FoundationAntD;
}(FoundationTools);

// -- eof --
