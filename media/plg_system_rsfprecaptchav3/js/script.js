var RSFormProReCAPTCHAv3 = {
	forms: {},
	add: function(key, action, formId) {
		formId = parseInt(formId);

		RSFormProReCAPTCHAv3.forms[formId] = {key: key, action: action};
	},
	execute: function(formId) {
		formId = parseInt(formId);

		var form = RSFormProReCAPTCHAv3.forms[formId];

		if (typeof form !== 'object') {
			return false;
		}

		grecaptcha.execute(form.key, {action: form.action}).then(function(token){
			document.getElementById('g-recaptcha-response-' + formId).value = token;
			RSFormPro.submitForm(RSFormPro.getForm(formId));
		});
	}
};