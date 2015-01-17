define(['app', 'tpl!../templates/modal.ejs'], function(App, modalTemplate){

  return App.module('Common.Modal', function(Modal, App, Backbone, Marionette, $, _){

    var body = $('body');

    Modal.LayoutView = Marionette.LayoutView.extend({
      className: 'modal fade',
      template: modalTemplate,

      initialize: function(options){
        var opt = options || {};
        this.title = opt.title || 'Modal Window';
      },

      regions : {
        modalBody : 'div.modal-body'
      },

      templateHelpers: function(){
        return {
          title: this.title
        };
      },

      events: {
        'show.bs.modal': 'beforeShowModal',
        'hidden.bs.modal': 'hideModal',
        'click .js-save-btn': 'submitModal'
      },

      beforeShowModal: function(){
        body.addClass('blured');
      },

      hideModal: function(){
        body.removeClass('blured');
        this.trigger('modal:closed');
        this.destroy();
      },

      submitModal: function(){
        this.trigger('modal:submit');
      }

    });

  });

});