(function() {

  var editionModes = {
    inline: {disableReturn: true, disableToolbar: true},
    partial: {disableDoubleReturn: true, disableToolbar: true},
    rich: {disableDoubleReturn: false, buttons: ['unorderedlist', 'orderedlist', 'bold', 'italic', 'underline', 'anchor', 'header1', 'header2', 'quote']},
    list: {disableDoubleReturn: true, buttons: ['unorderedlist', 'orderedlist']}
  };

  function Editable(elements, mode) {

    this.elements = elements.length ? elements : [elements];

    var options = editionModes[mode] ? editionModes[mode] : {};
    new MediumEditor(this.elements, options);
  };
  Editable.prototype.serialize = function() {

    var el,
        editables = [];

    for (var i=0, l=this.elements.length; i<l; i++) {
      el = this.elements[i];
      editables[$(el).data('editable-name')] = el.innerHTML.trim();
    }

    return editables;
  };

  function ToolbarView(options) {
    this.el = options.el;

    this.ui = {};
    this.ui.buttonExit = $('[data-action=exit]', this.el).get(0);
    this.ui.buttonSave = $('[data-action=save]', this.el).get(0);

    this.form = $('form', this.el).get(0);
  };
  ToolbarView.prototype.disable = function() {
    this.ui.buttonSave.disabled = true;
  };
  ToolbarView.prototype.enable = function() {
    this.ui.buttonSave.disabled = false;
  };

  function init() {

    var $body = $(document.body);
    var toolbar = new ToolbarView({el: $('#contentToolbar').get()});
    var $els = $('[data-editable-text]');
    var editables = [];

    $els.each(function(i, el) {
      editables.push(new Editable(el, el.getAttribute('data-editable-text')));
    });

    $(toolbar.form).on('submit', function(e) {
      e.preventDefault();

      var domains = {};
      for (var i=0, l=editables.length; i<l; i++) {
        $.extend(domains, editables[i].serialize());
      }

      $.ajax({
        url: toolbar.form.action,
        type: 'POST',
        data: domains,
        success: function() {
          $body.removeClass('contentedited');
          toolbar.disable();
        }
      });
    });

    $body.on('input', function(e) {
      $body.addClass('contentedited');
      toolbar.enable();
    });
  };

  init();

})();