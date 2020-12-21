import {LiveForm, Nette} from 'live-form-validation';

LiveForm.addError = function (el, message) {
    // Ignore elements with disabled live validation
    if (this.hasClass(el, this.options.disableLiveValidationClass))
        return;

    var groupEl = this.getGroupElement(el);
    this.setFormProperty(el.form, "hasError", true);
    this.addClass(groupEl, this.options.controlErrorClass);

    let groupSelector = $(el);
    if (groupSelector.is("select")) {
        let selectButton = groupSelector.parent().children('button');
        selectButton.removeClass('btn-primary');
        selectButton.addClass('btn-danger');
    }

    if (this.options.showValid) {
        this.removeClass(groupEl, this.options.controlValidClass);
    }

    if (!message) {
        message = '&nbsp;';
    } else {
        message = this.options.messageErrorPrefix + message;
    }

    var messageEl = this.getMessageElement(el);
    messageEl.innerHTML = message;
    messageEl.className = this.options.messageErrorClass;
};

LiveForm.removeError = function (el) {
    // We don't want to remove any errors during onLoadValidation
    if (this.getFormProperty(el.form, "onLoadValidation"))
        return;

    var groupEl = this.getGroupElement(el);

    let groupSelector = $(el);
    if (groupSelector.is("select")) {
        let selectButton = groupSelector.parent().children('button');
        selectButton.removeClass('btn-danger');
        selectButton.addClass('btn-primary');
    }

    this.removeClass(groupEl, this.options.controlErrorClass);

    var id = el.getAttribute('data-lfv-message-id');
    if (id) {
        var messageEl = this.getMessageElement(el);
        messageEl.innerHTML = '';
        messageEl.className = '';
    }

    if (this.options.showValid) {
        if (this.showValid(el))
            this.addClass(groupEl, this.options.controlValidClass);
        else
            this.removeClass(groupEl, this.options.controlValidClass);
    }
};

LiveForm.setOptions({
    showMessageClassOnParent: "form-control",
    messageParentClass: false,
    controlErrorClass: 'is-invalid',
    controlValidClass: 'has-success',
    messageErrorClass: 'invalid-feedback',
    enableHiddenMessageClass: 'show-hidden-error',
    disableLiveValidationClass: 'no-live-validation',
    disableShowValidClass: 'no-show-valid',
    messageTag: 'div',
    messageIdPostfix: '_message',
    messageErrorPrefix: '&nbsp;<i class="fas fa-exclamation-circle"></i>&nbsp;',
    showAllErrors: true,
    showValid: false,
    wait: false,
    focusScreenOffsetY: false
});

Nette.initOnLoad();
window.Nette = Nette;
window.LiveForm = LiveForm;