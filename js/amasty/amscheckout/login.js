AmastyScheckoutLogin = Class.create();
AmastyScheckoutLogin.prototype = {
    block: null,
    email: null,
    password: null,
    messagebox: null,
    checkUrl: null,
    timerDelay: 2000,
    guestCheckout: '0',
    messageboxEmail: null,
    messageboxPassword: null,
    validTld: [],
    initialize: function(
        block,
        messagebox,
        checkUrl,
        guestCheckout,
        messageboxEmail,
        messageboxPassword,
        validTld
    ){
        this.block = block;
        this.messagebox = messagebox;

        this.email = this.block.down('#amasty-scheckout-login-email');
        this.password = this.block.down('#amasty-scheckout-login-password');
        this.checkUrl = checkUrl;
        this.guestCheckout = guestCheckout;
        this.messageboxEmail = messageboxEmail;
        this.messageboxPassword = messageboxPassword;
        this.validTld = validTld;


        if (this.guestCheckout !== '1'){
            this.password.addClassName('required-entry');
        } else {
            this.password.disable();
        }

        this.events();
    },
    events: function () {
        this.smartKeyup(this.block, function(){

            if (!Validation.get('IsEmpty').test(this.email.value) &&
                (!Validation.get('validate-email').test(this.email.value) ||
                !this.isValidTld())
            ) {
                this.messagebox.update(this.messageboxEmail);
                if (this.guestCheckout === '1'){
                    this.password.disable();
                }
            } else if (Validation.get('validate-email').test(this.email.value) &&
                !Validation.get('IsEmpty').test(this.email.value)
            ){
                this.check();
                if (this.guestCheckout === '1'){
                    this.password.enable();
                }
            }

            if (!Validation.get('validate-password').test(this.password.value)) {
                this.messagebox.update(this.messageboxPassword);
            }
        });
    },
    smartKeyup: function(block, callback){
        var coverCollback = function(){
            block.timeoutId = null;
            callback.call(this);
        }.bind(this);

        block.select('input').each(function(input){
            input.on('keyup', function(event){
                if (block.timeoutId){
                    clearTimeout(block.timeoutId);
                }

                if (event.keyCode === 13){
                    coverCollback.call(this);
                } else {
                    block.timeoutId = window.setTimeout(coverCollback.bind(this), this.timerDelay);
                }
            }.bind(this));

            input.on('blur', function(){
                if (block.timeoutId){
                    clearTimeout(block.timeoutId);
                    coverCollback.call(this);
                }
            }.bind(this));

        }.bind(this));
    },
    check: function(){
        var params = {
            email: this.email.value,
            password: this.password.value
        };
        var billingProcess = new amLoadingProcess('billing');

        billingProcess.showSmallProcessing('billing', true);

        return new Ajax.Request(this.checkUrl, {
            method: 'post',
            parameters: params,
            onSuccess: function (response) {
                var result = response.responseText.evalJSON();
                this.messagebox.update(result.message);

                if (result.customerLoggedIn){
                    showLoading();
                    document.location.reload(true);
                }

                billingProcess.showSmallProcessing('billing', false);
            }.bind(this)
        });
    },
    params: function()
    {
        return {
            'method': this.password.value !== '' ? 'register' : 'guest',
            'billing[email]': this.email.value,
            'billing[customer_password]': this.password.value,
            'billing[confirm_password]': this.password.value
        };
    },
    isValidTld: function () {
        var matches = this.email.value.match(/([^.]{2,63})$/);
        if (matches.length) {
            var tld = matches[1];
            if (this.validTld.indexOf(tld) != -1) {
                return true;
            }
        }
        return false;
    }
}
