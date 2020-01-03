'use strict';

app.configure({

    data: {
        theme: 'light',
        themeMap: { dark: 'light', light: 'dark' },
        weak: 'no',
        tableBorder: true,
        tablePadding: 'no',
        menuWidth: 256,
        menuCollapsed: false,
        mobileDefaultCollapsed: true
    },

    method: {
        themeSwitch: function themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true);
        },
        colorWeakSwitch: function colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true);
            bsw.switchClass('app-weak', this.weak);
        },
        tableBorderSwitch: function tableBorderSwitch() {
            var _tableBorder = this.tableBorder ? 'yes' : 'no';
            var tableBorder = bsw.cookieMapNext('bsw_table_border', this.opposeMap, _tableBorder, true);
            this.tableBorder = tableBorder === 'yes';
        },
        tablePaddingSwitch: function tablePaddingSwitch() {
            this.tablePadding = bsw.cookieMapNext('bsw_table_padding', this.opposeMap, this.tablePadding, true);
            bsw.switchClass('padding', this.tablePadding, '.app-content');
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

            // table border
            var tableBorder = bsw.cookieMapCurrent('bsw_table_border', v.opposeMap, typeof v.configure.table_border === 'undefined' ? v.tableBorder : v.configure.table_border);
            v.tableBorder = tableBorder === 'yes';

            // table padding
            v.tablePadding = bsw.cookieMapCurrent('bsw_table_padding', v.opposeMap, v.configure.table_padding || v.tablePadding);
            bsw.switchClass('padding', v.tablePadding, '.app-content');

            // menu
            v.menuWidth = v.configure.menu_width || v.menuWidth;
            var collapsed = bsw.cookieMapCurrent('bsw_menu_collapsed', v.opposeMap, typeof v.configure.menu_collapsed === 'undefined' ? v.menuWidth : v.configure.menu_collapsed);

            setTimeout(function () {
                v.menuCollapsed = collapsed === 'yes';
            }, 10);
        }
    }
});
