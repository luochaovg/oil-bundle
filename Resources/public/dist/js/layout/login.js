/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-05-19 */
"use strict";bsw.configure({data:{size:"large",form:null,btnLoading:!1,account:null,password:null,captcha:null,google_captcha:null},method:{handleSubmit:function(a){var b=this;a.preventDefault();var c={account:this.account,password:this.password,captcha:this.captcha,google_captcha:this.google_captcha};return"undefined"!=typeof c.account&&c.account?"undefined"!=typeof c.password&&c.password?c.password.length<8||c.password.length>20?(this.btnLoading=!0,bsw.warning(bsw.lang.password_length_error,3).then(function(){return b.btnLoading=!1}),!1):"undefined"!=typeof c.captcha&&c.captcha?(c.password=bsw.rsaEncrypt(c.password),void bsw.request(this.api_login,c).then(function(a){b.btnLoading=!0,bsw.response(a,null,null,2).catch(function(){b.btnLoading=!1})})):(this.btnLoading=!0,bsw.error(bsw.lang.captcha_required,3).then(function(){return b.btnLoading=!1}),!1):(this.btnLoading=!0,bsw.error(bsw.lang.password_required,3).then(function(){return b.btnLoading=!1}),!1):(this.btnLoading=!0,bsw.error(bsw.lang.username_required,3).then(function(){return b.btnLoading=!1}),!1)}},logic:{form:function(a){a.form=a.$form.createForm(a)}}});