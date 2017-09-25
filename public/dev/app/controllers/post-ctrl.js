app.controller('postCtrl', function($scope, $state, $stateParams, $previousState, contentService, formatterService ){

    $scope.people = null;
    $scope.zoom = true;
    $scope.formatter = formatterService;

    $scope.$on('contentDeletedEvent', function(ev, id){
        $previousState.go();

    });

    contentService.getOne($stateParams.contentId).then(function(data){
        $scope.content = data;
    }, function(err){
        $previousState.go();
    })

})