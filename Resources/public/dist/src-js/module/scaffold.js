'use strict';

bsw.configure({
    method: {
        themeSwitch: function themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true, bsw.lang.theme);
        },
        colorWeakSwitch: function colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true, bsw.lang.color_weak);
            bsw.switchClass('bsw-weak', this.weak);
        },
        thirdMessageSwitch: function thirdMessageSwitch() {
            this.third_message = bsw.cookieMapNext('bsw_third_message', this.opposeMap, this.third_message, true, bsw.lang.third_message);
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
            // third message
            this.third_message = bsw.cookieMapCurrent('bsw_third_message', this.opposeMap, this.configure.third_message || this.third_message);
            // menu
            this.menuWidth = this.configure.menu_width || this.menuWidth;
            var collapsed = bsw.cookieMapCurrent('bsw_menu_collapsed', this.opposeMap, typeof this.configure.menu_collapsed === 'undefined' ? this.menuWidth : this.configure.menu_collapsed);

            var menuCollapsed = collapsed === 'yes';
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    },
    logic: {
        thirdMessage: function thirdMessage(v) {
            if (v.configure.third_message_second < 3) {
                return;
            }
            v.$nextTick(function () {
                setInterval(function () {
                    var tm = bsw.cookieMapCurrent('bsw_third_message', v.opposeMap, v.third_message);
                    if (tm === 'no') {
                        return;
                    }
                    v.no_loading_once = true;
                    bsw.request(v.api_third_message).then(function (res) {
                        if (res.error === 4967) {
                            return;
                        }
                        bsw.response(res).catch(function (reason) {
                            console.warn(reason);
                        });
                    }).catch(function (reason) {
                        console.warn(reason);
                    });
                }, v.configure.third_message_second * 1000);
            });
        }
    }
});
