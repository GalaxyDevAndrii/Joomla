(function (angular, _, angularDragula) {
    'use strict';

    var app = angular.module('assessment', ['ngRoute', 'pascalprecht.translate', angularDragula(angular)]);

    app.config([
        '$routeProvider', function ($routeProvider) {
            $routeProvider
                .when('/', {
                    templateUrl: 'app/views/main.html',
                    controller: 'MainController',
                    controllerAs: 'assessment',
                    reloadOnSearch: false,
                    resolve: {
                        assessment: [
                            'dataContext', function (dataContext) {
                                return dataContext.getAssessment();
                            }
                        ]
                    }
                })
                .when('/summary', {
                    templateUrl: 'app/views/summary.html',
                    controller: 'SummaryController',
                    controllerAs: 'summary',
                    resolve: {
                        assessment: [
                            'dataContext', function (dataContext) {
                                return dataContext.getAssessment();
                            }
                        ]
                    }
                })
                 .when('/login', {
                     templateUrl: 'app/views/login.html',
                     controller: 'LoginController',
                     controllerAs: 'login',
                     resolve: {
                         assessment: ['dataContext', function (dataContext) {
                             return dataContext.getAssessment();
                         }
                         ]
                     }
                 })
                 .when('/noaccess', {
                     templateUrl: 'app/views/noaccess.html',
                     controller: 'NoAccessController',
                     controllerAs: 'noAccess',
                     resolve: {
                         assessment: [
                             'dataContext', function (dataContext) {
                                 return dataContext.getAssessment();
                             }
                         ]
                     }
                 })
                .when('/error/404', {
                    templateUrl: 'app/views/notFoundError.html',
                    controller: 'NotFoundErrorController',
                    controllerAs: 'notFoundError',
                    resolve: {
                        assessment: [
                            'dataContext', function (dataContext) {
                                return dataContext.getAssessment();
                            }
                        ]
                    }
                })
                .otherwise({
                    redirectTo: '/error/404'
                });
        }
    ]).run([
        '$rootScope', '$location', 'settings', 'htmlTemplatesCache', '$templateCache', 'attemptsLimiter', 'accessLimiter', 'userContext', 'urlHelper',
        function ($rootScope, $location, settings, htmlTemplatesCache, $templateCache, attemptsLimiter, accessLimiter, userContext, urlHelper) {
            $rootScope.$on('$routeChangeStart', function (event, next) {
                var user = userContext.getCurrentUser();
                if (isXapiDisabled()) {
                    settings.xApi.enabled = false;
                }

                var xApiEnabled = settings.xApi.enabled;
                if (!$rootScope.skipLoginGuard && !user && (xApiEnabled || accessLimiter.accessLimitationEnabled())) {
                    forbidRedirects('/login');
                    return;
                }

                if (!accessLimiter.userHasAccess()) {
                    forbidRedirects('/noaccess');
                    return;
                }

                if (!attemptsLimiter.hasAvailableAttempt()) {
                    forbidRedirects('/summary');
                    return;
                }

                function isXapiDisabled() {
                    var xapi = urlHelper.getQueryStringValue('xapi');
                    return !settings.xApi.required && !_.isNull(xapi) && !_.isUndefined(xapi) && xapi.toLowerCase() === 'false';
                }

                function forbidRedirects(urlHash) {
                    if (next.originalPath !== urlHash) {
                        $location.path(urlHash);
                    }
                }
            });

            $rootScope.$on('$viewContentLoaded', function () {
                $rootScope.isVisible = true;
            });

            _.each(htmlTemplatesCache, function (template) {
                $templateCache.put(template.key, template.value);
            });


        }
    ]);
})(window.angular, window._, window.angularDragula);



(function () {
    'use strict';

    angular.module('assessment')
           .factory('eventPublisher', eventPublisher);

    eventPublisher.$inject = ['$rootScope', '$q'];

    function eventPublisher($rootScope, $q) {
        var factory = {
            publishRootScopeEvent: publishRootScopeEvent
        };

        return factory;

        function publishRootScopeEvent(eventName, data, successCallback, failCallback) {
            var listeners = $rootScope.$$listeners[eventName];
            var promises = [];

            _.each(listeners, function (listener) {
                if (_.isFunction(listener)) {
                    executeListenerFunction(listener);
                }
            });

            $q.all(promises).then(function () {
                if (_.isFunction(successCallback)) {
                    successCallback();
                }
            }).catch(function (reason) {
                if (_.isFunction(failCallback)) {
                    failCallback(reason);
                }
            });

            function executeListenerFunction(func) {
                var listenerResult = func($rootScope, data);
                if (listenerResult && _.isFunction(listenerResult.then)) {
                    promises.push(listenerResult);
                }
            }
        }
    }
}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('Assessment', factory);

    factory.$inject = ['$rootScope', 'eventPublisher'];

    function factory($rootScope, eventPublisher) {
        return function Assessment(id, templateId, title, createdOn, sections, questions, hasIntroductionContent, passedAfterwords, failedAfterwords, introductions) {
            var that = this;

            that.id = id;
            that.templateId = templateId;
            that.title = title;
            that.createdOn = createdOn;
            that.sections = sections;
            that.questions = questions || [];
            that.hasIntroductionContent = hasIntroductionContent || false;
            that.introductions = introductions || [];
            that.passedAfterwords = passedAfterwords;
            that.failedAfterwords = failedAfterwords;
            that.isCompleted = false;
            that.isFinished = false;

            that.getResult = function () {
                if (that.questions.length === 0) {
                    return 0;
                }

                var questionsThatAffectTheProgress = 0;
                _.each(that.questions, function(question){
                    if(question.affectProgress){
                        questionsThatAffectTheProgress++;
                    }
                });

                if(questionsThatAffectTheProgress === 0){
                    return 0;
                }

                var correct = 0;
                that.questions.forEach(function (question) {
                    if(question.affectProgress){
                        correct += question.score;
                    }
                });
                return Math.floor(correct / questionsThatAffectTheProgress);
            };

            that.start = function () {
                eventPublisher.publishRootScopeEvent('course:started');
            };

            that.restart = function (successCallback, failCallback) {
                that.isFinished = true;
                eventPublisher.publishRootScopeEvent('course:finished', that, successCallback, failCallback);
            };

            that.finish = function (successCallback, failCallback) {
                that.isFinished = true;
                eventPublisher.publishRootScopeEvent('course:finished', that, function() {
                    eventPublisher.publishRootScopeEvent('course:finalized', that, successCallback);
                }, failCallback);
            };

            that.sendCourseResult = function (masteryScore) {
                that.isCompleted = that.getResult() >= masteryScore;
                eventPublisher.publishRootScopeEvent('course:results', that);
            };

            that.getStatus = function () {
                if (!that.isFinished) {
                    return 'inProgress';
                }

                return that.isCompleted ? 'completed' : 'failed';
            };
        };
    }

}());

