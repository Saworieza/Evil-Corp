app.directive('lkrTaskList', function($timeout, formatterService, $stateParams) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/task-list.html',
        scope: {
            tasks: '=tasks',
            contentId: '@',
            filter: '='
        },

        link: function(scope, element, attrs) {
            scope.newTaskPanel = -1;
            scope.spaceCode = $stateParams.spaceCode;
            scope.prepareNewTask = function()
            {
                scope.newTask = {user_id: user.id, priority:1, state:0, space_code:scope.spaceCode, files:[], error:[]};

            }

            scope.$on('taskMenuChanged', function(ev){
                scope.newTaskPanel = -1;
            })

            scope.list = {field : 'due_date_group'};


            scope.getGroupText = function(task) {

                if(scope.list.field == '') return;

                if(scope.list.field == 'state') {

                    var states =[translations['NOT_STARTED'], translations['IN_PROGRESS'], translations['SUSPENDED'], translations['COMPLETED'], translations['CANCELED']];
                    return states[ task.state];
                }

                if(scope.list.field == 'priority') {

                    var prs =[translations['LOW'], translations['NORMAL'], translations['HIGH'], translations['CRITICAL'] ];
                    return prs[ task.priority];
                }

                if(scope.list.field == 'due_date_group') {

                    var dd = [translations['OVERDUE'], translations['TODAY'], translations['TOMORROW'], translations['NEXT_7_DAYS'], translations['LATER'], translations['SOMEDAY']];
                    return dd[task.due_date_group];
                }
                if(scope.list.field == 'space') {

                    return task.space;
                }

                return "";

            }


            scope.formatter = formatterService;

            scope.$on('taskInsertEvent', function(e, task){

                scope.tasks.push(task);
                scope.newTaskPanel = -1;

            });

            scope.prepareNewTask();

            scope.$on('newTaskClickEvent', function(){
                scope.newTaskClick();
            })

            scope.newTaskClick = function(){
                if(scope.newTaskPanel == -1)
                {
                    scope.prepareNewTask();
                    scope.newTaskPanel = 0;

                } else {
                    scope.newTaskPanel = -1;

                }
            }

            scope.$on('taskDeleteEvent', function(e, taskId)
            {
                scope.taskListPanel = -1;
                $timeout(function(){
                    var i = findWithAttr(scope.tasks, 'id', taskId);
                    if(i >= 0) scope.tasks.splice(i,1);
                    scope.getCounters();

                },100)
            })

            scope.$on('taskArchiveEvent', function(e, taskId){
                scope.taskListPanel = -1;
                $timeout(function(){
                    var i = findWithAttr(scope.tasks, 'id', taskId);
                    if(i >= 0) scope.tasks.splice(i,1);
                    scope.getCounters();


                },100)
            })


            scope.$on('taskCancelEvent', function(){
                scope.newTaskPanel = -1;

            } )




        }

    }})