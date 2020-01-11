/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-01-11 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}window.bsw=new FoundationAntD({rsaPublicKey:"-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----"},jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue(".bsw-body").template(bsw.config.template||null).data(Object.assign({bsw:bsw,timeFormat:"YYYY-MM-DD HH:mm:ss",opposeMap:{yes:"no",no:"yes"},formUrl:null,formMethod:null,theme:"light",themeMap:{dark:"light",light:"dark"},weak:"no",menuWidth:256,menuCollapsed:!1,mobileDefaultCollapsed:!0,ckEditor:{},no_loading_once:!1,spinning:!1,message:null,configure:{},modal:{visible:!1}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({moment:moment,redirect:function(a){var b=a.location;if(bsw.isMobile()&&this.mobileDefaultCollapsed&&bsw.cookie().set("bsw_menu_collapsed","yes"),b.startsWith("http")||b.startsWith("/"))return location.href=b},getBswData:function(a){return bsw.evalExpr(a.attr("bsw-data"))},redirectByVue:function(a){this.redirect(this.getBswData($(a.item.$el).find("span")))},dispatcher:function(a,b){var c=this,d=function(){var d=a.function||"console.log";c[d](a,b)};"undefined"==typeof a.confirm?d():bsw.showConfirm(a.confirm,bsw.lang.confirm_title,{onOk:function(){return d()}})},dispatcherByNative:function(a){this.dispatcher(this.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])},setUrlToForm:function(a,b){this.formUrl=a.location,this.formMethod=$(b).attr("bsw-method")},pagination:function(a,b){var c=this;b&&(a=bsw.setParams({page:b},a)),bsw.request(a).then(function(d){bsw.response(d).then(function(){c.preview_list=d.sets.preview.list,c.preview_page_number=b,c.preview_url=a,c.preview_pagination_data=d.sets.preview.page,c.preview_image_change(),history.replaceState({},"",a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},filter:function(a){var b=this,c=arguments.length>1&&void 0!==arguments[1]&&arguments[1],d=this;a.preventDefault(),d.filter_form.validateFields(function(a,e){if(a)return!1;for(var f in e)if(e.hasOwnProperty(f)){if(moment.isMoment(e[f])){var g=e[f]._f||d.filter_format[f];e[f]=e[f].format(g)}if(bsw.isArray(e[f]))for(var h=0;h<e[f].length;h++)if(moment.isMoment(e[f][h])){var i=e[f][h]._f||d.filter_format[f];e[f][h]=e[f][h].format(i)}}return b[b.formMethod+"FilterForm"](e,c)})},submitFilterForm:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c={},d=0;for(var e in a)a.hasOwnProperty(e)&&"undefined"!=typeof a[e]&&null!=a[e]&&0!==a[e].length&&(c[e]=a[e],d+=1);var f=bsw.unsetParamsBeginWith(["filter"]);f=bsw.setParams({filter:c},f),b?location.href=f:this.pagination(f)},persistence:function(a){var b=this,c=this;a.preventDefault(),c.persistence_form.validateFields(function(a,d){if(a)return!1;for(var e in d)if(d.hasOwnProperty(e)){if(moment.isMoment(d[e])){var f=d[e]._f||c.persistence_format[e];d[e]=d[e].format(f)}if(bsw.isArray(d[e]))for(var g=0;g<d[e].length;g++)if(moment.isMoment(d[e][g])){var h=d[e][g]._f||c.persistence_format[e];d[e][g]=d[e][g].format(h)}bsw.checkJsonDeep(d,e+".fileList")&&delete d[e]}return b[b.formMethod+"PersistenceForm"](d)})},submitPersistenceForm:function(a){var b={submit:a};bsw.request(this.formUrl,b).then(function(a){var b=bsw.parseQueryString();b.iframe?parent.postMessage({response:a,function:"handleResponse"},"*"):bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},uploaderChange:function(a){var b=a.file,c=a.fileList;a.event;"done"===b.status?this.spinning=!1:"uploading"===b.status&&(this.spinning=!0);var d=this.persistence_upload_field,e=this.persistence_file_list_key_collect[d];if(!b.response)return void(e.list=c);b.response.error&&(e.list=c.slice(0,-1));var f=e.list.slice(-1);if(f.length){var g,h=f[0].response.sets,i=(g={},_defineProperty(g,e.field,"attachment_id"),_defineProperty(g,e.md5,"attachment_md5"),_defineProperty(g,e.sha1,"attachment_sha1"),g);for(var j in i)if(i.hasOwnProperty(j)&&j&&i[j]){if(0===$("#"+j).length)continue;this.persistence_form.setFieldsValue(_defineProperty({},j,h[i[j]]))}}"undefined"!=typeof b.response.code&&500!==b.response.code||(this.spinning=!1),bsw.response(b.response).catch(function(a){console.warn(a)})},showModal:function(a){a.visible=!0,"undefined"==typeof a.width&&(a.width=bsw.popupCosySize().width),this.modal=Object.assign(this.modal,a)},showModalAfterRequest:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){var d=b.sets,e=d.logic||d;c.showModal({centered:!0,width:e.width||a.width||bsw.popupCosySize().width,title:e.title||a.title||bsw.lang.modal_title,content:d.content})}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},requestByAjax:function(a,b){var c=this;bsw.request(a.location).then(function(a){bsw.response(a).then(function(){c.preview_pagination_refresh()}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},multipleAction:function(a,b){var c=this.preview_selected_row;return 0===c.length?bsw.warning(bsw.lang.select_item_first):void bsw.request(a.location,{ids:c}).then(function(a){bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},showIFrame:function(a,b){var c=bsw.popupCosySize(),d=$(b).prev().attr("id");a.location=bsw.setParams({iframe:!0,fill:d},a.location);var e={visible:!0,width:a.width||c.width,title:a.title||bsw.lang.please_select,centered:!0,wrapClassName:"bsw-preview-iframe",content:'<iframe id="bsw-preview-iframe" src="'+a.location+'"></iframe>'};this.showModal(e),this.$nextTick(function(){$("#bsw-preview-iframe").height(a.height||c.height)})},showIFrameByNative:function(a){this.showIFrame(this.getBswData($(a)),a)},showIFrameByVue:function(a){this.showIFrameByNative($(a.target)[0])},fillParentForm:function(a,b){return a.ids=this.preview_selected_row,0===a.ids.length?bsw.warning(bsw.lang.select_item_first):void parent.postMessage(a,"*")},initCkEditor:function(){var a=this;$(".bsw-persistence .bsw-ck-editor").each(function(){var b=$(this).attr("id");ClassicEditor.create(this,{}).then(function(c){a.ckEditor[b]=c,c.plugins.get("FileRepository").createUploadAdapter=function(b){return new FileUploadAdapter(c,b,a.api_upload)},a.ckEditor[b].model.document.on("change:data",function(){a.persistence_form.setFieldsValue(_defineProperty({},b,a.ckEditor[b].getData()))})}).catch(function(a){console.warn(a.stack)})})},fillParentFormInParent:function(a,b){this.modal.visible=!1,this.persistence_form.setFieldsValue(_defineProperty({},a.fill,a.ids.join(",")))},handleResponseInParent:function(a,b){this.modal.visible=!1,bsw.response(a.response).catch(function(a){console.warn(a)})}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){c.context[b.arg]=b.value||b.expression}}},bsw.config.directive||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").attr("bsw-value")})},bsw.config.component||{})).init(function(a){var b=!1;a.scaffoldInit&&(b=a.scaffoldInit()),$("img.bsw-captcha").off("click").on("click",function(){var a=$(this).attr("src");a=bsw.setParams({t:bsw.timestamp()},a),$(this).attr("src",a)}),a.$nextTick(function(){for(var b in bsw.config.logic||[])bsw.config.logic.hasOwnProperty(b)&&bsw.config.logic[b](a)});var c=b?800:100;setTimeout(function(){$(".bsw-page-loading").fadeOut(200,function(){if("undefined"!=typeof a.message.content){var b=bsw.isNull(a.message.duration)?void 0:a.message.duration;try{bsw[a.message.classify](a.message.content,b,null,a.message.type)}catch(b){console.warn(bsw.lang.message_data_error),console.warn(a.message)}}"undefined"!=typeof a.tips.content&&a.showModal(a.tips)})},c)}),window.addEventListener("message",function(a){a.data.function+="InParent",bsw.cnf.v.dispatcher(a.data,$("body")[0])},!1)});