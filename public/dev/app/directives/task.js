app.directive('lkrTask', function($q, apiService, formatterService, $stateParams) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/task.html',
        scope: {
            task: '=task',
            contentId : '@'

        },

        link: function(scope, element, attrs) {

            scope.spaceCode = $stateParams.spaceCode;

            scope.noComments = angular.isDefined(attrs.noComments);

            scope.$watch('task', function(val){
                if(val)
                {
                    scope.edit = {editing : '',
                        owner: scope.task.user_id == user.id,
                        newRecord : angular.isDefined(attrs.newRecord),
                        error : [],
                        task: {}
                    };
                    jQuery.extend(scope.edit.task, scope.task);
                }
            }, true);

            scope.fileEditorId = 'feNewTask'+scope.contentId+scope.spaceCode;

            scope.formatter = formatterService;

            scope.states = [
                {id: 0, caption: translations['NOT_STARTED'], icon:'fa-stop'},
                {id: 1, caption: translations['IN_PROGRESS'], icon:'fa-play'},
                {id: 2, caption: translations['SUSPENDED'], icon:'fa-pause'},
                {id: 3, caption: translations['COMPLETED'], icon: 'fa-check'},
                {id: 4, caption: translations['CANCELED'], icon: 'fa-times'}];

            scope.priorities = [
                {id: 0, caption: translations['LOW']},
                {id: 1, caption: translations['NORMAL']},
                {id: 2, caption: translations['HIGH']},
                {id: 3, caption: translations['CRITICAL']}];


            scope.editClick = function(fieldName)
            {
                scope.edit.task = JSON.parse(JSON.stringify(scope.task));
                scope.edit.error = {};
                scope.edit.editing = fieldName;
            }

            scope.archiveTaskClick = function()
            {
                apiService.get(['task/archive', scope.task.id]).then(
                    function(res){

                        scope.$emit('taskArchiveEvent', scope.task.id );
                        scope.$emit('taskUpdateCounters');
                    });
            }

            scope.confirmDeleteClick = function(){
                apiService.delete(['task', scope.task.id]).then(
                    function(res){
                        scope.$emit('taskDeleteEvent', scope.task.id);
                        scope.$emit('taskUpdateCounters');
                    });
            }

            scope.cancelClick = function(){
                scope.edit.error = [];
                scope.$emit('taskCancelEvent');
            }

            scope.postClick = function(){
                var payLoad = scope.task;
                if(scope.contentId != ''){
                    payLoad['content_id'] = scope.contentId;
                }

                if(angular.isDefined($stateParams.spaceCode) )
                {
                    payLoad['space_code'] = $stateParams.spaceCode;
                }

                apiService.post(['task'] , payLoad).then(
                    function(res){

                           scope.$emit('taskInsertEvent', res);
                           scope.$emit('taskUpdateCounters');

                    }, function(err){
                            scope.edit.error = err;

                    });
            }

            scope.updateRadio = function(fieldName, valueId)
            {
                if(scope.edit.newRecord)
                {
                    scope.task[fieldName] = valueId;

                } else {
                    apiService.put(['task',scope.task.id] ,{field:fieldName, value: valueId}).then(
                        function(res){
                            scope.edit.editing = '';

                            scope.task[fieldName] = valueId;
                            scope.$emit('taskUpdateCounters');

                        }, function(err){
                            scope.edit.error = err;

                        });
                }
            }

            scope.updateField = function(fieldName) {
                if(angular.isUndefined(scope.edit.task[fieldName])) scope.edit.task[fieldName] = null;

                apiService.put(['task',scope.task.id] ,{field:fieldName, value: scope.edit.task[fieldName]}).then(
                function(res){
                    if(fieldName == 'assigned_to')
                    {
                        scope.task['assigned_to'] = res.assigned_to;
                        scope.task['assignments'] = res.assignments;
                        scope.task['my'] = res.my;
                        scope.task['delegated'] = res.delegated;


                    } else if(fieldName == 'due_date')  {
                        scope.task[fieldName] = scope.edit.task[fieldName];
                        scope.task['due_date_group'] = res.due_date_group;

                    } else  {
                        if(angular.isUndefined(scope.edit.task[fieldName] )){
                            scope.task[fieldName] = null;

                        } else {
                            scope.task[fieldName] = scope.edit.task[fieldName];
                        }
                    }
                    scope.$emit('taskUpdateCounters');

                    scope.edit.editing = '';

                }, function(err){
                        scope.edit.error = err;

                });
            }
            scope.listAssignments = function(items){
                if(items && items.length > 0)
                   return _.pluck(items, 'full_name').join(', ');
            }

            scope.loadUsers = function(ini)
            {
                if(ini.length < 3) return;
                return apiService.get(['user'],{ini: ini});
            }

            scope.allowEdit = function(fieldName){
                if(fieldName == 'state')
                {
                    if(scope.task.user_id == user.id){
                        return true;
                    } else {
                        for(var i=0; i<scope.task.assigned_to.length; i++){
                            if(scope.task.assigned_to[i].id == user.id)
                            {
                                return true;
                            }
                        }
                        return false;

                    }
                } else {

                   return scope.task.user_id == user.id;
                }
            }

        }

    }})
