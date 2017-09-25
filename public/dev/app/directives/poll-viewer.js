app.directive('lkrPollViewer', function(apiService) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/poll-viewer.html',
        replace : true,
        scope: {
            data: '=data',
            contentid: '=contentid',
            votes: '=votes',
            zoom: '='
        },

        link: function(scope) {

            scope.editing = false;
            scope.newOption = '';
            scope.myIndex = null;

            scope.addOptionClick = function()
            {
                scope.newOption = '';
                scope.editing = true;
            }

            scope.okClick = function()
            {
                if(scope.newOption == '') return;
                apiService.post(['vote', scope.contentid], {option: scope.newOption}).then(
                    function(res){
                        scope.editing = false;
                        scope.data = res;
                        scope.init();
                    })
            }

            scope.cancelClick = function()
            {
                scope.newOption = '';
                scope.editing = false;
            }

            scope.init = function()
            {
                scope.options = [];
                scope.data['voters'] =[];
                scope.total = 0;
                scope.data['values']=[];

                for(i=0;i<scope.data.options.length;++i)
                {
                    scope.data['values'][i]=0;
                    scope.data['voters'][i]='';
                }

                angular.forEach(scope.votes, function(vote){
                    scope.total++;
                    scope.data['values'][vote.choice] = scope.data['values'][vote.choice] + 1;
                    if(scope.data['voters'][vote.choice] != '') scope.data['voters'][vote.choice] = scope.data['voters'][vote.choice] + '<br>';
                    scope.data['voters'][vote.choice] = scope.data['voters'][vote.choice] + vote.user.full_name;
                    if(vote.user.id == user.id) scope.myIndex = vote.choice;
                })
            }

            scope.voteClick = function(index){
                apiService.get(['vote', scope.contentid, parseInt(index)]).then(
                    function(res){
                        scope.votes= res.votes;
                        scope.init();
                    }
                )
            }

            scope.getPercent = function(count){

                if(scope.total == 0) return "";
                if(count == 0) return "";

                var w = Math.floor( count * 100 / scope.total);
                return '('+ w+'%)';

            }
            scope.getValue = function(count){
                if(scope.total == 0) return "";

                var w = Math.floor( count * 100 / scope.total);
                return 'width: '+w+'%';

             }

            scope.init();
        }

    }
})