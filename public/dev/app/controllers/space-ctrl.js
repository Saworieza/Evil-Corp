app.controller('spaceCtrl', function($scope, $state, $stateParams, $timeout, spaceFactory, apiService, $modal){

    $scope.chat = [];
    $scope.chatFromId = 0;

    $scope.currentSpace = spaceFactory;
    spaceFactory.setCurrent($stateParams.spaceCode);

    $scope.modules = modules;
    $scope.selected = 0;

    $scope.chatMsg = {text: ''};

    $scope.inviteUsers = function()
    {
        apiService.get(['space', $stateParams.spaceCode, 'nousers']).then(
            function(res){
                $scope.usersToInvite = res;
                var peopleModal = $modal({template: 'partials/modal-space-invite.html', scope: $scope, animation: 'am-fade-and-slide-top', show: true});

            }
        )
    }

    $scope.inviteOkClick = function()
    {
        var invite = [];
        for(var i=0; i<$scope.usersToInvite.length;++i){
            if(angular.isDefined($scope.usersToInvite[i].checked) && $scope.usersToInvite[i].checked)
            {
                invite.push($scope.usersToInvite[i].id);
            }
        }
        if(invite.length>0){
            apiService.post(['user', 'invite', $stateParams.spaceCode], {users: invite}).then(
                function(res){
                    $scope.$broadcast('newUserInvited');

                }
            )
        }

    }

    $scope.chatSend = function()
    {
        console.log('chatsend', $scope.chatMsg.text);
        $scope.chatEvent($scope.chatMsg.text);
        $scope.chatMsg = {text:''};
    }

    $scope.chatEvent = function(msg)
    {
        if($scope.currentSpace.options().features.indexOf('chat') < 0) return;

            var params = {fromid : $scope.chatFromId};
        if(msg) params['message'] = msg;
        apiService.get(['chat', $stateParams.spaceCode], params).then(
            function(res){
                if(res.length > 0)
                {
                    angular.forEach(res, function(item) {
                        if(item.id > $scope.chatFromId)
                            $scope.chat.push(item);
                    });
                    $scope.chatFromId = res[res.length -1].id;

                    $timeout(function() {
                        if(! angular.isUndefined($('#chatpanel')[0] ))
                            $('#chatpanel').scrollTop($('#chatpanel')[0].scrollHeight);
                    })
                };
                var time = res.length == 0 ? 12000 : 6000;
                chatTimeout = $timeout($scope.chatEvent, time);

            }
        )
    }

    $scope.$on('$destroy', function(){
        $timeout.cancel(chatTimeout);

    });

    var chatTimeout = $timeout($scope.chatEvent, 4000);


})