app.directive('lkrContent', function(contentService,spaceFactory, starService, $sce, $state, apiService, $popover, formatterService, growl) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/content.html',
        scope: {
            content: '=content',
            hasTasks: '='
        },

        link: function(scope, element, attrs) {

            scope.newTaskView = false;

            scope.spaceFactory = spaceFactory;

            scope.sharePostClick = function(code)
            {
                console.log('share in', code);
                if(code =='') return;
                apiService.post(['content', scope.content.id,'share', code]).then(
                    function(data){
                        growl.success(translations['POST_SHARED_SUCCESS']);
                    }
                )
            }

            scope.icons = [ 'fa fa-comment', 'fa fa-link', 'fa fa-table', 'fa fa-chart', 'fa fa-check-square', 'fa fa-calendar', 'fa fa-map-marker'];

            scope.zoom = angular.isDefined(attrs.zoom);

            scope.currentView = 'comments';

            scope.formatter = formatterService;

            if(angular.isDefined( scope.content))
            {
                scope.content.content_text = $sce.trustAsHtml(scope.content.content_text);

            }

            scope.$on('taskCancelEvent', function(){
                scope.newTaskView = false;

            })

            scope.translateClick = function(content)
            {
                console.log('translate');
                apiService.get(['translate','Content', content.id]).then(
                    function(data)
                    {
                        content.translated = data.translated;
                        console.log('d',data);
                    }
                )
            }

            scope.$on('taskInsertEvent', function(event, task){
                scope.content.task_summary.push(task);
                scope.newTaskView = false;
                apiService.get('task/counters', {contentId: scope.content.id}).then(
                    function(res){
                        scope.content.task_count = res;
                    }
                )
            })

            scope.viewMoreClick = function()
            {
                contentService.viewMore(scope.content.id).then(
                    function(res){
                        scope.content.truncated = false;
                        scope.content.content_text = res;
                    }
                )
            }

            scope.newTaskClick = function()
            {
                scope.newTaskView = !scope.newTaskView;
                scope.newTask = {user_id: user.id, priority:1, state:0, files:[], error:[]};
            }
            scope.zoomClick = function(contentId){
                if(scope.viewMode != 'post') $state.go('post', {contentId: contentId});
            }

            scope.starredClick = function(id)
            {
                starService.toggle('Content', id).then(function(res){
                    scope.content.starred = res;
                })
            }

            scope.peopleClick = function()
            {
                scope.$emit('peopleDialogEvent', scope.content.user_id);
            }

            scope.contentMine = function(userId)
            {
                return userId == user.id;
            }

            scope.confirmDeleteClick = function(contentId)
            {
                contentService.delete(contentId).then(function(res){

                    scope.$emit('contentDeletedEvent',contentId);

                })
            }

            scope.getAvatar = function(id) {

                return assetsUrl + '/avatar/' + id + '.jpg';
            }


        }

    }})