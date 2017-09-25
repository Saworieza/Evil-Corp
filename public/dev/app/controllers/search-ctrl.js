app.controller('searchCtrl', function($scope, $state, $stateParams, $timeout, formatterService, $modal, apiService ){

    $scope.formatter = formatterService;

    $scope.searchText = $stateParams['query'];
    $scope.results = [];
    $scope.icons = [ 'fa fa-comment', 'fa fa-link', 'fa fa-table', 'fa fa-bar-chart', 'fa fa-check-square', 'fa fa-calendar', 'fa fa-map-marker'];


    $scope.newSearch = function()
    {
        console.log('=');
        if($scope.searchText != ''){
            $state.go('search', {query: $scope.searchText});
        }
    }

    $scope.search = function()
    {
        apiService.get('search', {query: $scope.searchText}).then(
            function(res)
            {
                $scope.results = res;
            }
        )
    }


    $scope.gotoSpace = function(code)
    {
        $timeout(function(){
            $state.go('space.stream', {spaceCode: code});

        });
    }

    $scope.zoomClick = function(contentId){
        $state.go('post', {contentId: contentId});
    }
    $scope.search();

})


