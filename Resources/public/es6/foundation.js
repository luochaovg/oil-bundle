//
// Copyright 2019
//

//
// Foundation for prototype
//

class FoundationPrototype {

    /**
     * String trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    trim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('(^[' + haystack + ']*)|([' + haystack + ']*$)', 'g'), '');
    }

    /**
     * String left trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    leftTrim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('(^[' + haystack + ']*)', 'g'), '');
    }

    /**
     * String right trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    rightTrim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('([' + haystack + ']*$)', 'g'), '');
    }

    /**
     * String pad
     *
     * @param target
     * @param padStr
     * @param length
     * @param type
     *
     * @return {*}
     */
    pad(target, padStr, length, type) {
        padStr = padStr.toString();
        type = type || 'left';

        if (target.length >= length || !['left', 'right', 'both'].contains(type)) {
            return target;
        }

        let padNum, _padNum;
        let last = (length - target.length) % padStr.length;
        padNum = _padNum = Math.floor((length - target.length) / padStr.length);

        if (last > 0) {
            padNum += 1;
        }

        let _that = target;
        for (let i = 0; i < padNum; i++) {
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
                    _that = (0 === i % 2) ? (padStr + _that) : (_that + padStr);
                    break;
            }
        }

        return _that;
    };

    /**
     * String fill
     *
     * @param target
     * @param fillStr
     * @param length
     * @param type
     *
     * @return {*}
     */
    fill(target, fillStr, length, type) {
        fillStr = fillStr.toString();
        type = type || 'left';

        if (length < 1 || !['left', 'right', 'both'].contains(type)) {
            return target;
        }

        let _that = target;
        for (let i = 0; i < length; i++) {
            switch (type) {
                case 'left':
                    _that = fillStr + _that;
                    break;
                case 'right':
                    _that += fillStr;
                    break;
                case 'both':
                    _that = (0 === i % 2) ? (fillStr + _that) : (_that + fillStr);
                    break;
            }
        }

        return _that;
    };

    /**
     * String repeat
     *
     * @param target
     * @param num
     *
     * @return {string}
     */
    repeat(target, num) {
        num = (isNaN(num) || num < 1) ? 1 : num + 1;
        return new Array(num).join(target);
    };

    /**
     * String upper first char of words
     *
     * @param target
     *
     * @return {*}
     */
    ucWords(target) {
        return target.replace(/\b(\w)+\b/g, function (word) {
            return word.replace(word.charAt(0), word.charAt(0).toUpperCase());
        });
    };

    /**
     * String upper first char
     *
     * @param target
     *
     * @return {*}
     */
    ucFirst(target) {
        return target.replace(target.charAt(0), target.charAt(0).toUpperCase());
    };

    /**
     * String lower first char
     *
     * @param target
     *
     * @return {*}
     */
    lcFirst(target) {
        return target.replace(target.charAt(0), target.charAt(0).toLowerCase());
    };

    /**
     * String big hump style
     *
     * @param target
     * @param split
     *
     * @return {*}
     */
    bigHump(target, split = '_') {
        let reg = new RegExp(split, 'g');
        return this.ucWords(target.replace(reg, ' ')).replace(/ /g, '');
    };

    /**
     * String small hump style
     *
     * @param target
     * @param split
     *
     * @return {*}
     */
    smallHump(target, split = '_') {
        return this.lcFirst(this.bigHump(target, split));
    };

    /**
     * String hump to under
     *
     * @param target
     * @param split
     *
     * @returns {*}
     */
    humpToUnder(target, split = '_') {
        return this.leftTrim(target.replace(/([A-Z])/g, `${split}$1`).toLowerCase(), split);
    };

    /**
     * Date format
     *
     * @param target
     * @param fmt
     *
     * @return {*}
     */
    format(target, fmt = 'yyyy-MM-dd hh:mm:ss') {
        let o = {
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

        for (let k in o) {
            if (!o.hasOwnProperty(k)) {
                continue;
            }
            if (new RegExp('(' + k + ')').test(fmt)) {
                fmt = fmt.replace(
                    RegExp.$1,
                    (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length))
                );
            }
        }

        return fmt;
    };

    /**
     * Array unique
     *
     * @param target
     *
     * @returns {*}
     */
    arrayUnique(target) {
        return Array.from(new Set(target));
    }

    /**
     * Array remove by value
     *
     * @param target
     * @param value
     *
     * @return {*}
     */
    arrayRemoveValue(target, value) {
        let index = target.indexOf(value);
        if (index > -1) {
            target.splice(index, 1);
        }
        return target;
    }

    /**
     * Array intersect
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayIntersect(first, second) {
        return first.filter((v) => second.indexOf(v) > -1);
    }

    /**
     * Array difference
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayDifference(first, second) {
        return first.filter((v) => second.indexOf(v) === -1);
    }

    /**
     * Array complement
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayComplement(first, second) {
        return first.filter((v) => !(second.indexOf(v) > -1)).concat(second.filter((v) => !(first.indexOf(v) > -1)));
    }

    /**
     * Array union
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayUnion(first, second) {
        return first.concat(second.filter((v) => !(first.indexOf(v) > -1)));
    }

    /**
     * Array swap
     *
     * @param source
     * @param first
     * @param last
     *
     * @return {array}
     */
    swap(source, first, last) {
        source[first] = source.splice(last, 1, source[first])[0];
        return source;
    }

    /**
     * Array up
     *
     * @param source
     * @param index
     *
     * @return {array}
     */
    up(source, index) {
        if (index === 0) {
            return source;
        }
        return this.swap(source, index, index - 1);
    }

    /**
     * Array down
     *
     * @param source
     * @param index
     *
     * @return {array}
     */
    down(source, index) {
        if (index === source.length - 1) {
            return source;
        }
        return this.swap(source, index, index + 1);
    }
}

