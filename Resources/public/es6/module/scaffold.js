bsw.configure({
    method: {
        themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true);
        },
        colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true);
            bsw.switchClass('bsw-weak', this.weak);
        },
        menuTrigger() {
            let _collapsed = this.menuCollapsed ? 'yes' : 'no';
            let collapsed = bsw.cookieMapNext('bsw_menu_collapsed', this.opposeMap, _collapsed, true);
            this.menuCollapsed = (collapsed === 'yes');
        },
        scaffoldInit() {
            // theme
            this.theme = bsw.cookieMapCurrent('bsw_theme', this.themeMap, this.configure.theme || this.theme);
            // color weak
            this.weak = bsw.cookieMapCurrent('bsw_color_weak', this.opposeMap, this.configure.weak || this.weak);
            bsw.switchClass('bsw-weak', this.weak);
            // menu
            this.menuWidth = this.configure.menu_width || this.menuWidth;
            let collapsed = bsw.cookieMapCurrent(
                'bsw_menu_collapsed',
                this.opposeMap,
                (typeof this.configure.menu_collapsed === 'undefined') ? this.menuWidth : this.configure.menu_collapsed
            );

            let menuCollapsed = (collapsed === 'yes');
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    },
});