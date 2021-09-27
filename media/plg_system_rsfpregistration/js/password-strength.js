var RSFormProPasswords = {
	Forms: [],
	userOptions: {},

	progressBars: {
		responsive: {
			container: 	'rsfp-progress',
			inner: 		'rsfp-bar',
			states: {
				danger:		'rsfp-bar-danger',
				warning: 	'rsfp-bar-warning',
				info: 		'',
				success: 	'rsfp-bar-success'
			}
		},
		bootstrap2: {
			container: 	'progress',
			inner: 		'bar',
			states: {
				danger:		'bar-danger',
				warning: 	'bar-warning',
				info: 		'bar-info',
				success: 	'bar-success'
			}
		},
		bootstrap3: {
			container: 	'progress',
			inner: 		'progress-bar',
			states: {
				danger:		'progress-bar-danger',
				warning: 	'progress-bar-warning',
				info: 		'progress-bar-info',
				success: 	'progress-bar-success'
			}
		},
		bootstrap4: {
			container: 	'progress',
			inner: 		'progress-bar',
			states: {
				danger:		'bg-danger',
				warning: 	'bg-warning',
				info: 		'bg-info',
				success: 	'bg-success'
			}
		},
		bootstrap5: {
			container: 	'progress',
			inner: 		'progress-bar',
			states: {
				danger:		'bg-danger',
				warning: 	'bg-warning',
				info: 		'bg-info',
				success: 	'bg-success'
			}
		},
		uikit: {
			container: 	'uk-progress',
			inner: 		'uk-progress-bar',
			states: {
				danger:		'uk-progress-danger',
				warning: 	'uk-progress-warning',
				info: 		'',
				success: 	'uk-progress-success'
			}
		},

		foundation: {
			container: 	'progress',
			inner: 		'progress-meter',
			states: {
				danger:		'alert',
				warning: 	'warning',
				info: 		'secondary',
				success: 	'success'
			}
		}
	},
	addForm: function(props) {
		RSFormProPasswords.Forms.push(props);
	},
	init: function(){
		for (var i = 0; i < RSFormProPasswords.Forms.length; i++) {
			var layout = RSFormProPasswords.Forms[i].layout;
			var formId = RSFormProPasswords.Forms[i].formId;
			var field  = RSFormProPasswords.Forms[i].field;

			if (typeof RSFormProPasswords.progressBars[layout] === 'undefined') {
				layout = 'responsive';
			}

			var element = RSFormPro.getFieldsByName(formId, field)[0];

			RSFormProPasswords.checkStrength(element, element.value, layout);

			RSFormProUtils.addEvent(element, 'keyup', function() {
				RSFormProPasswords.checkStrength(this, this.value, layout);
			});
		}
	},
	checkAndBuildLayout: function(input, layout) {
		var parent = input.parentNode;
		var currentProgressBar = parent.getElementsByClassName(RSFormProPasswords.progressBars[layout].container);
		if (currentProgressBar.length) {
			return currentProgressBar[0];
		} else {
			var inputWidth = input.offsetWidth;
			var progressBar = document.createElement("DIV");

			// create the progress bar's container
			progressBar.style.width = inputWidth+'px';
			progressBar.style.marginTop = '5px';
			progressBar.style.marginBottom = '0px';
			RSFormProUtils.addClass(progressBar, RSFormProPasswords.progressBars[layout].container);

			// create the inner progress bar
			var progressBarInner = document.createElement("DIV");
			RSFormProUtils.addClass(progressBarInner, RSFormProPasswords.progressBars[layout].inner);
			progressBar.appendChild(progressBarInner);

			// insert the progress bar into the DOM
			input.parentNode.insertBefore(progressBar, input.nextSibling);

			return progressBar;
		}
	},
	checkStrength: function (input, password, layout) {
		var minLength       = parseInt(RSFormProPasswords.userOptions.minLength);
		var minIntegers     = parseInt(RSFormProPasswords.userOptions.minIntegers);
		var minSymbols      = parseInt(RSFormProPasswords.userOptions.minSymbols);
		var minUppercase    = parseInt(RSFormProPasswords.userOptions.minUppercase);
		var minLowercase    = parseInt(RSFormProPasswords.userOptions.minLowercase);

		var barLayout = RSFormProPasswords.checkAndBuildLayout(input, layout);

		var container  = barLayout;
		var bar        = barLayout.getElementsByClassName(RSFormProPasswords.progressBars[layout].inner)[0];
		var match      = [];

		var states = [
			RSFormProPasswords.progressBars[layout].states.danger,
			RSFormProPasswords.progressBars[layout].states.warning,
			RSFormProPasswords.progressBars[layout].states.info,
			RSFormProPasswords.progressBars[layout].states.success
		];

		var classHandler = (['uikit', 'foundation'].indexOf(layout) > -1 ? container : bar);

		function removeClasses() {
			for (var i = 0; i < states.length; i++) {
				RSFormProUtils.removeClass(classHandler, states[i]);
			}
		}

		function setProgress(amount) {
			bar.style.width = amount + '%';
		}

		function addClass(name) {
			removeClasses();
			RSFormProUtils.addClass(classHandler, name);
		}

		var progress = 0;

		// Minimum length
		if (minLength > 0 && password.length >= minLength) {
			progress++;
		}

		// Minimum integers
		match = password.match(/[0-9]/g) || [];
		if (minIntegers > 0 && match.length >= minIntegers) {
			progress++;
		}

		// Minimum symbols
		match = password.match(/[^a-zA-Z0-9_]/g) || [];
		if (minSymbols > 0 && match.length >= minSymbols) {
			progress++;
		}

		// Minimum uppercase
		match = password.match(/[A-Z]/g) || [];
		if (minUppercase > 0 && match.length >= minUppercase) {
			progress++;
		}

		// Minimum lowercase
		match = password.match(/[a-z]/g) || [];
		if (minLowercase > 0 && match.length >= minLowercase) {
			progress++;
		}
		
		var count = 0;
		if (minLength > 0) {
			count++
		}
		if (minIntegers > 0) {
			count++;
		}
		if (minSymbols > 0) {
			count++;
		}
		if (minUppercase > 0) {
			count++;
		}
		if (minLowercase > 0) {
			count++;
		}

		states.reverse();
		
		setProgress(progress * (100 / count));
		
		var class_to_add = states[count - progress];
		if (typeof class_to_add == 'undefined') {
			class_to_add = states[3];
		}

		addClass(class_to_add);
	}
};