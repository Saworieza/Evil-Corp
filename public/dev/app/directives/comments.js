app.directive('lkrComments', function(apiService, formatterService) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/comments.html',
        replace : true,
        scope: {
            comments: '=comments',
            className: '@',
            contentId : '=contentId',
            commentCount: '=commentCount'
        },
        link: function(scope, element, attrs) {

            scope.body = '';
            scope.showPost = false;
            scope.formatter = formatterService;
            scope.newFiles = [];
            scope.showCommentEditor = false;

            scope.confirmDeleteClick = function(commentId)
            {
                apiService.delete(['comment',commentId]).then(
                    function(res){
                        var i = findWithAttr(scope.comments, "id", commentId);
                        scope.comments.splice(i,1);
                        scope.commentCount--;
                    }
                )
            }

            scope.commentFocus = function(id)
            {
                scope.showPost = true;

            }

            scope.peopleClick = function(id)
            {
                scope.$emit('peopleDialogEvent', id);
            }

            scope.commentPostClick = function()
            {
                var data = {body: scope.body, files: scope.newFiles};
                apiService.post(['comment', scope.className, scope.contentId], data).then(
                    function(res){
                        scope.commentCount++;
                        if(scope.newFiles.length >0 )
                            scope.$emit('commentNewFiles');

                        scope.comments.push(res);
                        scope.body='';
                        scope.newFiles = [];
                        scope.showPost = false;
                    }
                )
            }
            scope.getAvatar = function(id) {
                return assetsUrl + '/avatar/' + id + '.jpg';
            }
            scope.loadAllComments = function()
            {
                apiService.get(['comment', scope.className, scope.contentId]).then(
                    function(res){
                        scope.comments = res;
                        scope.commentCount = res.length;
                    }
                )
            }
        }


    }
})