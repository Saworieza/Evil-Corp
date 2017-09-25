app.directive('lkrLinkViewer', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/link-viewer.html',
        replace : true,
        scope: {
            data: '=data'
        }

    }
})
