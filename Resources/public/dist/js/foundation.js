/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-07-23 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}function _possibleConstructorReturn(a,b){if(!a)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!b||"object"!=typeof b&&"function"!=typeof b?a:b}function _inherits(a,b){if("function"!=typeof b&&null!==b)throw new TypeError("Super expression must either be null or a function, not "+typeof b);a.prototype=Object.create(b&&b.prototype,{constructor:{value:a,enumerable:!1,writable:!0,configurable:!0}}),b&&(Object.setPrototypeOf?Object.setPrototypeOf(a,b):a.__proto__=b)}function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a},_createClass=function(){function a(a,b){for(var c=0;c<b.length;c++){var d=b[c];d.enumerable=d.enumerable||!1,d.configurable=!0,"value"in d&&(d.writable=!0),Object.defineProperty(a,d.key,d)}}return function(b,c,d){return c&&a(b.prototype,c),d&&a(b,d),b}}(),FoundationPrototype=function(){function a(){_classCallCheck(this,a)}return _createClass(a,[{key:"trim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(^["+b+"]*)|(["+b+"]*$)","g"),"")}},{key:"leftTrim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(^["+b+"]*)","g"),"")}},{key:"rightTrim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(["+b+"]*$)","g"),"")}},{key:"pad",value:function(a,b,c,d){if(b=b.toString(),d=d||"left",a.length>=c||!["left","right","both"].contains(d))return a;var e=void 0,f=void 0,g=(c-a.length)%b.length;e=f=Math.floor((c-a.length)/b.length),g>0&&(e+=1);for(var h=a,i=0;i<e;i++)switch(i===f&&(b=b.substr(0,g)),d){case"left":h=b+h;break;case"right":h+=b;break;case"both":h=0===i%2?b+h:h+b}return h}},{key:"fill",value:function(a,b,c,d){if(b=b.toString(),d=d||"left",c<1||!["left","right","both"].contains(d))return a;for(var e=a,f=0;f<c;f++)switch(d){case"left":e=b+e;break;case"right":e+=b;break;case"both":e=0===f%2?b+e:e+b}return e}},{key:"repeat",value:function(a,b){return b=isNaN(b)||b<1?1:b+1,new Array(b).join(a)}},{key:"ucWords",value:function(a){return a.replace(/\b(\w)+\b/g,function(a){return a.replace(a.charAt(0),a.charAt(0).toUpperCase())})}},{key:"ucFirst",value:function(a){return a.replace(a.charAt(0),a.charAt(0).toUpperCase())}},{key:"lcFirst",value:function(a){return a.replace(a.charAt(0),a.charAt(0).toLowerCase())}},{key:"bigHump",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_",c=new RegExp(b,"g");return this.ucWords(a.replace(c," ")).replace(/ /g,"")}},{key:"smallHump",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_";return this.lcFirst(this.bigHump(a,b))}},{key:"humpToUnder",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_";return this.leftTrim(a.replace(/([A-Z])/g,b+"$1").toLowerCase(),b)}},{key:"format",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"yyyy-MM-dd hh:mm:ss",c={"M+":a.getMonth()+1,"d+":a.getDate(),"h+":a.getHours(),"m+":a.getMinutes(),"s+":a.getSeconds(),"q+":Math.floor((a.getMonth()+3)/3),S:a.getMilliseconds()};/(y+)/.test(b)&&(b=b.replace(RegExp.$1,(a.getFullYear()+"").substr(4-RegExp.$1.length)));for(var d in c)c.hasOwnProperty(d)&&new RegExp("("+d+")").test(b)&&(b=b.replace(RegExp.$1,1===RegExp.$1.length?c[d]:("00"+c[d]).substr((""+c[d]).length)));return b}},{key:"arrayUnique",value:function(a){return Array.from(new Set(a))}},{key:"arrayRemoveValue",value:function(a,b){var c=a.indexOf(b);return c>-1&&a.splice(c,1),a}},{key:"arrayIntersect",value:function(a,b){return a.filter(function(a){return b.indexOf(a)>-1})}},{key:"arrayDifference",value:function(a,b){return a.filter(function(a){return b.indexOf(a)===-1})}},{key:"arrayComplement",value:function(a,b){return a.filter(function(a){return!(b.indexOf(a)>-1)}).concat(b.filter(function(b){return!(a.indexOf(b)>-1)}))}},{key:"arrayUnion",value:function(a,b){return a.concat(b.filter(function(b){return!(a.indexOf(b)>-1)}))}},{key:"swap",value:function(a,b,c){return a[b]=a.splice(c,1,a[b])[0],a}},{key:"up",value:function(a,b){return 0===b?a:this.swap(a,b,b-1)}},{key:"down",value:function(a,b){return b===a.length-1?a:this.swap(a,b,b+1)}}]),a}(),FoundationTools=function(a){function b(){return _classCallCheck(this,b),_possibleConstructorReturn(this,(b.__proto__||Object.getPrototypeOf(b)).apply(this,arguments))}return _inherits(b,a),_createClass(b,[{key:"blank",value:function(){}},{key:"isArray",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&a.constructor===Array)}},{key:"isObject",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&a.constructor===Object)}},{key:"isNull",value:function(a){return!a&&("undefined"!=typeof a&&0!==a)}},{key:"isJson",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&"[object object]"===Object.prototype.toString.call(a).toLowerCase())}},{key:"isString",value:function(a){return null!==a&&("string"==typeof a&&a.constructor===String)}},{key:"isNumeric",value:function(a){return null!==a&&""!==a&&!isNaN(a)}},{key:"isBoolean",value:function(a){return null!==a&&("boolean"==typeof a&&a.constructor===Boolean)}},{key:"isFunction",value:function(a){return null!==a&&("function"==typeof a&&"[object function]"===Object.prototype.toString.call(a).toLowerCase())}},{key:"jsonLength",value:function(a){var b=0;for(var c in a)a.hasOwnProperty(c)&&b++;return b}},{key:"offset",value:function(a){a=a.jquery?a:$(a);var b=a.offset();return{left:b.left,top:b.top,width:a[0].offsetWidth,height:a[0].offsetHeight}}},{key:"timestamp",value:function(){var a=arguments.length>0&&void 0!==arguments[0]&&arguments[0],b=(new Date).getTime();return a?Math.ceil(b/1e3):b}},{key:"parseQueryString",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,b=arguments.length>1&&void 0!==arguments[1]&&arguments[1];a=decodeURIComponent(a||location.href),a.indexOf("?")===-1&&(a+="?");var c={},d=a.split("?");if(b&&(c.hostPart=d[0]),a=d[1],0===a.length)return c;a.indexOf("#")&&(a=a.split("#")[0]),a=a.split("&");var e=!0,f=!1,g=void 0;try{for(var h,i=a[Symbol.iterator]();!(e=(h=i.next()).done);e=!0){var j=h.value;j=j.split("="),c[j[0]]=j[1]}}catch(a){f=!0,g=a}finally{try{!e&&i.return&&i.return()}finally{if(f)throw g}}return c}},{key:"jsonBuildQuery",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=!(arguments.length>2&&void 0!==arguments[2])||arguments[2],d="",e={},f=void 0,g=void 0,h=void 0,i=void 0,j=void 0,k=void 0;for(var l in a)if(a.hasOwnProperty(l))if(f=a[l],this.isArray(f))for(k=0;k<f.length;++k)i=f[k],g=l+"["+k+"]",j={},j[g]=i,d+=this.jsonBuildQuery(j,b,c)+"&",e=Object.assign(e,this.jsonBuildQuery(j,b,c));else if(this.isObject(f))for(h in f)f.hasOwnProperty(h)&&(i=f[h],g=l+"["+h+"]",j={},j[g]=i,d+=this.jsonBuildQuery(j,b,c)+"&",e=Object.assign(e,this.jsonBuildQuery(j,b,c)));else void 0!==f&&null!==f&&(c&&(l=encodeURIComponent(l),f=encodeURIComponent(f)),d+=l+"="+f+"&",e[l]=f);return b?e:d.length?d.substr(0,d.length-1):d}},{key:"setParams",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=this.parseQueryString(b,!0),e=d.hostPart;delete d.hostPart,a=Object.assign(d,this.jsonBuildQuery(a,!0,c));var f=this.jsonBuildQuery(a);return b=e+"?"+f,this.trim(b,"?")}},{key:"unsetParams",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};b=b||location.href;var e=this.parseQueryString(b,!0),f=!0,g=!1,h=void 0;try{for(var i,j=(a||[])[Symbol.iterator]();!(f=(i=j.next()).done);f=!0){var k=i.value;"undefined"!=typeof e[k]&&(d[k]=e[k],delete e[k])}}catch(a){g=!0,h=a}finally{try{!f&&j.return&&j.return()}finally{if(g)throw h}}var l=e.hostPart;return delete e.hostPart,b=l+"?"+this.jsonBuildQuery(e,c),this.trim(b,"?")}},{key:"unsetParamsBeginWith",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};b=b||location.href;var e=this.parseQueryString(b,!0),f=!0,g=!1,h=void 0;try{for(var i,j=(a||[])[Symbol.iterator]();!(f=(i=j.next()).done);f=!0){var k=i.value;for(var l in e)e.hasOwnProperty(l)&&l.startsWith(k)&&(d[l]=e[l],delete e[l])}}catch(a){g=!0,h=a}finally{try{!f&&j.return&&j.return()}finally{if(g)throw h}}var m=e.hostPart;return delete e.hostPart,b=m+"?"+this.jsonBuildQuery(e,c),this.trim(b,"?")}},{key:"pam",value:function(a,b,c,d){b=b||1,c=c||["margin","padding"],d=d||["left","right"];var e=0;return c.each(function(c){d.each(function(d){e+=parseInt(a.css(c+"-"+d))*b})}),e}},{key:"device",value:function(){var a=navigator.userAgent;return{ie:a.indexOf("Trident")>-1,opera:a.indexOf("Presto")>-1,chrome:a.indexOf("AppleWebKit")>-1,firefox:a.indexOf("Gecko")>-1&&a.indexOf("KHTML")===-1,mobile:!!a.match(/AppleWebKit.*Mobile.*/),ios:!!a.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),android:a.indexOf("Android")>-1||a.indexOf("Linux")>-1,iPhone:a.indexOf("iPhone")>-1,iPad:a.indexOf("iPad")>-1,webApp:a.indexOf("Safari")===-1,version:navigator.appVersion}}},{key:"isMobile",value:function(){return/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)}},{key:"rand",value:function(a,b){b=b||0;var c=b,d=a-c;return parseInt(Number(Math.random()*d).toFixed(0))+c}},{key:"keyBind",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]&&arguments[3];c=c||$(document),c.unbind("keydown").bind("keydown",function(c){d?c.keyCode===a&&c.ctrlKey&&b&&b():c.keyCode===a&&b&&b()})}},{key:"media",value:function(){var a=document.body.clientWidth;return{xs:a<576,sm:a>=576,md:a>=768,lg:a>=992,xl:a>=1200,xxl:a>=1600}}},{key:"parseInt",value:function(a){function b(b){return a.apply(this,arguments)}return b.toString=function(){return a.toString()},b}(function(a){return a=parseInt(a),isNaN(a)?0:a})},{key:"cookie",value:function(){return{set:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:31536e3,d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:null,e=new Date;return e.setTime(e.getTime()+1e3*c),d=d?"; domain="+d:"",document.cookie=a+"="+encodeURI(b)+"; expires="+e.toUTCString()+"; path=/"+d,b},get:function(a){for(var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=document.cookie.split("; "),d=0;d<c.length;d++){var e=c[d].split("=");if(e[0]===a)return unescape(e[1])}return b},delete:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;b=b?"; domain="+b:"",document.cookie=a+"=; expires="+new Date(0).toUTCString()+"; path=/"+b}}}},{key:"cookieMapNext",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]&&arguments[3],e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:null,f=this.cookie(),g=f.get(a,c),h=b[g]||c;if(e){var i=this.ucFirst(h);this.message("success",e+": "+i)}return d?f.set(a,h):h}},{key:"cookieMapCurrent",value:function(a,b,c){var d=this.cookie().get(a,c);return b[d]?d:c}},{key:"evalExpr",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,c=null;try{c=window.eval("("+a+")")}catch(b){console.warn("Expression has syntax error: "+a),console.warn(b)}return c?c:b}},{key:"switchClass",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"html",d=$(c);d.removeClass(a),"yes"===b&&d.addClass(a)}},{key:"checkJsonDeep",value:function(a,b){var c=a;b=b.split(".");var d=!0,e=!1,f=void 0;try{for(var g,h=b[Symbol.iterator]();!(d=(g=h.next()).done);d=!0){var i=g.value;if("undefined"==typeof c[i]||!c[i])return!1;c=c[i]}}catch(a){e=!0,f=a}finally{try{!d&&h.return&&h.return()}finally{if(e)throw f}}return!0}},{key:"jsonFilter",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:["",null];for(var c in a)a.hasOwnProperty(c)&&b.indexOf(a[c])!==-1&&delete a[c];return a}}]),b}(FoundationPrototype),FoundationAntD=function(a){function b(a,c,d){var e=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};_classCallCheck(this,b);var f=_possibleConstructorReturn(this,(b.__proto__||Object.getPrototypeOf(b)).call(this));return f.v=c,f.d=d,f.config={},f.lang=e,f.cnf={marginTop:"150px",loadingMarginTop:"250px",shade:.1,zIndex:9999,requestTimeout:30,notificationDuration:5,messageDuration:5,confirmDuration:5,alertType:"message",notificationPlacement:"topRight",v:null,method:{get:"GET",post:"POST"}},f}return _inherits(b,a),_createClass(b,[{key:"configure",value:function(a){for(var b in a)a.hasOwnProperty(b)&&(this.config[b]=Object.assign(this.config[b]||{},a[b]))}},{key:"vue",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:".bsw-vue",b=this,c={};return{template:function(a){return a&&(c.template=a),this},data:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.data=function(){return a},this},computed:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.computed=a,this},method:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.methods=a,this},component:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.components=a,this},directive:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.directives=a,this},watch:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.watch=a,this},extra:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c=Object.assign(c,a),this},init:function(){var d=arguments.length>0&&void 0!==arguments[0]?arguments[0]:self.blank;c.el=a,b.cnf.v=new b.v(c),b.cnf.v.$nextTick(function(){for(var a in b.config.logic||[])b.config.logic.hasOwnProperty(a)&&b.config.logic[a](b.cnf.v)}),b.changeImageCaptcha(),d(b.cnf.v)}}}},{key:"notification",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank;"undefined"==typeof c&&(c=this.cnf.notificationDuration);var e={success:this.lang.success,info:this.lang.info,warning:this.lang.warning,error:this.lang.error}[a];return this.cnf.v.$notification[a]({placement:this.cnf.notificationPlacement,message:e,description:b,duration:c,onClose:d})}},{key:"message",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank;return"undefined"==typeof c&&(c=this.cnf.messageDuration),this.cnf.v.$message[a](b,c,d)}},{key:"confirm",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank,e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:{},f=e.title||{success:this.lang.success,info:this.lang.info,warning:this.lang.warning,error:this.lang.error}[a];"confirm"===a&&"undefined"==typeof e.width&&(e.width=this.popupCosySize().width);var g=this.cnf.v["$"+a](Object.assign({title:f,content:b,okText:this.lang.i_got_it,onOk:e.onOk||d,onCancel:d},e));return"undefined"==typeof c&&(c=this.cnf.confirmDuration),c&&setTimeout(function(){g.destroy()},1e3*c),g}},{key:"success",value:function(a,b,c,d){return this[d||this.cnf.alertType]("success",a,b,c)}},{key:"info",value:function(a,b,c,d){return this[d||this.cnf.alertType]("info",a,b,c)}},{key:"warning",value:function(a,b,c,d){return this[d||this.cnf.alertType]("warning",a,b,c)}},{key:"error",value:function(a,b,c,d){return this[d||this.cnf.alertType]("error",a,b,c)}},{key:"showConfirm",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};return this.cnf.v.$confirm(Object.assign({title:b,content:a,keyboard:!1,width:320,okText:this.lang.confirm,cancelText:this.lang.cancel},c))}},{key:"request",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:this.cnf.method.post,d=arguments.length>3&&void 0!==arguments[3]&&arguments[3],e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:1,f=this;return new Promise(function(g,h){$.ajax({type:d?f.cnf.method.post:c,data:b,url:a,processData:!d,contentType:!d&&"application/x-www-form-urlencoded",timeout:1e3*f.cnf.requestTimeout*(d?10:1),beforeSend:function(){f.cnf.v.noLoadingOnce?f.cnf.v.noLoadingOnce=!1:f.cnf.v.spinning=!0},success:function(a){f.cnf.v.spinning=!1,g(a)},error:function(g){if(f.cnf.v.spinning=!1,g.responseJSON){var i=g.responseJSON,j="["+i.code+"] "+i.message;return f.confirm(i.classify,j,0)}if(g.responseText){var k="["+g.status+"] "+g.statusText;return f.confirm("error",k,0)}return"timeout"===g.statusText&&(console.warn("Client request timeout: ",g),console.warn("Retry current request in times "+e),e<=3)?f.request(a,b,c,d,++e):void h(g)}})})}},{key:"response",value:function(a,b,c,d){if("undefined"==typeof a.code)return this.error(this.lang.response_error_message);var e=this;return new Promise(function(d,f){var g=function(a){f(a),"undefined"!=typeof a.sets.href&&(location.href=a.sets.href||location.href)},h=function(a){d(a),"undefined"!=typeof a.sets.href&&(location.href=a.sets.href||location.href)};if(a.error){if(a.message){var i=e.isNull(a.duration)?void 0:a.duration;e[a.classify](a.message,i,null,a.type).then(function(){g(a)}).catch(function(a){console.warn(a)})}else g(a);c&&c(a)}else{if(a.message){var j=e.isNull(a.duration)?void 0:a.duration;e[a.classify](a.message,j,null,a.type).then(function(){h(a)}).catch(function(a){console.warn(a)})}else h(a);b&&b(a)}})}},{key:"popupCosySize",value:function(){var a=arguments.length>0&&void 0!==arguments[0]&&arguments[0],b=document.body.clientWidth,c=document.body.clientHeight;return a||(b*=b<1285?1:.7,c*=c<666?.9:.75),{width:b,height:c}}},{key:"rsaEncrypt",value:function(a){var b=new JSEncrypt;return b.setPublicKey(this.cnf.v.rsaPublicKey),b.encrypt(a)}},{key:"base64Decode",value:function(a){return decodeURIComponent(atob(a))}},{key:"arrayBase64Decode",value:function(a){for(var b in a)a.hasOwnProperty(b)&&(this.isJson(a[b])?a[b]=this.arrayBase64Decode(a[b]):this.isString(a[b])&&(a[b]=this.base64Decode(a[b])));return a}},{key:"jsonFnHandler",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"fn",d=this;for(var e in a)if(a.hasOwnProperty(e)){var f=a[e];if(d.isJson(f))a[e]=d.jsonFnHandler(f,b,c);else if(d.isString(f)&&f.startsWith(c+":")){var g=d.ucFirst(f.split(":")[1]);g=""+b+g,"undefined"!=typeof d.cnf.v[g]?a[e]=d.cnf.v[g]:"undefined"!=typeof d[g]?a[e]=d[g]:(a[e]=d.blank,console.warn("Method "+g+" is undefined.",a))}}return a}},{key:"chart",value:function a(b){var c=this,d=b.option,a=echarts.init(document.getElementById("chart-"+b.id),b.theme);a.setOption(c.jsonFnHandler(d,"chartHandler")),this.cnf.v.$nextTick(function(){a.resize()}),$(window).resize(function(){return a.resize()})}},{key:"chartHandlerTooltipStack",value:function(a){var b=0,c=!0,d=!1,e=void 0;try{for(var f,g=a[Symbol.iterator]();!(c=(f=g.next()).done);c=!0){var h=f.value;b+=Math.floor(100*Number.parseFloat(h.data))}}catch(a){d=!0,e=a}finally{try{!c&&g.return&&g.return()}finally{if(d)throw e}}b/=100;var i=a[0].name+" ("+b+")<br>",j=!0,k=!1,l=void 0;try{for(var m,n=a[Symbol.iterator]();!(j=(m=n.next()).done);j=!0){var o=m.value,p=100*(Number.parseFloat(o.data)/b||0);p=p.toFixed(2),i+=o.marker+" "+o.seriesName+": "+o.data+" ("+p+"%)<br>"}}catch(a){k=!0,l=a}finally{try{!j&&n.return&&n.return()}finally{if(k)throw l}}return i}},{key:"chartHandlerTooltipNormal",value:function(a){return a[0].name+": "+a[0].value}},{key:"chartHandlerTooltipPositionFixed",value:function(a,b,c,d,e){var f={top:20};return f[["left","right"][+(a[0]<e.viewSize[0]/2)]]=10,f}},{key:"showMessage",value:function(a){var b=a.classify||"info",c=this.isNull(a.duration)?void 0:a.duration;try{this[b](a.content,c,null,a.type)}catch(b){console.warn(this.lang.message_data_error,a),console.warn(b)}}},{key:"showModal",value:function(a){var b=this.cnf.v;b.modal.visible=!1,a.visible=!0,"undefined"==typeof a.width&&(a.width=this.popupCosySize().width),a=Object.assign(b.modal,a),a.footer?b.footer="_footer":b.footer="footer",b.modal=a}},{key:"showDrawer",value:function(a){var b=this.cnf.v;b.drawer.visible=!1,a.visible=!0,"undefined"==typeof a.width&&(a.width=this.popupCosySize().width),a=Object.assign(b.drawer,a),b.drawer=a}},{key:"showResult",value:function(a){var b=this.cnf.v;b.result.visible=!1,a.visible=!0,a=Object.assign(b.result,a),b.result=a}},{key:"showModalAfterRequest",value:function(a,b){var c=this;this.request(a.location).then(function(b){c.response(b).then(function(){var d=c.jsonFilter(Object.assign(a,{width:b.sets.width||a.width||void 0,title:b.sets.title||a.title||c.lang.modal_title,content:b.sets.content}));c.showModal(d)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})}},{key:"showIFrame",value:function(a,b){var c=this.cnf.v,d=this,e=d.popupCosySize(),f=$(b).prev().attr("id");a.location=d.setParams({iframe:!0,repair:f},a.location);var g=d.jsonFilter(Object.assign(a,{width:a.width||e.width,title:a.title===!1?a.title:a.title||d.lang.please_select,content:'<iframe id="bsw-iframe" src="'+a.location+'"></iframe>'})),h=a.shape||"modal";"drawer"===h?(d.showDrawer(g),c.$nextTick(function(){var a=$("#bsw-iframe"),b=g.title?55:0,c=g.footer?73:0,e=d.popupCosySize(!0).height;"top"!==g.placement&&"bottom"!==g.placement||(e=g.height||512),a.height(e-b-c),a.parents("div.ant-drawer-body").css({margin:0,padding:0})})):(d.showModal(g),c.$nextTick(function(){var b=$("#bsw-iframe");b.height(a.height||e.height),b.parents("div.ant-modal-body").css({margin:0,padding:0})}))}},{key:"modalOnOk",value:function(a){if(a){var b=$(a.target).parents(".ant-modal-footer").prev().find(".bsw-modal-data"),c=bsw.dispatcherByBswDataElement(b,"ok");if(c===!0)return}bsw.cnf.v.modal.visible=!1}},{key:"modalOnCancel",value:function(a){if(a){var b=$(a.target).parents(".ant-modal-footer").prev().find(".bsw-modal-data"),c=bsw.dispatcherByBswDataElement(b,"cancel");if(c===!0)return}bsw.cnf.v.modal.visible=!1}},{key:"drawerOnOk",value:function(a){if(a){var b=$(a.target).parents(".bsw-footer-bar"),c=bsw.dispatcherByBswDataElement(b,"ok");if(c===!0)return}bsw.cnf.v.drawer.visible=!1}},{key:"drawerOnCancel",value:function(a){if(a){var b=$(a.target).parents(".bsw-footer-bar"),c=bsw.dispatcherByBswDataElement(b,"cancel");if(c===!0)return}bsw.cnf.v.drawer.visible=!1}},{key:"resultOnOk",value:function(a){if(a){var b=$(a.target).parent().find(".bsw-result-data"),c=bsw.dispatcherByBswDataElement(b,"ok");if(c===!0)return}bsw.cnf.v.result.visible=!1}},{key:"resultOnCancel",value:function(a){if(a){var b=$(a.target).parent().find(".bsw-result-data"),c=bsw.dispatcherByBswDataElement(b,"cancel");if(c===!0)return}bsw.cnf.v.result.visible=!1}},{key:"changeImageCaptcha",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"img.bsw-captcha",b=this;$(a).off("click").on("click",function(){var a=$(this).attr("src");a=b.setParams({t:b.timestamp()},a),$(this).attr("src",a)})}},{key:"messageAutoDiscovery",value:function(a){var b=this;"undefined"!=typeof a.message.content&&b.showMessage(b.arrayBase64Decode(a.message)),"undefined"!=typeof a.modal.content&&b.showModal(b.arrayBase64Decode(a.modal)),"undefined"!=typeof a.result.title&&b.showResult(b.arrayBase64Decode(a.result))}},{key:"getBswData",value:function(a){return a[0].dataBsw||a.data("bsw")||{}}},{key:"dispatcherByBswData",value:function(a,b){var c=this;if(a.iframe)return delete a.iframe,void parent.postMessage({data:a,function:"dispatcherByBswData"},"*");var d=function(){return a.function&&0!==a.function.length?"undefined"!=typeof c.cnf.v[a.function]?c.cnf.v[a.function](a,b):"undefined"!=typeof c[a.function]?c[a.function](a,b):console.warn("Method "+a.function+" is undefined.",a):console.warn("Attribute function should be configure in options.",a)};return"undefined"==typeof a.confirm?d():void c.showConfirm(a.confirm,c.lang.confirm_title,{onOk:function(){return d(),!1}})}},{key:"dispatcherByBswDataElement",value:function(a,b){if(a.length){var c=this,d=a[0].dataBsw;return d[b]?"undefined"!=typeof c.cnf.v[d[b]]?c.cnf.v[d[b]](d.extra||{},a):"undefined"!=typeof c[d[b]]?c[d[b]](d.extra||{},a):console.warn("Method "+d[b]+" is undefined.",d):void 0}}},{key:"redirect",value:function(a){if(a.function&&"redirect"!==a.function)return this.dispatcherByBswData(a,$("body"));var b=a.location;return this.isMobile()&&this.cnf.v.mobileDefaultCollapsed&&this.cookie().set("bsw_menu_collapsed","yes"),b.startsWith("http")||b.startsWith("/")?"undefined"==typeof a.window?location.href=b:window.open(b):void 0}},{key:"formItemFilterOption",value:function(a,b){return b.componentOptions.children[0].text.toUpperCase().indexOf(a.toUpperCase())>=0}},{key:"initCkEditor",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"persistenceForm",b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:".bsw-persistence .bsw-ck";if(window.DecoupledEditor){var c=this,d=this.cnf.v;$(b).each(function(){var b=this,e=$(b).prev("textarea").attr("id"),f=$(b).find(".bsw-ck-editor");DecoupledEditor.create(f[0],{language:c.lang.i18n_editor,placeholder:$(b).attr("placeholder")}).then(function(c){d.ckEditor[e]=c,c.isReadOnly="disabled"===$(b).attr("disabled"),c.plugins.get("FileRepository").createUploadAdapter=function(a){return new FileUploadAdapter(c,a,d.init.uploadApiUrl)},d.ckEditor[e].model.document.on("change:data",function(){d[a]&&d[a].setFieldsValue(_defineProperty({},e,d.ckEditor[e].getData()))}),$(b).find(".bsw-ck-toolbar").append(c.ui.view.toolbar.element)}).catch(function(a){console.warn(a.stack)})})}}},{key:"initClipboard",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:".ant-btn";if(window.Clipboard){var b=this,c=new Clipboard(a,{text:function a(c){if("undefined"!=typeof b.cnf.v.copy&&b.cnf.v.copy){var a=b.cnf.v.copy;return b.cnf.v.copy=null,a}return c.getAttribute("data-clipboard-text")}});c.on("success",function(a){b.success(b.lang.copy_success,3),a.clearSelection()}),c.on("error",function(a){b.error(b.lang.copy_failed,3),console.warn("Clipboard operation error",a)})}}},{key:"initUpwardInfect",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:".bsw-upward-infect";$(a).each(function(){for(var a=$(this),b=0;b<$(this).data("infect-level");b++)a=a.parent();a.addClass($(this).data("infect-class"))})}},{key:"dispatcherByBswDataInParent",value:function(a,b){var c=this;this.modalOnCancel(),this.drawerOnCancel(),this.cnf.v.$nextTick(function(){"undefined"!=typeof a.data.location&&(a.data.location=c.unsetParams(["iframe"],a.data.location)),c.dispatcherByBswData(a.data,b)})}},{key:"fillParentFormInParent",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"persistenceForm",d=this.cnf.v;this.modalOnCancel(),this.drawerOnCancel(),d.$nextTick(function(){d[c]&&a.repair&&d[c].setFieldsValue(_defineProperty({},a.repair,a.ids))})}},{key:"fillParentFormAfterAjaxInParent",value:function(a,b){var c=a.response.sets;c.repair=c.arguments.repair,this.fillParentFormInParent(c,b)}},{key:"handleResponseInParent",value:function(a,b){var c=this;this.modalOnCancel(),this.drawerOnCancel(),this.cnf.v.$nextTick(function(){c.response(a.response).catch(function(a){console.warn(a)})})}},{key:"showIFrameInParent",value:function(a,b){this.showIFrame(a.response.sets,b)}},{key:"fullScreenToggle",value:function(a,b){if(window.screenfull){var c=$(a.element)[0];screenfull.isEnabled?screenfull.toggle(c):console.warn("Your browser is not supported.")}}},{key:"wxJsApiPay",value:function(a){return window.WeixinJSBridge?void WeixinJSBridge.invoke("getBrandWCPayRequest",a,function(a){console.log(a),"get_brand_wcpay_request:ok"===a.err_msg&&console.log("Success")}):void console.log("The api just work in WeChat browser.")}}]),b}(FoundationTools);