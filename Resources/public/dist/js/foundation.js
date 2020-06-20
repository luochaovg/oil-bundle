/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-06-20 */
"use strict";function _possibleConstructorReturn(a,b){if(!a)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!b||"object"!=typeof b&&"function"!=typeof b?a:b}function _inherits(a,b){if("function"!=typeof b&&null!==b)throw new TypeError("Super expression must either be null or a function, not "+typeof b);a.prototype=Object.create(b&&b.prototype,{constructor:{value:a,enumerable:!1,writable:!0,configurable:!0}}),b&&(Object.setPrototypeOf?Object.setPrototypeOf(a,b):a.__proto__=b)}function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a},_createClass=function(){function a(a,b){for(var c=0;c<b.length;c++){var d=b[c];d.enumerable=d.enumerable||!1,d.configurable=!0,"value"in d&&(d.writable=!0),Object.defineProperty(a,d.key,d)}}return function(b,c,d){return c&&a(b.prototype,c),d&&a(b,d),b}}(),FoundationPrototype=function(){function a(){_classCallCheck(this,a)}return _createClass(a,[{key:"trim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(^["+b+"]*)|(["+b+"]*$)","g"),"")}},{key:"leftTrim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(^["+b+"]*)","g"),"")}},{key:"rightTrim",value:function(a,b){return b=b?"\\s"+b:"\\s",a.replace(new RegExp("(["+b+"]*$)","g"),"")}},{key:"pad",value:function(a,b,c,d){if(b=b.toString(),d=d||"left",a.length>=c||!["left","right","both"].contains(d))return a;var e=void 0,f=void 0,g=(c-a.length)%b.length;e=f=Math.floor((c-a.length)/b.length),g>0&&(e+=1);for(var h=a,i=0;i<e;i++)switch(i===f&&(b=b.substr(0,g)),d){case"left":h=b+h;break;case"right":h+=b;break;case"both":h=0===i%2?b+h:h+b}return h}},{key:"fill",value:function(a,b,c,d){if(b=b.toString(),d=d||"left",c<1||!["left","right","both"].contains(d))return a;for(var e=a,f=0;f<c;f++)switch(d){case"left":e=b+e;break;case"right":e+=b;break;case"both":e=0===f%2?b+e:e+b}return e}},{key:"repeat",value:function(a,b){return b=isNaN(b)||b<1?1:b+1,new Array(b).join(a)}},{key:"ucWords",value:function(a){return a.replace(/\b(\w)+\b/g,function(a){return a.replace(a.charAt(0),a.charAt(0).toUpperCase())})}},{key:"ucFirst",value:function(a){return a.replace(a.charAt(0),a.charAt(0).toUpperCase())}},{key:"lcFirst",value:function(a){return a.replace(a.charAt(0),a.charAt(0).toLowerCase())}},{key:"bigHump",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_",c=new RegExp(b,"g");return this.ucWords(a.replace(c," ")).replace(/ /g,"")}},{key:"smallHump",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_";return this.lcFirst(this.bigHump(a,b))}},{key:"humpToUnder",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_";return this.leftTrim(a.replace(/([A-Z])/g,b+"$1").toLowerCase(),b)}},{key:"format",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"yyyy-MM-dd hh:mm:ss",c={"M+":a.getMonth()+1,"d+":a.getDate(),"h+":a.getHours(),"m+":a.getMinutes(),"s+":a.getSeconds(),"q+":Math.floor((a.getMonth()+3)/3),S:a.getMilliseconds()};/(y+)/.test(b)&&(b=b.replace(RegExp.$1,(a.getFullYear()+"").substr(4-RegExp.$1.length)));for(var d in c)c.hasOwnProperty(d)&&new RegExp("("+d+")").test(b)&&(b=b.replace(RegExp.$1,1===RegExp.$1.length?c[d]:("00"+c[d]).substr((""+c[d]).length)));return b}},{key:"arrayUnique",value:function(a){return Array.from(new Set(a))}},{key:"arrayRemoveValue",value:function(a,b){var c=a.indexOf(b);return c>-1&&a.splice(c,1),a}},{key:"arrayIntersect",value:function(a,b){return a.filter(function(a){return b.indexOf(a)>-1})}},{key:"arrayDifference",value:function(a,b){return a.filter(function(a){return b.indexOf(a)===-1})}},{key:"arrayComplement",value:function(a,b){return a.filter(function(a){return!(b.indexOf(a)>-1)}).concat(b.filter(function(b){return!(a.indexOf(b)>-1)}))}},{key:"arrayUnion",value:function(a,b){return a.concat(b.filter(function(b){return!(a.indexOf(b)>-1)}))}},{key:"swap",value:function(a,b,c){return a[b]=a.splice(c,1,a[b])[0],a}},{key:"up",value:function(a,b){return 0===b?a:this.swap(a,b,b-1)}},{key:"down",value:function(a,b){return b===a.length-1?a:this.swap(a,b,b+1)}}]),a}(),FoundationTools=function(a){function b(){return _classCallCheck(this,b),_possibleConstructorReturn(this,(b.__proto__||Object.getPrototypeOf(b)).apply(this,arguments))}return _inherits(b,a),_createClass(b,[{key:"blank",value:function(){}},{key:"isArray",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&a.constructor===Array)}},{key:"isObject",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&a.constructor===Object)}},{key:"isNull",value:function(a){return!a&&("undefined"!=typeof a&&0!==a)}},{key:"isJson",value:function(a){return null!==a&&("object"===("undefined"==typeof a?"undefined":_typeof(a))&&"[object object]"===Object.prototype.toString.call(a).toLowerCase())}},{key:"isString",value:function(a){return null!==a&&("string"==typeof a&&a.constructor===String)}},{key:"isNumeric",value:function(a){return null!==a&&""!==a&&!isNaN(a)}},{key:"isBoolean",value:function(a){return null!==a&&("boolean"==typeof a&&a.constructor===Boolean)}},{key:"isFunction",value:function(a){return null!==a&&("function"==typeof a&&"[object function]"===Object.prototype.toString.call(a).toLowerCase())}},{key:"jsonLength",value:function(a){var b=0;for(var c in a)a.hasOwnProperty(c)&&b++;return b}},{key:"offset",value:function(a){a=a.jquery?a:$(a);var b=a.offset();return{left:b.left,top:b.top,width:a[0].offsetWidth,height:a[0].offsetHeight}}},{key:"timestamp",value:function(){var a=arguments.length>0&&void 0!==arguments[0]&&arguments[0],b=(new Date).getTime();return a?Math.ceil(b/1e3):b}},{key:"parseQueryString",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,b=arguments.length>1&&void 0!==arguments[1]&&arguments[1];a=decodeURIComponent(a||location.href),a.indexOf("?")===-1&&(a+="?");var c={},d=a.split("?");if(b&&(c.hostPart=d[0]),a=d[1],0===a.length)return c;a.indexOf("#")&&(a=a.split("#")[0]),a=a.split("&");var e=!0,f=!1,g=void 0;try{for(var h,i=a[Symbol.iterator]();!(e=(h=i.next()).done);e=!0){var j=h.value;j=j.split("="),c[j[0]]=j[1]}}catch(a){f=!0,g=a}finally{try{!e&&i.return&&i.return()}finally{if(f)throw g}}return c}},{key:"jsonBuildQuery",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=!(arguments.length>2&&void 0!==arguments[2])||arguments[2],d="",e={},f=void 0,g=void 0,h=void 0,i=void 0,j=void 0,k=void 0;for(var l in a)if(a.hasOwnProperty(l))if(f=a[l],this.isArray(f))for(k=0;k<f.length;++k)i=f[k],g=l+"["+k+"]",j={},j[g]=i,d+=this.jsonBuildQuery(j,b,c)+"&",e=Object.assign(e,this.jsonBuildQuery(j,b,c));else if(this.isObject(f))for(h in f)f.hasOwnProperty(h)&&(i=f[h],g=l+"["+h+"]",j={},j[g]=i,d+=this.jsonBuildQuery(j,b,c)+"&",e=Object.assign(e,this.jsonBuildQuery(j,b,c)));else void 0!==f&&null!==f&&(c&&(l=encodeURIComponent(l),f=encodeURIComponent(f)),d+=l+"="+f+"&",e[l]=f);return b?e:d.length?d.substr(0,d.length-1):d}},{key:"setParams",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=this.parseQueryString(b,!0),e=d.hostPart;delete d.hostPart,a=Object.assign(d,this.jsonBuildQuery(a,!0,c));var f=this.jsonBuildQuery(a);return b=e+"?"+f,this.trim(b,"?")}},{key:"unsetParams",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};b=b||location.href;var e=this.parseQueryString(b,!0),f=!0,g=!1,h=void 0;try{for(var i,j=(a||[])[Symbol.iterator]();!(f=(i=j.next()).done);f=!0){var k=i.value;"undefined"!=typeof e[k]&&(d[k]=e[k],delete e[k])}}catch(a){g=!0,h=a}finally{try{!f&&j.return&&j.return()}finally{if(g)throw h}}var l=e.hostPart;return delete e.hostPart,b=l+"?"+this.jsonBuildQuery(e,c),this.trim(b,"?")}},{key:"unsetParamsBeginWith",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};b=b||location.href;var e=this.parseQueryString(b,!0),f=!0,g=!1,h=void 0;try{for(var i,j=(a||[])[Symbol.iterator]();!(f=(i=j.next()).done);f=!0){var k=i.value;for(var l in e)e.hasOwnProperty(l)&&l.startsWith(k)&&(d[l]=e[l],delete e[l])}}catch(a){g=!0,h=a}finally{try{!f&&j.return&&j.return()}finally{if(g)throw h}}var m=e.hostPart;return delete e.hostPart,b=m+"?"+this.jsonBuildQuery(e,c),this.trim(b,"?")}},{key:"pam",value:function(a,b,c,d){b=b||1,c=c||["margin","padding"],d=d||["left","right"];var e=0;return c.each(function(c){d.each(function(d){e+=parseInt(a.css(c+"-"+d))*b})}),e}},{key:"device",value:function(){var a=navigator.userAgent;return{ie:a.indexOf("Trident")>-1,opera:a.indexOf("Presto")>-1,chrome:a.indexOf("AppleWebKit")>-1,firefox:a.indexOf("Gecko")>-1&&a.indexOf("KHTML")===-1,mobile:!!a.match(/AppleWebKit.*Mobile.*/),ios:!!a.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),android:a.indexOf("Android")>-1||a.indexOf("Linux")>-1,iPhone:a.indexOf("iPhone")>-1,iPad:a.indexOf("iPad")>-1,webApp:a.indexOf("Safari")===-1,version:navigator.appVersion}}},{key:"isMobile",value:function(){return/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)}},{key:"rand",value:function(a,b){b=b||0;var c=b,d=a-c;return parseInt(Number(Math.random()*d).toFixed(0))+c}},{key:"keyBind",value:function(a,b,c,d){c=c||$(document),c.unbind("keydown").bind("keydown",function(c){d?c.keyCode===a&&c.ctrlKey&&b&&b():c.keyCode===a&&b&&b()})}},{key:"media",value:function(){var a=document.body.clientWidth;return{xs:a<576,sm:a>=576,md:a>=768,lg:a>=992,xl:a>=1200,xxl:a>=1600}}},{key:"parseInt",value:function(a){function b(b){return a.apply(this,arguments)}return b.toString=function(){return a.toString()},b}(function(a){return a=parseInt(a),isNaN(a)?0:a})},{key:"cookie",value:function(){return{set:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:31536e3,d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:null,e=new Date;return e.setTime(e.getTime()+1e3*c),d=d?"; domain="+d:"",document.cookie=a+"="+encodeURI(b)+"; expires="+e.toUTCString()+"; path=/"+d,b},get:function(a){for(var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=document.cookie.split("; "),d=0;d<c.length;d++){var e=c[d].split("=");if(e[0]===a)return unescape(e[1])}return b},delete:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;b=b?"; domain="+b:"",document.cookie=a+"=; expires="+new Date(0).toUTCString()+"; path=/"+b}}}},{key:"cookieMapNext",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]&&arguments[3],e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:null,f=this.cookie(),g=f.get(a,c),h=b[g]||c;if(e){var i=this.ucFirst(h);this.message("success",e+": "+i)}return d?f.set(a,h):h}},{key:"cookieMapCurrent",value:function(a,b,c){var d=this.cookie().get(a,c);return b[d]?d:c}},{key:"evalExpr",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,c=null;try{c=window.eval("("+a+")")}catch(b){console.warn("Expression has syntax error: "+a),console.warn(b)}return c?c:b}},{key:"switchClass",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"html",d=$(c);d.removeClass(a),"yes"===b&&d.addClass(a)}},{key:"checkJsonDeep",value:function(a,b){var c=a;b=b.split(".");var d=!0,e=!1,f=void 0;try{for(var g,h=b[Symbol.iterator]();!(d=(g=h.next()).done);d=!0){var i=g.value;if("undefined"==typeof c[i]||!c[i])return!1;c=c[i]}}catch(a){e=!0,f=a}finally{try{!d&&h.return&&h.return()}finally{if(e)throw f}}return!0}}]),b}(FoundationPrototype),FoundationAntD=function(a){function b(a,c,d){var e=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};_classCallCheck(this,b);var f=_possibleConstructorReturn(this,(b.__proto__||Object.getPrototypeOf(b)).call(this));return f.v=c,f.d=d,f.config={},f.lang=e,f.cnf={marginTop:"150px",loadingMarginTop:"250px",shade:.1,zIndex:9999,requestTimeout:30,notificationDuration:5,messageDuration:5,confirmDuration:5,alertType:"message",notificationPlacement:"topRight",v:null,method:{get:"GET",post:"POST"}},f}return _inherits(b,a),_createClass(b,[{key:"configure",value:function(a){for(var b in a)a.hasOwnProperty(b)&&(this.config[b]=Object.assign(this.config[b]||{},a[b]))}},{key:"vue",value:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:".bsw-content",b=this,c={};return{template:function(a){return a&&(c.template=a),this},data:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.data=function(){return a},this},computed:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.computed=a,this},method:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.methods=a,this},component:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.components=a,this},directive:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.directives=a,this},watch:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return c.watch=a,this},init:function(){var d=arguments.length>0&&void 0!==arguments[0]?arguments[0]:self.blank;c.el=a,b.cnf.v=new b.v(c),d(b.cnf.v)}}}},{key:"notification",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank;"undefined"==typeof c&&(c=this.cnf.notificationDuration);var e={success:this.lang.success,info:this.lang.info,warning:this.lang.warning,error:this.lang.error}[a];return this.cnf.v.$notification[a]({placement:this.cnf.notificationPlacement,message:e,description:b,duration:c,onClose:d})}},{key:"message",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank;return"undefined"==typeof c&&(c=this.cnf.messageDuration),this.cnf.v.$message[a](b,c,d)}},{key:"confirm",value:function(a,b,c){var d=arguments.length>3&&void 0!==arguments[3]?arguments[3]:self.blank,e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:{},f=e.title||{success:this.lang.success,info:this.lang.info,warning:this.lang.warning,error:this.lang.error}[a];"confirm"===a&&"undefined"==typeof e.width&&(e.width=this.popupCosySize().width);var g=this.cnf.v["$"+a](Object.assign({title:f,content:b,okText:this.lang.i_got_it,onOk:e.onOk||d,onCancel:d},e));return"undefined"==typeof c&&(c=this.cnf.confirmDuration),c&&setTimeout(function(){g.destroy()},1e3*c),g}},{key:"success",value:function(a,b,c,d){return this[d||this.cnf.alertType]("success",a,b,c)}},{key:"info",value:function(a,b,c,d){return this[d||this.cnf.alertType]("info",a,b,c)}},{key:"warning",value:function(a,b,c,d){return this[d||this.cnf.alertType]("warning",a,b,c)}},{key:"error",value:function(a,b,c,d){return this[d||this.cnf.alertType]("error",a,b,c)}},{key:"showConfirm",value:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};return this.cnf.v.$confirm(Object.assign({title:b,content:a,keyboard:!1,width:320,okText:this.lang.confirm,cancelText:this.lang.cancel},c))}},{key:"request",value:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:this.cnf.method.post,d=arguments.length>3&&void 0!==arguments[3]&&arguments[3],e=arguments.length>4&&void 0!==arguments[4]?arguments[4]:1,f=this;return new Promise(function(g,h){$.ajax({type:d?f.cnf.method.post:c,data:b,url:a,processData:!d,contentType:!d&&"application/x-www-form-urlencoded",timeout:1e3*f.cnf.requestTimeout*(d?10:1),beforeSend:function(){f.cnf.v.noLoadingOnce?f.cnf.v.noLoadingOnce=!1:f.cnf.v.spinning=!0},success:function(a){f.cnf.v.spinning=!1,g(a)},error:function(g){if(f.cnf.v.spinning=!1,g.responseJSON){var i=g.responseJSON,j="["+i.code+"] "+i.message;return f.confirm(i.classify,j,0)}if(g.responseText){var k="["+g.status+"] "+g.statusText;return f.confirm("error",k,0)}return"timeout"===g.statusText&&(console.warn("Client request timeout: ",g),console.warn("Retry current request in times "+e),e<=3)?f.request(a,b,c,d,++e):void h(g)}})})}},{key:"response",value:function(a,b,c,d){if("undefined"==typeof a.code)return this.error(this.lang.response_error_message);var e=this;return new Promise(function(d,f){var g=function(a){f(a),"undefined"!=typeof a.sets.href&&(location.href=a.sets.href||location.href)},h=function(a){d(a),"undefined"!=typeof a.sets.href&&(location.href=a.sets.href||location.href)};if(a.error){if(a.message){var i=e.isNull(a.duration)?void 0:a.duration;e[a.classify](a.message,i,null,a.type).then(function(){g(a)}).catch(function(a){console.warn(a)})}else g(a);c&&c(a)}else{if(a.message){var j=e.isNull(a.duration)?void 0:a.duration;e[a.classify](a.message,j,null,a.type).then(function(){h(a)}).catch(function(a){console.warn(a)})}else h(a);b&&b(a)}})}},{key:"popupCosySize",value:function(){var a=document.body.clientWidth,b=document.body.clientHeight;return a*=a<1285?1:.7,b*=b<666?.9:.75,{width:a,height:b}}},{key:"rsaEncrypt",value:function(a){var b=new JSEncrypt;return b.setPublicKey(this.cnf.v.rsaPublicKey),b.encrypt(a)}},{key:"chart",value:function a(b){var c=this,d=b.option,a=echarts.init(document.getElementById("chart-"+b.id),b.theme),e=function a(b){for(var d in b)if(b.hasOwnProperty(d)){var e=b[d];if(c.isJson(e))b[d]=a(e);else if(c.isString(e)&&e.startsWith("fn:")){var f=c.ucFirst(e.split(":")[1]);b[d]=c["chartHandler"+f]}}return b};a.setOption(e(d)),this.cnf.v.$nextTick(function(){a.resize()}),$(window).resize(function(){return a.resize()})}},{key:"chartHandlerTooltipStack",value:function(a){var b=0,c=!0,d=!1,e=void 0;try{for(var f,g=a[Symbol.iterator]();!(c=(f=g.next()).done);c=!0){var h=f.value;b+=Math.floor(100*Number.parseFloat(h.data))}}catch(a){d=!0,e=a}finally{try{!c&&g.return&&g.return()}finally{if(d)throw e}}b/=100;var i=a[0].name+" ("+b+")<br>",j=!0,k=!1,l=void 0;try{for(var m,n=a[Symbol.iterator]();!(j=(m=n.next()).done);j=!0){var o=m.value,p=100*(Number.parseFloat(o.data)/b||0);p=p.toFixed(2),i+=o.marker+" "+o.seriesName+": "+o.data+" ("+p+"%)<br>"}}catch(a){k=!0,l=a}finally{try{!j&&n.return&&n.return()}finally{if(k)throw l}}return i}},{key:"chartHandlerTooltipNormal",value:function(a){return a[0].name+": "+a[0].value}},{key:"chartHandlerTooltipPositionFixed",value:function(a,b,c,d,e){var f={top:20};return f[["left","right"][+(a[0]<e.viewSize[0]/2)]]=10,f}},{key:"wxJsApiPay",value:function(a){return window.WeixinJSBridge?void WeixinJSBridge.invoke("getBrandWCPayRequest",a,function(a){console.log(a),"get_brand_wcpay_request:ok"===a.err_msg&&console.log("success")}):void console.log("Js api just work in WeiXin browser")}}]),b}(FoundationTools);