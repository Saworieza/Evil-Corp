app.directive('lkrChartViewer', function() {
    return {
        restrict: 'EA',
        templateUrl: 'directives/chart-viewer.html',
        replace : true,
        scope: {
            data: '=data',
            zoom: '@'
        }
    }
})