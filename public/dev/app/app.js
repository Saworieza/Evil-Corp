var app = angular.module('app', ['ngSanitize','ngAnimate', 'ui.router','angularSpinner', 'ui.calendar',
    'mgcrea.ngStrap', 'mentio', 'datePicker','monospaced.elastic','infinite-scroll','checklist-model',
    'pasvaz.bindonce', 'angularFileUpload', 'pascalprecht.translate', 'angular-growl', 'ngTagsInput', 'treeControl']);

app.config(['growlProvider', '$httpProvider', function (growlProvider, $httpProvider) {
    growlProvider.globalTimeToLive(3000);


    $httpProvider.defaults.headers.common['Token'] = token;

    var interceptor = ['$rootScope', '$q', function (scope, $q, Base64) {
        function success(response) {
            return response;
        }
        function error(response) {
            var status = response.status;
            if (status == 401) {

                location.hash = '';
               // location.reload(true);
             console.log('401', response);
                return;
            }
            return $q.reject(response);
        }
        return function (promise) {
            return promise.then(success, error);
        }
    }];

    $httpProvider.responseInterceptors.push(interceptor);

}]);
angular.module('infinite-scroll').value('THROTTLE_MILLISECONDS', 250)

app.config(function($stateProvider, $urlRouterProvider) {
    //
    // For any unmatched url, redirect to /state1
    $urlRouterProvider.otherwise("/home");
    //

    angular.forEach(modules, function (module) {
        $stateProvider
            .state('space.' + module.moduleName, {
                url: "/" + module.moduleName,
                templateUrl: "modules/" + module.moduleName + "/index.html",
                controller: module.moduleName + 'Ctrl'
            })

    })


    $stateProvider
        .state('home', {
            url: "/home",
            templateUrl: "pages/home.html",
            controller: 'homeCtrl'
        })

        .state('space', {
            url: "/space/:spaceCode",
            abstract: true,
            templateUrl: "pages/space.html",
            controller: 'spaceCtrl'
        })

        .state('space.stream', {
            url: "",
            templateUrl: "pages/space-stream.html",
            controller: "spaceStreamCtrl"
        })

        .state('space.files', {
            url: "/files",
            templateUrl: "pages/space-files.html",
            controller: 'spaceFilesCtrl'
        })

        .state('space.wiki', {
            url: "/wiki",
            templateUrl: "pages/space-wiki.html",
            controller: 'spaceWikiCtrl'
        })

        .state('space.tasks', {
            url: "/tasks",
            templateUrl: "pages/space-tasks.html",
            controller: 'tasksCtrl'
        })


        .state('space.admin', {
            url: "/admin",
            templateUrl: "pages/space-admin.html",
            controller: 'spaceAdminCtrl'
        })



        .state('search', {
            url: "/search/{query}",
            templateUrl: "pages/search.html",
            controller: 'searchCtrl'
        })


        .state('post', {
            url: "/post/{contentId}",
            templateUrl: "pages/post.html",
            controller: 'postCtrl'
        })

        .state('people.test', {
            url: "/test",
            templateUrl: "",
            controller: 'peopleCtrl'
        })
        .state('messages', {
            url: "/messages",
            templateUrl: "pages/messages.html",
            controller: "messagesCtrl"
        })

        .state('admin', {
            url: "/admin",
            templateUrl: "pages/admin.html",
            controller: "adminCtrl"
        })

        .state('profile', {
            url: "/profile",
            templateUrl: "pages/profile.html",
            controller: "profileCtrl"
        })
        .state('userprofile', {
            url: "/userprofile/:userid",
            templateUrl: "pages/userprofile.html",
            controller: "userprofileCtrl"
        })



    if (!h_tasks)
    {
        $stateProvider.state('tasks', {
            url: "/tasks",
            templateUrl: "pages/tasks.html",
            controller: 'tasksCtrl'
        })
            .state('task', {
                url: "/task/:taskId",
                templateUrl: "pages/single-task.html",
                controller: 'singleTaskCtrl'})

    }


    if (!h_cal) {
        $stateProvider.state('calendar', {
            url: "/calendar",
            templateUrl: "pages/calendar.html",
            controller: "calendarCtrl"
        });
    }

    if (!h_people) {
        $stateProvider.state('people', {
            url: "/people",
            templateUrl: "pages/people.html",
            controller: 'peopleCtrl'
        })
    }
});



app.service("$previousState",
    [ '$rootScope', '$state',
        function($rootScope, $state) {
            var previous = null;
            var memos = {};

            var lastPrevious = null;

            $rootScope.$on("$stateChangeStart", function(evt, toState, toStateParams, fromState, fromStateParams) {
                lastPrevious = previous;
                previous = { state: fromState, params: fromStateParams };

            });

            $rootScope.$on("$stateChangeError", function() {
                previous = lastPrevious;
                lastPrevious = null;
            });

            $rootScope.$on("$stateChangeSuccess", function() {
                lastPrevious = null;
            });

            var $previousState = {
                get: function(memoName) {
                    return memoName ? memos[memoName] : previous;
                },
                go: function(memoName) {
                    var to = $previousState.get(memoName);
                    console.log(to);
                    console.log($state);
                    if(to.state.name =='') return $state.go('home');

                    if(to.state.name != $state.current.name)
                    {
                        return $state.go(to.state, to.params)
                    } else {
                        return $state.go('home');
                    }
                },
                memo: function(memoName) {
                    memos[memoName] = previous;
                }
            };

            return $previousState;
        }]);

app.run(['$previousState', function($previousState) {
    "use strict";
    // Inject in .run so it can register $rootScope.$on.
}]);

app.filter('htmlToPlaintext', function() {
        return function(text) {
            return String(text).replace(/<[^>]+>/gm, '');
        }
    }
);

app.filter('groupBy', ['$parse', function ($parse) {
    return function (list, group_by) {

        var filtered = [];
        var prev_item = null;
        var group_changed = false;
        var new_field = 'group_by_CHANGED';
        angular.forEach(list, function (item) {
            group_changed = false;
            if (prev_item !== null) {
                group_by = angular.isArray(group_by) ? group_by : [group_by];

                //check each group by parameter
                for (var i = 0, len = group_by.length; i < len; i++) {
                    if ($parse(group_by[i])(prev_item) !== $parse(group_by[i])(item)) {
                        group_changed = true;
                    }
                }
            }
            else {
                group_changed = true;
            }
            if (group_changed) {
                item[new_field] = true;
            } else {
                item[new_field] = false;
            }

            filtered.push(item);
            prev_item = item;

        });

        return filtered;
    };
}]);

app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});

app.config(['$translateProvider', function ($translateProvider) {

    // Simply register translation table as object hash
    $translateProvider.translations('u', translations);
    $translateProvider.preferredLanguage('u');
}]);


