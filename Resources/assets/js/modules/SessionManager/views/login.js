define([
  'app',
  'config',
  'tpl!../templates/login.ejs'], function(App, Config, loginTemplate){

  return App.module('SessionManager', function(SessionManager, App, Backbone, Marionette, $, _){

    SessionManager.LoginView = Marionette.ItemView.extend({
      template: loginTemplate,
      className: 'login-block',

      initialize: function(){
        this.baseUrl = Config.baseUrl;
        this.basePath = Config.basePath;
      },

      serializeData: function(){
        return {
          baseUrl: this.baseUrl,
          basePath: this.basePath
        };
      },

      modelEvents: {
        'login:success' : 'onLoginSuccess',
        'login:fail' : 'onLoginFail'
      },

      events: {
        'click .js-submit' : 'submitForm'
      },

      submitForm: function(e){
        e.preventDefault();
        var arr = this.$('form').serializeArray(), i = arr.length, data = {};
        for(;i--;) {
          data[arr[i].name] = arr[i].value;
        }
        this.trigger('form:submit', data);
      },

      onLoginSuccess: function(){
        this.$el.fadeOut();
      },

      onLoginFail: function(){

      },

      onShow: function(){
        $('body').addClass('login-page');
      },

      onDestroy: function(){
        $('body').removeClass('login-page');
      }
    });

  });


});
