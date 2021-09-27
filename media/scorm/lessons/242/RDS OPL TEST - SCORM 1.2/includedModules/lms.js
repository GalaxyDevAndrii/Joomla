(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else {
        root.lms = factory();
    }
}(this, function () {
    var self = {
        initialized: false,
        apiWrapper: getApiWrapper(),
        startTime: null
    };

    var errorHandlers = [console.log.bind(console)]; // Array which contains all error handlers (use addErrorHandler method to add new handlers)

    var lmsReporting = {
        initialize: function () {
            self.initialized = self.apiWrapper.doLMSInitialize() == 'true';
            setLessonStatus('incomplete');
            if (self.initialized) {
                self.startTime = new Date();
                if (window.addEventListener) {
                    window.addEventListener('unload', endSession);
                    window.addEventListener('beforeunload', showWarningMessage);
                }
            }
        },

        progressProvider: {
            getProgress: getProgress,
            saveProgress: saveProgress,
            removeProgress: removeProgress
        },

        userInfoProvider: {
            getUsername: function () {
                return self.apiWrapper.doLMSGetValue('cmi.core.student_name');
            },
            getAccountId: function () {
                return self.apiWrapper.doLMSGetValue('cmi.core.student_id');
            },
            getAccountHomePage: function () {
                return window.location.origin || window.location.protocol + '//' + window.location.host;
            }
        },

        courseFinished: sendCourseResults,
        courseFinalized: finalizeCourseSession,

        addErrorHandler: addErrorHandler
    };

    return lmsReporting;

    function addErrorHandler(handler) {
        errorHandlers.push(handler);
    }

    function handleError(msg) {
        for (var index = 0; index < errorHandlers.length; index++) {
            errorHandlers[index](msg);
        }
    }

    function endSession() {
        var endTime = new Date();
        var duration = endTime.getTime() - self.startTime.getTime();
        self.apiWrapper.doLMSSetValue('cmi.core.session_time', convertTimeSpanToLmsTimeString(duration));
        self.apiWrapper.doLMSCommit();

        self.apiWrapper.doLMSFinish();
    }

    function showWarningMessage(event) {
        event.preventDefault();
        event.returnValue = '';
        return '';
    }

    function convertTimeSpanToLmsTimeString(milliseconds) {
        return new Date(null, null, null, null, null, null, milliseconds).toString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, '$1');
    }

    function getProgress() {
        var progress = {};
        try {
            progress = JSON.parse(self.apiWrapper.doLMSGetValue('cmi.suspend_data'));
        } catch (e) {
            handleError('Unable to restore progress');
        }
        return progress;
    }

    function saveProgress(progress) {
        var progressResult = self.apiWrapper.doLMSSetValue('cmi.suspend_data', JSON.stringify(progress)) == 'true';
        var statusResult = self.apiWrapper.doLMSSetValue('cmi.core.lesson_status', 'incomplete') == 'true';
        var result = progressResult && statusResult;
        if (result) {
            //console.log('Progress was saved');
            //console.dir(progress);

            self.apiWrapper.doLMSSetValue('cmi.core.exit', 'suspend');
            self.apiWrapper.doLMSCommit();
        }
        return result;
    }

    function removeProgress() {
        var result = self.apiWrapper.doLMSSetValue('cmi.suspend_data', '') == 'true';
        if (result) {
            self.apiWrapper.doLMSSetValue('cmi.core.exit', '');
            self.apiWrapper.doLMSCommit();
        }

        return result;
    }

    function getValue(value) {
        return (typeof value === 'function') ? value() : value;
    }

    function sendCourseResults(course) {
        self.apiWrapper.doLMSSetValue('cmi.core.score.min', '0');
        self.apiWrapper.doLMSSetValue('cmi.core.score.max', '100');
        self.apiWrapper.doLMSSetValue('cmi.core.score.raw', getValue(course.result) * 100);
        self.apiWrapper.doLMSSetValue('cmi.core.lesson_status', getValue(course.isCompleted) ? 'passed' : 'failed');
        self.apiWrapper.doLMSCommit();
    }

    function finalizeCourseSession() {
        if (window.removeEventListener) {
            window.removeEventListener('unload', endSession);
            window.removeEventListener('beforeunload', showWarningMessage);
        }

        endSession();
    }

    function setLessonStatus(status) {
        self.apiWrapper.doLMSSetValue('cmi.core.lesson_status', status);
    }

    function getApiWrapper() {
        // Define exception/error codes
        var _NoError = { "code": '0', "string": 'No Error', "diagnostic": 'No Error' };
        var _GeneralException = { "code": '101', "string": 'General Exception', "diagnostic": 'General Exception' };

        var initialized = false;

        // local variable definitions
        var apiHandle = null;

        return {
            doLMSInitialize: doLMSInitialize,
            doLMSFinish: doLMSFinish,
            doLMSGetValue: doLMSGetValue,
            doLMSSetValue: doLMSSetValue,
            doLMSCommit: doLMSCommit
        };

        /*******************************************************************************
        **
        ** Function: doLMSInitialize()
        ** Inputs:  None
        ** Return:  true if the initialization was successful, or
        **          false if the initialization failed.
        **
        ** Description:
        ** Initialize communication with LMS by calling the LMSInitialize
        ** function which will be implemented by the LMS.
        **
        *******************************************************************************/
        function doLMSInitialize() {
            if (initialized) return 'true';

            var api = getAPIHandle();
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nLMSInitialize was not successful.');
                return 'false';
            }

            var result = api.LMSInitialize('');
            if (result.toString() != 'true') {
                var err = ErrorHandler();
                handleError('LMSInitialize failed with error code: ' + err.code);
            }
            else {
                initialized = true;
            }

            return result.toString();
        }

        /*******************************************************************************
        **
        ** Function doLMSFinish()
        ** Inputs:  None
        ** Return:  true if successful
        **          false if failed.
        **
        ** Description:
        ** Close communication with LMS by calling the LMSFinish
        ** function which will be implemented by the LMS
        **
        *******************************************************************************/
        function doLMSFinish() {
            if (!initialized) return 'true';

            var api = getAPIHandle();
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nLMSFinish was not successful.');
                return 'false';
            }
            else {
                // call the LMSFinish function that should be implemented by the API
                var result = api.LMSFinish('');
                if (result.toString() != 'true') {
                    var err = ErrorHandler();
                    handleError('LMSFinish failed with error code: ' + err.code);
                }
            }

            initialized = false;

            return result.toString();
        }

        /*******************************************************************************
        **
        ** Function doLMSGetValue(name)
        ** Inputs:  name - string representing the cmi data model defined category or
        **             element (e.g. cmi.core.student_id)
        ** Return:  The value presently assigned by the LMS to the cmi data model
        **       element defined by the element or category identified by the name
        **       input value.
        **
        ** Description:
        ** Wraps the call to the LMS LMSGetValue method
        **
        *******************************************************************************/
        function doLMSGetValue(name) {
            var api = getAPIHandle();
            var result = '';
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nLMSGetValue was not successful.');
            }
            else if (!initialized && !doLMSInitialize()) {
                var err = ErrorHandler(); // get why doLMSInitialize() returned false
                handleError('LMSGetValue failed - Could not initialize communication with the LMS - error code: ' + err.code);
            }
            else {
                result = api.LMSGetValue(name);

                var error = ErrorHandler();
                if (error.code != _NoError.code) {
                    // an error was encountered so display the error description
                    handleError('LMSGetValue(' + name + ') failed. \n' + error.code + ': ' + error.string);
                    result = '';
                }
            }
            return result.toString();
        }

        /*******************************************************************************
        **
        ** Function doLMSSetValue(name, value)
        ** Inputs:  name -string representing the data model defined category or element
        **          value -the value that the named element or category will be assigned
        ** Return:  true if successful
        **          false if failed.
        **
        ** Description:
        ** Wraps the call to the LMS LMSSetValue function
        **
        *******************************************************************************/
        function doLMSSetValue(name, value) {
            var api = getAPIHandle();
            var result = 'false';
            var err;
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nLMSSetValue was not successful.');
            }
            else if (!initialized && !doLMSInitialize()) {
                err = ErrorHandler(); // get why doLMSInitialize() returned false
                handleError('LMSSetValue failed - Could not initialize communication with the LMS - error code: ' + err.code);
            }
            else {
                result = api.LMSSetValue(name, value);
                if (result.toString() != 'true') {
                    err = ErrorHandler();
                    handleError('LMSSetValue(' + name + ', ' + value + ') failed. \n' + err.code + ': ' + err.string);
                }
            }

            return result.toString();
        }

        /*******************************************************************************
        **
        ** Function doLMSCommit()
        ** Inputs:  None
        ** Return:  true if successful
        **          false if failed.
        **
        ** Description:
        ** Commits the data to the LMS.
        **
        *******************************************************************************/
        function doLMSCommit() {
            var api = getAPIHandle();
            var result = 'false';
            var err;
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nLMSCommit was not successful.');
            }
            else if (!initialized && !doLMSInitialize()) {
                err = ErrorHandler(); // get why doLMSInitialize() returned false
                handleError('LMSCommit failed - Could not initialize communication with the LMS - error code: ' + err.code);
            }
            else {
                result = api.LMSCommit('');
                if (result != 'true') {
                    err = ErrorHandler();
                    handleError('LMSCommit failed - error code: ' + err.code);
                }
            }

            return result.toString();
        }


        /*******************************************************************************
        **
        ** Function ErrorHandler()
        ** Inputs:  None
        ** Return:  The current error
        **
        ** Description:
        ** Determines if an error was encountered by the previous API call
        ** and if so, returns the error.
        **
        ** Usage:
        ** var last_error = ErrorHandler();
        ** if (last_error.code != _NoError.code)
        ** {
        **    handleError("Encountered an error. Code: " + last_error.code +
        **                                "\nMessage: " + last_error.string +
        **                                "\nDiagnostics: " + last_error.diagnostic);
        ** }
        *******************************************************************************/
        function ErrorHandler() {
            var error = { "code": _NoError.code, "string": _NoError.string, "diagnostic": _NoError.diagnostic };
            var api = getAPIHandle();
            if (api == null) {
                handleError('Unable to locate the LMS\'s API Implementation.\nCannot determine LMS error code.');
                error.code = _GeneralException.code;
                error.string = _GeneralException.string;
                error.diagnostic = 'Unable to locate the LMS\'s API Implementation. Cannot determine LMS error code.';
                return error;
            }

            // check for errors caused by or from the LMS
            error.code = api.LMSGetLastError().toString();
            if (error.code != _NoError.code) {
                // an error was encountered so display the error description
                error.string = api.LMSGetErrorString(error.code);
                error.diagnostic = api.LMSGetDiagnostic('');
            }

            return error;
        }

        /******************************************************************************
        **
        ** Function getAPIHandle()
        ** Inputs:  None
        ** Return:  value contained by APIHandle
        **
        ** Description:
        ** Returns the handle to API object if it was previously set,
        ** otherwise it returns null
        **
        *******************************************************************************/
        function getAPIHandle() {
            if (apiHandle == null) {
                apiHandle = getAPI();
            }

            return apiHandle;
        }


        /*******************************************************************************
        **
        ** Function findAPI(win)
        ** Inputs:  win - a Window Object
        ** Return:  If an API object is found, it's returned, otherwise null is returned
        **
        ** Description:
        ** This function looks for an object named API in parent and opener windows
        **
        *******************************************************************************/
        function findAPI(win) {
            var findAPITries = 0;
            while ((win.API == null) && (win.parent != null) && (win.parent != win)) {
                findAPITries++;
                // Note: 7 is an arbitrary number, but should be more than sufficient
                if (findAPITries > 7) {
                    handleError('Error finding API -- too deeply nested.');
                    return null;
                }

                win = win.parent;
            }
            return win.API;
        }

        /*******************************************************************************
        **
        ** Function getAPI()
        ** Inputs:  none
        ** Return:  If an API object is found, it's returned, otherwise null is returned
        **
        ** Description:
        ** This function looks for an object named API, first in the current window's
        ** frame hierarchy and then, if necessary, in the current window's opener window
        ** hierarchy (if there is an opener window).
        **
        *******************************************************************************/
        function getAPI() {
            var theAPI = findAPI(window);
            if ((theAPI == null) && (window.opener != null) && (typeof (window.opener) != 'undefined')) {
                theAPI = findAPI(window.opener);
            }

            if (theAPI == null) {
                handleError('Unable to find an API adapter');
            }

            return theAPI;
        }
    }
}));
