/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-08-05 */
"use strict";window.bsw=new FoundationAntD(jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue().template(bsw.config.template||null).data(Object.assign({bsw:bsw,locale:bsw.d.locales[bsw.lang.i18n_ant],noLoadingOnce:!1,spinning:!1,ckEditor:{},init:{configure:{},message:{},modal:{},result:{}}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({redirectByVue:function(a){bsw.redirect(bsw.getBswData($(a.item.$el).find("span")))},dispatcherByNative:function(a){bsw.dispatcherByBswData(bsw.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){var d=bsw.smallHump(b.arg);c.context.init[d]=b.value||b.expression}}},bsw.config.directive||{})).watch(Object.assign({},bsw.config.watch||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").data("bsw-value")})},bsw.config.component||{})).init(function(a){setTimeout(function(){bsw.messageAutoDiscovery(a.init)},100)})});