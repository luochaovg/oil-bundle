/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-01-08 */
"use strict";app.configure({method:{themeSwitch:function(){this.theme=bsw.cookieMapNext("bsw_theme",this.themeMap,this.theme,!0)},colorWeakSwitch:function(){this.weak=bsw.cookieMapNext("bsw_color_weak",this.opposeMap,this.weak,!0),bsw.switchClass("app-weak",this.weak)},menuTrigger:function(){var a=this.menuCollapsed?"yes":"no",b=bsw.cookieMapNext("bsw_menu_collapsed",this.opposeMap,a,!0);this.menuCollapsed="yes"===b}},logic:{menu:function(a){a.theme=bsw.cookieMapCurrent("bsw_theme",a.themeMap,a.configure.theme||a.theme),a.weak=bsw.cookieMapCurrent("bsw_color_weak",a.opposeMap,a.configure.weak||a.weak),bsw.switchClass("app-weak",a.weak),a.menuWidth=a.configure.menu_width||a.menuWidth;var b=bsw.cookieMapCurrent("bsw_menu_collapsed",a.opposeMap,"undefined"==typeof a.configure.menu_collapsed?a.menuWidth:a.configure.menu_collapsed);a.$nextTick(function(){a.menuCollapsed="yes"===b})}}});