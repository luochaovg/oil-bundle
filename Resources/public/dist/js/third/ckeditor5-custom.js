/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-08-16 */
"use strict";function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}var _createClass=function(){function a(a,b){for(var c=0;c<b.length;c++){var d=b[c];d.enumerable=d.enumerable||!1,d.configurable=!0,"value"in d&&(d.writable=!0),Object.defineProperty(a,d.key,d)}}return function(b,c,d){return c&&a(b.prototype,c),d&&a(b,d),b}}(),FileUploadAdapter=function(){function a(b,c,d){_classCallCheck(this,a),this.editor=b,this.loader=c,this.api=d}return _createClass(a,[{key:"upload",value:function(){var a=this;return this.loader.file.then(function(b){return new Promise(function(c,d){var e=new FormData;e.append("ck-editor",b),e.append("file_flag","ck-editor"),bsw.request(a.api,e,null,!0).then(function(a){a.error?d(a.message):c({default:a.sets.attachment_url})})})})}},{key:"abort",value:function(){}}]),a}();