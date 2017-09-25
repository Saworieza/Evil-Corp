app.directive('lkrField', function($timeout, apiService, $http) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/field.html',
        replace: true,
        scope: {
            formMode     : '@',
            field : '=',
            originalValue: '=value',
            fieldName    : '@value',
            updateCb     : '&',
            label        : '@',
            placeholder  : '@',
            type         : '@',
            allowEdit    : '@',
            radioValues  : '='
        },

        controller: function($scope) {

            //$scope.edit.original = $scope.value;

        },

        link: function(scope, element, attrs) {

            scope.edit = {editing: false, formMode: scope.formMode=='true'};

            scope.allowEdit = (scope.allowEdit == 'true');


            if(scope.type == 'date')
            {
                if(angular.isDefined(scope.originalValue))
                {
                    scope.edit.value = moment(scope.value).format();
                }
            }

            scope.radioClick = function(selectedId)
            {
                scope.edit.editing = false;
                scope.error = '';
                var fieldName = scope.fieldName;
                if(fieldName.indexOf('.') >=0 )
                    fieldName = fieldName.split('.')[fieldName.split('.').length - 1];

                scope.updateCb({fieldName: fieldName, fieldValue: selectedId}).then(
                    function(res){

                        scope.editing = false;
                    }, function(err){
                        scope.error = err;
                    });
            }

            scope.editClick = function()
            {
                if(scope.allowEdit=='false') return false;

                jQuery.extend(scope.edit.value, scope.originalValue);

//                angular.copy(scope.originalValue, scope.edit['value']);

                scope.edit.editing = true;
                scope.error = '';
            }

            scope.loadUsers = function(ini)
            {
                if(ini.length < 3) return;
                return apiService.get(['user'],{ini: ini});
            }


            scope.okClick = function()
            {
                var fieldName = scope.fieldName;
                var value = scope.edit.value;
                if(scope.type == 'date')
                {
                    if(angular.isUndefined(value))
                    {
                        value = "";
                    } else {
                        value = moment(value).startOf('day').format();

                    }
                }

                if(fieldName.indexOf('.') >=0 )
                    fieldName = fieldName.split('.')[fieldName.split('.').length - 1];
                scope.updateCb({fieldName: fieldName, fieldValue: value}).then(
                    function(res){

                        scope.edit.editing = false;

                    }, function(err){
                     console.log(err);
                        scope.error = err;
                });
            }
        }

    }})