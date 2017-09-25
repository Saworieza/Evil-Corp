app.controller('spaceAdminCtrl', function($scope, $state, $stateParams, $previousState, spaceFactory,
                                          apiService, growl, spaceFactory, formatterService, $modal ){

    $scope.currentSpace = spaceFactory;
    $scope.editSpace = {};
    $scope.users = [];
    $scope.usersOrder = 'full_name';
    $scope.formatter = formatterService;



    $scope.features = [
        {value: 'drive', text: translations['DRIVE']},
        {value:'wikis', text:translations['WIKIS']},
        {value:'tasks', text: translations['TASKS']},
        {value:'chat', text: translations['CHAT']},
    ];

    var editSpaceModal;
    $scope.editSpaceClick = function()
    {

        $scope.editSpace = {title: $scope.currentSpace.title(),
                            description: $scope.currentSpace.description(),
                            options : JSON.parse( JSON.stringify($scope.currentSpace.options())),
                            access : $scope.currentSpace.access() };

        editSpaceModal = $modal({template: 'partials/modal-space-edit.html', scope: $scope, animation: 'am-fade-and-slide-top', show: true});

    }

    $scope.switchRoleClick = function(userId){
        apiService.put(['user','switchrole', userId, $stateParams.spaceCode]).then(
            function(res){
                if(res == 1){
                    var i = findWithAttr($scope.users, 'id', userId);
                    $scope.users.splice(i,1);
                } else {
                    var e = _.findWhere($scope.users, {id: userId});
                    e.role = res;
                }
            }
        )
    }

    $scope.confirmDeleteClick = function(phrase)
    {
        if(phrase == translations['DELETE_SPACE_PHRASE']) {
            apiService.delete(['space',$stateParams.spaceCode]).then(
                function(res){
                    $state.go('home');
                    $scope.$emit('spaceListChanged');

                }
            )

        }
    }

    $scope.editSpaceOkClick = function()
    {
        apiService.put(['space', $stateParams.spaceCode], $scope.editSpace).then(
            function(res){
                editSpaceModal.hide();
                $scope.currentSpace.invalidateSpaceCache();
                $scope.currentSpace.setCurrent($stateParams.spaceCode);
                $scope.$emit('spaceListChanged');
            }
        )

    }

    $scope.loadUsers = function()
    {
        apiService.get(['space', $stateParams.spaceCode, 'allusers']).then(
            function(res){
                $scope.users = res;
            }
        )
    }

    $scope.$on('newUserInvited', function(){
        $scope.loadUsers();
    })

    $scope.inviteClick = function()
    {
        $scope.$parent.inviteUsers();

    }

    $scope.$parent.selected = 4;
    $scope.loadUsers();

})