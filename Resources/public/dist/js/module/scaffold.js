/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-06-03 */
"use strict";bsw.configure({method:{themeSwitch:function(){this.theme=bsw.cookieMapNext("bsw_theme",this.themeMap,this.theme,!0,bsw.lang.theme)},colorWeakSwitch:function(){this.weak=bsw.cookieMapNext("bsw_color_weak",this.opposeMap,this.weak,!0,bsw.lang.color_weak),bsw.switchClass("bsw-weak",this.weak)},thirdMessageSwitch:function(){this.third_message=bsw.cookieMapNext("bsw_third_message",this.opposeMap,this.third_message,!0,bsw.lang.third_message)},menuTrigger:function(){var a=this.menuCollapsed?"yes":"no",b=bsw.cookieMapNext("bsw_menu_collapsed",this.opposeMap,a,!0);this.menuCollapsed="yes"===b},scaffoldInit:function(){this.theme=bsw.cookieMapCurrent("bsw_theme",this.themeMap,this.configure.theme||this.theme),this.weak=bsw.cookieMapCurrent("bsw_color_weak",this.opposeMap,this.configure.weak||this.weak),bsw.switchClass("bsw-weak",this.weak),this.third_message=bsw.cookieMapCurrent("bsw_third_message",this.opposeMap,this.configure.third_message||this.third_message),this.menuWidth=this.configure.menu_width||this.menuWidth;var a=bsw.cookieMapCurrent("bsw_menu_collapsed",this.opposeMap,"undefined"==typeof this.configure.menu_collapsed?this.menuWidth:this.configure.menu_collapsed),b="yes"===a;return this.$nextTick(function(){this.menuCollapsed=b}),b}},logic:{thirdMessage:function(a){a.configure.third_message_second<3||a.$nextTick(function(){setInterval(function(){var b=bsw.cookieMapCurrent("bsw_third_message",a.opposeMap,a.third_message);"no"!==b&&(a.no_loading_once=!0,bsw.request(a.api_third_message).then(function(a){4967!==a.error&&bsw.response(a).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)}))},1e3*a.configure.third_message_second)})}}});