//
// Foundation for tools
//

class FoundationTools extends FoundationPrototype {

    /**
     * Blank fn
     */
    blank() {
    }

    /**
     * Is array
     *
     * @param value
     *
     * @return {boolean}
     */
    isArray(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && value.constructor === Array;
    }

    /**
     * Is object
     *
     * @param value
     *
     * @return {boolean}
     */
    isObject(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && value.constructor === Object;
    }

    /**
     * Is null
     *
     * @param value
     *
     * @return {boolean}
     */
    isNull(value) {
        if (value) {
            return false;
        }
        return typeof value !== 'undefined' && value !== 0;
    }

    /**
     * Is json
     *
     * @param value
     *
     * @return {boolean}
     */
    isJson(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && Object.prototype.toString.call(value).toLowerCase() === '[object object]';
    }

    /**
     * Is string
     *
     * @param value
     *
     * @return {boolean}
     */
    isString(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'string' && value.constructor === String;
    }

    /**
     * Is numeric
     *
     * @param value
     *
     * @return {boolean}
     */
    isNumeric(value) {
        if (null === value || '' === value) {
            return false;
        }
        return !isNaN(value);
    }

    /**
     * Is boolean
     *
     * @param value
     *
     * @return {boolean}
     */
    isBoolean(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'boolean' && value.constructor === Boolean;
    }

    /**
     * Is function
     *
     * @param value
     *
     * @return {boolean}
     */
    isFunction(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'function' && Object.prototype.toString.call(value).toLowerCase() === '[object function]';
    }

    /**
     * Get json length
     *
     * @param target
     *
     * @return {number}
     */
    jsonLength(target) {
        let length = 0;
        for (let i in target) {
            if (!target.hasOwnProperty(i)) {
                continue;
            }
            length++;
        }
        return length;
    }

    /**
     * Get element offset
     *
     * @param element
     *
     * @return {{left: *, top: *, width: number, height: number}}
     */
    offset(element) {
        element = element.jquery ? element : $(element);
        let pos = element.offset();
        return {
            left: pos.left,
            top: pos.top,
            width: element[0].offsetWidth,
            height: element[0].offsetHeight
        };
    }

    /**
     * Timestamp
     *
     * @param second
     *
     * @return {int}
     */
    timestamp(second = false) {
        let time = new Date().getTime();
        return second ? Math.ceil(time / 1000) : time;
    }

    /**
     * Parse query string
     *
     * @param url
     * @param hostPart
     *
     * @returns {array}
     */
    parseQueryString(url = null, hostPart = false) {
        url = decodeURIComponent(url || location.href);
        if (url.indexOf('?') === -1) {
            url = url + '?';
        }

        let items = {};
        let urlArr = url.split('?');
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
        for (let item of url) {
            item = item.split('=');
            items[item[0]] = item[1];
        }

        return items;
    }

