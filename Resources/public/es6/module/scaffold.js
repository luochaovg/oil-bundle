bsw.configure({
    method: {
        themeSwitch() {
            this.theme = bsw.cookieMapNext('bsw_theme', this.themeMap, this.theme, true, bsw.lang.theme);
        },
        colorWeakSwitch() {
            this.weak = bsw.cookieMapNext('bsw_color_weak', this.opposeMap, this.weak, true, bsw.lang.color_weak);
            bsw.switchClass('bsw-weak', this.weak);
        },
        thirdMessageSwitch() {
            this.thirdMessage = bsw.cookieMapNext('bsw_third_message', this.opposeMap, this.thirdMessage, true, bsw.lang.third_message);
        },

        menuTrigger() {
            let _collapsed = this.menuCollapsed ? 'yes' : 'no';
            let collapsed = bsw.cookieMapNext('bsw_menu_collapsed', this.opposeMap, _collapsed, true);
            this.menuCollapsed = (collapsed === 'yes');
            setTimeout(() => {
                $(window).resize();
            }, 300);
        },

        changeLanguageByVue(event) {
            let key = $(event.item.$el).find('span').attr('lang');
            bsw.request(this.init.languageApiUrl, {key}).then((res) => {
                bsw.response(res).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        scaffoldInit() {
            let cnf = this.init.configure;
            // theme
            this.theme = bsw.cookieMapCurrent('bsw_theme', this.themeMap, cnf.theme || this.theme);
            // color weak
            this.weak = bsw.cookieMapCurrent('bsw_color_weak', this.opposeMap, cnf.weak || this.weak);
            bsw.switchClass('bsw-weak', this.weak);
            // third message
            this.thirdMessage = bsw.cookieMapCurrent('bsw_third_message', this.opposeMap, cnf.third_message || this.third_message);
            // menu
            this.menuWidth = cnf.menu_width || this.menuWidth;
            let collapsed = bsw.cookieMapCurrent(
                'bsw_menu_collapsed',
                this.opposeMap,
                (typeof cnf.menu_collapsed === 'undefined') ? this.menuWidth : cnf.menu_collapsed
            );

            let menuCollapsed = (collapsed === 'yes');
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    },
    logic: {
        thirdMessage(v) {
            let cnf = v.init.configure;
            if (typeof cnf === 'undefined' || typeof cnf.third_message_second === 'undefined') {
                return;
            }
            if (cnf.third_message_second < 3) {
                return;
            }
            v.$nextTick(function () {
                setInterval(function () {
                    let tm = bsw.cookieMapCurrent('bsw_third_message', v.opposeMap, v.thirdMessage);
                    if (tm === 'no') {
                        return;
                    }
                    v.noLoadingOnce = true;
                    bsw.request(v.init.thirdMessageApiUrl).then((res) => {
                        if (res.error === 4967) {
                            return;
                        }
                        bsw.response(res).catch((reason => {
                            console.warn(reason);
                        }));
                    }).catch((reason => {
                        console.warn(reason);
                    }));
                }, cnf.third_message_second * 1000);
            });
        }
    }
});