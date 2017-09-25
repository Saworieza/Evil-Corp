app.directive('lkrEventViewer', function(formatterService, apiService, $locale) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/event-viewer.html',
        replace : true,
        scope: {
            data: '=data',
            calendar: '=calendar',
            author: '=author',
            contentid: '=contentid'
        },
        link: function(scope, element, attrs) {
            scope.month = moment(scope.data.start_date).format('MMM');
            scope.day = moment(scope.data.start_date).format('D');
            scope.myAtt = null;
            scope.dateTimeFormat = $locale.DATETIME_FORMATS.shortDate + ' H:mm';
            scope.dateFormat = $locale.DATETIME_FORMATS.shortDate;
            scope.new = {start_date: null, end_date:null, all_day: false};

            scope.formatter = formatterService;

            scope.changeOkClick = function()
            {
                scope.errors = {};
                if(! moment(scope.new.start_date).isValid()) {
                    scope.errors['event.start_date'] = 'Invalid date';
                    return;

                }
                if(! moment(scope.new.end_date).isValid()) {
                    scope.errors['event.end_date'] = 'Invalid date';
                    return;
                }

                if(scope.new.all_day) {
                    scope.new.start_date.setHours(12,00,00);
                    scope.new.end_date.setHours(12,00,00);
                }

                if(scope.new.end_date < scope.new.start_date) {
                    scope.errors['event.end_date'] = 'Invalid date';
                    return;
                }

                apiService.put(['event', scope.contentid], scope.new).then(
                    function(res){
                        scope.data.start_date = scope.new.start_date;
                        scope.data.end_date   = scope.new.end_date;
                        scope.data.all_day    = scope.new.all_day;
                        scope.editing = false;
                        scope.month = moment(scope.data.start_date).format('MMM');
                        scope.day = moment(scope.data.start_date).format('D');
                        scope.att = {0: {total:0, users: ''}, 1: {total:0, users: ''}, 2: {total:0, users: ''}};
                        scope.myAtt = null;
                        if(scope.data.all_day==1)
                        {
                            scope.fullDate = moment(scope.data.start_date).format('dddd, LL');
                            scope.endDate =  moment(scope.data.end_date).format('dddd, LL');

                        } else {

                            scope.fullDate = formatterService.longDate(scope.data.start_date);
                            scope.endDate =  formatterService.longDate(scope.data.end_date);
                        }
                    }
                )

            }

            scope.editClick = function()
            {
                scope.editing = true;
            }

            scope.init = function()
            {
                scope.att = {0: {total:0, users: ''}, 1: {total:0, users: ''}, 2: {total:0, users: ''}};
                if( scope.calendar != null)
                {
                    if( angular.isDefined(scope.calendar.attendants))
                    {
                      angular.forEach(scope.calendar.attendants, function(att){
                         scope.att[att.attending].total = scope.att[att.attending].total + 1;
                         if(scope.att[att.attending].users !='') scope.att[att.attending].users = scope.att[att.attending].users + '<br>';
                          scope.att[att.attending].users = scope.att[att.attending].users + att.user.full_name;
                          if(att.user.id == user.id)
                          {
                              scope.myAtt = att.attending;
                          }
                       })
                    }
                }
            }

            if(scope.data.all_day==1)
            {
                scope.fullDate = moment(scope.data.start_date).format('dddd, LL');
                scope.endDate =  moment(scope.data.end_date).format('dddd, LL');

            } else {

                scope.fullDate = formatterService.longDate(scope.data.start_date);
                scope.endDate =  formatterService.longDate(scope.data.end_date);
            }

            scope.assistClick = function(value)
            {
                apiService.get(['assist', scope.calendar.calendarable_id, parseInt(value)]).then(
                    function(res){
                        scope.calendar.attendants = res.attendants;
                        scope.init();
                    }
                )

            }

            scope.init();
        }

    }
})