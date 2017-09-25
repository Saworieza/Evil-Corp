app.directive('lkrFieldRadio', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/field-radio.html',
        replace: true,
        scope: {
            originalValue: '=value',
            fieldName    : '@value',
            radioValues  : '=',
            updateCb     : '&',
            label        : '@',
            allowEdit    : '@'
        },

        link: function(scope, element, attrs) {
            scope.editing = false;

            scope.radioClick = function(selectedId)
            {
                var fieldName = scope.fieldName;
                if(fieldName.indexOf('.') >=0 )
                    fieldName = fieldName.split('.')[fieldName.split('.').length - 1];

                scope.updateCb({fieldName: fieldName, fieldValue: selectedId}).then(
                    function(res){

                        scope.editing = false;
                    }, function(err){
                        console.log(err);
                        scope.error = err;
                    });
            }
        }

    }})