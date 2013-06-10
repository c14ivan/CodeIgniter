/*
 * jQuery validate.password plug-in 1.0
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validate.password/
 *
 * Copyright (c) 2009 Jörn Zaefferer
 *
 * $Id$
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($) {
	
	var LOWER = /[a-z]/,
		UPPER = /[A-Z]/,
		DIGIT = /[0-9]/,
		DIGITS = /[0-9].*[0-9]/,
		SPECIAL = /[^a-zA-Z0-9]/,
		SAME = /^(.)\1+$/;
		
	function rating(rate, message) {
		return {
			rate: rate,
			messageKey: message
		};
	}
	
	function uncapitalize(str) {
		return str.substring(0, 1).toLowerCase() + str.substring(1);
	}
	
	$.validator.passwordRating = function(password, username) {
		if (!password || password.length < 5)
			return rating(0, "too_short");
		if (username && password.toLowerCase().match(username.toLowerCase()))
			return rating(0, "similar_to_username");
		if (SAME.test(password))
			return rating(1, "very_weak");
		
		var lower = LOWER.test(password),
			upper = UPPER.test(uncapitalize(password)),
			digit = DIGIT.test(password),
			digits = DIGITS.test(password),
			special = SPECIAL.test(password);
		
		if (lower && upper && digit || lower && digits || upper && digits || special)
			return rating(4, "strong");
		if (lower && upper || lower && digit || upper && digit)
			return rating(3, "good");
		return rating(2, "weak");
	}
	
	$.validator.passwordRating.messages = {
		"similar_to_username": "Too similar to username",
		"too_short": "Too short",
		"very_weak": "Very weak",
		"weak": "Weak",
		"good": "Good",
		"strong": "Strong"
	}
	
	$.validator.addMethod("password", function(value, element, usernameField) {
		// use untrimmed value
		var password = element.value,
		// get username for comparison, if specified
			username = $(typeof usernameField != "boolean" ? usernameField : []);
			
		var rating = $.validator.passwordRating(password, username.val());
		// update message for this field
		
		var meter = $(".password-meter", element.parentElement);
		var text= $.validator.passwordRating.messages[rating.messageKey];
        if(typeof(this.settings.messages[element.name])!="undefined"){
            text=this.settings.messages[element.name]['pass_'+rating.messageKey];
        }
        
		meter.find(".password-meter-bar").removeClass().addClass("password-meter-bar").addClass("password-meter-" + rating.messageKey);
		meter.find(".password-meter-message")
		.removeClass()
		.addClass("password-meter-message")
		.addClass("password-meter-message-" + rating.messageKey)
		.text(text);
		// display process bar instead of error message
		
		return rating.rate > 2;
	}, "&nbsp;");
	// manually add class rule, to make username param optional
	$.validator.classRuleSettings.password = { password: true };
	
})(jQuery);

jQuery.fn.showPassword = function(conf) {
    var config = $.extend({
        str: 'Show password',
        className: 'password-toggler'
    }, conf);
    return this.each(function() {
        jQuery('input[type=password].passmeter', this)
            .each(function() {
            var field = jQuery(this);
            var classfield= field.attr('class');
            var fakeField = jQuery('<input type="text" name="dummy_pass" class="' + classfield + '" value="' + field.val() + '" />')
                .insertAfter(field)
                .hide();
            var check = jQuery('<span class="add-on"><input type="checkbox"  name="dummy_pass" title="'+ config.str + '" /></span>');
            var parentLabel = field.parents('label');
            if (parentLabel.length) {
                check.insertAfter(parentLabel)
            } else {
                check.insertAfter(fakeField)
            }
            check.find('input')
                .click(function() {
                if (jQuery(this)
                    .is(':checked')) {
                    field.hide();
                    fakeField.val(field.val())
                        .show()
                } else {
                    field.show();
                    fakeField.hide()
                }
            });
            fakeField.change(function() {
                field.val(fakeField.val())
            })
        })
    })
};