app.directive('lkrLocationEditor', function(apiService ) {
    return {
        restrict: 'EA',
        replace: true,
        templateUrl: 'directives/location-editor.html',
        scope: {
            data: '=data',
            errors: '=errors'
        },
        link: function(scope, element, attrs) {
            scope.mapClick = function(done)
            {
                scope.data.done = done;
                scope.data.waiting = true;
                scope.data.image = '';
                scope.errors = null;
                apiService.post('content/getStaticMap',{address:scope.data.address, zoom: scope.data.zoom, done: scope.data.done}).then(
                    function(res){
                        scope.data.image = res.image;
                        scope.data.done = true;
                        scope.data.waiting = false;

                    }, function(err){
                        scope.data.done = false;
                        scope.data.waiting = false;
                        scope.errors = err;

                    }, function(notif) {
                        scope.data.done = false;
                        scope.data.waiting = false;

                    }
                )
            }

            scope.keyPress = function()
            {
                scope.errors = null;
                scope.data.done = false;
            }

        }
    }


});