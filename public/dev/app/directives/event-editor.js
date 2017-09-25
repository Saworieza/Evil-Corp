app.directive('lkrEventEditor', function($locale) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/event-editor.html',
        scope: {
            data: '=data',
            errors: '=errors'
        },

        link: function(scope, element, attrs) {

            scope.dateTimeFormat = $locale.DATETIME_FORMATS.shortDate + ' H:mm';
            scope.dateFormat = $locale.DATETIME_FORMATS.shortDate;

            scope.eventTypes =
                [
                 translations['CONFERENCE'],
                 translations['CONVENTION'],
                 translations['EXAM'],
                 translations['EXPO'],
                 translations['GAME'],
                 translations['MEETING'],
                 translations['MILESTONE'],
                 translations['PARTY'],
                 translations['SEMINAR']
                ];

            scope.$watch('data.end_date', function() {
                scope.errors = {};
            })

            scope.$watch('data.start_date', function() {
                scope.errors = {};
                if(scope.data.start_date)
                    if(!scope.data.end_date || scope.data.end_date < scope.data.start_date) {
                        var d = new Date();
                        d = scope.data.start_date;
                        d.setHours( d.getHours()+1)
                        scope.data.end_date = d ;

                    }
            });


        }
    }
})