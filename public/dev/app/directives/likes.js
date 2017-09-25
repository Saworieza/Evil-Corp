app.directive('lkrLikes', function(apiService, formatterService) {
    return {
        restrict: 'E',
        templateUrl: 'directives/likes.html',

        scope: {
            likes: '=likes',
            objectId : '=objectId',
            likeCount: '=likeCount',
            btnClass: '@btnClass',
            className: '@className'
        },
        link: function(scope, element, attrs) {

            scope.iLike = false;

            scope.getLikeList = function()
            {
                if(angular.isUndefined(scope.likes)) return;
                var res = '';
                var sep = scope.likes.length < 20 ? '<br>' : ', ';

                for(var i=0; i<scope.likes.length ;i++){
                    if(res!='') res = res + sep;
                    res = res + scope.likes[i].user.full_name;
                    if(scope.likes[i].user.id == user.id) scope.iLike = true;
                }
                return res;
            }

            scope.onClick = function() {
                apiService.get(['like', scope.className, scope.objectId]).then(
                    function(res){
                        var found = -1;
                        for(var i=0; i<scope.likes.length ;i++){
                            if(scope.likes[i].user.id == user.id){
                                found = i;
                            }
                        }
                        if(!res && found>=0) scope.likes.splice(found,1);
                        if(res  && found<0) scope.likes.push({id:0, user:{id: user.id, full_name: user.fullname}});
                        scope.iLike = res;
                    })
            }
        }
    }});