(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('Question', factory);

    factory.$inject = ['$q', '$rootScope', 'ContentBlock', 'ExpandableBlockViewModel', 'resourceLoader'];

    function factory($q, $rootScope, ContentBlock, ExpandableBlockViewModel, resourceLoader) {
        return function Question(data, _protected) {
            var that = this;
            that.id = data.id;
            that.sectionId = data.sectionId;
            that.title = data.title;
            
            that.affectProgress = true;

            if (typeof data.isSurvey !== 'undefined') {
                that.isSurvey = data.isSurvey;
                that.affectProgress = !that.isSurvey;
            }

            that.learningContents = data.learningContents.map(function (contentBlock) {
                return mapContentBlock(contentBlock);
            });

            that.instructions = data.questionInstructions.map(function (contentBlock) {
                return mapContentBlock(contentBlock);
            });

            that.type = data.type;
            that.score = 0;

            that.answer = function () {
                _protected.answer.apply(this, arguments);

                $rootScope.$emit('question:answered', {
                    question: that,
                    answers: arguments[0]
                });
            };

            that.learningContentsExperienced = function (time) {
                $rootScope.$emit('learningContent:experienced', {
                    question: that,
                    time: time
                });
            };

            that.load = function () {
                return $q.when(null, function () {
                    return loadInstructions(that.instructions).then(function() {
                        that.instructions = mapInstructions(that.instructions);

                        if (!_.isNull(_protected) && _.isFunction(_protected.load)) {
                            return _protected.load.apply(that);
                        }
                    });
                });
            };

            function loadInstructions(items) {
                var promises = [];
                
                _.each(items, function (item) {
                    if (typeof item.content === typeof undefined) {
                        promises.push(resourceLoader.getLocalResource({ url: item.contentUrl, dataType: 'html' }).success(function (response) {
                            item.content = response;
                        }));
                    }

                    promises.push(loadInstructions(item.children));
                });

                return $q.all(promises);
            }

            function mapInstructions(items) {
                return _.map(items, function(item) {
                    if(item.children && item.children.length) {
                        return new ExpandableBlockViewModel(item);
                    }

                    return item;
                });
            }

            function mapContentBlock(item) {
                var contentUrl = 'content/' + that.sectionId + '/' + that.id + '/' + item.id + '.html';
                var children = _.map(item.children, function(childItem) {
                    return mapContentBlock(childItem);
                });

                return new ContentBlock(item.id, contentUrl, children);
            };
        };
    }

} ());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('SingleSelectText', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function SingleSelectText(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.options = data.answers;

            function answer(id) {
                that.score = 0;
                that.options.forEach(function (option) {
                    if (option.id === id && option.isCorrect) {
                        that.score = 100;
                    }
                });
            }
        };

    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('MultipleSelectText', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function MultipleSelectText(data) {
            var that = this,
                _protected = {
                    answer: answer
                };
            Question.call(that, data, _protected);

            that.options = data.answers;

            function answer(answers) {
                that.score = 100;
                that.options.forEach(function (option) {
                    if (_.contains(answers, option.id) !== option.isCorrect) {
                        that.score = 0;
                    }
                });
            }
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('SingleSelectImage', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function SingleSelectImage(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.correctAnswerId = data.correctAnswerId;

            that.options = data.answers;

            function answer(selectedOptionId) {
                that.score = selectedOptionId === that.correctAnswerId ? 100 : 0;
            }
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('DragAndDropText', factory);

    factory.$inject = ['Question'];

    function factory(Question) {

        return function DragAndDropText(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.background = data.background;
            that.dropspots = data.dropspots;

            function answer(spots) {
                var correct = 0;
                spots.forEach(function (spot) {
                    if (_.find(that.dropspots, function (dropspot) {
                        return dropspot.x === spot.x && dropspot.y === spot.y && dropspot.text === spot.text;
                    })) {
                        correct++;
                    }
                });

                that.score = (correct === that.dropspots.length) ? 100 : 0;
            }
        };

    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('TextMatching', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function TextMatching(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.answers = data.answers;

            function answer(pairs) {
                var correct = 0;

                pairs.forEach(function (pair) {
                    if (_.find(that.answers, function (item) {
                        return item.key === pair.key && item.value === pair.value;
                    })) {
                        correct++;
                    }
                });

                that.score = (correct === that.answers.length) ? 100 : 0;
            }
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('Statement', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function Statement(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.options = data.answers;

            function answer(statements) {
                var correct = 0;

                statements.forEach(function (statement) {
                    if (_.find(that.options, function (option) {
                        return option.text === statement.text && option.isCorrect === statement.state;
                    })) {
                        correct++;
                    }
                });

                that.score = (correct === that.options.length) ? 100 : 0;
            }
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('FillInTheBlanks', factory);

    factory.$inject = ['$q', 'Question', 'htmlContentLoader'];

    function factory($q, Question, htmlContentLoader) {
        return function FillInTheBlanks(data) {
            var that = this,
                _protected = {
                    answer: answer,
                    load: loadContent
                };

            Question.call(that, data, _protected);

            that.content = null;
            that.hasContent = data.hasContent;
            that.groups = data.answerGroups;

            function loadContent() {
                var that = this;
                return $q.when(null, function () {
                    if (that.hasContent) {
                        return htmlContentLoader.load('content/' + that.sectionId + '/' + that.id + '/content.html').success(function(content) {
                            that.content = content;
                        });
                    }
                });
            }
            
            function answer(answers) {
                var correct = 0;
                _.each(that.groups, function (group) {
                    if (_.find(group.answers, function (answer) {
                        return answer.isCorrect &&
                            (answer.matchCase ? answers[group.id] === answer.text : answers[group.id].toLowerCase() === answer.text.toLowerCase());
                    })) {
                        correct++;
                    }
                });

                that.score = correct === that.groups.length ? 100 : 0;
            }
        };
    }
}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('OpenQuestion', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function OpenQuestion(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            function answer(answers) {
                that.score = answers ? 100 : 0;
            }
        };

    }
}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('ScenarioQuestion', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function ScenarioQuestion(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.embedCode = data.embedCode;

            that.embedUrl = data.embedUrl;

            that.projectId = data.projectId;

            that.masteryScore = data.masteryScore;

            function answer(score) {
                that.score = score >= that.masteryScore ? 100 : 0;
            }
        };

    }
}());
(function (angular, _) {
    'use strict';

    angular
        .module('assessment')
        .factory('RankingText', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function RankingText(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.answers = _.shuffle(data.answers);
            that.correctOrderAnswers = data.answers;

            function answer(_answers) {
                that.score = 100;
                that.correctOrderAnswers.forEach(function (answer, index) {
                    if (answer.text !== _answers[index].text) {
                        that.score = 0;
                    }
                });
            }
        };

    }

}(window.angular, window._));
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('Hotspot', factory);

    factory.$inject = ['Question'];

    function factory(Question) {
        return function Hotspot(data) {
            var that = this,
                _protected = {
                    answer: answer
                };

            Question.call(that, data, _protected);

            that.background = data.background;

            that.spots = data.spots;

            that.isMultiple = data.isMultiple;

            function answer(marks) {
                var score = calculateScore(that.isMultiple, that.spots, marks);

                that.score = score;
            }

            function calculateScore(isMultiple, spots, placedMarks) {
                if (!_.isArray(spots) || spots.length === 0) {
                    return placedMarks.length ? 0 : 100;
                }

                var answerCorrect;
                if (!isMultiple) {
                    answerCorrect = _.some(spots, function (spot) {
                        return _.some(placedMarks, function (mark) {
                            return markIsInSpot(mark, spot);
                        });
                    });
                } else {
                    var spotsWithMarks = [];
                    var marksOnSpots = [];

                    _.each(placedMarks, function (mark) {
                        _.each(spots, function (spot) {
                            if (markIsInSpot(mark, spot)) {
                                spotsWithMarks.push(spot);
                                marksOnSpots.push(mark);
                            }
                        });
                    });

                    answerCorrect = _.uniq(spotsWithMarks).length === spots.length && _.uniq(marksOnSpots).length === placedMarks.length;
                }
                return answerCorrect ? 100 : 0;
            }

            function markIsInSpot(mark, spot) {
                var x = mark.x,
                    y = mark.y;

                var inside = false;
                for (var i = 0, j = spot.length - 1; i < spot.length; j = i++) {
                    var xi = spot[i].x,
                        yi = spot[i].y;
                    var xj = spot[j].x,
                        yj = spot[j].y;

                    var intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                    if (intersect) {
                        inside = !inside;
                    }
                }

                return inside;
            }
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('Section', factory);

    function factory() {
        return function Section(id, title, questions) {
            var that = this;

            that.id = id;
            that.title = title;
            that.questions = questions || [];

            that.getResult = function () {
                var questionsThatAffectTheProgress = 0;
                var result = _.reduce(that.questions, function (memo, question) {
                    if(!question.affectProgress){
                        return memo;
                    }
                    questionsThatAffectTheProgress++;
                    if (question.score == 100) {
                        return memo + question.score;
                    }
                    return memo;
                }, 0);
                if(questionsThatAffectTheProgress === 0){
                    return 100;
                }
                return questionsThatAffectTheProgress === 0 ? 0 : Math.floor(result / questionsThatAffectTheProgress);
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('ContentBlock', factory);

    function factory() {
        var ContentBlock = function(id, contentUrl, children) {
            this.id = id;
            this.contentUrl = contentUrl;
            this.children = children;

            this.content;
        };

        return ContentBlock;
    }
}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('DragAndDropTextViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function DragAndDropTextViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'dragAndDropText';
            };

            that.background = question.background;

            that.texts = question.dropspots.map(function (dropspot) {
                return {
                    text: dropspot.text
                };
            });
            that.texts.acceptValue = function (value) {
                that.texts.push(value);
            };
            that.texts.rejectValue = function (value) {
                var index = that.texts.indexOf(value);
                that.texts.splice(index, 1);

            };

            that.spots = question.dropspots.map(function (dropspot) {
                var spot = {
                    x: dropspot.x,
                    y: dropspot.y,
                    value: undefined,
                    acceptValue: function (value) {
                        spot.value = value;
                    },
                    rejectValue: function () {
                        spot.value = null;
                    }

                };

                return spot;
            });

            that.submitAnswer = function () {
                question.answer(_.map(that.spots, function (spot) {
                    return {
                        x: spot.x,
                        y: spot.y,
                        text: spot.value && spot.value.text
                    };
                }));
            };

        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('QuestionViewModel', factory);

    factory.$inject = ['HintViewModel'];

    function factory(HintViewModel) {
        return function QuestionViewModel(question) {
            var that = this;

            that.id = question.id;
            that.title = question.title;
            that.instructions = question.instructions;
            that.hint = new HintViewModel(question);

            that.isSurveyMode = question.hasOwnProperty('isSurvey') && question.isSurvey;
            
            that.getType = function () {
                throw 'Could not determine question type for question #' + that.id + ' (' + question.title + ')';
            };

            that.submit = function () {
                that.hint.deactivate();
                that.submitAnswer();
            };

            that.submitAnswer = function () {
                throw 'Question #' + that.id + ' could not be submitted';
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('SingleSelectImageViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function SingleSelectImageViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'singleSelectImage';
            };

            that.answers = question.options.map(function (option) {
                return {
                    id: option.id,
                    image: option.image,
                    checked: false
                };
            });

            that.checkAnswer = function (answer) {
                that.answers.forEach(function (item) {
                    item.checked = false;
                });
                answer.checked = true;
            };

            that.submitAnswer = function () {
                var item = _.find(that.answers, function (answer) {
                    return answer.checked;
                });
                question.answer(item ? item.id : null);
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('SingleSelectTextViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function SingleSelectTextViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'singleSelectText';
            };

            that.answers = question.options.map(function (option) {
                return {
                    id: option.id,
                    text: option.text,
                    checked: false
                };
            });

            that.checkAnswer = function (answer) {
                that.answers.forEach(function (item) {
                    item.checked = false;
                });
                answer.checked = true;
            };

            that.submitAnswer = function () {
                var item = _.find(that.answers, function (answer) {
                    return answer.checked;
                });
                question.answer(item ? item.id : null);
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('MultipleSelectTextViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function MultipleSelectTextViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'multipleSelectText';
            };

            that.answers = question.options.map(function (option) {
                return {
                    id: option.id,
                    text: option.text,
                    checked: false
                };
            });

            that.checkAnswer = function (answer) {
                answer.checked = !answer.checked;
            };

            that.submitAnswer = function () {
                question.answer(_.chain(that.answers)
                    .filter(function (answer) {
                        return answer.checked;
                    })
                    .map(function (answer) {
                        return answer.id;
                    }).value());
            };
        };
    }
}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('StatementViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function StatementViewModel(question) {
            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'statement';
            };

            that.statements = question.options.map(function (option) {
                return {
                    id: option.id,
                    text: option.text,
                    state: undefined
                };
            });

            that.setTrueState = function (statement) {
                statement.state = statement.state === true ? undefined : true;
            };

            that.setFalseState = function (statement) {
                statement.state = statement.state === false ? undefined : false;
            };

            that.submitAnswer = function () {
                question.answer(that.statements.map(function (statement) {
                    return {
                        id: statement.id,
                        text: statement.text,
                        state: statement.state
                    };
                }));
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('TextMatchingViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function TextMatchingViewModel(question) {
            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'textMatching';
            };


            that.sources = _.map(question.answers, function(answer) {
                    var source = {
                        id: answer.id,
                        key: answer.key,
                        value: null,

                        acceptValue: function(value) {
                            source.value = value;
                        },
                        rejectValue: function() {
                            source.value = null;
                        }
                    };

                    return source;
                });

            that.targets = _.chain(question.answers)
                .map(function (answer) {
                    var target = {
                        value: answer.value,
                        acceptValue: function (value) {
                            target.value = value;
                        },
                        rejectValue: function () {
                            target.value = null;
                        }
                    };
                    return target;
                })
                .shuffle()
                .value();

            that.submitAnswer = function () {
                question.answer(_.map(that.sources, function (source) {
                    return {
                        key: source.key,
                        value: source.value
                    };
                }));
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('FillInTheBlanksViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function FillInTheBlanksViewModel(question) {
            QuestionViewModel.call(this, question);

            var that = this;

            that.template = question.content;
            delete that.content;

            that.getType = function () {
                return 'fillInTheBlanks';
            };

            that.groups = question.groups.map(function (group) {
                return {
                    groupId: group.id,
                    answer: '',
                    answers: group.answers.map(function (answer) {
                        return {
                            text: answer.text
                        };
                    })
                };
            });

            that.submitAnswer = function () {
                question.answer(_.chain(that.groups)
                    .map(function (group) {
                        return {
                            groupId: group.groupId,
                            answer: group.answer
                        };
                    })
                    .reduce(function (obj, ctx) {
                        obj[ctx.groupId] = ctx.answer;
                        return obj;
                    }, {})
                    .value());
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('HotspotViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function HotspotViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'hotspot';
            };

            that.background = question.background;
            that.spots = question.spots;
            that.isMultiple = question.isMultiple;
            that.marks = [];

            that.addMark = function (mark) {
                if (!that.isMultiple) {
                    that.marks.splice(0, that.marks.length);
                }
                that.marks.push(mark);
            };

            that.removeMark = function (mark) {
                that.marks = _.without(that.marks, _.findWhere(that.marks, mark));
            };

            that.submitAnswer = function () {
                question.answer(that.marks);
            };

        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('OpenQuestionViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function OpenQuestionViewModel(question) {
            QuestionViewModel.call(this, question);

            var that = this;

            that.getType = function () {
                return 'openQuestion';
            };

            that.answeredText = '';

            that.submitAnswer = function () {
                question.answer(that.answeredText);
            };
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('ScenarioQuestionViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {
        return function ScenarioQuestionViewModel(question) {
            QuestionViewModel.call(this, question);

            var that = this,
                branchtrackInstance = Branchtrack.create(question.projectId);

            that.getType = function () {
                return 'scenarioQuestion';
            };

            that.embedCode = question.embedCode;

            that.submitAnswer = function () {
                question.answer(branchtrackInstance.score);
                branchtrackInstance.destroy();
            };
        };
    }

}());
(function (angular) {
    'use strict';

    angular
        .module('assessment')
        .factory('RankingTextViewModel', factory);

    factory.$inject = ['QuestionViewModel'];

    function factory(QuestionViewModel) {        
        return function RankingTextViewModel(question) {

            QuestionViewModel.call(this, question);

            var that = this;
            that.getType = function () {
                return 'rankingText';
            };

            that.answers = question.answers.map(function (answer) {
                return {
                    text: answer.text
                };
            });

            that.submitAnswer = function () {
                question.answer(that.answers);
            };
        };
    }

}(window.angular));
(function () {
    'use strict';

    angular
        .module('assessment')
        .factory('HintViewModel', factory);

    factory.$inject = ['$q', '$timeout', 'ExpandableBlockViewModel', 'resourceLoader'];

    function factory($q, $timeout, ExpandableBlockViewModel, resourceLoader) {
        return function HintViewModel(question) {
            var that = this;

            that.exists = question.learningContents && question.learningContents.length;
            that.learningContents = [];
            that.isDisplayed = false;
            that.scrollToQuestion = false;
            that.isLoaded = false;

            that.show = function () {
                that.isDisplayed = true;
                if (!that.isLoaded) {
                    getLearningContents(question.learningContents);
                } else {
                    that.hintStartTime = new Date();
                }
            };

            that.hide = function (scrollToQuestion) {
                that.scrollToQuestion = scrollToQuestion;
                that.isDisplayed = false;
                if (that.isLoaded) {
                    sendLearningContentsExperienced();
                }
            };

            that.deactivate = function () {
                if (that.isDisplayed && that.isLoaded) {
                    sendLearningContentsExperienced();
                }
            };

            function sendLearningContentsExperienced() {
                that.hintEndTime = new Date();
                question.learningContentsExperienced(that.hintEndTime - that.hintStartTime);
            }

            function getLearningContents(contentBlocks) {
                loadLearningContents(contentBlocks).then(function () {
                    that.learningContents = mapLearningContents(contentBlocks);

                    that.isLoaded = true;
                    that.hintStartTime = new Date();
                });
            }

            function loadLearningContents(items) {
                var promises = [];

                _.each(items, function (item) {
                    if (typeof item.content === typeof undefined) {
                        promises.push(resourceLoader.getLocalResource({ url: item.contentUrl, dataType: 'html' }).success(function (response) {
                            item.content = response;
                        }));
                    }

                    promises.push(loadLearningContents(item.children));
                });

                return $q.all(promises);
            }

            function mapLearningContents(items) {
                return _.map(items, function(item) {
                    if(item.children && item.children.length) {
                        return new ExpandableBlockViewModel(item);
                    }

                    return item;
                });
            }
        };

    }

}());
(function() {
  "use strict";

  angular.module("assessment").factory("ExpandableBlockViewModel", factory);

  function factory() {
    return ExpandableBlockViewModel;
  }

  function ExpandableBlockViewModel(contentBlock) {
    var that = this;

    that.content = contentBlock.content;
    that.children = contentBlock.children;
    that.isDisplayed = false;

    that.toggle = function() {
      that.isDisplayed = !that.isDisplayed;
    };
  }
})();

(function() {
    "use strict";
  
    angular.module("assessment").factory("SummaryQuestionListViewModel", factory);
  
    var overallAnsweredQuestionsStatusKey = '[overall answered questions status]';

    factory.$inject = ['$translate'];

    function factory($translate) {
      return function SummaryQuestionListViewModel(questionList, isExpanded, isPassed) {
        var that = this, 
          correctNumber = 0;
        
        that.questions = _.chain(questionList)
              .filter(function(question){
                  return question.affectProgress || question.isSurvey;
              }).map(function(question) {
                  var isCorrect = question.score === 100;
  
                  if(isCorrect) {
                      correctNumber++;
                  }
  
                  return {
                      title: question.title,
                      isCorrect: isCorrect,
                      isSurvey: question.isSurvey
                  };
              }).value();
  
        that.isPassed = isPassed;
        that.isExpanded = isExpanded;
  
        that.overallAnsweredQuestionsStatusText = $translate.instant(overallAnsweredQuestionsStatusKey)
          .replace('{correctQuestionsNumber}', correctNumber)
          .replace('{overallQuestionsNumber}', that.questions.length);
    
        that.toggle = function() {
          that.isExpanded = !that.isExpanded;
        };
      };
    }
  })();
  
(function() {
    "use strict";
  
    angular.module("assessment").factory("ResendResultsViewModel", factory);

    factory.$inject = ['$translate', 'eventPublisher'];

    var translate, eventPublisherInstance, skipSendinResultsText;
    var _skipSendingResultsKey = '[close without reporting results]',
        _endpointNameReplaceKey = '{endpointName}';

    function factory($translate, eventPublisher) {
      translate = $translate;
      eventPublisherInstance = eventPublisher;

      skipSendinResultsText = translate.instant(_skipSendingResultsKey);

      return ResendResultsViewModel;
    }

    function ResendResultsViewModel(data) {
      throwIfNotDefined(data.callbacks, 'callbacks');
      throwIfNotDefined(data.callbacks.next, 'callbacks.next');
      throwIfNotDefined(data.eventName, 'eventName');
      throwIfNotDefined(data.close, 'close');

      throwIfNotDefined(data.assessment, 'assessment');

      throwIfNotDefined(data.resultsSendErrorTitleKey, 'resultsSendErrorTitleKey');
      throwIfNotDefined(data.endpointNameKey, 'endpointNameKey');

      var endpointName = translate.instant(data.endpointNameKey);

      var viewModel = {
        next: data.callbacks.next,
        eventName: data.eventName,
        retryCount: data.retryCount || 10,
        resultsSendErrorTitleKey: data.resultsSendErrorTitleKey,
        skipSendinResultsText: skipSendinResultsText.replace(_endpointNameReplaceKey, endpointName),
        close: data.close,

        assessment: data.assessment,

        isCompositionComplete: false,
        isResendingResults: false,
        resendingFailed: false,
        retriesExceeded: false,

        resendResults: resendResults,
        skipResultsSending: skipResultsSending,
        hideDialog: hideDialog
      }

      return viewModel;

      function resendResults() {
        viewModel.isResendingResults = true;

        eventPublisherInstance.publishRootScopeEvent(viewModel.eventName, viewModel.assessment, onSuccess, onError);
      }

      function onSuccess() {
        hideDialog();
        viewModel.next();
      }

      function onError() {
        viewModel.retryCount ? viewModel.retryCount-- : viewModel.retriesExceeded = true;
                
        viewModel.isResendingResults = false;
        animateNotes();
      }

      function skipResultsSending() {
          hideDialog();
          viewModel.next();
      }

      function hideDialog() {
          viewModel.isResendingResults = false;
          viewModel.close();
      }

      function throwIfNotDefined(parameter, parameterName) {
          if(_.isNull(parameter) || _.isUndefined(parameter)) {
              throw 'Can\'t activate dialog. Parameter isn\'t defined. Parameter name: ' + parameterName;
          }
      }

      function animateNotes() {
          viewModel.resendingFailed = true;
          setTimeout(function() {
              viewModel.resendingFailed = false;
          }, 400);
      }
    }
  })();

(function () {
    'use strict';

    angular.module('assessment')
        .factory('contentViewModelMapper', factory);

    factory.$inject = ['ExpandableBlockViewModel'];

    function factory(ExpandableBlockViewModel) {
        return {
            map: map
        };

        function map(items) {
            return items.map(function(item) {
                if(item.children && item.children.length) {
                    return new ExpandableBlockViewModel(item);
                }

                return item;
            });
        }
    }
}());

(function () {
    'use strict';

    angular.module('assessment').factory('sectionsQueries', factory);

    factory.$inject = ['dataContext'];

    function factory(dataContext) {
        return {
            getSectionById: getSectionById
        };

        function getSectionById(sectionId) {
            var assessment = dataContext.getAssessment(),
                currentSection = null;

            currentSection = _.find(assessment.sections, function (section) {
                return section.id === sectionId;
            });

            return currentSection;
        }
    }
}());

(function () {
    'use strict';

    angular.module('assessment')
        .factory('dataContext', dataContext);

    dataContext.$inject = ['$rootScope', '$q', 'Assessment', 'Section', '$templateCache', 'questionsFactory', 'questionPool', 'questionDataProcessor', 'ContentBlock', 'resourceLoader'];// jshint ignore:line

    function dataContext($rootScope, $q, Assessment, Section, $templateCache, questionsFactory, questionPool, questionDataProcessor, ContentBlock, resourceLoader) { // jshint ignore:line

        var self = {
            isInited: false,
            assessment: null,

            id: null,
            templateId: null,
            title: null,
            hasIntroductionContent: false,
            passedAfterwords: [],
            failedAfterwords: [],
            sections: []
        };

        return {
            getAssessment: getAssessment
        };

        function init() {
            return resourceLoader.getLocalResource({ url: 'content/data.js', cache: false }).success(function (response) {
                resourceLoader.setCacheBuster(response.createdOn);
                var promises = [];
                self.id = response.id;
                self.templateId = response.templateId;
                self.title = response.title;
                self.createdOn = new Date(response.createdOn);
                self.hasIntroductionContent = response.hasIntroductionContent;
                self.introductions = response.introductions;

                if (response.hasIntroductionContent && response.introductions && response.introductions.length) {
                    response.introductions.forEach(function(introduction, index){
                        if (introduction.children.length) {
                            introduction.children.forEach(function(introChildren, childIndex){
                                promises.push(resourceLoader.getLocalResource({ url: 'content/introduction/' + introChildren.id + '.html'}).then(function (res) {
                                    self.introductions[index].children[childIndex].content = res.data;
                                }))
                            })
                        }
                        promises.push(resourceLoader.getLocalResource({ url: 'content/introduction/' + introduction.id + '.html' }).then(function (res) {
                            self.introductions[index].content = res.data;
                        }))
                    });
                }


                if (response.passedAfterwords && response.passedAfterwords.length) {
                    self.passedAfterwords = readContentBlocks(response.passedAfterwords);
                }

                if (response.failedAfterwords && response.failedAfterwords.length) {
                    self.failedAfterwords = readContentBlocks(response.failedAfterwords);
                }

                if (_.isArray(response.sections)) {
                    readSections(response.sections);
                }

                return $q.all(promises);
            });
        }

        function readSections(sections) {
            sections.forEach(function (section) {
                if (_.isArray(section.questions)) {
                    var sectionQuestions = _.chain(section.questions)
                        .map(function (questionData) {
                            return questionsFactory.createQuestion(section.id, questionDataProcessor.process(questionData));
                        })
                        .compact()
                        .value();

                    self.sections.push(new Section(section.id, section.title, sectionQuestions));
                }
            });
        }

        function readContentBlocks(feedbacks) {
            return feedbacks.map(mapContentBlock);
        }

        function mapContentBlock(item) {
            var contentUrl = 'content/afterword/' + item.id + '.html';
            var children = item.children.map(function(childItem) {
                return mapContentBlock(childItem);
            });

            return new ContentBlock(item.id, contentUrl, children);
        }

        function getAllQuestions() {
            return _.chain(self.sections)
                .pluck('questions')
                .flatten()
                .value();
        }

        function generateSections(questions) {
            var sectionsIds = _.chain(questions)
                .pluck('sectionId')
                .uniq()
                .value();

            return _.chain(self.sections)
                .filter(function (section) {
                    return _.contains(sectionsIds, section.id);
                })
                .map(function (section) {
                    return new Section(section.id, section.title, _.intersection(section.questions, questions));
                })
                .value();
        }

        function getAssessment() {
            if (!self.isInited) {
                self.isInited = true;
                return init().then(function () {
                    return getAssessment();
                });
            }

            if (!self.assessment || questionPool.isRefreshed()) {
                var questionsForCourse = questionPool.getQuestions(getAllQuestions()),
                    sectionsForCourse = generateSections(questionsForCourse);

                return $q.all(questionsForCourse.map(function (question) {
                    return question.load();
                })).then(function () {
                    return self.assessment = new Assessment(self.id, self.templateId, self.title, self.createdOn, sectionsForCourse, questionsForCourse, self.hasIntroductionContent, self.passedAfterwords, self.failedAfterwords, self.introductions);
                });
            }

            return self.assessment;
        }
    }

}());

(function () {
    'use strict';

    angular.module('assessment')
           .factory('imagePreviewFactory',  imagePreviewFactory);

    imagePreviewFactory.$inject = ['$rootScope'];

    function imagePreviewFactory($rootScope) {
        var factory = {
            showEventName: 'imagePreview.show',
            showImage: showImage
        };

        return factory;

        function showImage(imageUrl) {
            $rootScope.$emit(factory.showEventName, imageUrl);
        }
    }

}());
(function () {
	"use strict";

    angular.module('assessment')
		.factory('questionsFactory', questionsFactory);

	questionsFactory.$inject = [
		'SingleSelectText',
		'MultipleSelectText',
		'TextMatching',
		'DragAndDropText',
		'Statement',
		'SingleSelectImage',
		'FillInTheBlanks',
		'Hotspot',
		'OpenQuestion',
        'ScenarioQuestion',
        'RankingText'
	];

	function questionsFactory (
		SingleSelectText,
		MultipleSelectText,
		TextMatching,
		DragAndDropText,
		Statement,
		SingleSelectImage,
		FillInTheBlanks,
		Hotspot,
		OpenQuestion,
        ScenarioQuestion,
        RankingText
	) {
			
		var models = {
			singleSelectText: 	function (data) { return new SingleSelectText(data); },
			statement: 			function (data) { return new Statement(data); },
			singleSelectImage: 	function (data) { return new SingleSelectImage(data); },
			dragAndDropText: 	function (data) { return new DragAndDropText(data); },
			textMatching: 		function (data) { return new TextMatching(data); },
			fillInTheBlank:		function (data) { return new FillInTheBlanks(data); },
			hotspot: 			function (data) { return new Hotspot(data); },
			multipleSelect: 	function (data) { return new MultipleSelectText(data); },
			openQuestion: 		function (data) { return new OpenQuestion(data); },
			scenario:           function (data) { return new ScenarioQuestion(data) },
			rankingText:        function (data) { return new RankingText(data) }
		};
		
		return {
			createQuestion: function (sectionId, questionData) {
				questionData.sectionId = sectionId;
				
				if (!_.isFunction(models[questionData.type])) {
					return null;
                } else {
					return models[questionData.type](questionData);
				}
			}
		};
	}

})();
(function () {
    "use strict";

    angular.module('assessment')
		.factory('questionDataProcessor', questionDataProcessor);
    
    questionDataProcessor.$inject = ['settings'];

    function questionDataProcessor(settings) {

        var processors = {
            singleSelectText: function (data) { return randomize(data, 'answers'); },
            statement: function (data) { return randomize(data, 'answers'); },
            singleSelectImage: function (data) { return randomize(data, 'answers'); },
            dragAndDropText: function (data) { return randomize(data, 'dropspots'); },
            textMatching: function (data) { return randomize(data, 'answers'); },
            multipleSelect: function (data) { return randomize(data, 'answers'); }
        };

        function randomize(data, property) {
            if (settings.answers.randomize) {
                data[property] = _.shuffle(data[property]);
            }

            return data;
        }

        return {
            process: function (questionData) {
                if(!_.isFunction(processors[questionData.type]))
                    return questionData;

                return processors[questionData.type](questionData);
            }
        };
    }

})();
(function () {
	"use strict";
	
    angular.module('assessment')
		.factory('viewmodelsFactory', viewmodelsFactory);
		
	viewmodelsFactory.$inject = [
        'SingleSelectTextViewModel', 
        'StatementViewModel', 
		'SingleSelectImageViewModel', 
		'DragAndDropTextViewModel',
		'TextMatchingViewModel', 
		'FillInTheBlanksViewModel', 
		'HotspotViewModel', 
		'MultipleSelectTextViewModel', 
		'OpenQuestionViewModel',
        'ScenarioQuestionViewModel',
        'RankingTextViewModel'
	];
		
	function viewmodelsFactory (
		SingleSelectTextViewModel, 
        StatementViewModel, 
		SingleSelectImageViewModel, 
		DragAndDropTextViewModel,
		TextMatchingViewModel, 
		FillInTheBlanksViewModel, 
		HotspotViewModel, 
		MultipleSelectTextViewModel, 
		OpenQuestionViewModel,
        ScenarioQuestionViewModel,
        RankingTextViewModel
	) {
	
		var viewmodels = {
			singleSelectText: 	SingleSelectTextViewModel,
			statement: 			StatementViewModel,
			singleSelectImage: 	SingleSelectImageViewModel,
			dragAndDropText: 	DragAndDropTextViewModel,
			textMatching: 		TextMatchingViewModel,
			fillInTheBlank:		FillInTheBlanksViewModel,
			hotspot: 			HotspotViewModel,
			multipleSelect: 	MultipleSelectTextViewModel,
			openQuestion:       OpenQuestionViewModel,
			scenario:           ScenarioQuestionViewModel,
            rankingText:        RankingTextViewModel
		};
		
		return {
		    createQuestionViewmodel: function (question) {
				if (!_.isFunction(viewmodels[question.type])) {
					throw 'Unknown question type';
				}
				return new viewmodels[question.type](question);
			}
		};
	}
	
})();
(function () {

    angular.module('assessment')
        .filter('leadingZeros', pad);

    function pad() {
        return function (number, length) {
            var str = number + '';
            while (str.length < length) {
                str = '0' + str;
            }
            return str;
        };
    }

}());
(function () {

    angular.module('assessment')
        .filter('time', timeFilter);

    function timeFilter() {
        return function (secondsValue, type) {
            switch (type) {
                case 'h':
                    return Math.floor(secondsValue / 3600);
                case 'm':
                    return Math.floor(secondsValue % 3600 / 60);
                case 's':
                    return Math.floor(secondsValue % 3600 % 60);
            }
        };
    }

})();
(function () {
    "use strict";
    angular.module('assessment')
		.service('accessLimiter', accessLimiter);

    accessLimiter.$inject = ['$injector', 'userContext'];

    function accessLimiter($injector, userContext) {
        var accessLimitation = { enabled: false },
            isInitialized = false;

        return {
            accessLimitationEnabled: accessLimitationEnabled,
            userHasAccess: userHasAccess,
            initialize: initialize
        };

        function accessLimitationEnabled() {
            if (!isInitialized) {
                initialize();
                isInitialized = true;
            }

            return accessLimitation.enabled;
        }

        function userHasAccess() {
            if (!accessLimitationEnabled())
                return true;
                
            var user = userContext.getCurrentUser();
            if (!user)
                return false;

            return _.some(accessLimitation.allowedUsers, function (item) {
                return _.isString(item.email) && (item.email.trim().toLowerCase() === user.email.trim().toLowerCase());
            });
        }

        function initialize() {
            var publishSettings = $injector.has('publishSettings') ? $injector.get('publishSettings') : null;
            if (!publishSettings || !publishSettings.accessLimitation || hasLms(publishSettings))
                return;

            accessLimitation = publishSettings.accessLimitation;
        }

        function hasLms(publishSettings) {
            var hasLms = false;
            if (publishSettings.modules) {
                hasLms = _.some(publishSettings.modules, function (module) {
                    return module.name === 'lms';
                });
            }

            return hasLms;
        }
    }

})();
(function () {
    "use strict";

    angular.module('assessment')
		.service('attemptsLimiter', attemptsLimiter);

    attemptsLimiter.$inject = ['$rootScope', 'settings'];

    function attemptsLimiter($rootScope, settings) {
        var self = {
            attemptCount: 0,
            limit: settings.attempt.hasLimit ? settings.attempt.limit : Infinity,
        };

        if (settings.attempt.hasLimit) {
            $rootScope.$on('course:started', function () {
                self.attemptCount++;
            });
        }

        return {
            limit: self.limit,
            hasLimit: settings.attempt.hasLimit,
            hasAvailableAttempt: hasAvailableAttempt,
            getAvailableAttemptCount: getAvailableAttemptCount
        };

        function hasAvailableAttempt() {
            return getAvailableAttemptCount() > 0;
        }

        function getAvailableAttemptCount() {
            return self.limit - self.attemptCount;
        }
    }

})();
(function () {
    "use strict";

    angular.module('assessment')
        .service('timer', timer);

    timer.$inject = ['$timeout'];

    function timer($timeout) {
        var onTickSubscribers = [],
            onStoppedSubscribers = [],
            isTimerOn = false,
            diff,
            duration,
            startTime;

        return {
            setTime: setTime,
            onStopped: onStopped,
            onTick: onTick,
            start: start,
            dispose: dispose
        };

        function setTime(timeInSeconds) {
            duration = timeInSeconds;
        }

        function onStopped(callback) {
            onStoppedSubscribers.push(callback);
        }

        function onTick(callback) {
            onTickSubscribers.push(callback);
        }

        function start() {
            isTimerOn = true;
            startTime = Date.now();
            tick();
        }
        function tick() {
            $timeout(function () {
              if (!isTimerOn) {
                return;
              }

              diff = duration - (((Date.now() - startTime) / 1000) | 0);

              if (diff > 0) {
                  setTimeout(tick, 1000)
                  fireTickEvent();
              } else {
                  diff = 0;
                  fireStoppedEvent();
                  dispose();
              }
            });
          }

        function fireTickEvent() {
            onTickSubscribers.forEach(function (callback) {
                callback(diff);
            });
        }

        function fireStoppedEvent() {
            onStoppedSubscribers.forEach(function (callback) {
                callback();
            });
        }

        function dispose() {
            duration = 0;
            isTimerOn = false;
            onTickSubscribers = [];
            onStoppedSubscribers = [];
        }
    }

})();
(function () {
	"use strict";
	
    angular.module('assessment')
		.service('htmlContentLoader', htmlContentLoader);
	
	htmlContentLoader.$inject = ['$q', 'resourceLoader'];
	
	function htmlContentLoader($q, resourceLoader) {
		return {
			load: load,
			loadCollection: loadCollection
		};

		function load(url) {
			return resourceLoader.getLocalResource({ url: url, dataType: 'html' });
		}

		function loadCollection(items) {
			var promises = [];
			
			items.forEach(function (item) {
				if (typeof item.content === typeof undefined) {
					promises.push(load(item.contentUrl).success(function (response) {
						item.content = response;
					}));
				}

				promises.push(loadCollection(item.children));
			});

			return $q.all(promises);
		}
	}
	
})();
(function () {
	"use strict";

    angular.module('assessment')
		.service('questionPool', questionPool);

	questionPool.$inject = ['settings'];

	function questionPool(settings) {
		var self = {
			isRefreshed: false
		};

		return {
			getQuestions: getQuestions,
			isRefreshed: isRefreshed,
			refresh: refresh
		};

		function getQuestions(allQuestions) {
			self.isRefreshed = false;

			var questionPool = allQuestions;
			if (settings.questionPool.randomizeOrder) {
				questionPool = _.shuffle(questionPool);
			}
			if (settings.questionPool.mode === 'subset') {
				questionPool = _.first(questionPool, settings.questionPool.subsetSize);
			}

			return questionPool;
		}

		function isRefreshed() {
			return self.isRefreshed;
		}

		function refresh() {
			if (settings.questionPool.randomizePerAttempt && !settings.showGivenAnswers) {
				self.isRefreshed = true;
			}
		}
	}

})();
(function () {
    "use strict";
    angular.module('assessment')
		.service('userContext', userContext);

    userContext.$inject = ['user'];

    function userContext(user) {
        var currentUser = user || {},
            isInitialized = false;

        function getCurrentUser(){
            return currentUser.email && currentUser.username ? currentUser : null;
        }

        function getUsername() {
            var user = getCurrentUser();

            return user && user.username ? user.username : null;
        }

        function set(email, username){
            currentUser.email = email;
            currentUser.username = username;
        }

        function clear() {
            currentUser.email = null;
            currentUser.username = null;
        }

        return {
            getCurrentUser: getCurrentUser,
            getUsername: getUsername,
            set: set,
            clear: clear
        };
    }

})();
(function () {
    "use strict";
    angular.module('assessment')
		.service('documentBlockHelper', documentBlockHelper);

    documentBlockHelper.$inject = ['$translate'];

    var constants = {
      types: {
          pdf: 'pdf',
          word: 'word',
          exel: 'exel',
          powerpoint: 'powerpoint',
          zip: 'zip'
      },
      downloadLocalizationKey: '[download]',
      containerSelector: '.document-container',
      sizeAttrName: 'data-document-size',
      typeAttrName: 'data-document-type',
      downloadBtnSelector: '.download-document-btn',
      documentTitleWrapperSelector: '.document-title-wrapper'
    };

    function documentBlockHelper($translate) {
        return {
            getDocumentBlockContent: function(html) {
                var $output = $('<output>').html(html),
                    $container = $output.find(constants.containerSelector);

                return $translate(constants.downloadLocalizationKey).then(function (translation) {
                    var downloadText = translation;
                    
                    var documentData = {
                        type: $container.attr(constants.typeAttrName),
                        size: +$container.attr(constants.sizeAttrName)
                    };

                    var documentSizeString = getSize(documentData.size);
                    var downloadBtnText = downloadText + ' (' + documentSizeString + ')';

                    if(documentData.type === constants.types.zip) {
                        $container.addClass(documentData.type);
                    }
                    
                    $output.find(constants.downloadBtnSelector)
                        .text(downloadBtnText);
                    
                    var iconClass = documentData.type === constants.types.zip ? 'icon zip' : 'icon file';
                    var $typeIcon = $('<div class="icon-container">' +
                        '<span class="document-type-text">' + documentData.type + '</span>' +
                        '</div>')
                        .addClass(iconClass);
                    var $typeIconWrapper = $('<div class="document-icon"></div>')
                        .append($typeIcon);

                    var $documentInfo = $(constants.documentTitleWrapperSelector, $container)
                        .prepend($typeIconWrapper);

                    var content = $output.children()[0];
                    return content;
                });
            }
        };
    }


    function getSize(size) {
        var sizeStr = '';
        if (!size || size < 1024) {
            return '0 Kb';
        }
        sizeStr = (size / (1024 * 1024)).toFixed(2);

        return sizeStr + ' MB';
    }
})();
(function () {
    'use strict';

    angular.module('assessment')
           .provider('settings', settingsProvider);

    function settingsProvider() {
        var cachedSettings = {};

        return {
            setSettings: function (settings) {
                if (!_.isObject(settings)) {
                    throw 'Settings is empty!';
                }

                cachedSettings = settings;
            },
            $get: function () {
                return cachedSettings;
            }
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment')
           .provider('htmlTemplatesCache', htmlTemplatesCache);

    function htmlTemplatesCache() {
        var templates = [];
        return {
            set: function (value) {
                if (!_.isArray(value) || value.length === 0) {
                    return;
                }

                templates = value;
            },
            $get: function () {
                return templates;
            }
        };
    }
}());
(function() {
    'use strict';

    angular.module('assessment')
           .provider('user', userProvider);

    function userProvider() {
        var user = null;
        return {
            set: function (value) {
                if (!_.isObject(value) || !(value.username || value.email)) {
                    return;
                }

                user = value;
            },
            use: function (userInfoProvider) {
                if(!userInfoProvider) {
                    return;
                }
                var accountId = userInfoProvider.getAccountId(),
                    accountHomePage = userInfoProvider.getAccountHomePage(),
                    username = userInfoProvider.getUsername();
                if(!accountId || !accountHomePage || !username) {
                    return;
                }
                user = {
                    email: accountId,
                    username: username,
                    account: {
                        homePage: accountHomePage,
                        name: accountId
                    }
                };
            },
            $get: function () {
                return user;
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('tooltip', tooltip);

    function tooltip() {
        return {
            transclude: true,
            restrict: 'E',
            templateUrl: 'app/views/widgets/tooltip.html'
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('radio', radio);

    function radio() {
        return {
            scope: {
                checked: '=',
                title: '@'
            },
            replace: true,
            restrict: 'E',
            template: '<label class="radio" ng-class="{ checked: checked }">{{title}}</label>'
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('checkbox', checkbox);

    function checkbox() {
        return {
            scope: {
                checked: '=',
                title: '@'
            },
            replace: true,
            restrict: 'E',
            template: '<label class="checkbox" ng-class="{ checked: checked }">{{title}}</label>'
        };
    }

}());
(function () {
    'use strict';

    angular.module('assessment')
        .directive('progressControl', progressControl);

    progressControl.$inject = ['$translate', '$window'];

    var translate,
        hintKey = '[tracking and tracing mastery score hint]';

    function progressControl($translate, $window) {
        translate = $translate;
        return {
            restrict: 'E',
            template: '<div class="progress-control-wrapper">\
                <canvas class="progress-control-circle"></canvas>\
                <div class="progress-control-info">\
                    <span class="progress-control-info-score"></span>\
                </div>\
                <span class="progress-control-mastery-score"></span>\
            </div>',
            scope: {
                progress: '=',
                masteryScore: '='
            },
            link: function ($scope, $element) {
                $scope.$watch(['progress', 'masteryScore'], onUpdate);                   

                angular.element($window).on('resize', onUpdate);
                
                $scope.$on('$destroy', function() {
                    angular.element($window).off('resize', onUpdate);
                });

                function onUpdate() {
                    createProgressControl($scope, $element);
                }
            }
        };

        function createProgressControl(scope, $element) {
            removeFontPreloadElements();

            var 
                $wrapper = $element.children('.progress-control-wrapper'),
                $canvas = $wrapper.children('canvas');
                
            $wrapper.addClass( scope.progress >= scope.masteryScore ? 'success' : 'fail' );

            var 
                width = $canvas.width() || 120,
                height = $canvas.height() || 120;

            $canvas.attr('width', width);
            $canvas.attr('height', height);
            $canvas.attr('data-score', scope.progress);
            $canvas.attr('data-masteryScore', scope.masteryScore);

            var drawingSettings = {
                width: width,
                height: height,
                circle: {
                    position: {
                        X: width / 2,
                        Y: height / 2
                    },
                    width: parseInt($canvas.css('column-width')) || 2,
                    color: $canvas.css('border-top-color') || $canvas.css('border-color') || ( (scope.progress >= scope.masteryScore) ? '#4caf50' : '#f16162' ),
                    shadowColor: $canvas.css('color') || 'rgba(66,81,95,.7)',
                    radius: height / 2
                }
            };

            drawingSettings.circle.radius -= drawingSettings.circle.width;
                    
            var context = $canvas[0].getContext('2d');

            context.clearRect(0, 0, width, height);

            buildProgressControl(scope, context, drawingSettings, $element);
        }

        function removeFontPreloadElements() {
            $('.fontPreload').remove();
        }

        function buildProgressControl(scope, context, drawingSettings, $element) {
            var progressAngle = 2 * Math.PI * (scope.progress / 100) - 0.5 * Math.PI;

            //  drawing circle
            drawCircle(context, drawingSettings.circle.position.X, drawingSettings.circle.position.Y, drawingSettings.circle.radius, drawingSettings.circle.color, drawingSettings.circle.width, progressAngle, false);

            //  drawing circle background
            if(scope.progress) {
                if(scope.progress !== 100) {
                    drawCircle(context, drawingSettings.circle.position.X, drawingSettings.circle.position.Y, drawingSettings.circle.radius, drawingSettings.circle.shadowColor, drawingSettings.circle.width, progressAngle, true);
                }
            } else {
                drawCircle(context, drawingSettings.circle.position.X, drawingSettings.circle.position.Y, drawingSettings.circle.radius, drawingSettings.circle.shadowColor, drawingSettings.circle.width);
            }

            var $wrapper = $element.children('.progress-control-wrapper'),
                $infoContainer = $wrapper.children('.progress-control-info'),
                $masteryScoreContainer = $wrapper.children('.progress-control-mastery-score'),
                $infoScore = $infoContainer.children('.progress-control-info-score'),
                $masteryScoreWrapper = $('<span class="score-wrapper">');

            $masteryScoreWrapper.text(scope.masteryScore);
            $masteryScoreContainer.html($masteryScoreWrapper);
            $infoScore.text(scope.progress);

            var masteryScoreAngle = 2 * Math.PI * (scope.masteryScore / 100) - 0.5 * Math.PI,
                masteryScoreX = drawingSettings.circle.position.X + (Math.cos(masteryScoreAngle) * (drawingSettings.circle.radius)),
                masteryScoreY = drawingSettings.circle.position.Y + (Math.sin(masteryScoreAngle) * (drawingSettings.circle.radius));

            $masteryScoreContainer.css({ top: masteryScoreY - $masteryScoreContainer.height() / 2, left: masteryScoreX - $masteryScoreContainer.width() / 2 });

            translate(hintKey).then(function (translation) {
                addTooltip($masteryScoreContainer, translation, 'mastery-scroll-hint');
            });
        }

        function drawCircle(context, circleX, circleY, circleRadius, color, lineWidth, angle, isToRight) {
            if (angle === undefined) {
                angle = 1.5 * Math.PI;
            }

            context.beginPath();
            context.arc(circleX, circleY, circleRadius, -0.5 * Math.PI, angle, isToRight);
            context.lineWidth = lineWidth;
            context.strokeStyle = color;
            context.stroke();
            context.closePath();
        }

        function addTooltip($element, text, className) {
            var $tooltip = $('<span class="tooltip-container">'),
                $title = $('<span>'),
                elementTop = $element.offset().top - $element.parent().offset().top - $element.outerHeight(),
                elementLeft = $element.offset().left - $element.parent().offset().left;

            $title.addClass('title');
            $title.text(text || $element.text());

            $tooltip.html($title); 
            $tooltip.addClass(className);       
            $tooltip.css({marginTop: elementTop, marginLeft: elementLeft });
            
            $element.after($tooltip);

            return $tooltip;
        }
    }
}());
(function () {
    angular.module('assessment')
    .directive('imageLoader', imageLoader);
    function imageLoader() {
        return {
            restrict: 'E',
            scope: {
                width: '=',
                height: '=',
                url: '=',
                scaleBySmallerSide: '='
            },
            templateUrl: 'app/views/widgets/imageLoader.html',
            link: link
        };
        function link(scope, element) {
            var $element = $(element);
            var $imageLoaderIcon = $('.image-loader-icon', $element);
            var image = new Image();
            image.className = 'image';
            image.style.display = 'none';
            image.style.width = 'auto';
            image.style.height = 'auto';
            if (!scope.scaleBySmallerSide) {
                image.style.maxWidth = scope.width + 'px';
                image.style.maxHeight = scope.height + 'px';
            }
            $element.append(image);
            var $image = $(image);
            scope.$watch('url', function (url) {
                $image.hide();
                $element.addClass('loading');
                $imageLoaderIcon.show();
                if (url) {
                    $element.addClass('loading');
                    var width = scope.width;
                    var height = scope.height;
                    var resizedImageUrl = '';
                    if (scope.scaleBySmallerSide) {
                        resizedImageUrl = url + '?height=' + height + '&width=' + width + '&scaleBySmallerSide=true';
                    } else {
                        var maxSize = width > height ? width : height; // grab image with bigger size to avoid reloading after screen rotation
                        resizedImageUrl = url + '?height=' + maxSize + '&width=' + maxSize;
                    }
                    image.onload = function () {
                        $imageLoaderIcon.hide();
                        $element.removeClass('loading');
                        $image.fadeIn();
                    };
                    image.src = resizedImageUrl;
                }
            });
        }
    }
}());
(function () {

    angular.module('assessment')
        .directive('imageFullscreenPreview', imageFullscreenPreview);

    function imageFullscreenPreview() {
        return {
            restrict: 'A',
            transclude: true,
            template: '',
            link: link
        };

        function link(scope, element) {
            var $element = $(element);
            var size = getPreviewImageSize();
            scope.preview.width = size.width;
            scope.preview.height = size.height;

            var resizeHandler = $(window).on('resize', function () {
                updatePreviewImageSize($element);
            });

            var orientationChangeHandler = $(window).on('orientationchange', function () {
                updatePreviewImageSize($element);
            });

            scope.$watch('preview.visible', function() {
                if(scope.preview.visible) {
                    $('body').css('overflow', 'hidden');
                } else {                    
                    $('body').css('overflow', 'auto');
                }
            });

            scope.$on('$destroy', unbindEvents);

            function unbindEvents() {
                $(window).unbind('resize', resizeHandler);
                $(window).unbind('orientationchange', orientationChangeHandler);
            }

            function updatePreviewImageSize($element) {
                var size = getPreviewImageSize();
                scope.preview.width = size.width;
                scope.preview.height = size.height;
                var browserWidth = size.width;
                var browserHeight = size.height;

                $('img', $element).css('maxWidth', browserWidth + 'px').css('maxHeight', browserHeight + 'px');
            }

            function getPreviewImageSize() {
                return {
                    width: Math.max(document.documentElement.clientWidth, window.innerWidth || 0) - 92,
                    height: Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 92
                };
            }
        }
    }
}());
(function () {

    angular.module('assessment')
        .directive('clickableArea', clickableArea);

    function clickableArea() {
        return {
            scope: {
                clickHandler: '='
            },
            restrict: 'A',
            link: link
        };

        function link(scope, element) {
            var offset, x, y;

            $(element).on('click', handler);

            function handler(e) {
                offset = $(element).offset();
                x = e.pageX - offset.left;
                y = e.pageY - offset.top;

                // workaround for specific version of Chrome with next bug:
                // https://code.google.com/p/chromium/issues/detail?id=423802
                if (isChromeWithPageCoordsBug()) {
                    x -= window.scrollX;
                    y -= window.scrollY;
                }

                if (typeof (scope.clickHandler) === 'function') {
                    scope.clickHandler({
                        x: x,
                        y: y
                    });
                    scope.$apply();
                }
            }

            function isChromeWithPageCoordsBug() {
                var ua = navigator.userAgent.toLowerCase();
                if (ua.match(/(chrome)\/?\s*([\d\.]+)/i)) {
                    return window.navigator.appVersion.match(/Chrome\/(.*?) /)[1] === '38.0.2125.102';
                }
                return false;
            }

        }
    }
}());
(function () {

    angular.module('assessment')
      .directive('statementItem', statementItem);

    function statementItem() {
        return {
            restrict: 'A',
            scope: {
                question: '=',
                statement: '='
            },
            templateUrl: 'app/views/statementItem.html'
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('textMatching', textMatching);

    function textMatching() {
        return {
            link: function ($scope, element) {
                var clone;

                $scope.$watch(function () {
                    return element.children().length;
                }, function () {
                    $scope.$evalAsync(function () {
                        clone = element
                            .clone()
                            .css({
                                left: '-9999px',
                                top: '-9999px',
                                position: 'absolute',
                                visibility: 'hidden',
                                width: '100%'
                            })
                            .insertAfter(element);

                        handler();
                    });
                });

                var handler = function () {

                    if (element && clone) {
                        var maxHeight = 0;
                        clone.find('.text-matching-table').each(function () {
                            if ($(this).outerHeight() > maxHeight) {
                                maxHeight = $(this).outerHeight();
                            }
                        });
                        element.find('.text-matching-table').each(function () {
                            $(this).height(maxHeight);
                        });
                    }

                };

                $(window).on('resize', _.debounce(handler));

            }
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('singleSelectImage', singleSelectImage);

    function singleSelectImage() {
        return {
            controller: ['imagePreviewFactory', singleSelectImageController],
            controllerAs: 'singleSelectImage'
        };
    }

    function singleSelectImageController(imagePreviewFactory) {
        var that = this;

        that.openPreviewImage = openPreviewImage;

        function openPreviewImage(imageUrl) {
            imagePreviewFactory.showImage(imageUrl);
        }
    }

}());
(function (angular) {

    angular.module('assessment')
        .directive('rankingText', rankingText);

    rankingText.$inject = ['dragulaService'];

    function rankingText(dragulaService) {
        return {
            link: function (scope, element) {
                dragulaService.options(scope, scope.question.id, {
                    mirrorContainer: element[0]
                });
            }
        };
    }

} (window.angular));
(function () {

    angular.module('assessment')
        .directive('assessmentScrollControl', assessmentScrollControl);

    function isMobileDevice() {
        var ua = navigator.userAgent.toLowerCase();
        return ua.indexOf('ipod') !== -1 || ua.indexOf('iphone') !== -1 || ua.indexOf('ipad') !== -1 || ua.indexOf('android') !== -1;
    }

    assessmentScrollControl.$inject = ['$routeParams', '$location', '$timeout', '$window'];

    function assessmentScrollControl($routeParams, $location, $timeout, $window) {
        return {
            restrict: 'A',
            link: function ($scope, $element) {    
                if (isMobileDevice()) {
                    subscribeToMobileEvents($scope, $element);
                } else if ($scope.assessment.hasIntroductionContent) {
                    subscribeToEvents($scope, $element, $timeout);
                }

                if (!$scope.assessment.hasIntroductionContent) {
                    broadcastAssessmentStartedEvent($scope);
                }

                if ($routeParams.tryAgain) {
                    $location.search('tryAgain', null);

                    if ($scope.scrollToQuestions) {
                        $timeout($scope.scrollToQuestions, 1000);
                    }
                }
            }
        };
    }

    function scrollTo(scrollTop) {
        $('html, body').animate({ scrollTop: scrollTop }, 1000);
    }

    function broadcastAssessmentStartedEvent($scope) {
        if (!$scope.assessmentStarted) {
            $scope.assessmentStarted = true;
            $scope.$emit('$assessmentStarted');
        }
    }

    function subscribeToMobileEvents($scope, $container) {
        var $window = $(window),
            $header = $container.children('header'),
            $questions = $container.children('questions');

        $scope.scrollToQuestions = function () {
            scrollTo($questions.offset().top - $header.height());
        };

        var
            //Events handlers
            windowScrollHandler = function () {
                var questionsReached = $window.scrollTop() >= $questions.offset().top - $window.height();

                if (questionsReached) {
                    broadcastAssessmentStartedEvent($scope);
                    $container.addClass('questions-reached');
                }
            },

            previousWindowSize = {},
            windowResizeHandler = function () {
                var windowWidth = $window.width(),
                    windowHeight = $window.height() + 250; //250px - reserve for Chrome window height resize

                //Check if mobile device orientation changed
                if (windowWidth !== previousWindowSize.width && windowHeight !== previousWindowSize.height) {
                    $('.main-background', $container).height(windowHeight);
                }

                previousWindowSize.width = windowWidth;
                previousWindowSize.height = windowHeight;
            };

        $window
            .bind('scroll', windowScrollHandler)
            .bind('resize', windowResizeHandler)
            .ready(windowResizeHandler);

        $scope.$on('$destroy', function () {
            $window
                .unbind('scroll', windowScrollHandler)
                .unbind('resize', windowResizeHandler);
        });
    }

    function subscribeToEvents($scope, $container, $timeout) {
        var $window = $(window),
            $introduction = $container.children('introduction'),
            $header = $container.children('header'),
            $questions = $container.children('questions'),
            $introductionContent = $introduction.find('[content]');

        $scope.scrollToQuestions = function () {
            scrollTo($questions.offset().top);
        };

        $questions.css('top', $window.height());

        //Events handlers
        var
            windowScrollHandler = function () {
                var scrollableHeight = $questions.offset().top - $introduction.height(),
                    windowScrollTop = $window.scrollTop(),
                    questionsReached = windowScrollTop >= scrollableHeight,
                    introGone = windowScrollTop >= ($questions.offset().top - $header.height());

                //Fix for browser initial scrolling
                if (scrollableHeight < 0) {
                    return;
                }

                if (questionsReached && !introGone) {
                    broadcastAssessmentStartedEvent($scope);

                    $introduction
                        .css('top', scrollableHeight)
                        .css('position', 'absolute')
                        .css('z-index', 0);
                } else {
                    $introduction
                        .css('top', 0)
                        .css('position', 'fixed');

                    if(introGone) {
                        $introduction.css('z-index', -1);
                    }
                }

                $introductionContent.scrollTop(windowScrollTop);

                //Header logo appearance
                $container
                    .toggleClass('questions-reached', questionsReached)
                    .toggleClass('intro-gone', introGone);
            },
            windowResizeHandler = function () {
                $introduction.height($window.height());
                $('.main-background', $header).height($window.height());
            },
            introContentScrollHandler = function () {
                var topPositionClass = 'at-top-position',
                    bottomPositionClass = 'at-bottom-position';

                var scrollTop = Math.round($introductionContent.scrollTop()),
                    isTopPosition = scrollTop === 0,
                    isBottomPosition = scrollTop >= ($introductionContent[0].scrollHeight - $introductionContent.outerHeight() - 1);

                $introduction
                    .toggleClass(topPositionClass, isTopPosition)
                    .toggleClass(bottomPositionClass, isBottomPosition);
            },
            introHeightUpdatedHandler = function () {
                var introductionContentOuterHeight = $introductionContent.outerHeight(),
                    introductionContentScrollHeight = $introductionContent[0].scrollHeight;

                if (introductionContentScrollHeight > introductionContentOuterHeight) {
                    $questions.css('top', introductionContentScrollHeight - introductionContentOuterHeight + $introduction.height() + 500); // 500px - scroll pause between intro and questions
                    $introductionContent.bind('scroll', introContentScrollHandler);
                } else {
                    $questions.css('top', $introduction.height());
                }

                introContentScrollHandler();
            };

        $scope.contentLoaded = function () {
            $timeout(introHeightUpdatedHandler, 500);
        };

        $window
            .bind('resize', windowResizeHandler)
            .bind('resize', introHeightUpdatedHandler)
            .bind('scroll resize', windowScrollHandler)
            .ready(windowResizeHandler);

        $scope.$on('$destroy', function () {
            $window
                .unbind('resize', windowResizeHandler)
                .unbind('resize', introHeightUpdatedHandler)
                .unbind('scroll resize', windowScrollHandler);
        });
    }

})();
(function () {

    angular.module('assessment')
        .directive('summaryScrollControl', summaryScrollControl);

    function summaryScrollControl() {
        return {
            restrict: 'A',
            link: subscribeToEvents
        };
    }

    function subscribeToEvents($scope, $container) {
        var $window = $(window),
            $progressWrapper = $container.find('.summary-header-progress-wrapper'),
            progressWrapperDefaultToTop;

        var handlers = {
            windowScroll: function() {
                var progressWrapperPassed = $window.scrollTop() >= progressWrapperDefaultToTop,
                    hasPassedClass = $container.hasClass('progress-wrapper-passed');

                if (progressWrapperPassed && !hasPassedClass) {
                    $container.addClass('progress-wrapper-passed');
                } else if (!progressWrapperPassed && hasPassedClass) {
                    $container.removeClass('progress-wrapper-passed');
                }
            },
            progressWrapperToTopUpdated: function() {
                progressWrapperDefaultToTop = calculateProgressWrapperDefaultToTop($progressWrapper);
                changeTopForContentWrapper($container, progressWrapperDefaultToTop);
            }
        }

        $scope.$watch($progressWrapper.prop('clientHeight'), handlers.progressWrapperToTopUpdated);

        $window.bind('scroll', handlers.windowScroll)
            .bind('resize', handlers.progressWrapperToTopUpdated);

        $scope.scrollToTop = function () {
            $('html, body').animate({ scrollTop: 0 }, 1000);
        };
        $scope.$on('$destroy', function () {
            $window.unbind('scroll', handlers.windowScroll)
                .unbind('resize', handlers.progressWrapperToTopUpdated);

        });
    }

    function calculateProgressWrapperDefaultToTop($progressWrapper) {
        return $progressWrapper.offset().top + $progressWrapper.prop('clientHeight');
    }

    function changeTopForContentWrapper ($container, progressWrapperDefaultToTop) {
        var $summaryContentWrapper = $container.find('.summary-content-wrapper'),
            $summaryHeader = $container.find('.summary-header');

            if($(document).width() > 640) {
                $summaryContentWrapper.css({ paddingTop: '' });
            } else {
                $summaryContentWrapper.css({ paddingTop: progressWrapperDefaultToTop - $summaryHeader.prop('clientHeight') });
            }
    }

})();
(function () {

    angular.module('assessment')
        .directive('adjustDropspotWidth', adjustDropspotWidth);

    function adjustDropspotWidth() {
        
        return {
            restrict: 'A',
            link: function ($scope, element) {
                var $element = $(element);
                var $question = $(element).closest('.question');
                addDragHandler($element, $question);
                
                $element.on('dragstop', function () {
                    addDragHandler($element, $question);
                    $('.dropspot.ui-droppable', $question).css('min-width', '');
                });
            }
        };

        function addDragHandler($element, $question) {
            $element.one('drag', function () {
                $('.dropspot.ui-droppable.active', $question).css('min-width', $element.css('width'));
            });
        }
    }
}());
(function () {

    angular.module('assessment')
        .directive('draggable', draggable);

    function draggable() {
        return {
            restrict: 'A',
            scope: {
                value: '=',
                scope: '='
            },
            link: function ($scope, element) {
                $(element).css('touch-action','none');
				$(element).draggable({
                    containment: 'document',
                    appendTo: $(element).closest('section'),
                    helper: function () {
                        return $(element)
                            .clone()
                            .addClass('handle')
                            .css({
                                width: $(this).outerWidth(),
                                height: $(this).outerHeight()
                            });
                    },
                    scope: $scope.scope || 'default',                    
                    revert: true,
                    revertDuration: 0,
                    zIndex: 10000,
                    refreshPositions: true,

                    start: function () {
                        $(element).css('visibility', 'hidden');
                    },
                    stop: function () {
                        $(element).css('visibility', 'visible');
                    }
                });
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('droppable', droppable);

    function droppable() {
        return {
            restrict: 'A',
            scope: {
                acceptValue: '=',
                rejectValue: '=',
                accept: '=',
                scope: '='
            },
            link: function ($scope, element) {
                $(element).droppable({
                    accept: function (arg) {
                        if ($(element).find(arg).length) {
                            return true;
                        }
                        if ($scope.accept) {
                            return $scope.accept > $(element).find('.ui-draggable').length;
                        }
                        return $(arg).hasClass('ui-draggable');
                    },
                    activeClass: 'active',
                    greedy: true,
                    hoverClass: 'hover',
                    scope: $scope.scope || 'default',
                    tolerance: 'intersect',
                    drop: function (event, ui) {
                        ui.draggable.trigger('dragstop');

                        var draggable = ui.draggable.isolateScope();
                        if (draggable) {

                            var previous = $(ui.draggable).closest('[droppable]').isolateScope();
                            if (previous === $scope) {
                                return;
                            }

                            if ($scope.acceptValue) {
                                $scope.acceptValue(draggable.value);
                                $scope.$apply();

                                if (previous && previous.rejectValue) {
                                    previous.rejectValue(draggable.value);
                                    previous.$apply();
                                }

                            }
                        }

                    }
                });
            }
        };
    }

}());
(function () {
    
    angular.module('assessment')
        .directive('background', radio);

    function radio() {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.css('background-position', '0 0').css('background-repeat', 'no-repeat');

                var src = attrs.background, image;
                if (src) {
                    image = new Image();
                    image.onload = function () {
                        element
                            .css('background-image', 'url(' + src + ')')
                            .css('height', image.height)
                            .css('width', image.width);
                    };
                    image.src = src;
                }
            }
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('blankInput', blank);

    function blank() {
        return {
            restrict: 'C',
            transclude: 'element',
            replace: true,
            scope: true,
            controller: ['$scope', '$element', function ($scope, $element) {
                var
                    attr = $element.attr('data-group-id'),
                    question = $scope.question,
                    group = _.find(question.groups, function (g) {
                        return g.groupId === attr;
                    });

                if (!group) {
                    throw 'Can\'t find answer group with id' + attr;
                }

                $scope.group = group;
            }],
            link: function ($scope, $element) {
                $element
                    .removeClass('blankInput')
                    .on('change', 'input', function () {
                        $scope.group.answer = $(this).val();
                        $scope.$apply();
                    });
                if ($scope.group.answer) {
                    $element.find('input').val($scope.group.answer);
                }

                $('.highlight', $element).on('click', function () {
                    $('input', $element).focus();
                });
            },
            template: '<div class="input-wrapper">' +
                        '<input type="text" />' +
                        '<div class="highlight"></div>' +
                      '</div>'
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('blankSelect', blankSelect);

    function blankSelect() {
        return {
            restrict: 'C',
            transclude: 'element',
            replace: true,
            scope: true,
            controller: ['$scope', '$element', function ($scope, $element) {
                var
                    attr = $element.attr('data-group-id'),
                    question = $scope.question,
                    group = _.find(question.groups, function (g) {
                        return g.groupId === attr;
                    });

                if (!group) {
                    throw 'Can\'t find answer group with id' + attr;
                }

                $scope.group = group;
            }],
            link: function ($scope, $element) {
                $element
                    .removeClass('blankSelect')
                    .on('click', function () {
                        show($element, $scope.group.answers, function (newValue) {
                            $scope.group.answer = newValue;
                            $scope.$apply();
                        });
                        //e.preventDefault();
                        //e.stopImmediatePropagation();
                    });
                if ($scope.group.answer) {
                    $element.find('.current').text($scope.group.answer);
                }
            },
            template: '<div class="select-wrapper">' +
                        '<div class="hiden">' +
                            '<div ng-repeat="(key, value) in group.answers">{{ value.text }}</div>' +
                        '</div>' +
                        '<div class="current"><span class="default">{{ "[fill in the blank choose answer]" | translate }}</span></div>' +
                        '<div class="highlight"></div>' +
                      '</div>'
        };
    }

    function show($element, options, callback) {

        if ($element.hasClass('active')) {
            return;
        }

        $element.addClass('active');

        var
            containerTop = ($element.offset().top + $element.outerHeight() + 1),
            container = $('<div />')
                .addClass('select-container')
                .css({
                    position: 'absolute',
                    left: ($element.offset().left - 37) + 'px',
                    top: (containerTop + 10) + 'px',
                    width: ($element.outerWidth() + 40) + 'px',
                    opacity: 0
                })
                .append($('<ul/>')
                    .addClass('unstyled')
                    .on('click', 'li', function () {
                        var text = $(this).text();
                        $element.find('.current').text(text);
                        if (callback) {
                            callback(text);
                        }
                    })
                    .append(_.chain(options)
                        .filter(function (option) {
                            return option.text !== $element.find('.current').text();
                        })
                        .map(function (option) {
                            return $('<li/>')
                                .text(option.text);
                        }).value())
                )
                .appendTo('.container')
                .animate({top: containerTop, opacity: 1}, 200);

        var handler = function () {
            container.animate({ opacity: 0 }, 200, function(){                
                container.remove();
                $element.removeClass('active');
                $('html').off('click', handler);
                $(window).off('resize', handler);
            });
        };

        setTimeout(function () {
            $('html').on('click', handler);
            $(window).on('resize', handler);
        }, 0);

    }

}());
(function () {

    angular.module('assessment')
        .directive('styledContent', styledContent);

    styledContent.$inject = ['urlHelper'];

    function styledContent(urlHelper) {
        
        return {
            restrict: 'A',
            link: function ($scope, element) {
                var $element = $(element),
                    imageWrapper = '<div class="image-wrapper"></div>',
                    tableWrapper = '<div class="table-wrapper"></div>';
                $element.addClass('styled-content');

                $scope.$on('$includeContentLoaded', function () {

                    $('img', $element).each(function (index, image) {
                        var $image = $(image),
                            $wrapper = $(imageWrapper).css('float', $image.css('float')),
                		    $parent = $image.parent();

                        if ($image.closest('.cropped-image').length > 0) {
                            return;
                        }
                        
                        if ($parent.prop('tagName') == "TD" && $parent[0].style.width == "") {
                            $wrapper.css('width', $image[0].style.width);
                            $wrapper.css('height', $image[0].style.height);
                        }

                        $image.height('auto');
                        $image.css('float', 'none');
                        $image.wrap($wrapper);
                    });

                    $('table', $element).each(function (index, table) {
                        var $table = $(table),
                            $wrapper = $(tableWrapper).css('text-align', $table.attr('align'));
                        $table.attr('align', 'center');
                        $table.wrap($wrapper);
                    });

                    $('.audio-editor iframe', $element).each(function (index, iframe) {
                        var $iframe = $(iframe);

                        var src = $iframe.attr('src');
                        $iframe.attr('src', urlHelper.addQueryStringParameter(src, 'style_variables', getStyles()));
                    });
 
                });
            }
        };
    }

    function getStyles() {
        return window.LessProcessor && window.LessProcessor.vars ? JSON.stringify({ '@main-color': window.LessProcessor.vars['@main-color'], '@content-body-color': window.LessProcessor.vars['@content-body-color'], '@text-color': window.LessProcessor.vars['@text-color'] }) : undefined;
    }
}());
(function () {

    angular.module('assessment')
        .directive('entireHeight', entireHeight);

    function entireHeight() {
        return {
            restrict: 'A',
            link: function ($scope, $element) {

                $(window).resize(changeHeight);
                _.defer(changeHeight);

                function changeHeight() {
                    $element.css('height', 'auto');

                    var screenHeight = $(window).height(),
                        elementScrollHeight = $element[0].scrollHeight;

                    $element.css('height', screenHeight > elementScrollHeight ? screenHeight : elementScrollHeight);
                }
            }
        };
    }

}());
(function () {

    angular.module('assessment')
        .directive('slideToggle', slideToggle);

    function slideToggle() {
        return {
            restrict: 'A',
            multiElement: true,
            link: function ($scope, element, attr) {
                var $element = $(element);
                
                $scope.$watch(attr.slideToggle, function (value) {
                    var scrollToSelector = $scope.$eval(attr.slideToggleScrollTo),
                        $scrollToElement = $(element).closest(scrollToSelector);

                    if (value) {
                        $element.css('height', '').hide().slideDown(function() {$element.css('overflow', '')});
                    } else {
                        $element.css('overflow', 'hidden').animate({height: 0});
                    }

                    if ($scrollToElement.length && !value) {
                        var headerHeight = $('.main-header').height() + 5;
                        $('html, body').animate({
                            scrollTop: $scrollToElement.offset().top - headerHeight
                        });
                    }
                });
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('showToggle', showToggle);

    function showToggle() {
        return {
            restrict: 'A',
            link: function ($scope, $element, attr) {
                $element.hide();
                $scope.$watch(attr.showToggle, function (value) {
                    $element.animate({
                        height: value ? 'show' : 'hide',
                        opacity: value ? 1 : 0
                    });
                });
            }
        };
    }
}());
(function () {
    'use strict';
    
    //Unlike ngBindHtml, this directive allows you to use nested bindings and emit event $includeContentLoaded.
    angular.module('assessment').directive('htmlCompile', directive);

    directive.$inject = ['$compile', 'documentBlockHelper'];

    function directive($compile, documentBlockHelper) {
        return {
            restrict: 'A',
            link: function ($scope, $element, attrs) {
                var unbind = $scope.$watch(attrs.htmlCompile, set);

                function set(newValue) {
                    if (!_.isUndefined(newValue)) {
                        var dataType = getLearningContentType(newValue);
                        switch (dataType) {
                            case 'hotspot': {
                                var hotspotOnImage = HotspotStorage.create($(newValue)[0]);

                                $element.addClass('hotspot-on-image-container');
                                $element.html(hotspotOnImage.element);

                                $element.on('$destroy', function () {
                                    HotspotStorage.remove(hotspotOnImage);
                                });
                                break;
                            }
                            case 'document': {
                                documentBlockHelper.getDocumentBlockContent(newValue).then(function(content) {
                                    $element.append(content);
                                });
                                break;
                            }
                            default: {
                                $element.html(newValue);
                            }
                        }
                        $compile($element.contents())($scope);
                        $scope.$emit('$includeContentLoaded');
                        unbind();
                    }
                }

                function getLearningContentType(data) {
                    var $output = $('<output>').html(data),
                        dataType = $('[data-type]', $output).attr('data-type');

                    return dataType;
                }
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('autosize', autosizeDirective);

    function autosizeDirective() {
        return {
            restrict: 'A',
            link: link
        };

        function link(scope, element) {
            autosize($(element), { setOverflowX: false, setOverflowY: false});
        }
    }
}());
(function () {

    angular.module('assessment')
        .directive('mainBackground', directive);

    directive.$inject = ['settings'];


    function directive(settings) {
        return {
            restrict: 'E',
            link: function ($scope, $element) {
                var background = settings.background;

                if (!_.isObject(background) || !_.isObject(background.header)) {
                    return;
                }

                var $image = $('.main-background-image', $element),
                    $overlay = $('.main-background-overlay', $element),
                    height = '100%';

                $image.css({
                    'height': height
                });

                if(_.isObject(background.header.image) && _.isString(background.header.image.url)) {

                    var src = background.header.image.url,
                        position = '0 0',
                        repeat = 'no-repeat',
                        size = 'auto';

                    if (background.header.image.option === 'repeat') {
                        repeat = 'repeat';
                    }

                    if (background.header.image.option === 'fullscreen') {
                        size = 'cover';
                        position = 'center';
                    }

                    $image.css({
                        'background-image': 'url(' + src + ')',
                        'background-position': position,
                        '-webkit-background-size': size,
                        'background-size': size,
                        'background-repeat': repeat
                    });
                }

                if (background.header.brightness) {
                    $overlay.css({
                        "background-color": background.header.brightness > 0 ? 'white' : 'black',
                        "opacity": background.header.brightness > 0 ? background.header.brightness : -background.header.brightness
                    });
                }

                if (background.header.color) {
                    $image.css({
                        'background-color': background.header.color
                    });
                }
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('secondaryBackground', directive);

    directive.$inject = ['settings'];


    function directive(settings) {
        return {
            restrict: 'E',
            link: function ($scope, $element) {
                var background = settings.background;

                if (!_.isObject(background) || !_.isObject(background.body) || !background.body.enabled) {
                    return;
                }

                var $image = $('.secondary-background-image', $element),
                    $overlay = $('.secondary-background-overlay', $element);

                if(background.body.texture) {
                    var src = background.body.texture,
                        position = '0 0',
                        repeat = 'repeat',
                        size = 'auto';
                        
                    $image.css({
                        'background-image': 'url(' + src + ')',
                        'background-position': position,
                        '-webkit-background-size': size,
                        'background-size': size,
                        'background-repeat': repeat
                    });
                } else if(background.body.color) {
                    $image.css({
                        'background-color': background.body.color
                    });
                }

                if (background.body.brightness) {
                    $overlay.css({
                        "background-color": background.body.brightness > 0 ? 'white' : 'black',
                        "opacity": background.body.brightness > 0 ? background.body.brightness : -background.body.brightness
                    });
                }

                if (background.body.color) {
                    $image.css({
                        'background-color': background.body.color
                    });
                }
            }
        };
    }
}());
(function () {
    'use strict';
    
    angular.module('assessment').directive('hint', directive);

    directive.$inject = ['$compile', 'documentBlockHelper'];

    function directive($compile, documentBlockHelper) {
        return {
            restrict: 'A',
            link: function ($scope, $element, attrs) {
                var unbind = $scope.$watch(attrs.hint, set);

                function set(newValue) {
                    if (!_.isUndefined(newValue)) {
                        var dataType = getLearningContentType(newValue);
                        switch(dataType){
                            case 'hotspot': {
							    var hotspotOnImage = HotspotStorage.create($(newValue)[0]);
                        
								$element.addClass('hotspot-on-image-container');
								$element.html(hotspotOnImage.element);
                                
								$element.on('$destroy', function () {
								    HotspotStorage.remove(hotspotOnImage);
								});
                                onHintCreated();
                                break;
                            }
                            case 'document': {
                                documentBlockHelper.getDocumentBlockContent(newValue).then(function(content) {
                                    $element.append(content);
                                    onHintCreated();
                                });
                                break;
                            }
                            default: {
                                $element.html(newValue);
                                onHintCreated();
                            }
                        }
                    }
                }

                function onHintCreated() {
                    $compile($element.contents())($scope);
                    $scope.$emit('$includeContentLoaded');
                    unbind();
                }
                
                function getLearningContentType(data){
                    var $output = $('<output>').html(data),
                        dataType = $('[data-type]', $output).attr('data-type');

                    return dataType;
                }
            }
        };
    }
}());
(function () {

    angular.module('assessment')
        .directive('timer', timer);

    function timer() {
        return {
            restrict: 'E',
            scope: {
                timeInSeconds: '=timeInSeconds'
            },
            templateUrl: 'app/views/timer.html'
        };
    }

})();
(function () {
    'use strict';

    angular.module('assessment').directive('loginLeave', directive);

    function directive() {
        return {
            restrict: 'A',
            link: function ($scope, $element, attrs) {
                var $parent = $element.closest('.login'),
                    $containerToLeft = $parent.find('.login-container'),
                    $overlay = $parent.find('.login-overlay'),
                    $loader = $parent.find('.loader-container'),
                    loginController = $scope.login,
                    duration = 300,
                    leftOffset = 60,
                    data = $scope.$eval(attrs.loginLeave);

                $element.on(data.event, function(evt) {
                    if(evt.keyCode === undefined || evt.keyCode === 13) {
                        handler();
                    }
                });

                function handler() {
                    if (data.method === 'skip') {
                        animation(loginController.skip);
                    } else {
                        if (loginController.usernameIsValid() && loginController.emailIsValid()) {
                            animation(loginController.submit);
                        } else {
                            loginController.submit();
                            $scope.$apply();
                        }
                    }
                }

                function animation(calllback) {
                    var containerWidth = $containerToLeft.width();
                    $containerToLeft.animate({
                        left: '-' + (containerWidth + leftOffset) + 'px'
                    }, duration, function () {
                        $loader.show();
                        $overlay.animate({
                            'background-color': 'white'
                        }, duration, function () {
                            calllback.apply();
                            $scope.$apply();
                        });
                    });

                }
            }
        };
    }
}());
(function () {
    
        angular.module('assessment')
            .directive('preventTouch', preventTouch);
    
        function preventTouch() {
            return {
                restrict: 'A',
                link: function ($scope, element) {
                    $(element).on("touchmove", function(event) {
                        event.preventDefault();
                    });
                }
            };
        }
    }());
(function () {

    angular.module('assessment')
        .directive('courseTitle', courseTitle);

    function courseTitle() {
        return {
            restrict: 'A',
            link: function ($scope, element) {
                var $element = $(element),
                    unbind = $scope.$watch(checkFontSize);

                function checkFontSize() {
                    var lenght = $element[0].innerHTML.length;

                    if (window.innerWidth > 640) {
                        changeFontSize($element, lenght, 20, 30);
                    } else {
                        changeFontSize($element, lenght, 22, 24);
                    }

                    unbind();
                }

            }
        };
    }

    function changeFontSize($element, lenght, minValue, maxValue) {
        if (lenght > 70) {
            lenght < 140 ? $element.css('font-size', maxValue + 'px') : $element.css('font-size', minValue + 'px');
        }
    }
}());
(function () {

    angular.module('assessment')
        .directive('blockBodyScroll', blockBodyScroll);

    function blockBodyScroll() {
        return {
            restrict: 'A',
            scope: {
                blockScrolling: '@'
            },
            link: function ($scope) {   
                $scope.$watch('blockScrolling', onValueChanged);
            }
        };

        function onValueChanged(newValue) {
            var isBlocked = newValue === 'true',
                $body = $('body');
            
            $body.css({
                'max-height': isBlocked ? '100vh' : 'none',
                'overflow': isBlocked ? 'hidden' : 'visible'
            });
        }
    }

})();
(function () {

    angular
        .module('assessment')
        .controller('ImagePreviewController', ImagePreviewController);

    ImagePreviewController.$inject = ['$scope', '$rootScope', '$document', 'imagePreviewFactory'];

    function ImagePreviewController($scope, $rootScope, $document, imagePreviewFactory) {
        var that = this;

        that.imageUrl = undefined;
        that.visible = false;

        that.show = function (imageUrl) {
            that.visible = true;
            that.imageUrl = imageUrl;
            
            $document.on('keydown', escapeHandler);
        }

        that.hide = function () {
            that.visible = false;

            $document.off('keydown', escapeHandler);
        }

        var unbind = $rootScope.$on(imagePreviewFactory.showEventName, function (event, imageUrl) {
            that.show(imageUrl);
        });
        $scope.$on('$destroy', function(){
            that.hide();
            
            unbind();
        });

        function escapeHandler(event) {
            if(event.keyCode === 27) {
                that.hide();

                event.preventDefault();
                event.stopPropagation();
            }
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment')
        .controller('MainController', MainController);

    MainController.$inject = ['$scope', '$rootScope', '$location', '$injector', 'assessment', 'settings', 'timer', 'viewmodelsFactory', 'userContext', 'ExpandableBlockViewModel'];

    var cachedQuestions = [];

    function MainController($scope, $rootScope, $location, $injector, assessment, settings, timer, viewmodelsFactory, userContext, ExpandableBlockViewModel) {
        var submitted = false;
        var that = this;
        var xAPIManager = $injector.has('xAPIManager') ? $injector.get('xAPIManager') : null;
        var user = userContext.getCurrentUser();

        if (settings.xApi.enabled && !xAPIManager.isInitialized && user) {
            xAPIManager.init(assessment.id, assessment.title, $location.absUrl(), user.email.trim(), user.username.trim(), user.account || null, settings.xApi);
        }
        that.title = $rootScope.title = assessment.title;
        that.hasIntroductionContent = assessment.hasIntroductionContent;
        that.introductions = mapIntroductions(assessment.introductions);
        that.logoUrl = settings.logo && settings.logo.url;
        that.mode = settings.assessmentMode;
        that.showGivenAnswers = settings.showGivenAnswers;


        function mapIntroductions(items) {
            return _.map(items, function (item) {
                if (item.children && item.children.length) {
                    return new ExpandableBlockViewModel(item);
                }

                return item;
            });
        }

        if (that.showGivenAnswers && cachedQuestions.length) {
            _.each(cachedQuestions, function(item, index) {
                if (item.getType() === 'scenarioQuestion') {
                    var questionModel = _.find(assessment.questions, function (quest) { return quest.id === item.id });
                    if (questionModel) {
                        cachedQuestions[index] = viewmodelsFactory.createQuestionViewmodel(questionModel);
                    }
                }
            });
            that.questions = cachedQuestions;
        } else {
            that.questions = assessment.questions.map(function (question) {
                return viewmodelsFactory.createQuestionViewmodel(question);
            });
            cachedQuestions = that.questions;
        }

        that.submit = function () {
            if (submitted) {
                return;
            }

            submitted = true;
            that.questions.forEach(function (question) {
                question.submit();
            });

            assessment.sendCourseResult(settings.masteryScore.score);

            $location.path('/summary').replace();
        };

        assessment.start();

        // timer definition
        $scope.timerEnabled = settings.timer.enabled;
        if (settings.timer.enabled) {
            var time = settings.timer.time,
                timeInSeconds = time.hours * 3600 + time.minutes * 60 + time.seconds;

            timer.setTime(timeInSeconds);
            $scope.timerRemainingTime = timeInSeconds;

            timer.onTick(function (remainingTime) {
                $scope.timerRemainingTime = remainingTime;
                $scope.$apply();
            });

            timer.onStopped(function () {
                that.submit();
                $scope.$apply();
            });

            $scope.$on('$assessmentStarted', function () {
                timer.start();
            });

            $scope.$on('$destroy', function () {
                timer.dispose();
            });
        }
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .controller('SummaryController', SummaryController);

    SummaryController.$inject = ['$rootScope', '$scope', '$location', '$timeout', 'settings', '$window', 'translate', 'htmlContentLoader', 'contentViewModelMapper', 'assessment', 'questionPool', 'attemptsLimiter', 'userContext', 'SummaryQuestionListViewModel', 'ResendResultsViewModel'];

    function SummaryController($rootScope, $scope, $location, $timeout, settings, $window, translate, htmlContentLoader, contentViewModelMapper, assessment, questionPool, attemptsLimiter, userContext, SummaryQuestionListViewModel, ResendResultsViewModel) {
        var that = this,
            userName = userContext.getUsername();

        that.showQuestionResults = settings.showQuestionResults;
        that.showUserNameOnResultsPage = settings.showUserNameOnResultsPage;
        
        $rootScope.title = 'Summary | ' + assessment.title;
        that.title = assessment.title;
        that.logoUrl = settings.logo && settings.logo.url;

        that.progress = assessment.getResult();
        that.masteryScore = settings.masteryScore.score;
        that.reachMasteryScore = that.progress >= that.masteryScore;
        that.finished = false;
        that.isSendingRequest = false;
        that.attemptsLimited = attemptsLimiter.hasLimit;
        that.singleAttempt = attemptsLimiter.limit === 1;
        that.availableAttemptCount = attemptsLimiter.getAvailableAttemptCount();
        that.canTryAgain = attemptsLimiter.hasAvailableAttempt();

        that.callToActionText = translate.get(getCallToActionTranslationKey(!!userName), '{userName}', userName);

        that.showPassedAfterwords = assessment.passedAfterwords.length && that.reachMasteryScore;
        that.showFailedAfterwords = assessment.failedAfterwords.length && !that.reachMasteryScore;
        that.isAfterwordLoading = false;
        that.afterwordContent = [];

        if (that.showPassedAfterwords) {
            that.isAfterwordLoading = true;
            htmlContentLoader.loadCollection(assessment.passedAfterwords).then(function() {
                that.afterwordContent = contentViewModelMapper.map(assessment.passedAfterwords);
                that.isAfterwordLoading = false;
            });
        } else if (that.showFailedAfterwords) {
            that.isAfterwordLoading = true;
            htmlContentLoader.loadCollection(assessment.failedAfterwords).then(function() {
                that.afterwordContent = contentViewModelMapper.map(assessment.failedAfterwords);
                that.isAfterwordLoading = false;
            });
        }

        that.questionList = new SummaryQuestionListViewModel(assessment.questions, !(that.showPassedAfterwords || that.showFailedAfterwords), that.reachMasteryScore);

        that.skipFinishAssessment = false;
        that.showResendResultsToWebhooksDialog = false;
        that.resendResultsToWebhooksDialog = new ResendResultsViewModel({
            callbacks: {
                next: closeAssessment
            },
            eventName: 'course:resendToWebhooks',
            close: hideResendResultsToWebhooksDialog,
            resultsSendErrorTitleKey: '[results send error title]',
            endpointNameKey: '[webhooks endpoint name key]',
            assessment: assessment
        });

        that.tryAgain = function () {
            if (that.finished) {
                return;
            }
            that.isSendingRequest = true;

            assessment.restart(restartCourse, errorHandler);
        };

        that.finish = function () {
            if (that.finished) {
                return;
            }

            if(that.skipFinishAssessment) {
                return showResendResultsToWebhooksDialog();
            }

            that.isSendingRequest = true;
            assessment.finish(closeAssessment, errorHandler);
        };

        function restartCourse() {
            that.finished = true;
            that.isSendingRequest = false;
            questionPool.refresh();
            $location.path('/').search('tryAgain');
        }

        function closeAssessment() {
            that.finished = true;
            that.isSendingRequest = false;
            $scope.$applyAsync();
            $window.close();
            if (!inIframe()) {
                $timeout(function () {
                    $window.alert('Thank you, you can close the page now');
                }, 100);
            }
        }

        function errorHandler(reason) {
            that.isSendingRequest = false;

            if(reason && !reason.problem) {
                $window.alert(reason);
            }

            if(reason && reason.problem === 'webhooks') {
                that.skipFinishAssessment = true;
                showResendResultsToWebhooksDialog();
            }
        }

        function showResendResultsToWebhooksDialog() {
            that.showResendResultsToWebhooksDialog = true;
        }

        function hideResendResultsToWebhooksDialog() {
            that.showResendResultsToWebhooksDialog = false;
        }
        
        function inIframe() {
            // browsers can block access to window.top due to same origin policy, so exception can be thrown here.
            try {
                return $window.self !== $window.top;
            } catch (e) {
                return true;
            }
        }

        function getCallToActionTranslationKey(hasUsername) {
            if(hasUsername && that.showUserNameOnResultsPage) {
                return that.reachMasteryScore ? '[congratulations with name]' : '[do not give up with name]';
            }

            return that.reachMasteryScore ? '[congratulations]' : '[do not give up]';
        }
    }

}());

(function() {
    'use strict';

    angular
        .module('assessment')
        .controller('NotFoundErrorController', ErrorController);

    ErrorController.$inject = ['$rootScope', '$location', 'settings', 'assessment'];

    function ErrorController($rootScope, $location, settings, assessment) {
        var that = this;

        $rootScope.title = '404 |' + assessment.title;
        
        that.logoUrl = settings.logo && settings.logo.url;
        
        that.goHome = function() {
            $location.path('/');
        };
    }

}());
(function () {
    'use strict';

    angular
        .module('assessment')
        .controller('NoAccessController', NoAccessController);

    NoAccessController.$inject = ['$rootScope', '$location', 'settings', 'assessment', 'userContext'];

    function NoAccessController($rootScope, $location, settings, assessment, userContext) {
        var that = this;

        $rootScope.title = 'Access limited |' + assessment.title;

        that.logoUrl = settings.logo && settings.logo.url;

        that.goToLogin = function () {
            $rootScope.skipLoginGuard = false;
            userContext.clear();
            $location.path('/login');
        };
    }

}());
(function () {
    'use strict';

    var constants = {
        email: /^[^@\s]+@[^@\s]+$/
    };

    angular
        .module('assessment')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$rootScope', '$location', '$injector', 'settings', 'assessment', 'user', 'accessLimiter', 'userContext'];

    function LoginController($rootScope, $location, $injector, settings, assessment, user, accessLimiter, userContext) {
        var that = this,
            xAPIManager = $injector.has('xAPIManager') ? $injector.get('xAPIManager') : null;

        $rootScope.title = assessment.title;

        that.courseTitle = assessment.title;
        that.questionsLength = assessment.questions.length;
        that.username = user ? user.username : '';
        that.email = user ? user.email : '';
        that.account = user ? user.account : null;
        that.xAPIEnabled = settings.xApi.enabled;

        that.emailModified = false,
        that.usernameModified = false;

        that.allowToSkip = that.xAPIEnabled ? !settings.xApi.required : true;

        that.usernameIsValid = function () {
            return !!that.username && !!that.username.trim();
        };

        that.emailIsValid = function () {
            return !!that.email && (constants.email.test(that.email.trim()) || that.account);
        };

        that.markUsernameAsModified = function () {
            that.usernameModified = true;
        };

        that.markEmailAsModified = function () {
            that.emailModified = true;
        };

        that.submit = function () {
            if (that.usernameIsValid() && that.emailIsValid()) {
                    userContext.set(that.email.trim() , that.username.trim());

                    if (that.xAPIEnabled && accessLimiter.userHasAccess()) {
                        xAPIManager.init(assessment.id, assessment.title, $location.absUrl(), that.email.trim(), that.username.trim(), that.account, settings.xApi);
                    }

                    startCourse();
            } else {
                that.markUsernameAsModified();
                that.markEmailAsModified();
            }
        };

        that.skip = function () {
            if (!that.allowToSkip) {
                return;
            }

            if (that.xAPIEnabled) {
                xAPIManager.off();
            }

            $rootScope.skipLoginGuard = true;

            startCourse();
        };

        if (that.username || that.email || that.account) {
            that.submit();
        }

        function startCourse() {
            if (that.xAPIEnabled && accessLimiter.userHasAccess()) {
                $rootScope.isXApiInitialized = true;
            }

            $location.path('/').replace();
        }
    }
}());
(function () {
    'use strict';

    var app = angular.module('assessment.xApi', ['ngRoute']);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/error/xapi/:backUrl?', {
                templateUrl: 'app/modules/xApi/views/xApiError.html',
                controller: 'XApiErrorController',
                controllerAs: 'xApiError',
                resolve: {
                    assessment: ['dataContext', function (dataContext) {
                        return dataContext.getAssessment();
                    }]
                }
            });
    }]);

}());
(function () {
    'use strict';

    angular
        .module('assessment.xApi')
        .controller('XApiErrorController', ErrorsController);

    ErrorsController.$inject = ['$rootScope', '$location', '$routeParams', 'settings', 'xAPIManager', 'assessment'];

    function ErrorsController($rootScope, $location, $routeParams, settings, xAPIManager, assessment) {
        var that = this,
            backUrl = $routeParams.backUrl || '/';

        $rootScope.title = assessment.title;

        that.logoUrl = settings.logo && settings.logo.url;
        that.allowToContinue = !settings.xApi.required;

        that.restartCourse = function () {
            $rootScope.isXApiInitialized = false;
            $location.path('/').replace();
        };

        that.continue = function () {
            if (!that.allowToContinue) {
                return;
            }
            xAPIManager.off();
            $location.path(backUrl);
        };
    }

}());
(function () {
    'use strict';
    
    angular.module('assessment.xApi').factory('xApiInteractionTypes', xApiInteractionTypes);
    
    function xApiInteractionTypes() {
        var interactionTypes = {
            choice: 'choice',
            fillIn: 'fill-in',
            matching: 'matching',
            sequencing: 'sequencing',
            other: 'other'
        };
        
        return interactionTypes;
    }
    
}());
(function () {
    'use strict';

    angular.module('assessment.xApi')
        .factory('xApiSettings', xApiSettings);

    xApiSettings.$inject = ['settings', 'publishSettings'];

    function xApiSettings(settingsProvider, publishSettingsProvider) {
        var settings = {
            xApi: {
                allowedVerbs: [],
                version: '1.0.0'
            },
            init: init
        };

        var host = window.location.host;
        var lrsHost = publishSettingsProvider.defaultLRSUrl || 'https://reports.easygenerator.com';

        var defaultSettings = {
            lrs: {
                uri: lrsHost + '/xApi/statements',
                authenticationRequired: false,
                credentials: {
                    username: '',
                    password: ''
                }
            },
            allowedVerbs: ['started', 'stopped', 'experienced', 'mastered', 'answered', 'passed', 'failed']
        };

        return settings;

        function init() {
            if (settingsProvider.xApi.selectedLrs !== 'default') {
                $.extend(settings.xApi, settingsProvider.xApi);
            } else {
                $.extend(settings.xApi, defaultSettings);
            }
        }
    }
}());

(function () {
    'use strict';

    angular.module('assessment.xApi')
        .factory('xApiSupportedQuestionTypes', xApisupportedQeustionTypes);

    function xApisupportedQeustionTypes() {
        var supportedQuestionTypes = {
            singleChoice: 'singleSelectText',
            multipleChoice: 'multipleSelect',
            singleChoiceImage: 'singleSelectImage',
            fillInTheBlanks: 'fillInTheBlank',
            textMatching: 'textMatching',
            dragAndDropText: 'dragAndDropText',
            statement: 'statement',
            hotspot: 'hotspot',
            openQuestion: 'openQuestion',
            scenario: 'scenario',
            rankingText: 'rankingText'
        };

        function checkIfQuestionSupported(type) {
            for (var prop in supportedQuestionTypes) {
                if (supportedQuestionTypes[prop] === type) {
                    return true;
                }
            }
            return false;
        }

        return {
            types: supportedQuestionTypes,
            checkIfQuestionSupported: checkIfQuestionSupported
        };
    }

}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('xApiVerbs', xApiVerbs);

    function xApiVerbs() {
        var verbs = {
            started: {
                id: 'http://adlnet.gov/expapi/verbs/launched',
                display: {
                    'en-US': 'started'
                }
            },
            stopped: {
                id: 'http://adlnet.gov/expapi/verbs/exited',
                display: {
                    'en-US': 'stopped'
                }
            },
            passed: {
                id: 'http://adlnet.gov/expapi/verbs/passed',
                display: {
                    'en-US': 'passed'
                }
            },
            failed: {
                id: 'http://adlnet.gov/expapi/verbs/failed',
                display: {
                    'en-US': 'failed'
                }
            },
            experienced: {
                id: 'http://adlnet.gov/expapi/verbs/experienced',
                display: {
                    'en-US': 'experienced'
                }
            },
            answered: {
                id: 'http://adlnet.gov/expapi/verbs/answered',
                display: {
                    'en-US': 'answered'
                }
            },
            mastered: {
                id: 'http://adlnet.gov/expapi/verbs/mastered',
                display: {
                    'en-US': 'mastered'
                }
            }
        };

        return verbs;
    }

}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('dragAndDropTextDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var correctAnswersCoords = _.map(question.dropspots, function (item) {
                return '(' + item.x + ',' + item.y + ')';
            }).join('[,]');

            var correctAnswersTexts = _.map(question.dropspots, function(item) {
                return item.text;
            }).join('[,]');

            var enteredAnswersCoords = _.chain(question.dropspots).map(function (item) {
                var answer = answers.find(function (a) { return a.text === item.text });
                if (answer) {
                    return '(' + answer.x + ',' + answer.y + ')';
                } else {
                    return '(-1,-1)';
                }
            }).value().join('[,]')

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: enteredAnswersCoords
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.other,
                    correctResponsesPattern: [correctAnswersCoords]
                })
            });
            return {
                data: {
                    object: activity,
                    result: result
                },
                extensions: {
                    "http://easygenerator/expapi/question/imageUrl": question.background,
                    "http://easygenerator/expapi/question/answers": correctAnswersTexts
                }
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('fillInTheBlanksDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var correctAnswersItems = _.flatten(_.map(question.groups, function (item) {
                var correctAnswers = _.where(item.answers, {
                    isCorrect: true
                });
                return _.map(correctAnswers, function (answer) {
                    return answer.text + '[.]' + item.id;
                });
            })).join('[,]');

            var enteredAnswersItems = _.map(answers, function (item, key) {
                return item + '[.]' + key;
            }).join('[,]');

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: enteredAnswersItems
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.fillIn,
                    correctResponsesPattern: [correctAnswersItems]
                })
            });

            return {
                data: {
                    object: activity,
                    result: result
                },
                extensions: {
                    "http://easygenerator/expapi/question/content": question.content,
                }
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('hotspotDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var placedMarkers = _.map(answers, function (mark) {
                return '(' + mark.x + ',' + mark.y + ')';
            }).join('[,]');

            var spots = _.map(question.spots, function (spot) {
                var polygonCoordinates = _.map(spot, function (spotCoordinates) {
                    return '(' + spotCoordinates.x + ',' + spotCoordinates.y + ')';
                });
                return polygonCoordinates.join('[.]');
            }).join('[,]');

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: placedMarkers
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.other,
                    correctResponsesPattern: [spots]
                })
            });

            return {
                data: {
                    object: activity,
                    result: result
                },
                extensions: {
                    "http://easygenerator/expapi/question/imageUrl": question.background
                }
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('multipleChoiceDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var selectedAnswersTexts = _.map(answers, function (item) {
                return item;
            }).join('[,]');

            var correctAnswersTexts = _.chain(question.options)
                .filter(function (item) {
                    return item.isCorrect;
                }).map(function (item) {
                    return item.id;
                }).value().join('[,]');

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: selectedAnswersTexts
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.choice,
                    correctResponsesPattern: !!question.isSurvey ? [] : [correctAnswersTexts],
                    choices: _.map(question.options, function (option) {
                        return {
                            id: option.id,
                            description: {
                                'en-US': option.text
                            }
                        };
                    })
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('openQuestionDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            
            var enteredAnswerText = answers;

            var result = new TinCan.Result({
                response: enteredAnswerText
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.other
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('singleChoiceDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var selectedAnswer = answers || '';

            var correctAnswersId = _.chain(question.options)
                .filter(function (item) {
                    return item.isCorrect;
                }).map(function (item) {
                    return item.id;
                }).value().join('[,]');

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: selectedAnswer.toString()
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.choice,
                    correctResponsesPattern: !!question.isSurvey ? [] : [correctAnswersId],
                    choices: _.map(question.options, function (option) {
                        return {
                            id: option.id,
                            description: {
                                'en-US': option.text
                            }
                        };
                    })
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('singleChoiceImageDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var selectedAnswersId = answers || '';

            var correctAnswerId = question.correctAnswerId;

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: selectedAnswersId.toString()
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.choice,
                    correctResponsesPattern: [correctAnswerId],
                    choices: _.map(question.options, function (option) {
                        return {
                            id: option.id,
                            description: {
                                'en-US': option.image
                            }
                        };
                    })
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('statementDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var correctAnswersIds = _.map(question.options, function (item) {
                return item.id + '[.]' + item.isCorrect;
            }).join('[,]');

            var selectedAnswersIds = _.chain(answers)
                .filter(function (statement) {
                    return !_.isNull(statement.state) && !_.isUndefined(statement.state);
                }).map(function (statement) {
                    return statement.id + '[.]' + statement.state;
                }).value().join('[,]');

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: selectedAnswersIds
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.choice,
                    correctResponsesPattern: !!question.isSurvey ? [] : [correctAnswersIds],
                    choices: _.map(question.options, function (option) {
                        return {
                            id: option.id,
                            description: {
                                'en-US': option.text
                            }
                        };
                    })
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('textMatchingDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
			var sources = _.pluck(question.answers, 'key'),
				targets = _.uniq(_.pluck(question.answers, 'value'));
				
            var response = _.map(answers, function (value) {
                return _.indexOf(sources, value.key) + '[.]' + (value.value ? _.indexOf(targets, value.value) : '');
            }).join('[,]');

            var correctResponsesPattern = [_.map(question.answers, function (answer, index) {
                    return index.toString() + '[.]' + _.indexOf(targets, answer.value);
                }).join('[,]')];

            var source = _.map(sources, function (answer, index) {
                return {
                    id: index.toString(),
                    description: {
                        'en-US': answer
                    }
                };
            });

            var target = _.map(targets, function (answer, index) {
                return {
                    id: index.toString(),
                    description: {
                        'en-US': answer
                    }
                };
            });

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: response
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.matching,
                    correctResponsesPattern: correctResponsesPattern,
                    source: source,
                    target: target
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('scenarioQuestionDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, questionUrl) {

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                })
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.other
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('rankingTextDataBuilder', factory);

    factory.$inject = ['xApiInteractionTypes'];

    function factory(interactionTypes) {
        return function (question, answers, questionUrl) {
            var answersSequencing = _.map(answers, function (item) {
                return item.text.toLowerCase();
            }).join("[,]");

            var correctAnswersSequencing = _.map(question.correctOrderAnswers, function (item) {
                return item.text.toLowerCase();
            }).join("[,]");

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: question.score / 100
                }),
                response: answersSequencing
            });

            var activity = new TinCan.Activity({
                id: questionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    },
                    type: 'http://adlnet.gov/expapi/activities/cmi.interaction',
                    interactionType: interactionTypes.sequencing,
                    correctResponsesPattern: [correctAnswersSequencing],
                    choices: _.map(question.correctOrderAnswers, function (answer) {
                        return {
                            id: answer.text,
                            description: {
                                'en-US': answer.text
                            }
                        };
                    })
                })
            });

            return {
                object: activity,
                result: result
            };
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('questionDataBuilder', factory);

    factory.$inject = ['xApiVerbs', 'xApiSupportedQuestionTypes', 'sectionsQueries',
                      'dragAndDropTextDataBuilder', 'fillInTheBlanksDataBuilder', 'hotspotDataBuilder', 'multipleChoiceDataBuilder',
                      'singleChoiceDataBuilder', 'singleChoiceImageDataBuilder', 'statementDataBuilder', 'textMatchingDataBuilder',
                      'openQuestionDataBuilder', 'scenarioQuestionDataBuilder', 'rankingTextDataBuilder'];

    function factory(verbs, supportedQuestionTypes, sectionsQueries,
        dragAndDropTextDataBuilder, fillInTheBlanksDataBuilder, hotspotDataBuilder, multipleChoiceDataBuilder,
        singleChoiceDataBuilder, singleChoiceImageDataBuilder, statementDataBuilder, textMatchingDataBuilder,
        openQuestionDataBuilder, scenarioQuestionDataBuilder, rankingTextDataBuilder) {
        return {
            questionAnswered: questionAnswered
        };

        function questionAnswered(item, rootUrl) {
            if (!supportedQuestionTypes.checkIfQuestionSupported(item.question.type)) {
                throw 'Question type is not supported';
            }

            var question = item.question,
                answers = item.answers,
                section = sectionsQueries.getSectionById(question.sectionId),
                questionUrl = rootUrl + '#section/' + section.id + '/question/' + question.id,
                parentUrl = rootUrl + '#sections?section_id=' + section.id,
                data = null,
                types = supportedQuestionTypes.types,
                extensions= {};
            
            switch (item.question.type) {
                case types.multipleChoice:
                    data = multipleChoiceDataBuilder(question, answers, questionUrl);
                    break;
                case types.singleChoice:
                    data = singleChoiceDataBuilder(question, answers, questionUrl);
                    break;
                case types.singleChoiceImage:
                    data = singleChoiceImageDataBuilder(question, answers, questionUrl);
                    break;
                case types.statement:
                    data = statementDataBuilder(question, answers, questionUrl);
                    break;
                case types.dragAndDropText:
                    var result =  dragAndDropTextDataBuilder(question, answers, questionUrl);
                    data = result.data;
                    extensions = result.extensions;
                    break;
                case types.fillInTheBlanks:
                    var result = fillInTheBlanksDataBuilder(question, answers, questionUrl);
                    data = result.data;
                    extensions = result.extensions;
                    break;
                case types.hotspot:
                    var result = hotspotDataBuilder(question, answers, questionUrl);
                    data = result.data;
                    extensions = result.extensions;
                    break;
                case types.textMatching:
                    data = textMatchingDataBuilder(question, answers, questionUrl);
                    break;
                case types.openQuestion:
                    data = openQuestionDataBuilder(question, answers, questionUrl);
                    break;
                case types.scenario:
                    data = scenarioQuestionDataBuilder(question, questionUrl);
                    break;
                case types.rankingText:
                    data = rankingTextDataBuilder(question, answers, questionUrl);
                    break;
            }
            
            var context = defaultContext(question, parentUrl, section.title);
            context.extensions = _.extend(context.extensions, extensions);
            data.context = context;
            data.verb = verbs.answered;

            return data;
        }

        function defaultContext(question, parentUrl, sectionTitle) {
            var parentActivity = new TinCan.Activity({
                id: parentUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': sectionTitle
                    }
                })
            });
            var context = new TinCan.Context({
                contextActivities: new TinCan.ContextActivities({
                    parent: [parentActivity]
                }),
                extensions: {
                    'http://easygenerator/expapi/question/survey': question.hasOwnProperty('isSurvey') && question.isSurvey,
                    'http://easygenerator/expapi/question/type': question.type
                }
            });
            return context;
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('courseDataBuilder', factory);

    factory.$inject = ['xApiVerbs'];

    function factory(verbs) {
        return {
            courseStartedData: courseStartedData,
            courseResultData: courseResultData,
            courseStoppedData: courseStoppedData
        };

        function courseStartedData() {
            return {
                verb: verbs.started
            };
        }

        function courseResultData(course) {
            var resultVerb = course.isCompleted ? verbs.passed : verbs.failed;

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: course.getResult() / 100
                })
            });

            return {
                verb: resultVerb,
                result: result
            };
        }

        function courseStoppedData() {
            return {
                verb: verbs.stopped
            };
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('sectionDataBuilder', factory);

    factory.$inject = ['xApiVerbs'];

    function factory(verbs) {
        return {
            sectionMasteredData: sectionMasteredData
        };

        function sectionMasteredData(section, rootUrl) {
            var sectionUrl = rootUrl + '#sections?section_id=' + section.id;

            var result = new TinCan.Result({
                score: new TinCan.Score({
                    scaled: section.getResult() / 100
                })
            });

            var activity = new TinCan.Activity({
                id: sectionUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': section.title
                    }
                })
            });

            return {
                verb: verbs.mastered,
                object: activity,
                result: result,
            };
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('learningContentDataBuilder', factory);

    factory.$inject = ['xApiVerbs', 'sectionsQueries', 'dateTimeConverter'];

    function factory(verbs, sectionsQueries, dateTimeConverter) {

        return {
            learningContentExperienced: learningContentExperienced
        };

        function learningContentExperienced(question, spentTime, rootUrl) {
            var section = sectionsQueries.getSectionById(question.sectionId),
                questionUrl = rootUrl + '#section/' + section.id + '/question/' + question.id,
                parentUrl = rootUrl + '#sections?section_id=' + section.id,
                learningContentUrl = rootUrl + '#section/' + section.id + '/question/' + question.id + '/learningContents';

            var result = new TinCan.Result({
                duration: dateTimeConverter.timeToISODurationString(spentTime)
            });

            var activity = new TinCan.Activity({
                id: learningContentUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': question.title
                    }
                })
            });

            var context = new TinCan.Context({
                contextActivities: new TinCan.ContextActivities({
                    parent: [new TinCan.Activity({
                        id: questionUrl,
                        definition: new TinCan.ActivityDefinition({
                            name: {
                                'en-US': question.title
                            }
                        })
                    })],
                    grouping: [new TinCan.Activity({
                        id: parentUrl,
                        definition: new TinCan.ActivityDefinition({
                            name: {
                                'en-US': section.title
                            }
                        })
                    })]
                })
            });

            return {
                object: activity,
                result: result,
                context: context,
                verb: verbs.experienced
            };
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('xApiDataBuilder', dataBuilder);

    dataBuilder.$inject = ['courseDataBuilder', 'sectionDataBuilder', 'questionDataBuilder', 'learningContentDataBuilder'];

    function dataBuilder(courseDataBuilder, sectionDataBuilder, questionDataBuilder, learningContentDataBuilder) {
        /*private fields*/
        var actor = null,
            courseId = '',
            activityName = '',
            activityUrl = '',
            rootUrl = '',
            sessionId = '';
        /*private fields*/

        var builder = {
            init: init,
            courseStarted: courseStarted,
            courseResults: courseResults,
            courseStopped: courseStopped,
            sectionMastered: sectionMastered,
            questionAnswered: questionAnswered,
            learningContentExperienced: learningContentExperienced
        };

        return builder;

        function init(id, title, url, agent) {
            courseId = id;
            activityName = title;
            rootUrl = url.split('?')[0].split('#')[0];
            activityUrl = rootUrl + '?course_id=' + id;
            actor = agent;
        }

        function courseStarted() {
            sessionId = TinCan.Utils.getUUID();
            var data = courseDataBuilder.courseStartedData();
            data.object = defaultActivity();
            data.context = defaultContext();
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function courseResults(course) {
            var data = courseDataBuilder.courseResultData(course);
            data.object = defaultActivity();
            data.context = defaultContext();
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function courseStopped() {
            var data = courseDataBuilder.courseStoppedData();
            data.object = defaultActivity();
            data.context = defaultContext();
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function sectionMastered(section) {
            var data = sectionDataBuilder.sectionMasteredData(section, rootUrl);
            data.context = defaultContext();
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function questionAnswered(item) {
            var data = questionDataBuilder.questionAnswered(item, rootUrl);
            data.context = addExtensionsToContext(data.context);
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function learningContentExperienced(item) {
            var data = learningContentDataBuilder.learningContentExperienced(item.question, item.time, rootUrl);
            data.context = addExtensionsToContext(data.context);
            data.actor = actor || {};

            return new TinCan.Statement(data);
        }

        function defaultActivity() {
            var activity = new TinCan.Activity({
                id: activityUrl,
                definition: new TinCan.ActivityDefinition({
                    name: {
                        'en-US': activityName
                    }
                })
            });
            return activity;
        }

        function defaultContext() {
            var context = new TinCan.Context({
                contextActivities: new TinCan.ContextActivities({})
            });
            context = addExtensionsToContext(context);
            return context;
        }

        function addExtensionsToContext(context) {
            context.extensions = context.extensions || {};
            context.extensions['http://easygenerator/expapi/course/id'] = courseId;
            context.registration = sessionId;
            return context;
        }
    }
}());

(function () {
    'use strict';

    angular.module('assessment.xApi').factory('dateTimeConverter', factory);

    function factory() {
        return {
            timeToISODurationString: timeToISODurationString
        };

        function timeToISODurationString(timeInMilliseconds) {
            timeInMilliseconds /= 1000;
            var hours = parseInt(timeInMilliseconds / 3600, 10);
            timeInMilliseconds -= hours * 3600;
            var minutes = parseInt(timeInMilliseconds / 60, 10);
            timeInMilliseconds -= minutes * 60;
            var seconds = parseInt(timeInMilliseconds, 10);
            return 'PT' + hours + 'H' + minutes + 'M' + seconds + 'S';
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('errorsHandler', errorsHandler);

    errorsHandler.$inject = ['$location', '$timeout'];

    function errorsHandler($location, $timeout) {
        return {
            handleError: handleError
        };

        function handleError() {
            if ($location.hash().indexOf('/error/xapi') !== -1) {
                return;
            }
            var hash = $location.hash().slice(1, $location.hash().length);

            var navigateUrl = '/error/xapi' + encodeURIComponent(_.isEmpty(hash) ? '' : hash);
            $timeout(function () {
                $location.path(navigateUrl).replace();
            }, 100);
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi').factory('xApiEventsHandler', xApiEventsHandler);

    xApiEventsHandler.$inject = ['$rootScope', 'xApiRequestManager', 'xApiDataBuilder', 'xApiSettings', 'StatementsStorage'];

    function xApiEventsHandler($rootScope, requestManager, dataBuilder, xApiSettings, statementsStorage) {
        var unbindFunctions = [];

        unbindFunctions.push($rootScope.$on('course:started', function () {
            sendStatementIfAllowed(dataBuilder.courseStarted());
        }));

        unbindFunctions.push($rootScope.$on('course:results', function (scope, data) {
            _.each(data.sections, function (section) {
                sendStatementIfAllowed(dataBuilder.sectionMastered(section));
            });
            sendStatementIfAllowed(dataBuilder.courseResults(data));
        }));

        unbindFunctions.push($rootScope.$on('course:finished', function () {
            return sendStatementIfAllowed(dataBuilder.courseStopped());
        }));

        unbindFunctions.push($rootScope.$on('question:answered', function (scope, data) {
            sendStatementIfAllowed(dataBuilder.questionAnswered(data));
        }));

        unbindFunctions.push($rootScope.$on('learningContent:experienced', function (scope, data) {
            sendStatementIfAllowed(dataBuilder.learningContentExperienced(data));
        }));

        function sendStatementIfAllowed(statement) {
            if (!_.contains(xApiSettings.xApi.allowedVerbs, statement.verb.display['en-US'])) {
                return undefined;
            }

            statementsStorage.push(statement);
            return requestManager.sendStatements();
        }

        function unbindAll() {
            _.each(unbindFunctions, function (func) {
                func.apply();
            });
        }

        return {
            off: unbindAll
        };
    }

}());

(function () {
    'use strict';

    angular.module('assessment.xApi')
        .factory('xApiRequestManager', xApiRequestManager);

    xApiRequestManager.$inject = ['$q', 'StatementsStorage', 'errorsHandler'];

    function xApiRequestManager($q, statementsStorage, errorsHandler) {
        var xApi = null,
            defers = [];

        return {
            sendStatements: sendStatements,
            init: init
        };

        function init(xapi) {
            xApi = xapi;
        }

        function sendStatements() {
            send();
            return $q.all(defers);
        }

        function send() {
            var tempArray = [],
                stmts = statementsStorage.shift();

            if (stmts.length !== 0) {
                _.each(stmts, function (stmt) {
					tempArray.push(stmt.item);
                });
					
				while(tempArray.length) {
					var defer = $q.defer();
					defers.push(defer.promise);
						
					xApi.sendStatements(tempArray.splice(0,5), function (errors) {
						_.each(errors, function (error) {
							if (error.err != null) {
								errorsHandler.handleError();
							}
						});
						defer.resolve();
					});
				}
			} 
        }
    }

}());

(function () {
    'use strict';

    angular.module('assessment.xApi')
        .factory('StatementsStorage', statementsStorage);

    function statementsStorage() {
        var statements = [];

        function push(stmt) {
            statements.push({
                item: stmt
            });
        }

        function shift() {
            var tempStatements = statements;
            statements = [];
            return tempStatements;
        }

        return {
            push: push,
            shift: shift
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.xApi')
        .service('xAPIManager', xAPIManager);

    xAPIManager.$inject = ['errorsHandler', 'xApiDataBuilder', 'xApiEventsHandler', 'xApiSettings', 'xApiRequestManager'];

    function xAPIManager(errorsHandler, xApiDataBuilder, xApiEventsHandler, xApiSettings, xApiRequestManager) {
        var xApi = null,
            actor = null,
            isInitialized = false;

        return {
            isInitialized: isInitialized,
            init: init,
            off: off
        };

        function init(id, title, absUrl, email, username, account) {
            xApiSettings.init();
            xApi = new TinCan();
            xApi.actor = createActor(username, email, account) || {};
            try {
                xApi.addRecordStore(createLRS());
            } catch (e) {
                errorsHandler.handleError();
            }
            xApiRequestManager.init(xApi);
            xApiDataBuilder.init(id, title, absUrl, xApi.actor);
            isInitialized = true;

        }

        function off() {
            xApiEventsHandler.off();
            isInitialized = false;
        }

        function createLRS() {
            var xApi = xApiSettings.xApi;

            return new TinCan.LRS({
                endpoint: xApi.lrs.uri.split('/statements')[0],
                version: xApi.version,
                username: xApi.lrs.credentials.username,
                password: xApi.lrs.credentials.password,
                allowFail: false
            });
        }

        function createActor(username, email, account) {
            try {
                if(account) {
                    actor = new TinCan.Agent({
                        name: username || account.name,
                        account: account
                    });
                } else {
                    actor = new TinCan.Agent({
                        name: username,
                        mbox: 'mailto:' + email
                    });
                }
            } catch (e) {
                errorsHandler.handleError();
            }
            return actor;
        }
    }
}());

(function () {
    'use strict';

    angular.module('assessment.progressStorer', []).run(runBlock);

    runBlock.$inject = ['$rootScope', 'dataContext'];

    function runBlock($rootScope, dataContext) {
        $rootScope.$on('course:finished', function () {
            saveResult();
        });

        function saveResult() {
            var assessment = dataContext.getAssessment();
            var resultKey = 'course_result' + assessment.id + assessment.templateId;

            var result = {
                score: assessment.getResult(),
                status: assessment.getStatus()
            };

            try {
                var string = JSON.stringify(result);
                localStorage.setItem(resultKey, string);
            } catch (e) {
                console.log('Failed to store course result');
            }
            return true;
        }
    }

}());
(function () {
    'use strict';

    var constants = {
        sendResultAttemptsTimeout: 10000,
        sendResultAttemptsCount: 10,
        resultCallbackUrlParameterName: 'ltiResultCallbackUrl',
        errorMessage: 'Something went wrong and your final score has not been reported ' + 
            '(reason: LTI reporting failed). Please contact the author of the course.'
    };

    angular.module('assessment.ltiResultsSender', []).run(ltiResultsSender);

    ltiResultsSender.$inject = ['$rootScope', '$q', 'urlHelper'];

    function ltiResultsSender($rootScope, $q, urlHelper) {
        var resultCallbackUrl = urlHelper.getQueryStringValue(constants.resultCallbackUrlParameterName);
        if (!resultCallbackUrl) {
            return;
        }

        $rootScope.$on('course:finished', function (scope, assessment) {
            var requestParams = {
                url: resultCallbackUrl,
                method: 'POST',
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    score: assessment.getResult() / 100
                }
            };

            var token = urlHelper.getQueryStringValue('token');
            if (token) {
                requestParams.headers = {
                    Authorization: 'Bearer ' + token
                };
            }

            return sendRequest(requestParams, 0, $q.defer());
        });
    }

    function sendRequest(params, attemptNumber, defer) {
        $.ajax(params).done(function() {
            defer.resolve();
        }).fail(function () {
            if (attemptNumber >= constants.sendResultAttemptsCount) {
                return defer.reject(constants.errorMessage);
            }

            setTimeout(function () {
                sendRequest(params, ++attemptNumber, defer);
            }, constants.sendResultAttemptsTimeout);
        });

        return defer.promise;
    }

}());

(function() {
    'use strict';
    
    angular.module('assessment.webhooksResultsSender', []).run(factory);

    factory.$inject = ['$rootScope', '$q', 'settings', 'userContext'];

    function factory($rootScope, $q, settingsProvider, userContext) {
        function WebhooksModule () {
            this.url = settingsProvider.webhooks.url;
            this.resendToWebhooksEventListener = undefined;
            this.initialized = true;
            $rootScope.$on('course:finished', this.sendResults.bind(this));
        }

        WebhooksModule.prototype.sendResults = function (scope, assessment) {
            var self = this,
                defer = $q.defer(),
                user = userContext.getCurrentUser();

            if(!(_.isNull(user) || _.isUndefined(user))) {
                var data = {
                    courseId: assessment.id,
                    learnerId: user.email,
                    score: assessment.getResult(),
                    finishedOn: (new Date()).toISOString(),
                    status: assessment.getStatus() == 'completed' ? 'passed' : 'failed'
                };
    
                
                fetch(this.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(function(response) {
                    if(response.status !== 200) {
                        if(!self.resendToWebhooksEventListener) {
                            self.subscribeToResendEvent();
                        }
    
                        return defer.reject({ problem: 'webhooks' });
                    }
    
                    defer.resolve();
                });
            } else {
                defer.resolve();
            }

            return defer.promise;
        }

        WebhooksModule.prototype.subscribeToResendEvent = function() {
            this.resendToWebhooksEventListener = $rootScope.$on('course:resendToWebhooks', this.sendResults.bind(this));
        }

        return new WebhooksModule();
    }
}());
(function () {
    'use strict';

    angular.module('assessment.publishSettings', []).run(runBlock);

    runBlock.$inject = ['publishModules', 'publishModulesInitializer'];

    function runBlock(publishModules, publishModulesInitializer) {
        if (publishModules && publishModules.length > 0) {
            publishModulesInitializer.init(publishModules);
        }
    }
}());
(function () {
    'use strict';
    angular.module('assessment.publishSettings')
           .service('publishModulesInitializer', PublishModulesInitializer);

    PublishModulesInitializer.$inject = ['$rootScope'];

    function PublishModulesInitializer($rootScope) {
        var that = this;

        that.init = function(modules) {
            _.each(modules, function(module) {
                initModule(module);
            });
        };

        function initModule(module) {
            if (_.isFunction(module.initialize)) {
                module.initialize();
            }
            if (_.isFunction(module.courseFinished)) {
                $rootScope.$on('course:finished', function (scope, data) {
                    var eventData = {
                        result: data.getResult() / 100,
                        isCompleted: data.isCompleted
                    };

                    module.courseFinished(eventData);
                });
            }
            if (_.isFunction(module.courseFinalized)) {
                $rootScope.$on('course:finalized', function (scope, data) {
                    module.courseFinalized();
                });
            }
        }
    }
}());
(function () {
    'use strict';

    angular.module('assessment.publishSettings')
           .provider('publishSettings', settingsProvider);

    function settingsProvider() {
        var settings;

        return {
            setSettings: function (value) {
                settings = value;
            },
            $get: function () {
                return settings;
            }
        };
    }
}());
(function () {
    'use strict';

    angular.module('assessment.publishSettings')
           .provider('publishModules', modulesProvider);

    function modulesProvider() {
        var modules;

        return {
            set: function (value) {
                modules = value;
            },
            $get: function () {
                return modules;
            }
        };
    }
}());
(function () {
    'use strict';

    angular.module('bootstrapping', []).run(runBlock);

    runBlock.$inject = ['$q', 'detectDeviceTask', 'readSettingsTask', 'preloadHtmlTask',
        'authenticationTask', 'fixIEScrollTask', 'publishModuleLoader'];

    function runBlock($q, detectDeviceTask, readSettingsTask, preloadHtmlTask,
        authenticationTask, fixIEScrollTask, publishModuleLoader) {
        var tasks = {
            'detectDeviceTask': detectDeviceTask,
            'fixIEScrollTask': fixIEScrollTask,
            'readSettings': readSettingsTask,
            'authenticationTask': authenticationTask,
            'preloadHtmlTask': preloadHtmlTask
        };

        $q.all(tasks).then(function (data) {
            var bootstrapModules = ['assessment'],
                settings = data.readSettings,
                publishSettings = settings.publishSettings,
                publishModules = publishSettings.modules,
                user = data.authenticationTask,
                preloadHtmls = data.preloadHtmlTask,
                promises = [];

            var hasLms = false;
            if (publishSettings && publishSettings.modules) {
                _.each(publishModules, function (module) {
                    !hasLms && (hasLms = module.name === 'lms');

                    promises.push(publishModuleLoader.load(module.name).then(function (moduleInstance) {
                        return moduleInstance;
                    }, function () {
                        throw 'Cannot load publish module "' + module.name + '".';
                    }));
                });
            }

            $q.all(promises).then(function (publishModules) {
                angular.module('assessment').config(['$routeProvider', 'settingsProvider', 'htmlTemplatesCacheProvider', 'userProvider', '$translateProvider',
                    function ($routeProvider, settingsProvider, htmlTemplatesCacheProvider, userProvider, $translateProvider) {
                        settingsProvider.setSettings(settings.templateSettings);
                        userProvider.set(user);
                        if (publishModules && publishModules.length > 0) {
                            _.each(publishModules, function (module) {
                                if (_.isObject(module.userInfoProvider)) {
                                    userProvider.use(module.userInfoProvider);
                                }
                            });
                        }
                        htmlTemplatesCacheProvider.set(preloadHtmls);

                        $translateProvider
                            .translations('xx', settings.translations)
                            .preferredLanguage('xx');

                        window.WebFontLoader && WebFontLoader.load(settings.templateSettings.fonts, settings.manifest, publishSettings);
                        window.LessProcessor && LessProcessor.load(settings.templateSettings.colors, settings.templateSettings.fonts);
                    }]);

                if (!settings || !settings.templateSettings || _.isEmpty(settings.templateSettings) || (settings.templateSettings.xApi && settings.templateSettings.xApi.enabled)) {
                    bootstrapModules.push('assessment.xApi');

                    if (settings.templateSettings.webhooks && settings.templateSettings.webhooks.url) {
                        bootstrapModules.push('assessment.webhooksResultsSender');
                    }
                }

                if (publishSettings) {
                    angular.module('assessment.publishSettings').config(['publishSettingsProvider', 'publishModulesProvider', function (publishSettingsProvider, publishModulesProvider) {
                        publishSettingsProvider.setSettings(publishSettings);
                        publishModulesProvider.set(publishModules);
                    }]);

                    bootstrapModules.push('assessment.publishSettings');
                }

                if (!hasLms) {
                    bootstrapModules.push('assessment.progressStorer');
                }

                bootstrapModules.push('assessment.ltiResultsSender');

                angular.bootstrap(document, bootstrapModules);
            });
        });
    }
}());

(function () {
  'use strict';

  angular.module('bootstrapping').service('localLoader', localLoader);

  localLoader.$inject = ['$http'];

  function localLoader($http) {
    let expires = +new Date();

    return {
      setCacheBuster: setCacheBuster,
      getCacheBuster: getCacheBuster,
      getLocalResource: getLocalResource
    };

    function setCacheBuster(cacheBuster) {
      if (!cacheBuster) {
        return;
      }
      expires = +Date.parse(cacheBuster);
    }

    function getCacheBuster() {
      return expires;
    }

    function getLocalResource(requestOptions) {
      const url = requestOptions.url;
      const cache = requestOptions.cache || true;
      const headers = requestOptions.headers;
      const dataType = requestOptions.dataType;
      const contentType = requestOptions.contentType;

      return $http.get(url, {
        cache: cache,
        headers: _buildHeaders(headers, cache),
        params: {
          _: expires
        },
        dataType: dataType,
        contentType: contentType
      });
    }

    function _buildHeaders(headers, cacheBuster) {
      headers = headers || {};
      cacheBuster = cacheBuster || false;
      const _headers = Object.assign({}, headers);
      const CACHE_EXPIRATION_DATE_IN_SECONDS = 2678400;
      if (cacheBuster) {
        _headers['Cache-Control'] = 'public';
        _headers.Pragma = 'public';
        _headers.Expires = CACHE_EXPIRATION_DATE_IN_SECONDS;
      } else {
        _headers['Cache-Control'] = 'no-cache';
        _headers.Pragma = 'no-cache';
        _headers.Expires = 0;
      }

      return _headers;
    }
  }
})();

(function () {
    'use strict';
    angular.module('bootstrapping')
           .service('fileReadingService', FileReadingService);

    FileReadingService.$inject = ['$q', 'localLoader'];

    function FileReadingService($q, localLoader) {
        var that = this;

        that.readHtml = function (url) {
            return read(url, function (html) {
                return _.isString(html) ? html : null;
            });
        };

        function read(url, callback) {
            var defer = $q.defer();
            localLoader.getLocalResource({ url , cache: true}).success(function (response) {
                var result = callback.call(this, response);
                defer.resolve(result);
            }).error(function () {
                defer.resolve(null);
            });

            return defer.promise;
        }
    }
}());
(function () {
    'use strict';
    angular.module('bootstrapping')
           .service('publishModuleLoader', PublishModuleLoader);

    PublishModuleLoader.$inject = ['$q', '$window'];

    function PublishModuleLoader($q, $window) {
        var that = this;

        that.load = function(moduleName) {
            var deferred = $q.defer(),
                scriptLoader;
            
            /* jshint ignore:start */
            scriptLoader = $script;
            /* jshint ignore:end */

            var dependencyName = 'publishModule';
            scriptLoader('includedModules/' + moduleName + '.js', dependencyName);

            scriptLoader.ready(dependencyName, onScriptReady);

            return deferred.promise;

            function onScriptReady() {
                if ($window[moduleName]) {
                    deferred.resolve($window[moduleName]);
                } else {
                    deferred.reject();
                }
            }
        };
    }
}());
(function () {
    'use strict';

    angular.module('bootstrapping')
        .service('preloadImagesService', preloadImagesService);

    preloadImagesService.$inject = ['$q'];

    function preloadImagesService($q) {
        var that = this;

        that.preloadImage = function(key, img) {
            var defer = $q.defer(),
                image = new Image();

            img.hasOwnProperty('attributes') && img.attributes.forEach(function (attr) {
                image.setAttribute(attr.key, attr.value);
            });

            image.onload = function() {
                defer.resolve({key: key, image: image});
            }

            image.onerror = function() {
                defer.resolve({key: key, image: image});
            }

            image.src = img.src;

            return defer.promise;
        }

        that.preloadImages = function(images) {
            var promises = [],
                defer = $q.defer();

            for(var key in images) {
                promises.push(that.preloadImage(key, images[key]));
            }

            $q.all(promises).then(function(imagesArr){
                var result = {};

                for(var i = 0; i < imagesArr.length; i++) {
                    result[imagesArr[i].key] = imagesArr[i].image;
                }

                defer.resolve(result);
            })

            return defer.promise;
        }
    }
})();
(function () {
    'use strict';

    angular.module('bootstrapping').factory('detectDeviceTask', detectDeviceTask);

    detectDeviceTask.$inject = ['$q', '$document'];

    function detectDeviceTask($q, $document) {
        var body = $document[0].body;
        body.className = body.className + (isMobileDevice() ? ' touch' : ' no-touch');
        return $q.when();
    }

    function isMobileDevice() {
        var ua = navigator.userAgent.toLowerCase();
        return ua.indexOf('ipod') !== -1 || ua.indexOf('iphone') !== -1 || ua.indexOf('ipad') !== -1 || ua.indexOf('android') !== -1;
    }

}());
(function () {
  'use strict';

  angular.module('bootstrapping').factory('readSettingsTask', readSettingsTask);

  readSettingsTask.$inject = ['$q', 'preloadImagesService', 'localLoader'];

  function readSettingsTask($q, preloadImagesService, localLoader) {
    return localLoader
      .getLocalResource({ url: 'content/data.js', cache: false })
      .success(function (response) {
        localLoader.setCacheBuster(response.createdOn);
      })
      .then(function () {
        return ConfigurationReader.read('', localLoader.getCacheBuster()).then(function (settings) {
          var mergedSettings = ConfigurationReader.init(settings);

          for (var prop in mergedSettings) {
            settings[prop] = mergedSettings[prop];
          }

          if (settings.templateSettings.showUserNameOnResultsPage === undefined) {
            settings.templateSettings.showUserNameOnResultsPage = true;
          }

          var imagesToPreload = {};
          var backgroundSettings = settings.templateSettings.background;

          if (backgroundSettings.body.texture) {
            imagesToPreload.texture = {
              src: backgroundSettings.body.texture,
              attributes: [
                {
                  key: 'crossOrigin',
                  value: 'Anonymous'
                }
              ]
            };
          }

          if (backgroundSettings.header.image && backgroundSettings.header.image.url) {
            imagesToPreload.header = {
              src: backgroundSettings.header.image.url
            };
          }

          if (settings.templateSettings.logo && settings.templateSettings.logo.url) {
            imagesToPreload.logo = {
              src: settings.templateSettings.logo.url
            };
          }

          return preloadImagesService.preloadImages(imagesToPreload).then(function (images) {
            var colors = settings.templateSettings.colors,
              brightness = backgroundSettings.body.brightness,
              colorObj = {
                r: 0,
                g: 0,
                b: 0
              };

            if (backgroundSettings.body.texture) {
              colorObj = getAverageRGB(images.texture);
            } else if (backgroundSettings.body.color) {
              colorObj = hexToRgb(one.color(backgroundSettings.body.color).hex());
            }

            if (brightness) {
              setBrightnessToRgb(colorObj, brightness);
            }

            var color = {
              key: '@content-body-color',
              value: 'rgb(' + colorObj.r + ',' + colorObj.g + ',' + colorObj.b + ')'
            };

            colors.push(color);

            return settings;
          });
        });
      });
  }

  function getAverageRGB(imgEl) {
    var blockSize = 5, // only visit every 5 pixels
      defaultRGB = { r: 0, g: 0, b: 0 }, // for non-supporting envs
      canvas = document.createElement('canvas'),
      context = canvas.getContext && canvas.getContext('2d'),
      data,
      width,
      height,
      i = -4,
      length,
      rgb = { r: 0, g: 0, b: 0 },
      count = 0;

    if (!context) {
      return defaultRGB;
    }

    height = canvas.height = imgEl.naturalHeight > 100 ? 100 : imgEl.naturalHeight;
    width = canvas.width = imgEl.naturalWidth > 100 ? 100 : imgEl.naturalWidth;

    try {
      context.drawImage(imgEl, 0, 0);
      data = context.getImageData(0, 0, width, height);
    } catch (e) {
      console.warn("Canvas doesn't work (maybe texture image on a different domain), default title color applied.");
      return defaultRGB;
    }

    length = data.data.length;

    while ((i += blockSize * 4) < length) {
      ++count;
      rgb.r += data.data[i];
      rgb.g += data.data[i + 1];
      rgb.b += data.data[i + 2];
    }

    rgb.r = Math.floor(rgb.r / count);
    rgb.g = Math.floor(rgb.g / count);
    rgb.b = Math.floor(rgb.b / count);

    return rgb;
  }

  function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    return result
      ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16)
        }
      : null;
  }

  function setBrightnessToRgb(rgb, brightness) {
    rgb.r += brightness > 0 ? (255 - rgb.r) * brightness : rgb.r * brightness;
    rgb.g += brightness > 0 ? (255 - rgb.g) * brightness : rgb.g * brightness;
    rgb.b += brightness > 0 ? (255 - rgb.b) * brightness : rgb.b * brightness;
  }
})();

(function () {
    'use strict';

    angular.module('bootstrapping').factory('preloadHtmlTask', preloadHtmlTask);

    preloadHtmlTask.$inject = ['$q', 'fileReadingService'];

    var htmlStack = [
        'app/views/widgets/tooltip.html',
        'app/views/main.html',
        'app/views/hint.html',
        'app/views/dragAndDropText.html',
        'app/views/fillInTheBlanks.html',
        'app/views/hotspot.html',
        'app/views/multipleSelectText.html',
        'app/views/singleSelectImage.html',
        'app/views/singleSelectText.html',
        'app/views/statement.html',
        'app/views/textMatching.html',
        'app/views/statementItem.html',
        'app/views/openQuestion.html'
    ];

    function preloadHtmlTask($q, fileReadingService) {
        var dfr = $q.defer(),
            promises = [],
            templates = [];

        htmlStack.forEach(function(url) {
            promises.push(fileReadingService.readHtml(url).then(function (response) {
                templates.push({
                    key: url,
                    value: response
                });
            }));
        });

        $q.all(promises).then(function() {
            dfr.resolve(templates);
        });

        return dfr.promise;
    }
}());

(function () {
    'use strict';

    angular.module('bootstrapping').factory('authenticationTask', authenticationTask);

    authenticationTask.$inject = ['$q'];

    function authenticationTask($q) {
        var dfr = $q.defer(),
            username = getQueryStringValue('name'),
            email = getQueryStringValue('email');

        dfr.resolve({ username: username, email: email });

        return dfr.promise;
    }

    function getQueryStringValue(key) {	
        var urlParams = window.location.search;	
        var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");	
        var results = regex.exec(urlParams);	
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));	
    }
    
}());

(function () {
    'use strict';

    angular.module('bootstrapping').service('fixIEScrollTask', fixIEScrollTask);

    fixIEScrollTask.$inject = ['$window'];

    function fixIEScrollTask($window) {
        if($window.navigator.userAgent.match(/Trident\/7\./) 
            || $window.navigator.userAgent.match(/Edge/i)) { // if IE or Edge
                $('body').on("mousewheel", function() {
                    // remove default behavior
                    event.preventDefault(); 

                    //scroll without smoothing
                    var wheelDelta = event.wheelDelta;
                    var currentScrollPosition = $window.pageYOffset;
                    $window.scrollTo(0, currentScrollPosition - wheelDelta);
                });

                $('body').on("keydown", function(event) {
                    var currentScrollPosition = $window.pageYOffset,
                        wheelDelta = 40;

                    if(event.keyCode == 38) {                        
                        $window.scrollTo(0, currentScrollPosition - wheelDelta);

                        event.preventDefault();
                    } else if(event.keyCode == 40) {
                        $window.scrollTo(0, currentScrollPosition + wheelDelta);
                        
                        event.preventDefault(); 
                    }
                });
        }
    }

}());
(function () {
    'use strict';

    angular.injector(['ng', 'bootstrapping']).invoke(function () { });

}());
(function () {
	"use strict";
	
    angular.module('assessment')
		.service('translate', translate);
	
        translate.$inject = ['$translate'];
	
	function translate($translate) {
		return {
			get: getTranslation,
		};

		function getTranslation(key, replacementKey, replacement) {
            var translation = $translate.instant(key);

            return replacementKey && replacement ? translation.replace(replacementKey, replacement) : translation;
        }
	}
	
})();
(function () {
    "use strict";

    angular.module('assessment')
          .service('urlHelper', urlHelper);

    function urlHelper() {
        return {
            getQueryStringValue: function(key) {
                var urlParams = window.location.search;
                var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
                var results = regex.exec(urlParams);
                return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            },
            addQueryStringParameter: function(url, key, value) {
                value = encodeURIComponent(value);
                
                var regex = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                if (url.match(regex)) {
                    return url.replace(regex, '$1' + key + '=' + value + '$2');
                } else {
                    return url + separator + key + '=' + value;
                }
            }
        };
    }
}());
(function () {
  'use strict';

  angular.module('assessment').service('resourceLoader', resourceLoader);

  resourceLoader.$inject = ['$http'];

  function resourceLoader($http) {
    let expires = +new Date();

    return {
      setCacheBuster: setCacheBuster,
      getCacheBuster: getCacheBuster,
      getLocalResource: getLocalResource
    };

    function setCacheBuster(cacheBuster) {
      if (!cacheBuster) {
        return;
      }
      expires = +Date.parse(cacheBuster);
    }

    function getCacheBuster() {
      return expires;
    }

    function getLocalResource(requestOptions) {
      const url = requestOptions.url;
      const cache = requestOptions.cache || true;
      const headers = requestOptions.headers;
      const dataType = requestOptions.dataType;
      const contentType = requestOptions.contentType;

      return $http.get(url, {
        cache: cache,
        headers: _buildHeaders(headers, cache),
        params: {
          _: expires
        },
        dataType: dataType,
        contentType: contentType
      });
    }

    function _buildHeaders(headers, cacheBuster) {
      headers = headers || {};
      cacheBuster = cacheBuster || false;
      const _headers = Object.assign({}, headers);
      const CACHE_EXPIRATION_DATE_IN_SECONDS = 2678400;
      if (cacheBuster) {
        _headers['Cache-Control'] = 'public';
        _headers.Pragma = 'public';
        _headers.Expires = CACHE_EXPIRATION_DATE_IN_SECONDS;
      } else {
        _headers['Cache-Control'] = 'no-cache';
        _headers.Pragma = 'no-cache';
        _headers.Expires = 0;
      }

      return _headers;
    }
  }
})();
