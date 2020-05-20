/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-05-20 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a};window.bsw=new FoundationAntD({rsaPublicKey:"-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----"},jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue(".bsw-body").template(bsw.config.template||null).data(Object.assign({bsw:bsw,locale:bsw.d.locales[bsw.lang.i18n_ant],timeFormat:"YYYY-MM-DD HH:mm:ss",opposeMap:{yes:"no",no:"yes"},formUrl:null,formMethod:null,theme:"light",themeMap:{dark:"light",light:"dark"},weak:"no",third_message:"yes",menuWidth:256,menuCollapsed:!1,mobileDefaultCollapsed:!0,ckEditor:{},no_loading_once:!1,spinning:!1,configure:{},message:{},tips:{},modal:{visible:!1}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({moment:moment,redirect:function(a){if(a.function&&"redirect"!==a.function)return this.dispatcher(a,$("body"));var b=a.location;return bsw.isMobile()&&this.mobileDefaultCollapsed&&bsw.cookie().set("bsw_menu_collapsed","yes"),b.startsWith("http")||b.startsWith("/")?"undefined"==typeof a.window?location.href=b:window.open(b):void 0},getBswData:function(a){return bsw.evalExpr(a.attr("bsw-data"))},redirectByVue:function(a){this.redirect(this.getBswData($(a.item.$el).find("span")))},dispatcher:function(a,b){var c=this,d=function(){var d=a.function||"console.log";c[d](a,b)};"undefined"==typeof a.confirm?d():bsw.showConfirm(a.confirm,bsw.lang.confirm_title,{onOk:function(){return d()}})},dispatcherByNative:function(a){this.dispatcher(this.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])},setUrlToForm:function(a,b){this.formUrl=a.location,this.formMethod=$(b).attr("bsw-method")},pagination:function(a,b){var c=this;return b&&(a=bsw.setParams({page:b},a)),0===c.preview_list.length?location.href=a:void bsw.request(a).then(function(d){bsw.response(d).then(function(){c.preview_list=d.sets.preview.list,c.preview_page_number=b,c.preview_url=a,c.preview_pagination_data=d.sets.preview.page,c.preview_image_change(),history.replaceState({},"",a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},export:function(a,b){var c={title:bsw.lang.export_mission,width:768};c.location=bsw.setParams({filter:a,route:b},this.api_export),this.showIFrame(c,$("body")[0])},filter:function(a){var b=this,c=arguments.length>1&&void 0!==arguments[1]&&arguments[1],d=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null,e=this;a.preventDefault(),e.filter_form.validateFields(function(a,f){if(a)return!1;for(var g in f)if(f.hasOwnProperty(g)){if(moment.isMoment(f[g])){var h=f[g]._f||e.filter_format[g];f[g]=f[g].format(h),c=!0}if(bsw.isArray(f[g]))for(var i=0;i<f[g].length;i++)if(moment.isMoment(f[g][i])){var j=f[g][i]._f||e.filter_format[g];f[g][i]=f[g][i].format(j),c=!0}}var k={};for(var l in f)f.hasOwnProperty(l)&&"undefined"!=typeof f[l]&&null!=f[l]&&0!==f[l].length&&(k[l]=f[l]);return"export"===b.formMethod?b.export(k,d):b[b.formMethod+"FilterForm"](k,c)})},searchFilterForm:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c={},d=bsw.unsetParamsBeginWith(["filter"]);return d=bsw.unsetParams(["page"],d,!1,c),d=bsw.setParams({filter:a},d),_typeof(c.page)&&c.page>1&&(b=!0),b?location.href=d:void this.pagination(d)},persistence:function(a){var b=this,c=this;a.preventDefault(),c.persistence_form.validateFields(function(a,d){if(a)return!1;for(var e in d)if(d.hasOwnProperty(e)){if(moment.isMoment(d[e])){var f=d[e]._f||c.persistence_format[e];d[e]=d[e].format(f)}if(bsw.isArray(d[e]))for(var g=0;g<d[e].length;g++)if(moment.isMoment(d[e][g])){var h=d[e][g]._f||c.persistence_format[e];d[e][g]=d[e][g].format(h)}bsw.checkJsonDeep(d,e+".fileList")&&delete d[e]}return b[b.formMethod+"PersistenceForm"](d)})},submitPersistenceForm:function(a){bsw.request(this.formUrl,{submit:a}).then(function(a){var b=bsw.parseQueryString();if(b.iframe){a.sets.arguments=bsw.parseQueryString();var c=a.sets.function||"handleResponse";parent.postMessage({response:a,function:c},"*")}else bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},uploaderChange:function(a){var b=a.file,c=a.fileList;a.event;"done"===b.status?this.spinning=!1:"uploading"===b.status&&(this.spinning=!0);var d=this.persistence_upload_field,e=this.persistence_file_list_key_collect[d];if(!b.response)return void(e.list=c);b.response.error&&(e.list=c.slice(0,-1));var f=e.list.slice(-1);if(f.length){var g,h=f[0].response.sets,i=(g={},_defineProperty(g,e.id,"attachment_id"),_defineProperty(g,e.md5,"attachment_md5"),_defineProperty(g,e.sha1,"attachment_sha1"),_defineProperty(g,e.url,"attachment_url"),g);for(var j in i)if(i.hasOwnProperty(j)&&j&&i[j]){if(0===$("#"+j).length)continue;this.persistence_form&&this.persistence_form.setFieldsValue(_defineProperty({},j,h[i[j]]))}}if("undefined"!=typeof b.response.code&&500!==b.response.code||(this.spinning=!1),b.response.sets.href){var k=b.response.sets.function||"handleResponse";parent.postMessage({response:b.response,function:k},"*")}else bsw.response(b.response).catch(function(a){console.warn(a)})},switchFieldShapeWithSelect:function(a){var b=this.persistence_switch_field,c=this.persistence_field_shape_now,d=this.persistence_field_shape_collect[b];for(var e in d)d.hasOwnProperty(e)&&(c[e]=d[e].includes(a))},showModal:function(a){a.visible=!0,"undefined"==typeof a.width&&(a.width=bsw.popupCosySize().width),this.modal=Object.assign(this.modal,a)},showModalAfterRequest:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){var d=b.sets,e=d.logic||d;c.showModal({centered:!0,width:e.width||a.width||bsw.popupCosySize().width,title:e.title||a.title||bsw.lang.modal_title,content:d.content})}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},requestByAjax:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){"undefined"!=typeof a.refresh&&a.refresh&&c.preview_pagination_refresh()}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},selectedRowHandler:function(a){for(var b=[],c=0;c<this.preview_selected_row.length;c++)bsw.isString(this.preview_selected_row[c])&&(b[c]=bsw.evalExpr(this.preview_selected_row[c]),a&&(b[c]=b[c][a]||null));return b},multipleAction:function(a,b){var c=this.selectedRowHandler();return 0===c.length?bsw.warning(bsw.lang.select_item_first):void bsw.request(a.location,{ids:c}).then(function(a){bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},showIFrame:function(a,b){var c=bsw.popupCosySize(),d=$(b).prev().attr("id");a.location=bsw.setParams({iframe:!0,repair:d},a.location);var e={visible:!0,width:a.width||c.width,title:a.title===!1?a.title:a.title||bsw.lang.please_select,centered:!0,wrapClassName:"bsw-iframe-container",content:'<iframe id="bsw-iframe" src="'+a.location+'"></iframe>'};this.showModal(e),this.$nextTick(function(){$("#bsw-iframe").height(a.height||c.height)})},showIFrameWithChecked:function(a,b){var c=this.selectedRowHandler(a.selector).join(","),d={ids:c};if("undefined"!=typeof a.form){var e="fill["+a.form+"]";d=_defineProperty({},e,c)}a.location=bsw.setParams(d,a.location),this.showIFrame(a,b)},showIFrameByNative:function(a){this.showIFrame(this.getBswData($(a)),a)},showIFrameByVue:function(a){this.showIFrameByNative($(a.target)[0])},fillParentForm:function(a,b){return a.ids=this.selectedRowHandler(a.selector).join(","),0===a.ids.length?bsw.warning(bsw.lang.select_item_first):void parent.postMessage(a,"*")},verifyJsonFormat:function(a,b){var c=this.persistence_form.getFieldValue(a.field),d=bsw.setParams(_defineProperty({},a.key,c),a.url);window.open(d)},initCkEditor:function(){var a=this;$(".bsw-persistence .bsw-ck").each(function(){var b=this,c=$(b).prev("textarea").attr("id"),d=$(b).find(".bsw-ck-editor");DecoupledEditor.create(d[0],{language:bsw.lang.i18n_editor,placeholder:$(b).attr("placeholder")}).then(function(d){a.ckEditor[c]=d,d.isReadOnly="disabled"===$(b).attr("disabled"),d.plugins.get("FileRepository").createUploadAdapter=function(b){return new FileUploadAdapter(d,b,a.api_upload)},a.ckEditor[c].model.document.on("change:data",function(){a.persistence_form&&a.persistence_form.setFieldsValue(_defineProperty({},c,a.ckEditor[c].getData()))}),$(b).find(".bsw-ck-toolbar").append(d.ui.view.toolbar.element)}).catch(function(a){console.warn(a.stack)})})},fillParentFormInParent:function(a,b){this.modal.visible=!1,this.persistence_form&&a.repair&&this.persistence_form.setFieldsValue(_defineProperty({},a.repair,a.ids))},fillParentFormAfterAjaxInParent:function(a,b){var c=a.response.sets;c.repair=c.arguments.repair,this.fillParentFormInParent(c,b)},handleResponseInParent:function(a,b){this.modal.visible=!1,bsw.response(a.response).catch(function(a){console.warn(a)})}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){c.context[b.arg]=b.value||b.expression}}},bsw.config.directive||{})).watch(Object.assign({},bsw.config.watch||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").attr("bsw-value")})},bsw.config.component||{})).init(function(a){var b=!1;a.scaffoldInit&&(b=a.scaffoldInit()),a.$nextTick(function(){for(var b in bsw.config.logic||[])bsw.config.logic.hasOwnProperty(b)&&bsw.config.logic[b](a)}),$("img.bsw-captcha").off("click").on("click",function(){var a=$(this).attr("src");a=bsw.setParams({t:bsw.timestamp()},a),$(this).attr("src",a)});var c=b?1e3:400;setTimeout(function(){$(".bsw-page-loading").fadeOut(300,function(){if("undefined"!=typeof a.message.content){var b=bsw.isNull(a.message.duration)?void 0:a.message.duration;try{bsw[a.message.classify](a.message.content,b,null,a.message.type)}catch(b){console.warn(bsw.lang.message_data_error),console.warn(a.message)}}"undefined"!=typeof a.tips.content&&a.showModal(a.tips)})},c)}),window.addEventListener("message",function(a){a.data.function+="InParent",bsw.cnf.v.dispatcher(a.data,$("body")[0])},!1)});