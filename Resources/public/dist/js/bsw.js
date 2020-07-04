/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-07-04 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a};window.bsw=new FoundationAntD(jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue(".bsw-body").template(bsw.config.template||null).data(Object.assign({bsw:bsw,locale:bsw.d.locales[bsw.lang.i18n_ant],timeFormat:"YYYY-MM-DD HH:mm:ss",opposeMap:{yes:"no",no:"yes"},submitFormUrl:null,submitFormMethod:null,theme:"light",themeMap:{dark:"light",light:"dark"},weak:"no",thirdMessage:"yes",menuWidth:256,menuCollapsed:!1,mobileDefaultCollapsed:!0,ckEditor:{},noLoadingOnce:!1,spinning:!1,configure:{},message:{},tips:{},modal:{visible:!1,centered:!0},footer:"footer",drawer:{visible:!1}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({moment:moment,redirect:function(a){if(a.function&&"redirect"!==a.function)return this.dispatcher(a,$("body"));var b=a.location;return bsw.isMobile()&&this.mobileDefaultCollapsed&&bsw.cookie().set("bsw_menu_collapsed","yes"),b.startsWith("http")||b.startsWith("/")?"undefined"==typeof a.window?location.href=b:window.open(b):void 0},getBswData:function(a){return a[0].dataBsw||a.data("bsw")||{}},redirectByVue:function(a){this.redirect(this.getBswData($(a.item.$el).find("span")))},tabsLinksSwitch:function(a){this.redirect(this.getBswData($("#tabs_link_"+a)))},dispatcher:function(a,b){var c=this;if(a.iframe)return delete a.iframe,void parent.postMessage({data:a,function:"dispatcher"},"*");var d=function(){return a.function&&0!==a.function.length?"undefined"==typeof c[a.function]?console.error("Method "+a.function+" is undefined.",a):void c[a.function](a,b):console.error("Attribute function should be configure in options.",a)};"undefined"==typeof a.confirm?d():bsw.showConfirm(a.confirm,bsw.lang.confirm_title,{onOk:function(){return d()}})},dispatcherByNative:function(a){this.dispatcher(this.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])},setUrlToForm:function(a,b){this.submitFormUrl=a.location,this.submitFormMethod=$(b).attr("bsw-method")},previewGetUrl:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};return a=a||this.previewUrl,bsw.setParams(Object.assign({page:this.previewPageNumber},b),a)},previewPaginationRefresh:function(a){this.noLoadingOnce=!0,this.pagination(this.previewGetUrl(),null,a)},previewImageChange:function(){var a=this,b=setInterval(function(){return c()},50),c=function(){var c=$("img"),d=0;c.each(function(){d+=this.complete?1:0});var e=a.previewColumns[0].fixed;a.previewColumns[0].fixed=!e,a.previewColumns[0].fixed=e,(d>=c.length||0===c.length)&&clearInterval(b)}},pagination:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=this;return b&&(a=bsw.setParams({page:b},a)),c||"undefined"==typeof d.previewList||0===d.previewList.length?location.href=a:void bsw.request(a).then(function(c){bsw.response(c).then(function(){d.previewList=c.sets.preview.list,d.previewPageNumber=b,d.previewUrl=a,d.previewPaginationData=c.sets.preview.page,d.previewImageChange(),history.replaceState({},"",a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},filterFormAction:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=this,d=arguments[2],e=arguments[3],f=this;a.preventDefault(),f[d].validateFields(function(a,d){if(a)return!1;for(var g in d)if(d.hasOwnProperty(g)){if(moment.isMoment(d[g])){var h=d[g]._f||f[e][g];d[g]=d[g].format(h),b=!0}if(bsw.isArray(d[g]))for(var i=0;i<d[g].length;i++)if(moment.isMoment(d[g][i])){var j=d[g][i]._f||f[e][g];d[g][i]=d[g][i].format(j),b=!0}}var k={};for(var l in d)d.hasOwnProperty(l)&&"undefined"!=typeof d[l]&&null!=d[l]&&0!==d[l].length&&(k[l]=d[l]);return c[c.submitFormMethod+"FilterForm"](k,b)})},searchFilterForm:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c={},d=bsw.unsetParamsBeginWith(["filter"]);d=bsw.unsetParams(["page"],d,!1,c),d=bsw.setParams({filter:a},d),_typeof(c.page)&&c.page>1&&(b=!0),this.pagination(d,null,b)},exportFilterForm:function(a){var b=this,c=bsw.unsetParamsBeginWith(["filter"]);c=bsw.unsetParams(["page"],c),c=bsw.setParams({filter:a,scene:"export"},c),bsw.request(c).then(function(a){bsw.response(a).then(function(){var c={title:bsw.lang.export_mission,width:768,height:700};c.location=bsw.setParams(a.sets,b.exportApiUrl,!0),b.showIFrame(c,$("body")[0])}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},submitFormAction:function(a,b,c){var d=this,e=this;a.preventDefault(),e[b].validateFields(function(a,b){if(a)return!1;for(var f in b)if(b.hasOwnProperty(f)){if(moment.isMoment(b[f])){var g=b[f]._f||e[c][f];b[f]=b[f].format(g)}if(bsw.isArray(b[f]))for(var h=0;h<b[f].length;h++)if(moment.isMoment(b[f][h])){var i=b[f][h]._f||e[c][f];b[f][h]=b[f][h].format(i)}bsw.checkJsonDeep(b,f+".fileList")&&delete b[f]}return d[d.submitFormMethod+"PersistenceForm"](b)})},submitPersistenceForm:function(a){bsw.request(this.submitFormUrl,{submit:a}).then(function(a){var b=bsw.parseQueryString();if(b.iframe){a.sets.arguments=bsw.parseQueryString();var c=a.sets.function||"handleResponse";parent.postMessage({response:a,function:c},"*")}else bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},uploaderChange:function(a){var b=a.file,c=a.fileList,d=(a.event,arguments.length>1&&void 0!==arguments[1]?arguments[1]:"persistenceForm");"done"===b.status?this.spinning=!1:"uploading"===b.status&&(this.spinning=!0);var e=this.persistenceUploadField,f=this.persistenceFileListKeyCollect[e];if(!b.response)return void(f.list=c);b.response.error&&(f.list=c.slice(0,-1));var g=f.list.slice(-1);if(g.length){var h,i=g[0].response.sets,j=(h={},_defineProperty(h,f.id,"attachment_id"),_defineProperty(h,f.md5,"attachment_md5"),_defineProperty(h,f.sha1,"attachment_sha1"),_defineProperty(h,f.url,"attachment_url"),h);for(var k in j)if(j.hasOwnProperty(k)&&k&&j[k]){if(0===$("#"+k).length)continue;this[d]&&this[d].setFieldsValue(_defineProperty({},k,i[j[k]]))}}if("undefined"!=typeof b.response.code&&500!==b.response.code||(this.spinning=!1),b.response.sets.href){var l=b.response.sets.function||"handleResponse";parent.postMessage({response:b.response,function:l},"*")}else bsw.response(b.response).catch(function(a){console.warn(a)})},switchFieldShapeWithSelect:function(a,b){var c=this.persistenceSwitchField,d=this.persistenceFieldShapeNow,e=this.persistenceFieldShapeCollect[c];for(var f in e)e.hasOwnProperty(f)&&(d[f]=e[f].includes(a))},formItemFilterOption:function(a,b){return b.componentOptions.children[0].text.toUpperCase().indexOf(a.toUpperCase())>=0},showModal:function(a){this.modal.visible=!1,a.visible=!0,"undefined"==typeof a.width&&(a.width=bsw.popupCosySize().width),a=Object.assign(this.modal,a),a.footer?this.footer="_footer":this.footer="footer",this.modal=a},showDrawer:function(a){this.drawer.visible=!1,a.visible=!0,"undefined"==typeof a.width&&(a.width=bsw.popupCosySize().width),a=Object.assign(this.drawer,a),this.drawer=a},executeMethod:function(a,b){if(a.length){var c=a[0].dataBsw;if(c[b]){if("undefined"==typeof this[c[b]])return console.error("Method "+c[b]+" is undefined.",c);this[c[b]](c,event)}}},modalOnOk:function(a){if(this.modal.visible=!1,a){var b=$(a.target).parents(".ant-modal-footer").prev().find(".bsw-modal-data");this.executeMethod(b,"ok")}},modalOnCancel:function(a){if(this.modal.visible=!1,a){var b=$(a.target).parents(".ant-modal-footer").prev().find(".bsw-modal-data");this.executeMethod(b,"cancel")}},drawerOnOk:function(a){if(this.drawer.visible=!1,a){var b=$(a.target).parents(".bsw-footer-bar");this.executeMethod(b,"ok")}},drawerOnCancel:function(a){if(this.drawer.visible=!1,a){var b=$(a.target).parents(".bsw-footer-bar");this.executeMethod(b,"cancel")}},showModalAfterRequest:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){var d=bsw.jsonFilter(Object.assign(a,{width:b.sets.width||a.width||void 0,title:b.sets.title||a.title||bsw.lang.modal_title,content:b.sets.content}));c.showModal(d)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},requestByAjax:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){"undefined"!=typeof a.refresh&&a.refresh&&c.previewPaginationRefresh(!1)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},selectedRowHandler:function(a){for(var b=[],c=0;c<this.previewSelectedRow.length;c++)bsw.isString(this.previewSelectedRow[c])&&(b[c]=bsw.evalExpr(this.previewSelectedRow[c]),a&&(b[c]=b[c][a]||null));return b},multipleAction:function(a,b){var c=this.selectedRowHandler();return 0===c.length?bsw.warning(bsw.lang.select_item_first):void bsw.request(a.location,{ids:c}).then(function(a){bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},showIFrame:function(a,b){var c=bsw.popupCosySize(),d=$(b).prev().attr("id");a.location=bsw.setParams({iframe:!0,repair:d},a.location);var e=bsw.jsonFilter(Object.assign(a,{width:a.width||c.width,title:a.title===!1?a.title:a.title||bsw.lang.please_select,content:'<iframe id="bsw-iframe" src="'+a.location+'"></iframe>'})),f=a.shape||"modal";"drawer"===f?(this.showDrawer(e),this.$nextTick(function(){var a=$("#bsw-iframe"),b=e.footer?73:0;a.height(bsw.popupCosySize(!0).height-b-55),a.parents("div.ant-drawer-body").css({margin:0,padding:0})})):(this.showModal(e),this.$nextTick(function(){var b=$("#bsw-iframe");b.height(a.height||c.height),b.parents("div.ant-modal-body").css({margin:0,padding:0})}))},showIFrameWithChecked:function(a,b){var c=this.selectedRowHandler(a.selector).join(","),d={ids:c};if("undefined"!=typeof a.form){var e="fill["+a.form+"]";d=_defineProperty({},e,c)}a.location=bsw.setParams(d,a.location),this.showIFrame(a,b)},showIFrameByNative:function(a){this.showIFrame(this.getBswData($(a)),a)},showIFrameByVue:function(a){this.showIFrameByNative($(a.target)[0])},fillParentForm:function(a,b){return a.ids=this.selectedRowHandler(a.selector).join(","),0===a.ids.length?bsw.warning(bsw.lang.select_item_first):void parent.postMessage(a,"*")},verifyJsonFormat:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"persistenceForm",d=this[c].getFieldValue(a.field),e=bsw.setParams(_defineProperty({},a.key,d),a.url);window.open(e)},initCkEditor:function(){var a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"persistenceForm",b=this;$(".bsw-persistence .bsw-ck").each(function(){var c=this,d=$(c).prev("textarea").attr("id"),e=$(c).find(".bsw-ck-editor");DecoupledEditor.create(e[0],{language:bsw.lang.i18n_editor,placeholder:$(c).attr("placeholder")}).then(function(e){b.ckEditor[d]=e,e.isReadOnly="disabled"===$(c).attr("disabled"),e.plugins.get("FileRepository").createUploadAdapter=function(a){return new FileUploadAdapter(e,a,b.uploadApiUrl)},b.ckEditor[d].model.document.on("change:data",function(){b[a]&&b[a].setFieldsValue(_defineProperty({},d,b.ckEditor[d].getData()))}),$(c).find(".bsw-ck-toolbar").append(e.ui.view.toolbar.element)}).catch(function(a){console.warn(a.stack)})})},dispatcherInParent:function(a,b){this.modalOnCancel(),this.drawerOnCancel(),this.$nextTick(function(){"undefined"!=typeof a.data.location&&(a.data.location=bsw.unsetParams(["iframe"],a.data.location)),this.dispatcher(a.data,b)})},fillParentFormInParent:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"persistenceForm";this.modalOnCancel(),this.drawerOnCancel(),this.$nextTick(function(){this[c]&&a.repair&&this[c].setFieldsValue(_defineProperty({},a.repair,a.ids))})},fillParentFormAfterAjaxInParent:function(a,b){var c=a.response.sets;c.repair=c.arguments.repair,this.fillParentFormInParent(c,b)},handleResponseInParent:function(a,b){this.modalOnCancel(),this.drawerOnCancel(),this.$nextTick(function(){bsw.response(a.response).catch(function(a){console.warn(a)})})},showIFrameInParent:function(a,b){this.showIFrame(a.response.sets,b)},refreshPreviewInParent:function(a,b){this.handleResponseInParent(a,b),this.previewPaginationRefresh(!1)}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){var d=bsw.smallHump(b.arg);c.context[d]=b.value||b.expression}}},bsw.config.directive||{})).watch(Object.assign({},bsw.config.watch||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").data("bsw-value")})},bsw.config.component||{})).init(function(a){var b=!1;a.scaffoldInit&&(b=a.scaffoldInit()),a.$nextTick(function(){for(var b in bsw.config.logic||[])bsw.config.logic.hasOwnProperty(b)&&bsw.config.logic[b](a)}),$("img.bsw-captcha").off("click").on("click",function(){var a=$(this).attr("src");a=bsw.setParams({t:bsw.timestamp()},a),$(this).attr("src",a)});var c=b?1e3:400;setTimeout(function(){$(window).resize(),$(".bsw-page-loading").fadeOut(300,function(){if("undefined"!=typeof a.message.content){var b=bsw.isNull(a.message.duration)?void 0:a.message.duration;try{bsw[a.message.classify](Base64.decode(a.message.content),b,null,a.message.type)}catch(b){console.warn(bsw.lang.message_data_error),console.warn(a.message)}}if("undefined"!=typeof a.tips.content){for(var c=["title","content"],d=0;d<c.length;d++)"undefined"!=typeof a.tips[c[d]]&&(a.tips[c[d]]=Base64.decode(a.tips[c[d]]));a.showModal(a.tips)}})},c)}),window.addEventListener("message",function(a){a.data.function+="InParent",bsw.cnf.v.dispatcher(a.data,$("body")[0])},!1)});