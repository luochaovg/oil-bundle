bsw.configure({
    data: {
        size: 'large',
        form: null,
        btnLoading: false,
        account: null,
        password: null,
        captcha: null,
        google_captcha: null,
    },
    method: {
        handleSubmit(e) {
            e.preventDefault();
            let login = {
                account: this.account,
                password: this.password,
                captcha: this.captcha,
                google_captcha: this.google_captcha,
            };

            // account
            if (typeof login.account === 'undefined' || !login.account) {
                this.btnLoading = true;
                bsw.error(bsw.lang.username_required, 3).then(() => this.btnLoading = false);
                return false;
            }

            // password
            if (typeof login.password === 'undefined' || !login.password) {
                this.btnLoading = true;
                bsw.error(bsw.lang.password_required, 3).then(() => this.btnLoading = false);
                return false;
            }

            if (login.password.length < 8 || login.password.length > 20) {
                this.btnLoading = true;
                bsw.warning(bsw.lang.password_length_error, 3).then(() => this.btnLoading = false);
                return false;
            }

            // number captcha
            if (typeof login.captcha === 'undefined' || !login.captcha) {
                this.btnLoading = true;
                bsw.error(bsw.lang.captcha_required, 3).then(() => this.btnLoading = false);
                return false;
            }

            login.password = bsw.rsaEncrypt(login.password);
            bsw.request(this.api_login, login).then((res) => {
                this.btnLoading = true;
                bsw.response(res, null, null, 2).catch(() => {
                    this.btnLoading = false;
                });
            });
        },
    },
    logic: {
        form: function (v) {
            v.form = v.$form.createForm(v);
        }
    }
});