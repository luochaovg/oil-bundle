'use strict';

app.configure({
    method: {
        themeSwitch: function themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true);
        },
        colorWeakSwitch: function colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true);
            bsw.switchClass('app-weak', this.weak);
        },
        menuTrigger: function menuTrigger() {
            var _collapsed = this.menuCollapsed ? 'yes' : 'no';
            var collapsed = bsw.cookieMapNext('bsw_menu_collapsed', this.opposeMap, _collapsed, true);
            this.menuCollapsed = collapsed === 'yes';
        }
    },

    logic: {
        menu: function menu(v) {
            // theme
            v.theme = bsw.cookieMapCurrent('bsw_theme', v.themeMap, v.configure.theme || v.theme);
            // color weak
            v.weak = bsw.cookieMapCurrent('bsw_color_weak', v.opposeMap, v.configure.weak || v.weak);
            bsw.switchClass('app-weak', v.weak);
            // menu
            v.menuWidth = v.configure.menu_width || v.menuWidth;
            var collapsed = bsw.cookieMapCurrent('bsw_menu_collapsed', v.opposeMap, typeof v.configure.menu_collapsed === 'undefined' ? v.menuWidth : v.configure.menu_collapsed);
            v.$nextTick(function () {
                v.menuCollapsed = collapsed === 'yes';
            });
        }
    }
});
