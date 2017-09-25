app.controller('mainCtrl', function($scope, $state, spaceFactory, formatterService, $modal, apiService, $timeout ){


    $scope.formatter = formatterService;
    $scope.sp = {spaceSearch : ''};
    $scope.userProfile = {};
    $scope.activePage = 0;
    $scope.msg = {privateMessage : ''};
    $scope.msgCount = 0;
    $scope.spaceFactory = spaceFactory;
    $scope.spaces = [];

    $scope.loadSpaces = function()
    {
        apiService.get('space').then(
            function(res){
                $scope.spaces = res;
                $scope.spaceFactory.spaceList(res);

            })
    }



    $scope.loadSpaces();
    $scope.search = { text : ''};

    $scope.doSearch = function()

    {

        if($scope.search.text != ''){
            $state.go('search', {query: $scope.search.text});
            $scope.search = { text : ''};
        }
    }

    $scope.sendMessageClick = function(){

        if($scope.msg.privateMessage == '') return;

        apiService.post('message', {to_id: $scope.userProfile.id, body: $scope.msg.privateMessage}).then(
            function(res){
                $scope.msg = {privateMessage : ''};

            }
        )
    }

    $scope.$on('peopleDialogEvent', function(e,id){

        apiService.getCached(['profile', id], null, true).then(
            function(res){
                $scope.userProfile = res;

                var peopleModal = $modal({template: 'partials/modal-people.html', scope: $scope, animation: 'am-fade-and-slide-top', show: true});

            }
        )
    })

    $scope.$on('messageCountChanged', function(){
        $scope.mainPool();
    })

    $scope.gotoSpace = function(code)
    {
        $scope.spaceSearch = '';
        $timeout(function(){
            $scope.sp = {spaceSearch : ''};

            $state.go('space.stream', {spaceCode: code});
        }, 200);
    }


    $scope.gotoPage = function(page)
    {
        $timeout(function(){
            $state.go(page);
        });
    }

    $scope.$on('spaceListChanged', function(e){
        $scope.loadSpaces();
    });

    $scope.getLogoutUrl = function()
    {
        return siteUrl + '/logout';
    }


    $scope.mainPool = function()
    {
        apiService.get('mainpool').then(
            function(res)
            {

                if(res.msgs > $scope.msgCount)
                    sndBell.play();

                $scope.msgCount = res.msgs;

                $timeout($scope.mainPool, 60000);
            }
        )

    }

    $scope.mainPool();
})





