app.directive('lkrFieldText', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/field-text.html',
        replace: true,
        scope: {
            originalValue: '=value',
            fieldName    : '@value',
            updateCb     : '&',
            label        : '@',
            placeholder  : '@',
            allowEdit    : '@'
        },

        link: function(scope, element, attrs) {
            scope.editing = false;
            scope.editValue = scope.originalValue;

            scope.allowEdit = (scope.allowEdit == 'true');


            scope.editClick = function()
            {
                scope.editValue = scope.originalValue;
                scope.editing = true;
                scope.error = '';
            }

            scope.okClick = function()
            {
                var fieldName = scope.fieldName;
                if(fieldName.indexOf('.') >=0 )
                    fieldName = fieldName.split('.')[fieldName.split('.').length - 1];

                scope.updateCb({fieldName: fieldName, fieldValue: scope.editValue}).then(
                    function(res){

                        scope.editing = false;
                    }, function(err){
                     console.log(err);
                        scope.error = err;
                });
            }
        }

    }})