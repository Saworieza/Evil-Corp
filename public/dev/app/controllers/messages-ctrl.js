app.controller('messagesCtrl', function($scope, $state,$cacheFactory, spaceFactory, formatterService, $modal, apiService ){

    $scope.formatter = formatterService;

    $scope.msgs = [];
    $scope.newMessage = {to:[], body:'', panel: -1};
    $scope.viewIndex = 0;

    $scope.viewClick = function(index)
    {
        $scope.viewIndex = index;

        $scope.loadMessages( $scope.viewIndex==1 );

    }

    $scope.loadMessages = function(sent){

        var params = null;

        if(angular.isDefined(sent) && sent)
            params = { sent: true};

        apiService.get('message', params).then(
            function(res){
                $scope.msgs = res;
            }
        )
    }

    $scope.checkAllClick = function(value){
        angular.forEach($scope.msgs, function(msg){
            if(! msg.read)
                msg.checked = value;
        })
    }

    $scope.markAllReadClick = function()
    {

        angular.forEach($scope.msgs, function(msg){
            if(angular.isDefined(msg.checked) && msg.checked)
            {
                apiService.put(['message', msg.id]).then(
                    function(res){
                        msg.read = res;
                        msg.checked = false;


                    })
            }
        })
        $scope.$emit('messageCountChanged');

    }

    $scope.newMessageClick = function()
    {
        $scope.newMessage.panel = $scope.newMessage.panel == 0 ? $scope.newMessage.panel = -1 : $scope.newMessage.panel = 0;
        $scope.newMessage.to = [];
        $scope.newMessage.body = '';
    }

    $scope.sendClick = function()
    {
        if($scope.newMessage.body == '' || $scope.newMessage.to.length ==0) return;

        apiService.post('message', {to_id: $scope.newMessage.to, body: $scope.newMessage.body}).then(
            function(){
                $scope.newMessage = {to:[], body:'', panel: -1};
                if($scope.viewIndex == 1)
                    $scope.loadMessages(true);
            }
        );

    }



    $scope.loadUsers = function(ini)
    {
        if(ini.length < 3) return;
        return apiService.get(['user'],{ini: ini});
    }

    $scope.$on('replyMessage', function(e,r){
        var msg = _.findWhere($scope.msgs, {id: r.id});

        var to_id = msg.from_id;

        apiService.post('message', {to_id: to_id, body: r.body});


    })

    $scope.$on('markMessageAsRead', function(e,id){
        var msg = _.findWhere($scope.msgs, {id: id});
        apiService.put(['message', id]).then(
            function(res){
                msg.read = res;
                msg.checked = false;
                $scope.$emit('messageCountChanged');

            })
    })

    $scope.loadMessages();


})


