app.controller('adminCtrl', function($scope, spaceFactory, formatterService, $modal, apiService, growl, $timeout ){

    $scope.formatter = formatterService;

    $scope.users = [];


    $scope.viewIndex = 0;
    $scope.spacesOrder = 'title';
    $scope.sort = {column:'full_name', asc: true};
    $scope.newUserPanel = true;
    $scope.newuser = {full_name: '', email: '', password:'', password_confirmation:'', admin:false, create_spaces:true};

    $scope.newUserFormClick = function()
    {
        $scope.newUserPanel = !$scope.newUserPanel;
    }

    $scope.newUserFormCancelClick = function(){
        $scope.newuser = {full_name: '', email: '', password:'', password_confirmation:'', admin:false, create_spaces:true};
        $scope.newUserPanel = !$scope.newUserPanel;

    }

    $scope.newUserFormOkClick = function(){
        apiService.post('newuserform', $scope.newuser).then(
            function(res){
                $scope.newUserPanel = !$scope.newUserPanel;
                $scope.newuser = {full_name: '', email: '', password:'', password_confirmation:'', admin:false, create_spaces:true};
                $scope.loadUsers();

            },function(err){
                $scope.errors = err;
            }
        );

    }

    $scope.saveConfig = function()
    {
        apiService.put('config', $scope.appInfo ).then(
            function(data)
            {
                $timeout( function(){
                    growl.success( 'New configuration saved. Please, refresh your page.' );

                },1000)
            }
        )

    }

    $scope.loadSysLog = function()
    {
        apiService.get('syslog').then(
            function(data){
                $scope.syslog = data;
            }
        );

    }

    $scope.deleteLog = function()
    {
        apiService.delete('syslog').then(
            function(data){
                $scope.syslog = '';
            }
        );
    }

    $scope.spaceStateClick = function(space)
    {
        apiService.put(['adminspaces', space.id, 'activate']).then(
            function(data)
            {
                console.log('data', data);
                space.active = data;
                $scope.$emit('spaceListChanged');
            }
        )
    }

    $scope.setOrder = function(colName)
    {
        if($scope.sort.column == colName) {
            $scope.sort.asc = !$scope.sort.asc;
        } else {
            $scope.sort.column = colName;
            $scope.sort.asc = false;
        }

    }

    $scope.loadUsers = function()
    {
        apiService.get('adminuser').then(
            function(res){
                $scope.users = res.data;
                $scope.userCounters = res.extra;
            }
        )
    }
    $scope.loadAppInfo = function()
    {
        apiService.get('admin/appinfo').then(
            function(res){
                $scope.appInfo = res;
                console.log('res', $scope.appInfo);
            }
        )
    }

    $scope.upgradeClick = function()
    {
        apiService.get('admin/upgrade').then(
            function(res){
            }
        )
    }

    $scope.confirmDeleteClick = function(code, phrase)
    {
        if(phrase == translations['DELETE_SPACE_PHRASE']) {
            apiService.delete(['space',code]).then(
                function(res){
                    $scope.$emit('spaceListChanged');
                    $scope.loadSpaces();

                }
            )

        }
    }

    $scope.confirmModerator = function(spaceCode)
    {
        apiService.put(['user/makemoderator', spaceCode]).then(
            function(res){

            }
        )
    }

    $scope.getAppVersion = function()
    {
        return appVersion;
    }

    $scope.loadSpaces = function()
    {
        apiService.get('adminspaces').then(
            function(res){
                $scope.spaces = res;
            }
        )
    }

    $scope.errors = {};
    $scope.newUser = {waiting: false, email: ''};


    $scope.runMaintenanceClick = function(){
        apiService.get('admin/maintenance');
    }

    $scope.adminClick = function(id){
        apiService.put(['user/admin', id]).then(
            function(res){
                var i = findWithAttr($scope.users, 'id', id);
                $scope.users[i].admin = res;
            }
        )
    }

    $scope.createSpacesClick = function(id){
        apiService.put(['user/createspaces', id]).then(
            function(res){
                var i = findWithAttr($scope.users, 'id', id);
                $scope.users[i].create_spaces = res;
            }
        )
    }

    $scope.reNewClick = function(id){
        apiService.put(['user/renew', id]).then(
            function(res){
                var i = findWithAttr($scope.users, 'id', id);
                $scope.users[i] = res;
            }
        )
    }

    $scope.updateStateClick = function(id, state){
        apiService.put(['user/state',id, state]).then(
            function(res){
                var i = findWithAttr($scope.users, 'id', id);
                res = parseInt(res);
                if(res < 3 )
                {
                    $scope.users[i].state = res;

                } else {
                    $scope.users.splice(i,1);
                }
            }
        )
    }

    $scope.newUserClick = function()
    {
        if($scope.newUser.email == '') return;
        $scope.newUser.waiting = true;
        apiService.post('user', {email: $scope.newUser.email}).then(
            function(res){
                $scope.loadUsers();
                $scope.newUser.waiting = false;
                $scope.errors = {};
                $scope.newUser = {waiting: false, email: ''};
                growl.success( translations['INVITATION_SENT'] );
            }, function(err){
                $scope.newUser.waiting = false;
                $scope.errors = err;
            }
        )

    }

    $scope.loadUsers();
    $scope.loadSpaces();
    $scope.loadAppInfo();

})




