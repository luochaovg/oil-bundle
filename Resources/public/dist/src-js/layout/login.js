'use strict';

app.configure({
    data: {
        size: 'large',
        form: null,
        btnLoading: false,
        account: null,
        password: null,
        captcha: null,
        google_captcha: null
    },
    method: {
        handleSubmit: function handleSubmit(e) {
            var _this = this;

            e.preventDefault();
            var login = {
                account: this.account,
                password: this.password,
                captcha: this.captcha,
                google_captcha: this.google_captcha
            };

            // account
            if (typeof login.account === 'undefined' || !login.account) {
                this.btnLoading = true;
                app.error(app.lang.username_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
                return false;
            }

            // password
            if (typeof login.password === 'undefined' || !login.password) {
                this.btnLoading = true;
                app.error(app.lang.password_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
                return false;
            }

            if (login.password.length < 8 || login.password.length > 20) {
                this.btnLoading = true;
                app.warning(app.lang.password_length_error, 3).then(function () {
                    return _this.btnLoading = false;
                });
                return false;
            }

            // number captcha
            if (typeof login.captcha === 'undefined' || !login.captcha) {
                this.btnLoading = true;
                app.error(app.lang.captcha_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
                return false;
            }

            login.password = app.rsaEncrypt(login.password);
            app.request(this.api_login, login).then(function (res) {
                _this.btnLoading = true;
                app.response(res, null, null, 2).catch(function () {
                    _this.btnLoading = false;
                });
            });
        }
    },
    logic: {
        form: function form(v) {
            v.form = v.$form.createForm(v);
        }
    }
});
