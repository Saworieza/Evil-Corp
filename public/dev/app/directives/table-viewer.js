app.directive('lkrTableViewer', function(apiService) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/table-viewer.html',
        replace : true,
        scope: {
            data: '=data',
            contentid: '=',
            zoom: '@'
        },
        link: function(scope) {

            scope.getExportUrl = function()
            {
                return apiUrl + '/csv/' + scope.contentid;

            }

        }
    }
})