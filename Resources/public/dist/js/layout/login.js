/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-08-24 */
"use strict";bsw.configure({data:{loginForm:null,btnLoading:!1,submitFormMethod:"doLogin",rsaPublicKey:"-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----"},method:{userLogin:function(a){this.submitFormAction(a,"loginForm")},doLoginPersistenceForm:function(a){var b=this;return a.account.length?a.password.length?a.password.length<8||a.password.length>20?(this.btnLoading=!0,bsw.warning(bsw.lang.password_length_error,3).then(function(){return b.btnLoading=!1})):a.captcha.length?(a.password=bsw.rsaEncrypt(a.password),void bsw.request(this.init.loginApiUrl,a).then(function(a){b.btnLoading=!0,bsw.response(a,null,null,2).catch(function(){b.btnLoading=!1})})):(this.btnLoading=!0,bsw.error(bsw.lang.captcha_required,3).then(function(){return b.btnLoading=!1})):(this.btnLoading=!0,bsw.error(bsw.lang.password_required,3).then(function(){return b.btnLoading=!1})):(this.btnLoading=!0,bsw.error(bsw.lang.username_required,3).then(function(){return b.btnLoading=!1}))}},logic:{createForm:function(a){a.loginForm=a.$form.createForm(a)}}});