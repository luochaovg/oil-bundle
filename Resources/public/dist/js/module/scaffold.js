/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-07-17 */
"use strict";bsw.configure({method:{themeSwitch:function(a,b){this.theme=bsw.cookieMapNext("bsw_theme",this.themeMap,this.theme,!0,bsw.lang.theme)},colorWeakSwitch:function(a,b){this.weak=bsw.cookieMapNext("bsw_color_weak",this.opposeMap,this.weak,!0,bsw.lang.color_weak),bsw.switchClass("bsw-weak",this.weak)},thirdMessageSwitch:function(a,b){this.thirdMessage=bsw.cookieMapNext("bsw_third_message",this.opposeMap,this.thirdMessage,!0,bsw.lang.third_message)},menuTrigger:function(){var a=this.menuCollapsed?"yes":"no",b=bsw.cookieMapNext("bsw_menu_collapsed",this.opposeMap,a,!0);this.menuCollapsed="yes"===b,setTimeout(function(){$(window).resize()},300)},changeLanguageByVue:function(a){var b=$(a.item.$el).find("span").attr("lang");bsw.request(this.init.languageApiUrl,{key:b}).then(function(a){bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},scaffoldInit:function(){var a=this.init.configure;this.theme=bsw.cookieMapCurrent("bsw_theme",this.themeMap,a.theme||this.theme),this.weak=bsw.cookieMapCurrent("bsw_color_weak",this.opposeMap,a.weak||this.weak),bsw.switchClass("bsw-weak",this.weak),this.thirdMessage=bsw.cookieMapCurrent("bsw_third_message",this.opposeMap,a.third_message||this.third_message),this.menuWidth=a.menu_width||this.menuWidth;var b=bsw.cookieMapCurrent("bsw_menu_collapsed",this.opposeMap,"undefined"==typeof a.menu_collapsed?this.menuWidth:a.menu_collapsed),c="yes"===b;return this.$nextTick(function(){this.menuCollapsed=c}),c}},logic:{thirdMessage:function(a){var b=a.init.configure;"undefined"!=typeof b&&"undefined"!=typeof b.third_message_second&&(b.third_message_second<3||a.$nextTick(function(){setInterval(function(){var b=bsw.cookieMapCurrent("bsw_third_message",a.opposeMap,a.thirdMessage);"no"!==b&&(a.noLoadingOnce=!0,bsw.request(a.init.thirdMessageApiUrl).then(function(a){4967!==a.error&&bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)}))},1e3*b.third_message_second)}))}}});