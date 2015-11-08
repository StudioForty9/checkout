
var SimpleForm = Class.create(VarienForm, {
  selector: '.opc .input-text, .opc select, .opc .radio, .opc .checkbox',
  initialize: function($super, formId, firstFieldFocus) {
    $super(formId, firstFieldFocus);
    $$(this.selector).invoke('observe', 'focus', this.onFieldFocus.bind(this));
    $$(this.selector).invoke('observe', 'blur', this.onFieldBlur.bind(this));
  },
  onFieldFocus: function(evt) {
    Event.element(evt).up(1).addClassName('simple-focus');
  },
  onFieldBlur: function(evt) {
    Event.element(evt).up(1).removeClassName('simple-focus');
  }
});

var EmailForm = Class.create(SimpleForm, {
  initialize: function($super, formId, firstFieldFocus) {
    $super(formId, firstFieldFocus);
    this.form.observe('submit', this.bindEmailPost.bind(this));
  },
  bindEmailPost: function (evt) {
    Event.stop(evt);
    this.checkEmail();
  },
  toggleLoadingIndicator: function(flag) {
    if (flag) {
      $('login-please-wait').show();
    } else {
      $('login-please-wait').hide();
    }
  },
  checkEmail: function (button) {
    button = button || {};
    if (!this.validator || !this.validator.validate()) {
      return this;
    }
    this.toggleLoadingIndicator(true);
    var request = new Ajax.Request('/simplecheckoutlogin/checkout/user/', {
      method: 'post',
      parameters: { email: $('check-email').value },
      onSuccess: this.onSuccess.bind(this)
    });
  },
  onSuccess: function(data) {
    this.toggleLoadingIndicator(false);
    var email = $("check-email").value;
    if (parseInt(data.responseText, 10) >= 1) {
      $("login-email").value = email;
      $("check-account").hide();
      $("has-account").show();
      $("login-password").focus();
      $("current-customer-email").innerText = email;
    } else {
      $("billing:email").value = email;
      checkout.gotoSection('billing', true);
      $("register-customer-password").hide();
    }
  }
});

var LoginForm = Class.create(SimpleForm, {
  inputId: 'login-email',
  initialize: function($super, formId, firstFieldFocus) {
    $super(formId, firstFieldFocus);
    this.form.observe('submit', this.postLogin.bind(this));
  },
  bindLoginPost: function(evt) {
    Event.stop(evt);
    this.postLogin();
  },
  postLogin: function() {
    if (this.validator && this.validator.validate()) {
      $('login-button').disabled = true;
      this.submit();
    }
  },
  goBack: function(button) {
    $('has-account').hide();
    $('check-account').show();
  }
});
