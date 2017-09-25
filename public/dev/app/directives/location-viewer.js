app.directive('lkrLocationViewer', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/location-viewer.html',
        replace : true,
        scope: {
            data: '=data'
        }

    }
})
