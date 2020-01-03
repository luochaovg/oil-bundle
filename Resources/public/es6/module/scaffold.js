app.configure({
    method: {
        themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true);
        },
        colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true);
            bsw.switchClass('app-weak', this.weak);
        },
        menuTrigger() {
            let _collapsed = this.menuCollapsed ? 'yes' : 'no';
            let collapsed = bsw.cookieMapNext('bsw_menu_collapsed', this.opposeMap, _collapsed, true);
            this.menuCollapsed = (collapsed === 'yes');
        }
    },

    logic: {
        menu(v) {
            // theme
            v.theme = bsw.cookieMapCurrent('bsw_theme', v.themeMap, v.configure.theme || v.theme);
            // color weak
            v.weak = bsw.cookieMapCurrent('bsw_color_weak', v.opposeMap, v.configure.weak || v.weak);
            bsw.switchClass('app-weak', v.weak);
            // menu
            v.menuWidth = v.configure.menu_width || v.menuWidth;
            let collapsed = bsw.cookieMapCurrent(
                'bsw_menu_collapsed',
                v.opposeMap,
                (typeof v.configure.menu_collapsed === 'undefined') ? v.menuWidth : v.configure.menu_collapsed
            );
            v.$nextTick(function () {
                v.menuCollapsed = (collapsed === 'yes');
            });
        }
    }
});