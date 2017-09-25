app.controller('userprofileCtrl', function($scope,  formatterService, apiService, $stateParams, $timeout, $state){

    $scope.formatter = formatterService;
    $scope.icons = [ 'fa fa-comment', 'fa fa-link', 'fa fa-table', 'fa fa-bar-chart', 'fa fa-check-square', 'fa fa-calendar', 'fa fa-map-marker'];
    $scope.privateMessage = '';



    $scope.userid =  $stateParams['userid'];

    apiService.get(['profile',  $scope.userid]).then(
        function(res){
            $scope.profile = res;
            $scope.avatar = formatterService.getAvatar($scope.profile.id);
            for(var i = 0; i<res.custom_fields.length; i++)
            {
                if(res.custom_fields[i].type == 'date')
                    $scope.profile['custom_'+res.custom_fields[i].name] = moment($scope.profile['custom_'+res.custom_fields[i].name]).format('L');

            }
        }
    )

    apiService.get(['profile/activity',  $scope.userid]).then(
        function(res){
            $scope.contents = res;
        }
    )

    $scope.gotoSpace = function(code)
    {
        $timeout(function(){
            $state.go('space.stream', {spaceCode: code});

        });
    }

    $scope.zoomClick = function(contentId){
        $state.go('post', {contentId: contentId});
    }

    $scope.sendMessageClick = function(){

        if($scope.privateMessage == '') return;

        apiService.post('message', {to_id: $scope.userid, body: $scope.privateMessage}).then(
            function(res){
                $scope.privateMessage = '';

            }
        )
    }
})




