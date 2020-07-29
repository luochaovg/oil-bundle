/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-07-29 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a};window.bsw=new FoundationAntD(jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue().template(bsw.config.template||null).data(Object.assign({bsw:bsw,locale:bsw.d.locales[bsw.lang.i18n_ant],timeFormat:"YYYY-MM-DD HH:mm:ss",opposeMap:{yes:"no",no:"yes"},submitFormUrl:null,submitFormMethod:null,theme:"light",themeMap:{dark:"light",light:"dark"},weak:"no",thirdMessage:"yes",menuWidth:256,menuCollapsed:!1,mobileDefaultCollapsed:!0,ckEditor:{},noLoadingOnce:!1,spinning:!1,init:{configure:{},message:{},modal:{},result:{}},footer:"footer",modal:{visible:!1,centered:!0},drawer:{visible:!1},result:{visible:!1}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({moment:moment,redirectByVue:function(a){bsw.redirect(bsw.getBswData($(a.item.$el).find("span")))},tabsLinksSwitch:function(a){bsw.redirect(bsw.getBswData($("#tabs_link_"+a)))},dispatcherByNative:function(a){bsw.dispatcherByBswData(bsw.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])},selectedRowHandler:function(a){for(var b=[],c=0;c<this.previewSelectedRow.length;c++)bsw.isString(this.previewSelectedRow[c])&&(b[c]=bsw.evalExpr(this.previewSelectedRow[c]),a&&(b[c]=b[c][a]||null));return b},multipleAction:function(a,b){var c=this.selectedRowHandler();return 0===c.length?bsw.warning(bsw.lang.select_item_first):void bsw.request(a.location,{ids:c}).then(function(a){bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},showIFrameWithChecked:function(a,b){var c=this.selectedRowHandler(a.selector).join(","),d={ids:c};if("undefined"!=typeof a.form){var e="fill["+a.form+"]";d=_defineProperty({},e,c)}a.location=bsw.setParams(d,a.location),bsw.showIFrame(a,b)},showIFrameByNative:function(a){bsw.showIFrame(bsw.getBswData($(a)),a)},showIFrameByVue:function(a){this.showIFrameByNative($(a.target)[0])},fillParentForm:function(a,b){return a.ids=this.selectedRowHandler(a.selector).join(","),0===a.ids.length?bsw.warning(bsw.lang.select_item_first):void parent.postMessage(a,"*")},verifyJsonFormat:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"persistenceForm",d=this[c].getFieldValue(a.field),e=bsw.setParams(_defineProperty({},a.key,d),a.url);window.open(e)},setUrlToForm:function(a,b){this.submitFormUrl=a.location,this.submitFormMethod=$(b).attr("bsw-method")},previewGetUrl:function(a){var b=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};return a=a||this.previewUrl,bsw.setParams(Object.assign({page:this.previewPageNumber},b),a)},previewPaginationRefresh:function(a){this.noLoadingOnce=!0,this.pagination(this.previewGetUrl(),null,a)},previewImageChange:function(){var a=this;if(0!==a.previewColumns.length)var b=setInterval(function(){return c()},50),c=function(){var c=$("img"),d=0;c.each(function(){d+=this.complete?1:0});var e=a.previewColumns[0].fixed;a.previewColumns[0].fixed=!e,a.previewColumns[0].fixed=e,(d>=c.length||0===c.length)&&clearInterval(b)}},pagination:function(a,b){var c=arguments.length>2&&void 0!==arguments[2]&&arguments[2],d=this;return b&&(a=bsw.setParams({page:b},a)),c||"undefined"==typeof d.previewList||0===d.previewList.length?location.href=a:void bsw.request(a).then(function(c){bsw.response(c).then(function(){d.previewList=c.sets.preview.list,d.previewPageNumber=b,d.previewUrl=a,d.previewPaginationData=c.sets.preview.page,d.previewImageChange(),history.replaceState({},"",a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},filterFormAction:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c=this,d=arguments[2],e=arguments[3],f=this;a.preventDefault(),f[d].validateFields(function(a,d){if(a)return!1;for(var g in d)if(d.hasOwnProperty(g)){if(moment.isMoment(d[g])){var h=d[g]._f||f[e][g];d[g]=d[g].format(h),b=!0}if(bsw.isArray(d[g]))for(var i=0;i<d[g].length;i++)if(moment.isMoment(d[g][i])){var j=d[g][i]._f||f[e][g];d[g][i]=d[g][i].format(j),b=!0}}var k={};for(var l in d)d.hasOwnProperty(l)&&"undefined"!=typeof d[l]&&null!=d[l]&&0!==d[l].length&&(k[l]=d[l]);return c[c.submitFormMethod+"FilterForm"](k,b)})},searchFilterForm:function(a){var b=arguments.length>1&&void 0!==arguments[1]&&arguments[1],c={},d=bsw.unsetParamsBeginWith(["filter"]);d=bsw.unsetParams(["page"],d,!1,c),d=bsw.setParams({filter:a},d),_typeof(c.page)&&c.page>1&&(b=!0),this.pagination(d,null,b)},exportFilterForm:function(a){var b=this,c=bsw.unsetParamsBeginWith(["filter"]);c=bsw.unsetParams(["page"],c),c=bsw.setParams({filter:a,scene:"export"},c),bsw.request(c).then(function(a){bsw.response(a).then(function(){var c={title:bsw.lang.export_mission,width:700,height:700,minHeight:700};c.location=bsw.setParams(a.sets,b.init.exportApiUrl,!0),bsw.showIFrame(c,$("body")[0])}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},submitFormAction:function(a,b,c){var d=this,e=this;a.preventDefault(),e[b].validateFields(function(a,b){if(a)return!1;for(var f in b)if(b.hasOwnProperty(f)){if(moment.isMoment(b[f])){var g=b[f]._f||e[c][f];b[f]=b[f].format(g)}if(bsw.isArray(b[f]))for(var h=0;h<b[f].length;h++)if(moment.isMoment(b[f][h])){var i=b[f][h]._f||e[c][f];b[f][h]=b[f][h].format(i)}bsw.checkJsonDeep(b,f+".fileList")&&delete b[f]}return d[d.submitFormMethod+"PersistenceForm"](b)})},submitPersistenceForm:function(a){bsw.request(this.submitFormUrl,{submit:a}).then(function(a){var b=bsw.parseQueryString();if(b.iframe){a.sets.arguments=bsw.parseQueryString();var c=a.sets.function||"handleResponse";parent.postMessage({response:a,function:c},"*")}else bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},uploaderChange:function(a,b){var c=a.file,d=a.fileList,e=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"persistenceForm";"done"===c.status?this.spinning=!1:"uploading"===c.status&&(this.spinning=!0);var f=this.persistenceFileListKeyCollect[b];if(!c.response)return void(f.list=d);c.response.error&&(f.list=d.slice(0,-1));var g=f.list.slice(-1);if(g.length){var h,i=g[0].response.sets,j=(h={},_defineProperty(h,f.id,"attachment_id"),_defineProperty(h,f.md5,"attachment_md5"),_defineProperty(h,f.sha1,"attachment_sha1"),_defineProperty(h,f.url,"attachment_url"),h);for(var k in j)if(j.hasOwnProperty(k)&&k&&j[k]){if(0===$("#"+k).length)continue;this[e]&&this[e].setFieldsValue(_defineProperty({},k,i[j[k]]))}}if("undefined"!=typeof c.response.code&&500!==c.response.code||(this.spinning=!1),c.response.sets.href){var l=c.response.sets.function||"handleResponse";parent.postMessage({response:c.response,function:l},"*")}else bsw.response(c.response).catch(function(a){console.warn(a)})},switchFieldShapeWithSelect:function(a,b,c){var d=this.persistenceFieldShapeNow,e=this.persistenceFieldShapeCollect[c];for(var f in e)e.hasOwnProperty(f)&&(d[f]=e[f].includes(a))},requestByAjax:function(a,b){var c=this;bsw.request(a.location).then(function(b){bsw.response(b).then(function(){"undefined"!=typeof a.refresh&&a.refresh&&c.previewPaginationRefresh(!1)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},copyFileLink:function(a,b){this.copy=a.link},getFormDataByEvent:function(a){var b=a.target.id,c=a.target.value,d=bsw.getBswData($(a.target)),e=d.form||"persistenceForm";return{field:b,value:c,data:d,form:e}},refreshPreviewInParent:function(a,b){bsw.handleResponseInParent(a,b),this.previewPaginationRefresh(!1)}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){var d=bsw.smallHump(b.arg);c.context.init[d]=b.value||b.expression}}},bsw.config.directive||{})).watch(Object.assign({},bsw.config.watch||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").data("bsw-value")})},bsw.config.component||{})).init(function(a){var b=!1;a.scaffoldInit&&(b=a.scaffoldInit()),bsw.initClipboard(),bsw.initUpwardInfect();var c=b?1200:500;setTimeout(function(){$(window).resize(),$(".bsw-page-loading").fadeOut(300,function(){bsw.messageAutoDiscovery(a.init),bsw.autoIFrameHeight()})},c)}),window.addEventListener("message",function(a){a.data.function+="InParent",bsw.dispatcherByBswData(a.data,$("body")[0])},!1)});