app.directive('lkrPollEditor', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/poll-editor.html',
        scope: {
            data: '=data'
        },
        link: function(scope, element, attrs) {
            scope.addRowClick = function(){
                scope.data.options.push('');
            }

            scope.delRowClick = function(index){
                if(scope.data.options.length > 1)
                    scope.data.options.splice(index,1);
            }
        }
    }
})