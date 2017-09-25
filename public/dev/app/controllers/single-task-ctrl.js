app.controller('singleTaskCtrl', function($scope, $state, $stateParams, $previousState, apiService, formatterService ){

    $scope.formatter = formatterService;
    $scope.taskId = $stateParams.taskId;
    $scope.task = null;

    apiService.get(['task'], {task_id: $scope.taskId}).then(
        function(res){
          $scope.task = res[0];
        });

    $scope.goBack = function(taskId){
        $previousState.go();
    }



})