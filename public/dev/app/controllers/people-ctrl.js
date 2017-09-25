app.controller('peopleCtrl', function($scope, apiService, formatterService){

    $scope.formatter = formatterService;

    $scope.showPhone = showPhone;
    $scope.showOrg = showOrg;
    $scope.order = 'full_name';


    apiService.getCached('profile').then(
        function(res){
            $scope.users = res.data;
            $scope.customFields = res.extra;
        }
    )

    $scope.setOrder = function(field)
    {
        $scope.order = 'custom_' + field;
    }

    $scope.peopleClick = function(id)
    {

        $scope.$emit('peopleDialogEvent', id);
    }

})


app.filter('peopleFilter', function(){
    return function(dataArray, searchTerm, customFields){
        if(!dataArray ) return;
        if( !searchTerm){
            return dataArray
        }else{

            var term=searchTerm.toLowerCase();
            return dataArray.filter(function( item){

                result = item.full_name.toLowerCase().indexOf(term) > -1;
                if(item.organization)
                    result = result || item.organization.toLowerCase().indexOf(term) > -1;

                if(item.position)
                    result = result || item.position.toLowerCase().indexOf(term) > -1;

                angular.forEach(customFields, function(field){
                    if(item['custom_'+field.name])
                        result = result || item['custom_'+field.name].toLowerCase().indexOf(term) > -1;
                })
                return result;
            });
        }
    }
});