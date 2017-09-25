app.controller('profileCtrl', function($scope, spaceFactory, formatterService, $upload, $timeout, $locale, apiService, usSpinnerService ){

    $scope.formatter = formatterService;
    $scope.password= {old : "", new: "", conf: ""};
    $scope.profileFields = profileFields;
    $scope.dateFormat = $locale.DATETIME_FORMATS.longDate;
    apiService.get(['profile', user.id]).then(
        function(res){
            $scope.profile = res;
            for(var i = 0; i<res.custom_fields.length; i++)
            {
                if(res.custom_fields[i].type == 'date')
                    $scope.profile['custom_'+res.custom_fields[i].name] = moment($scope.profile['custom_'+res.custom_fields[i].name]).toDate();

            }
            $scope.avatar = formatterService.getAvatar($scope.profile.id);
        }
    )

    $scope.updateSettingsClick = function()
    {
        apiService.post('profile/settings', $scope.profile).then();

    }

    $scope.updateProfileClick = function()
    {
        $scope.errors = null;
        apiService.post('profile', $scope.profile).then(
            function(res){
            }, function(err){
                $scope.errors = err;
            }
        )
    }

    $scope.onFileSelect = function($files){

        var file = $files[0];
        usSpinnerService.spin('avatarSpinner');

        $upload.upload({
            url: apiUrl + '/profile/avatar',
            file: file})
            .success(function(data, status, headers, config) {

                $timeout(function() {
                    var av = Math.floor((Math.random() * 1000000) + 1);

                    $scope.profile.avatar = $scope.avatar = formatterService.getAvatar($scope.profile.id)+'?'+av;
                    usSpinnerService.stop('avatarSpinner');
                    //location.reload(true);
                },1000)
            }).error(function(){
                usSpinnerService.stop('avatarSpinner');

            })
    }

    $scope.updatePassword = function() {
        $scope.errors = null;
        apiService.post('profile/password', $scope.password).then(
            function(res){
                $scope.password= {old : "", new: "", conf: ""};

            },function(err){

                $scope.errors = err;
            })

    }


    $scope.scrollTo = function(id) {
        $timeout(function() {
            $("html,body").animate({ scrollTop: $('#'+id).offset().top -60 }, 600);
        })
    }

    $scope.photoClick = function() {
        $timeout(function() {
            $('#photoEditor').click();
        })
    }

})