    /**
     * Json to query string
     *
     * @param source
     * @param returnObject
     * @param needEncode
     *
     * @return {string}
     */
    jsonBuildQuery(source, returnObject = false, needEncode = true) {
        let query = '', _query = {}, name, value, fullSubName, subName, subValue, innerObject, i;
        for (let name in source) {
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
                query += `${name}=${value}&`;
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
     * @param items
     * @param url
     * @param needEncode
     *
     * @return {string}
     */
    setParams(items, url = null, needEncode = false) {
        let queryParams = this.parseQueryString(url, true);
        let host = queryParams.hostPart;
        delete queryParams.hostPart;

        items = Object.assign(queryParams, this.jsonBuildQuery(items, true, needEncode));
        let queryString = this.jsonBuildQuery(items);
        url = `${host}?${queryString}`;

        return this.trim(url, '?');
    }

    /**
     * Url remove items
     *
     * @param items
     * @param url
     * @param needEncode
     * @param effect
     *
     * @return {string}
     */
    unsetParams(items, url, needEncode = false, effect = {}) {
        url = url || location.href;
        let queryParams = this.parseQueryString(url, true);

        for (let v of (items || [])) {
            if (typeof queryParams[v] !== 'undefined') {
                effect[v] = queryParams[v];
                delete queryParams[v];
            }
        }

        let host = queryParams.hostPart;
        delete queryParams.hostPart;

        url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
        return this.trim(url, '?');
    }

    /**
     * Url remove items
     *
     * @param items
     * @param url
     * @param needEncode
     * @param effect
     *
     * @return {string}
     */
    unsetParamsBeginWith(items, url, needEncode = false, effect = {}) {
        url = url || location.href;
        let queryParams = this.parseQueryString(url, true);

        for (let v of (items || [])) {
            for (let w in queryParams) {
                if (!queryParams.hasOwnProperty(w)) {
                    continue;
                }
                if (w.startsWith(v)) {
                    effect[w] = queryParams[w];
                    delete queryParams[w];
                }
            }
        }

        let host = queryParams.hostPart;
        delete queryParams.hostPart;

        url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
        return this.trim(url, '?');
    }

    /**
     * Count px of padding and margin
     *
     * @param element
     * @param length
     * @param type
     * @param pos
     * @return {number}
     */
    pam(element, length, type, pos) {
        length = length || 1;
        type = type || ['margin', 'padding'];
        pos = pos || ['left', 'right'];

        let px = 0;
        type.each(function (m) {
            pos.each(function (n) {
                px += parseInt(element.css(m + '-' + n)) * length;
            });
        });

        return px;
    }

    /**
     * Device checker
     *
     * @return {{}}
     */
    device() {
        let u = navigator.userAgent;
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
    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    /**
     * Rand a number
     *
     * @param end
     * @param begin
     * @return {*}
     */
    rand(end, begin) {
        begin = begin || 0;
        let rank = begin;
        let _end = end - rank;

        return parseInt(Number(Math.random() * _end).toFixed(0)) + rank;
    }

    /**
     * Bind key down
     *
     * @param num
     * @param callback
     * @param element
     * @param ctrl
     */
    keyBind(num, callback, element, ctrl = false) {
        element = element || $(document);
        element.unbind('keydown').bind('keydown', function (event) {
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
    media() {
        let width = document.body.clientWidth;
        return {
            'xs': width < 576,
            'sm': width >= 576,
            'md': width >= 768,
            'lg': width >= 992,
            'xl': width >= 1200,
            'xxl': width >= 1600,
        };
    }

    /**
     * Int value
     *
     * @param value
     *
     * @return int
     */
    parseInt(value) {
        value = parseInt(value);
        return isNaN(value) ? 0 : value;
    }

    /**
     * Cookie tools
     *
     * @return {{}}
     */
    cookie() {
        return {
            set: function (name, value, time = 86400 * 365, domain = null) {
                let expires = new Date();
                expires.setTime(expires.getTime() + time * 1000);
                if (!domain) {
                    domain = '';
                } else {
                    domain = '; domain=' + domain;
                }
                document.cookie = name + '=' + encodeURI(value) + '; expires=' + expires.toUTCString() + '; path=/' + domain;
                return value;
            },
            get: function (name, def = false) {
                let cookieArray = document.cookie.split('; ');
                for (let i = 0; i < cookieArray.length; i++) {
                    let arr = cookieArray[i].split('=');
                    if (arr[0] === name) {
                        return unescape(arr[1]);
                    }
                }
                return def;
            },
            delete: function (name, domain = null) {
                if (!domain) {
                    domain = '';
                } else {
                    domain = '; domain=' + domain;
                }
                document.cookie = name + '=; expires=' + (new Date(0)).toUTCString() + '; path=/' + domain;
            }
        };
    }

    /**
     * Get next value with map use cookie
     *
     * @param name
     * @param map
     * @param def
     * @param set
     * @param tips
     *
     * @return mixed
     */
    cookieMapNext(name, map, def, set = false, tips = null) {
        let ck = this.cookie();
        let current = ck.get(name, def);
        let next = map[current] || def;

        if (tips) {
            let _next = this.ucFirst(next);
            this.message('success', `${tips}: ${_next}`);
        }

        return set ? ck.set(name, next) : next;
    }

    /**
     * Get current value with map use cookie
     *
     * @param name
     * @param map
     * @param def
     *
     * @return mixed
     */
    cookieMapCurrent(name, map, def) {
        let current = this.cookie().get(name, def);

        return map[current] ? current : def;
    }

    /**
     * Eval expression
     *
     * @param expression
     * @param def
     *
     * @returns {{}}
     */
    evalExpr(expression, def = null) {
        let data = null;
        try {
            data = window.eval(`(${expression})`);
        } catch (e) {
            console.warn(`Expression has syntax error: ${expression}`);
            console.warn(e);
        }

        return data ? data : def;
    }

    /**
     * Switch class name
     *
     * @param cls
     * @param add
     * @param selector
     */
    switchClass(cls, add, selector = 'html') {
        let container = $(selector);
        container.removeClass(cls);
        add === 'yes' && container.addClass(cls);
    }

    /**
     * Check json keys exists
     *
     * @param target
     * @param keys
     *
     * @returns boolean
     */
    checkJsonDeep(target, keys) {
        let origin = target;
        keys = keys.split('.');
        for (let key of keys) {
            if (typeof origin[key] === 'undefined' || !origin[key]) {
                return false;
            }
            origin = origin[key];
        }

        return true;
    }

    /**
     * Json filter
     *
     * @param target
     * @param filter
     */
    jsonFilter(target, filter = ['', null]) {
        for (let i in target) {
            if (!target.hasOwnProperty(i)) {
                continue;
            }
            if (filter.indexOf(target[i]) !== -1) {
                delete target[i];
            }
        }
        return target;
    }
}

//
// Foundation for AntD
//

class FoundationAntD extends FoundationTools {

    /**
     * Constructor
     *
     * @param jQuery
     * @param Vue
     * @param AntD
     * @param lang
     */
    constructor(jQuery, Vue, AntD, lang = {}) {
        super();
        this.v = Vue;
        this.d = AntD;
        this.config = {};
        this.lang = lang;
        this.cnf = {
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
        };
    }

    /**
     * Page configure
     *
     * @param config
     */
    configure(config) {
        for (let key in config) {
            if (!config.hasOwnProperty(key)) {
                continue;
            }
            this.config[key] = Object.assign(this.config[key] || {}, config[key]);
        }
    }

    /**
     * Init with VUE
     *
     * @param selector
     *
     * @return {*}
     */
    vue(selector = '.bsw-vue') {
        let that = this;
        let conf = {};
        return {
            template(item) {
                item && (conf.template = item);
                return this;
            },
            data(item = {}) {
                conf.data = () => item;
                return this;
            },
            computed(item = {}) {
                conf.computed = item;
                return this;
            },
            method(item = {}) {
                conf.methods = item;
                return this;
            },
            component(item = {}) {
                conf.components = item;
                return this;
            },
            directive(item = {}) {
                conf.directives = item;
                return this;
            },
            watch(item = {}) {
                conf.watch = item;
                return this;
            },
            init(logic = self.blank) {
                conf.el = selector;
                that.cnf.v = new that.v(conf);
                that.cnf.v.$nextTick(function () {
                    // logic
                    for (let fn in that.config.logic || []) {
                        if (!that.config.logic.hasOwnProperty(fn)) {
                            continue;
                        }
                        that.config.logic[fn](that.cnf.v);
                    }
                });
                that.changeImageCaptcha();
                logic(that.cnf.v);
            }
        };
    }

    /**
     * Show use notification
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     *
     * @returns {*}
     */
    notification(type, description, duration, onClose = self.blank) {
        if (typeof duration === 'undefined') {
            duration = this.cnf.notificationDuration;
        }

        let message = {
            success: this.lang.success,
            info: this.lang.info,
            warning: this.lang.warning,
            error: this.lang.error,
        }[type];

        return this.cnf.v.$notification[type]({
            placement: this.cnf.notificationPlacement,
            message,
            description,
            duration,
            onClose,
        });
    }

    /**
     * Show use message
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     *
     * @returns {*}
     */
    message(type, description, duration, onClose = self.blank) {
        if (typeof duration === 'undefined') {
            duration = this.cnf.messageDuration;
        }

        return this.cnf.v.$message[type](description, duration, onClose);
    }

    /**
     * Show use confirm
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     * @param options
     *
     * @returns {*}
     */
    confirm(type, description, duration, onClose = self.blank, options = {}) {
        let title = options.title || {
            success: this.lang.success,
            info: this.lang.info,
            warning: this.lang.warning,
            error: this.lang.error,
        }[type];

        if (type === 'confirm' && typeof options.width === 'undefined') {
            options.width = this.popupCosySize().width;
        }

        let modal = this.cnf.v[`$${type}`](Object.assign({
            title,
            content: description,
            okText: this.lang.i_got_it,
            onOk: options.onOk || onClose,
            onCancel: onClose,
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
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    success(description, duration, onClose, type) {
        return this[type || this.cnf.alertType]('success', description, duration, onClose);
    }

    /**
     * Show info
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    info(description, duration, onClose, type) {
        return this[type || this.cnf.alertType]('info', description, duration, onClose);
    }

    /**
     * Show warning
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    warning(description, duration, onClose, type) {
        return this[type || this.cnf.alertType]('warning', description, duration, onClose);
    }

    /**
     * Show error
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    error(description, duration, onClose, type) {
        return this[type || this.cnf.alertType]('error', description, duration, onClose);
    }

    /**
     * Show confirm
     *
     * @param content
     * @param title
     * @param options
     *
     * @return {*}
     */
    showConfirm(content, title, options = {}) {
        return this.cnf.v.$confirm(Object.assign({
            title,
            content,
            keyboard: false,
            width: 320,
            okText: this.lang.confirm,
            cancelText: this.lang.cancel,
        }, options));
    }

    /**
     * Request
     *
     * @param url
     * @param data
     * @param type
     * @param upload
     * @param times
     *
     * @returns {Promise}
     */
    request(url, data = {}, type = this.cnf.method.post, upload = false, times = 1) {
        let that = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: upload ? that.cnf.method.post : type,
                data,
                url,
                processData: !upload,
                contentType: upload ? false : 'application/x-www-form-urlencoded',
                timeout: that.cnf.requestTimeout * 1000 * (upload ? 10 : 1),
                beforeSend: function () {
                    if (that.cnf.v.noLoadingOnce) {
                        that.cnf.v.noLoadingOnce = false;
                    } else {
                        that.cnf.v.spinning = true;
                    }
                },
                success: function (data) {
                    that.cnf.v.spinning = false;
                    resolve(data);
                },
                error: function (obj) {
                    that.cnf.v.spinning = false;
                    if (obj.responseJSON) {
                        let result = obj.responseJSON;
                        let message = `[${result.code}] ${result.message}`;
                        return that.confirm(result.classify, message, 0);
                    }

                    if (obj.responseText) {
                        let message = `[${obj.status}] ${obj.statusText}`;
                        return that.confirm('error', message, 0);
                    }

                    if (obj.statusText === 'timeout') {
                        console.warn('Client request timeout: ', obj);
                        console.warn(`Retry current request in times ${times}`);
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
     * @param result
     * @param successSameHandler
     * @param failedSameHandler
     * @param duration
     */
    response(result, successSameHandler, failedSameHandler, duration) {
        if (typeof result.code === 'undefined') {
            return this.error(this.lang.response_error_message);
        }

        let that = this;
        return new Promise(function (resolve, reject) {

            let failedHandler = function (result) {
                reject(result);
                if (typeof result.sets.href !== 'undefined') {
                    location.href = result.sets.href || location.href;
                }
            };

            let successHandler = function (result) {
                resolve(result);
                if (typeof result.sets.href !== 'undefined') {
                    location.href = result.sets.href || location.href;
                }
            };

            if (result.error) {
                if (result.message) {
                    let duration = that.isNull(result.duration) ? undefined : result.duration;
                    that[result.classify](result.message, duration, null, result.type).then(() => {
                        failedHandler(result);
                    }).catch((reason => {
                        console.warn(reason);
                    }));
                } else {
                    failedHandler(result);
                }
                failedSameHandler && failedSameHandler(result);

            } else {

                if (result.message) {
                    let duration = that.isNull(result.duration) ? undefined : result.duration;
                    that[result.classify](result.message, duration, null, result.type).then(function () {
                        successHandler(result);
                    }).catch((reason => {
                        console.warn(reason);
                    }));
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
    popupCosySize(honest = false) {
        let width = document.body.clientWidth;
        let height = document.body.clientHeight;

        if (!honest) {
            width *= (width < 1285 ? 1 : .7);
            height *= (height < 666 ? .9 : .75);
        }

        return {width, height};
    }

    /**
     * Encrypt by rsa public key
     *
     * @param text
     *
     * @returns string
     */
    rsaEncrypt(text) {
        let encrypt = new JSEncrypt();
        encrypt.setPublicKey(this.cnf.v.rsaPublicKey);

        return encrypt.encrypt(text);
    }

    /**
     * Base64 decode
     *
     * @param text
     *
     * @returns {string}
     */
    base64Decode(text) {
        return decodeURIComponent(atob(text));
    }

    /**
     * Base64 decode (array)
     *
     * @param target
     *
     * @returns {*}
     */
    arrayBase64Decode(target) {
        for (let key in target) {
            if (!target.hasOwnProperty(key)) {
                continue;
            }
            if (this.isJson(target[key])) {
                target[key] = this.arrayBase64Decode(target[key]);
            } else if (this.isString(target[key])) {
                target[key] = this.base64Decode(target[key]);
            }
        }
        return target;
    }

    /**
     * Json fn handler
     *
     * @param target
     * @param fnPrefix
     * @param fnTag
     *
     * @returns {*}
     */
    jsonFnHandler(target, fnPrefix, fnTag = 'fn') {
        let that = this;
        for (let key in target) {
            if (!target.hasOwnProperty(key)) {
                continue;
            }
            let item = target[key];
            if (that.isJson(item)) {
                target[key] = that.jsonFnHandler(item, fnPrefix, fnTag);
            } else if (that.isString(item) && item.startsWith(`${fnTag}:`)) {
                let fn = that.ucFirst(item.split(':')[1]);
                fn = `${fnPrefix}${fn}`;
                if (typeof that.cnf.v[fn] !== 'undefined') {
                    target[key] = that.cnf.v[fn];
                } else if (typeof that[fn] !== 'undefined') {
                    target[key] = that[fn];
                } else {
                    target[key] = that.blank;
                    console.warn(`Method ${fn} is undefined.`, target);
                }
            }
        }
        return target;
    }

    /**
     * Create chart
     *
     * @param option
     *
     * @returns void
     */
    chart(option) {
        let that = this;
        let o = option.option;
        let chart = echarts.init(document.getElementById(`chart-${option.id}`), option.theme);

        chart.setOption(that.jsonFnHandler(o, 'chartHandler'));
        this.cnf.v.$nextTick(function () {
            chart.resize();
        });

        $(window).resize(() => chart.resize());
    }

    /**
     * Chart handler -> tooltip stack
     *
     * @param params
     *
     * @returns {string}
     */
    chartHandlerTooltipStack(params) {
        let total = 0;
        for (let item of params) {
            total += Math.floor(Number.parseFloat(item.data) * 100);
        }

        total /= 100;

        let tpl = `${params[0].name} (${total})<br>`;
        for (let item of params) {
            let percent = ((Number.parseFloat(item.data) / total) || 0) * 100;
            percent = percent.toFixed(2);
            tpl += `${item.marker} ${item.seriesName}: ${item.data} (${percent}%)<br>`;
        }

        return tpl;
    }

    /**
     * Chart handler -> tooltip normal
     *
     * @param params
     *
     * @returns {string}
     */
    chartHandlerTooltipNormal(params) {
        return params[0].name + ': ' + params[0].value;
    }

    /**
     * Chart handler -> tooltip position fixed
     *
     * @param pos
     * @param params
     * @param dom
     * @param rect
     * @param size
     *
     * @returns {{top: number}}
     */
    chartHandlerTooltipPositionFixed(pos, params, dom, rect, size) {
        let obj = {top: 20};
        obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 10;
        return obj;
    }

    /**
     * Show message
     *
     * @param options
     */
    showMessage(options) {
        let classify = options.classify || 'info';
        let duration = this.isNull(options.duration) ? undefined : options.duration;
        try {
            this[classify](options.content, duration, null, options.type);
        } catch (e) {
            console.warn(this.lang.message_data_error, options);
            console.warn(e);
        }
    }

    /**
     * Show modal popup
     *
     * @param options
     */
    showModal(options) {
        let v = this.cnf.v;
        v.modal.visible = false;
        options.visible = true;
        if (typeof options.width === 'undefined') {
            options.width = this.popupCosySize().width;
        }
        options = Object.assign(v.modal, options);
        if (options.footer) {
            v.footer = '_footer';
        } else {
            v.footer = 'footer';
        }
        v.modal = options;
    }

    /**
     * Show drawer popup
     *
     * @param options
     */
    showDrawer(options) {
        let v = this.cnf.v;
        v.drawer.visible = false;
        options.visible = true;
        if (typeof options.width === 'undefined') {
            options.width = this.popupCosySize().width;
        }
        options = Object.assign(v.drawer, options);
        v.drawer = options;
    }

    /**
     * Show result popup
     *
     * @param options
     */
    showResult(options) {
        let v = this.cnf.v;
        v.result.visible = false;
        options.visible = true;
        options = Object.assign(v.result, options);
        v.result = options;
    }

    /**
     * Show modal after request
     *
     * @param data
     * @param element
     */
    showModalAfterRequest(data, element) {
        this.request(data.location).then((res) => {
            this.response(res).then(() => {
                let options = this.jsonFilter(Object.assign(data, {
                    width: res.sets.width || data.width || undefined,
                    title: res.sets.title || data.title || this.lang.modal_title,
                    content: res.sets.content,
                }));
                this.showModal(options);
            }).catch((reason => {
                console.warn(reason);
            }));
        }).catch((reason => {
            console.warn(reason);
        }));
    }

    /**
     * Show iframe by popup (modal/drawer)
     *
     * @param data
     * @param element
     */
    showIFrame(data, element) {
        let v = this.cnf.v;
        let that = this;
        let size = that.popupCosySize();
        let repair = $(element).prev().attr('id');
        data.location = that.setParams({iframe: true, repair}, data.location);

        let options = that.jsonFilter(Object.assign(data, {
            width: data.width || size.width,
            title: data.title === false ? data.title : (data.title || that.lang.please_select),
            content: `<iframe id="bsw-iframe" src="${data.location}"></iframe>`,
        }));

        let mode = data.shape || 'modal';
        if (mode === 'drawer') {
            that.showDrawer(options);
            v.$nextTick(function () {
                let iframe = $("#bsw-iframe");
                let headerHeight = options.title ? 55 : 0;
                let footerHeight = options.footer ? 73 : 0;
                let height = that.popupCosySize(true).height;
                if (options.placement === 'top' || options.placement === 'bottom') {
                    height = options.height || 512;
                }
                iframe.height(height - headerHeight - footerHeight);
                iframe.parents("div.ant-drawer-body").css({margin: 0, padding: 0});
            });
        } else {
            that.showModal(options);
            v.$nextTick(function () {
                let iframe = $("#bsw-iframe");
                iframe.height(data.height || size.height);
                iframe.parents("div.ant-modal-body").css({margin: 0, padding: 0});
            });
        }
    }

    /**
     * Modal onclick ok
     *
     * @param event
     */
    modalOnOk(event) {
        if (event) {
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.modal.visible = false;
    }

    /**
     * Modal onclick cancel
     *
     * @param event
     */
    modalOnCancel(event) {
        if (event) {
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.modal.visible = false;
    }

    /**
     * Drawer onclick ok
     *
     * @param event
     */
    drawerOnOk(event) {
        if (event) {
            let element = $(event.target).parents('.bsw-footer-bar');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.drawer.visible = false;
    }

    /**
     * Drawer onclick cancel
     *
     * @param event
     */
    drawerOnCancel(event) {
        if (event) {
            let element = $(event.target).parents('.bsw-footer-bar');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.drawer.visible = false;
    }

    /**
     * Result onclick ok
     *
     * @param event
     */
    resultOnOk(event) {
        if (event) {
            let element = $(event.target).parent().find('.bsw-result-data');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.result.visible = false;
    }

    /**
     * Result onclick cancel
     *
     * @param event
     */
    resultOnCancel(event) {
        if (event) {
            let element = $(event.target).parent().find('.bsw-result-data');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        bsw.cnf.v.result.visible = false;
    }

    /**
     * Change image captcha
     *
     * @param selector
     */
    changeImageCaptcha(selector = 'img.bsw-captcha') {
        let that = this;
        $(selector).off('click').on('click', function () {
            let src = $(this).attr('src');
            src = that.setParams({t: that.timestamp()}, src);
            $(this).attr('src', src);
        });
    }

    /**
     * Message auto discovery
     */
    messageAutoDiscovery(discovery) {
        let that = this;
        // message
        if (typeof discovery.message.content !== 'undefined') {
            that.showMessage(that.arrayBase64Decode(discovery.message));
        }
        // modal
        if (typeof discovery.modal.content !== 'undefined') {
            that.showModal(that.arrayBase64Decode(discovery.modal));
        }
        // result
        if (typeof discovery.result.title !== 'undefined') {
            that.showResult(that.arrayBase64Decode(discovery.result));
        }
    }

    /**
     * Get bsw data
     *
     * @param object
     *
     * @returns {*|{}}
     */
    getBswData(object) {
        return object[0].dataBsw || object.data('bsw') || {};
    }

    /**
     * Dispatcher by bsw data
     *
     * @param data
     * @param element
     */
    dispatcherByBswData(data, element) {
        let that = this;
        if (data.iframe) {
            delete data.iframe;
            parent.postMessage({data, function: 'dispatcherByBswData'}, '*');
            return;
        }
        let action = function () {
            if (!data.function || data.function.length === 0) {
                return console.warn(`Attribute function should be configure in options.`, data);
            }
            if (typeof that.cnf.v[data.function] !== 'undefined') {
                return that.cnf.v[data.function](data, element);
            } else if (typeof that[data.function] !== 'undefined') {
                return that[data.function](data, element);
            }
            return console.warn(`Method ${data.function} is undefined.`, data);
        };
        if (typeof data.confirm === 'undefined') {
            return action();
        }
        that.showConfirm(data.confirm, that.lang.confirm_title, {
            onOk: () => {
                action();
                return false;
            }
        });
    }

    /**
     * Dispatcher by bsw data element
     *
     * @param element
     * @param fn
     */
    dispatcherByBswDataElement(element, fn) {
        if (!element.length) {
            return;
        }
        let that = this;
        let data = element[0].dataBsw;
        if (data[fn]) {
            if (typeof that.cnf.v[data[fn]] !== 'undefined') {
                return that.cnf.v[data[fn]](data, element);
            } else if (typeof that[data[fn]] !== 'undefined') {
                return that[data[fn]](data, element);
            }
            return console.warn(`Method ${data[fn]} is undefined.`, data);
        }
    }

    /**
     * Redirect (by bsw data)
     *
     * @param data
     * {*}
     */
    redirect(data) {
        if (data.function && data.function !== 'redirect') {
            return this.dispatcherByBswData(data, $('body'));
        }
        let url = data.location;
        if (this.isMobile() && this.cnf.v.mobileDefaultCollapsed) {
            this.cookie().set('bsw_menu_collapsed', 'yes');
        }
        if (url.startsWith('http') || url.startsWith('/')) {
            if (typeof data.window === 'undefined') {
                return location.href = url;
            } else {
                return window.open(url);
            }
        }
    }

    /**
     * Form item filter option
     *
     * @param input
     * @param option
     *
     * @returns {boolean}
     */
    formItemFilterOption(input, option) {
        return option.componentOptions.children[0].text.toUpperCase().indexOf(input.toUpperCase()) >= 0;
    }

    /**
     * Init ck editor
     *
     * @param form
     * @param selector
     */
    initCkEditor(form = 'persistenceForm', selector = '.bsw-persistence .bsw-ck') {
        if (!window.DecoupledEditor) {
            return;
        }
        let that = this;
        let v = this.cnf.v;
        $(selector).each(function () {
            let em = this;
            let id = $(em).prev('textarea').attr('id');
            let container = $(em).find('.bsw-ck-editor');
            DecoupledEditor.create(container[0], {
                language: that.lang.i18n_editor,
                placeholder: $(em).attr('placeholder'),
            }).then(editor => {
                v.ckEditor[id] = editor;
                editor.isReadOnly = $(em).attr('disabled') === 'disabled';
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new FileUploadAdapter(editor, loader, v.init.uploadApiUrl);
                };
                v.ckEditor[id].model.document.on('change:data', function () {
                    if (v[form]) {
                        v[form].setFieldsValue({[id]: v.ckEditor[id].getData()});
                    }
                });
                $(em).find('.bsw-ck-toolbar').append(editor.ui.view.toolbar.element);
            }).catch(err => {
                console.warn(err.stack);
            });
        });
    }

    /**
     * Init clipboard
     *
     * @param selector
     */
    initClipboard(selector = '.ant-btn') {
        if (!window.Clipboard) {
            return;
        }
        let that = this;
        let clipboard = new Clipboard(selector, {
            text: function (trigger) {
                if (typeof that.cnf.v.copy !== 'undefined' && that.cnf.v.copy) {
                    let text = that.cnf.v.copy;
                    that.cnf.v.copy = null;
                    return text;
                }
                return trigger.getAttribute('data-clipboard-text');
            }
        });
        clipboard.on('success', function (e) {
            that.success(that.lang.copy_success, 3);
            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            that.error(that.lang.copy_failed, 3);
            console.warn('Clipboard operation error', e);
        });
    }

    /**
     * [in parent] Dispatcher by bsw data
     *
     * @param data
     * @param element
     */
    dispatcherByBswDataInParent(data, element) {
        let that = this;
        this.modalOnCancel();
        this.drawerOnCancel();
        this.cnf.v.$nextTick(function () {
            if (typeof data.data.location !== 'undefined') {
                data.data.location = that.unsetParams(['iframe'], data.data.location);
            }
            that.dispatcherByBswData(data.data, element);
        });
    }

    /**
     * [in parent] Filter form item
     *
     * @param data
     * @param element
     * @param form
     */
    fillParentFormInParent(data, element, form = 'persistenceForm') {
        let v = this.cnf.v;
        this.modalOnCancel();
        this.drawerOnCancel();
        v.$nextTick(function () {
            if (v[form] && data.repair) {
                v[form].setFieldsValue({[data.repair]: data.ids});
            }
        });
    }

    /**
     * [in parent] fill form after ajax
     *
     * @param res
     * @param element
     */
    fillParentFormAfterAjaxInParent(res, element) {
        let data = res.response.sets;
        data.repair = data.arguments.repair;
        this.fillParentFormInParent(data, element);
    }

    /**
     * [in parent] Handler response
     *
     * @param data
     * @param element
     */
    handleResponseInParent(data, element) {
        let that = this;
        this.modalOnCancel();
        this.drawerOnCancel();
        this.cnf.v.$nextTick(function () {
            that.response(data.response).catch((reason => {
                console.warn(reason);
            }));
        });
    }

    /**
     * [in parent] Show iframe
     *
     * @param data
     * @param element
     */
    showIFrameInParent(data, element) {
        this.showIFrame(data.response.sets, element);
    }

    /**
     * Full screen
     *
     * @param data
     * @param element
     */
    fullScreenToggle(data, element) {
        if (!window.screenfull) {
            return;
        }
        let container = $(data.element)[0];
        if (screenfull.isEnabled) {
            screenfull.toggle(container);
        } else {
            console.warn('Your browser is not supported.');
        }
    }

    /**
     * WeChat pay by js api
     *
     * @param config object
     *
     * @returns void
     */
    wxJsApiPay(config) {
        if (!window.WeixinJSBridge) {
            console.log("The api just work in WeChat browser.");
            return;
        }
        WeixinJSBridge.invoke('getBrandWCPayRequest', config, function (result) {
                console.log(result);
                if (result.err_msg === "get_brand_wcpay_request:ok") {
                    console.log('Success');
                }
            }
        );
    }
}

// -- eof --
