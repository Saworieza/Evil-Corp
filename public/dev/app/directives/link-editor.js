app.directive('lkrLinkEditor', function(apiService ) {
    return {
        restrict: 'EA',
        replace: true,
        templateUrl: 'directives/link-editor.html',
        scope: {
            data: '=data',
            errors: '=errors'

        },
        link: function(scope, element, attrs) {
            scope.linkClick = function()
            {
                scope.data.done = false;
                scope.data.waiting = true;
                scope.data.title = '';
                scope.data.image = '';
                scope.data.description = '';
                scope.data.icon = '';
                scope.errors = null;
                apiService.post('content/geturl',{url:scope.data.url}).then(
                    function(data){
                        scope.data.title = data.title;
                        scope.data.image = data.image;
                        scope.data.description = data.description;
                        scope.data.url = data.url;
                        scope.data.icon = data.icon;
                        scope.data.done = true;
                        scope.data.waiting = false;

                    }, function(err){
                        scope.data.done = false;
                        scope.errors = err;
                        scope.data.waiting = false;

                    }, function(notf) {
                        scope.data.done = false;
                        scope.data.waiting = false;
                    });

            }

            scope.keyPress = function()
            {
                scope.errors = null;
                scope.data.done = false;
            }

            scope.pasteLink = function(){
                setTimeout(function() {
                    scope.linkClick();
                }, 100);
            }
        }
    }


});