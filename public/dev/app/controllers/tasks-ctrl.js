app.controller('tasksCtrl', function($scope, $state, $stateParams, $previousState, apiService ){

    $scope.tasks = []
    $scope.$parent.selected = 3;

    $scope.showingArchived = false;

    $scope.spaceCode = $stateParams.spaceCode;

    $scope.menuIndex = 0;

    $scope.loadTasks = function(archived){

        var param = angular.isDefined($stateParams.spaceCode) ? {space_code: $stateParams.spaceCode} : {};

        if(angular.isDefined(archived)) param['archived'] = 1;

        apiService.get(['task'], param).then(
            function(data){
                $scope.tasks = data;
                $scope.getCounters();
            }
        )
    }


    $scope.menuClick = function(index)
    {
        $scope.menuIndex = index;
        var filters = [{}, {my:true}, {delegated:true}];

        if(index < 3)
        {
            if($scope.showingArchived) $scope.loadTasks();
            $scope.showingArchived = false;
            $scope.filter = filters[index];
        } else {
            $scope.loadTasks(true);
            $scope.showingArchived = true;
            $scope.filter= {};
        }
        $scope.$broadcast('taskMenuChanged');

    }
    $scope.newTaskClick = function(){
        $scope.$broadcast('newTaskClickEvent');

    }

    $scope.archivedClick = function()
    {

        $scope.loadTasks(true);
        $scope.showingArchived = true;
        $scope.filter = {};
    }

    $scope.$on('taskUpdateCounters', function(data) {
        $scope.getCounters();
    });

    $scope.getCounters = function(){
        var param = angular.isDefined($stateParams.spaceCode) ? {space_code: $stateParams.spaceCode} : null;

        apiService.get(['task/counters'], param).then(
            function(res){
                $scope.counters = res;
            }
        )
    }

    $scope.loadTasks();


})