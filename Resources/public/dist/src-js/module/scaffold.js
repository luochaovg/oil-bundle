'use strict';

bsw.configure({
    method: {
        themeSwitch: function themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true);
        },
        colorWeakSwitch: function colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true);
            bsw.switchClass('bsw-weak', this.weak);
        },
        menuTrigger: function menuTrigger() {
            var _collapsed = this.menuCollapsed ? 'yes' : 'no';
            var collapsed = bsw.cookieMapNext('bsw_menu_collapsed', this.opposeMap, _collapsed, true);
            this.menuCollapsed = collapsed === 'yes';
        },
        scaffoldInit: function scaffoldInit() {
            // theme
            this.theme = bsw.cookieMapCurrent('bsw_theme', this.themeMap, this.configure.theme || this.theme);
            // color weak
            this.weak = bsw.cookieMapCurrent('bsw_color_weak', this.opposeMap, this.configure.weak || this.weak);
            bsw.switchClass('bsw-weak', this.weak);
            // menu
            this.menuWidth = this.configure.menu_width || this.menuWidth;
            var collapsed = bsw.cookieMapCurrent('bsw_menu_collapsed', this.opposeMap, typeof this.configure.menu_collapsed === 'undefined' ? this.menuWidth : this.configure.menu_collapsed);

            var menuCollapsed = collapsed === 'yes';
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    }
});
