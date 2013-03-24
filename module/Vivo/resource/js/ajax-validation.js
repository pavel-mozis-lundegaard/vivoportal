/**
 * Sets up AJAX validation for an element on document ready
 * Note: Either valInfoId or applyStatusSelector must be set otherwise the validation will not noticeable
 * @param {object} options Properties:
 *      elementId: {string}             REQUIRED Id of the validated element
 *      url: {string}                   REQUIRED Server URL
 *      valInfoId: {string}             Id of the validation info span. Set to null to disable.
 *      applyStatusSelector: {string}   Status classes are applied to the elements having this class
 *      action: {string}                Action called on the UI component
 *      displayErrors: {boolean}        Should the error messages be displayed
 *      waitMsg: {string}               Message displayed when waiting for the server response. Set to null to disable.
 *      validMsg: {string}              Message displayed when the data is valid. Set to null to disable.
 *      classChecking: {string}         Class used for status 'checking'
 *      classValid: {string}            Class used for status 'valid'
 *      classInvalid: {string}          Class used for status 'invalid'
 */
function setUpAjaxValidation(options)
{
    $(document).ready(function() {
        doSetUpAjaxValidation(options);
    });
}

/**
 * Sets up AJAX validation for an element
 * Note: Either valInfoId or applyStatusSelector must be set otherwise the validation will not noticeable
 * @param {object} options Properties:
 *      elementId: {string}             REQUIRED Id of the validated element
 *      url: {string}                   REQUIRED Server URL
 *      valInfoId: {string}             Id of the validation info span. Set to null to disable.
 *      applyStatusSelector: {string}   Status classes are applied to the elements having this class
 *      action: {string}                Action called on the UI component
 *      displayErrors: {boolean}        Should the error messages be displayed
 *      waitMsg: {string}               Message displayed when waiting for the server response. Set to null to disable.
 *      validMsg: {string}              Message displayed when the data is valid. Set to null to disable.
 *      classChecking: {string}         Class used for status 'checking'
 *      classValid: {string}            Class used for status 'valid'
 *      classInvalid: {string}          Class used for status 'invalid'
 */
function doSetUpAjaxValidation(options)
{
    var defaultOptions  = {
        elementId:          null,
        url:                null,
        valInfoId:          null,                       //Set to null to disable info span
        applyStatusSelector:null,                       //Set to null to disable status classes
        action:             'ajaxValidateFormField',
        displayErrors:      true,
        waitMsg:            'Checking validity...',     //Set to null to disable
        validMsg:           'Data is valid',            //Set to null to disable
        classChecking:      'ajax-validation-check',
        classValid:         'ajax-validation-valid',
        classInvalid:       'ajax-validation-invalid'
    };
    options = $.extend({}, defaultOptions, options);
    var valInfo;
    if (options.valInfoId != null) {
        valInfo = $('#' + options.valInfoId);
    } else {
        valInfo = null;
    }
    var elem    = $('#' + options.elementId);
    var data    = 'act=root->main->main->' + options.action + '&field=' + elem.attr('name')  + '&value=';
    var statusElements;
    if (options.applyStatusSelector != null) {
        statusElements = $(options.applyStatusSelector);
    } else {
        statusElements = null;
    }
    elem.keyup(function () {
        var t = this;
        var msgs;
        if (this.value != this.lastValue) {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            if (valInfo != null) {
                if (options.waitMsg != null) {
                    valInfo.html(options.waitMsg);
                } else {
                    valInfo.html('');
                }
            }
            if (statusElements != null) {
                statusElements.removeClass(options.classValid + ' ' + options.classInvalid);
                statusElements.addClass(options.classChecking);
            }
            this.timer = setTimeout(function () {
                $.ajax({
                    url: options.url,
                    data: data + t.value,
                    dataType: 'json',
                    type: 'post',
                    success: function (j) {
                        if (valInfo != null) {
                            valInfo.html('');
                        }
                        if (j.valid) {
                            //Data is valid
                            if (valInfo != null && options.validMsg != null) {
                                valInfo.html(options.validMsg);
                            }
                            if (statusElements != null) {
                                statusElements.removeClass(options.classInvalid + ' ' + options.classChecking);
                                statusElements.addClass(options.classValid);
                            }
                        } else {
                            //Data is not valid
                            if (valInfo != null && options.displayErrors && !jQuery.isEmptyObject(j.messages)) {
                                msgs    = '<ul>';
                                for (var messageKey in j.messages) {
                                    msgs    = msgs + '<li>' + j.messages[messageKey] + '</li>';
                                }
                                msgs    = msgs + '</ul>';
                                valInfo.html(msgs);
                            }
                            if (statusElements != null) {
                                statusElements.removeClass(options.classValid + ' ' + options.classChecking);
                                statusElements.addClass(options.classInvalid);
                            }
                        }
                    }
                });
            }, 200);
            this.lastValue = this.value;
        }
    });
}
