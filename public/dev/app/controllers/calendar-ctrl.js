app.controller('calendarCtrl', function($scope, spaceFactory, formatterService,  $timeout, apiService, $modal, contentService  ){

    $scope.formatter = formatterService;
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $scope.eventSources = [
        {events: [], color: '#008BBC', textColor: 'white'},
        {events: [], color: '#E862AC', textColor: 'white'}
    ] ;

    $scope.renderCalender = function(calendar) {
        calendar.fullCalendar('render');
    };

    $scope.alertOnEventClick = function( event, allDay, jsEvent, view ){
        if(event.type == 'event')
        {
            contentService.getOne(event.id).then(function(data){
                $scope.event = data;
                var eventModal = $modal({template: 'partials/modal-event.html', scope: $scope, animation: 'am-fade-and-slide-top', show: true});

            })

        } else if(event.type == 'task'){
            apiService.get(['task', event.id]).then(function(data){
                $scope.task = data;
                var taskModal = $modal({template: 'partials/modal-task.html', scope: $scope, animation: 'am-fade-and-slide-top', show: true});

            })
        }
    };

    $scope.eventSources = [
        {events:[], color: '#008BBC', textColor: 'white'},
        {events:[], color: '#E862AC', textColor: 'white'}
    ];


    $scope.loadEvents = function()
    {
        apiService.get('calendar').then(
            function(res){
                for(i=0; i<res.events.length;++i)
                {
                    var st = new Date(res.events[i].start);
                    var en = new Date(res.events[i].end);
                    $scope.eventSources[0].events.push(
                        {title: res.events[i].title,
                         start: st,
                         end: en,
                         allDay: res.events[i].allDay,
                         id: res.events[i].id,
                         type: 'event'

                        });
                }

                for(i=0; i<res.tasks.length;++i) {

                    var st = new Date(res.tasks[i].start);
                    var en = new Date(res.tasks[i].end);
                    $scope.eventSources[1].events.push(
                        {
                            title: res.tasks[i].title,
                            start: st,
                            end: en,

                            allDay: res.tasks[i].allDay,
                            id: res.tasks[i].id,
                            type: 'task'
                        });

                }

            }
        )
    }

    $scope.renderCalender = function(calendar) {
        calendar.fullCalendar('render');
    };

    $scope.loadEvents();

    $scope.uiConfig = {
        calendar:{
            editable: false,
            height: 750,
            lang: locale,
            header:{
                left: 'title',
                center: '',
                right: 'today prev,next'
            },
            eventClick: $scope.alertOnEventClick
        }
    };


})




