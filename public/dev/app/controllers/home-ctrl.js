app.controller('homeCtrl', function($scope, $state,$cacheFactory, $timeout, spaceFactory, formatterService, $modal, apiService ){

    $scope.formatter = formatterService;

    $scope.features = [
        {value: 'drive', text: translations['DRIVE']},
        {value:'wikis', text:translations['WIKIS']},
        {value:'tasks', text: translations['TASKS']},
        {value:'chat', text: translations['CHAT']},
    ];
    $scope.spaces = [];

    $scope.createSpaceClick = function()
    {
        $scope.editSpace = {title: "", description: '', options:{ features: ['drive', 'wikis', 'tasks', 'chat']}, access: 'PU'};
        $scope.error = null;
        $scope.spaceForm = $modal({template: 'partials/modal-space-edit.html', title: 'New space', scope: $scope, animation: 'am-fade-and-slide-top', show: true});
    }

    $scope.canCreateSpace = function()
    {
        return user.cs == 1;
    }

    $scope.editSpaceOkClick = function()
    {
        apiService.post(['space'], $scope.editSpace).then(
            function(res){
                $scope.spaceForm.hide();
                $scope.$emit('spaceListChanged');
                //document.location.href= '#/space/'+res;
                $state.go('space.stream', {spaceCode: res});

            }, function(err){
                $scope.error = err;
            }
        )
    }

    $scope.zoomClick = function(contentId){
       $state.go('post', {contentId: contentId});
    }

    $scope.testClick = function()
    {
        spaceFactory.loadSpaces().then(
            function(res){
                $scope.activity = res;
            });

    }

    $scope.joinClick = function(code){

        apiService.put(['space', code, 'join']).then(
            function(){
                $state.go('space.stream', {spaceCode: code});
                $scope.$emit('spaceListChanged');
            });
    }

    $scope.icons = [ 'fa fa-comment', 'fa fa-link', 'fa fa-table', 'fa fa-bar-chart', 'fa fa-check-square', 'fa fa-calendar', 'fa fa-map-marker'];

    $scope.getLogoutUrl = function()
    {
        return siteUrl + '/logout';
    }

    $scope.gotoSpace = function(code)
    {
        $timeout(function(){
            $state.go('space.stream', {spaceCode: code});

        });
    }

        $scope.loadSpaces = function()
    {
        apiService.get('home').then(
            function(res){
                var color = $('.btn-info').css( "background-color" );
                $scope.spaces = res.spaces;
                $scope.join = res.join;
                $timeout(function(){
                    angular.forEach(res.spaces, function(space){
                        //8ED2F3
                        $('#spark_'+space.code).sparkline(space.counter, {disableTooltips: true, disableHighlight: true, type: 'bar',
                            barColor: color,
                            height:'40px', barWidth: '15', barSpacing:'2'} );

                    });


                })
            }
        )
    }

    $scope.loadContent = function () {
        apiService.get('home/content').then(
            function (res) {
                $scope.contents = res;
            }
        )
    }

    $scope.events = [];
    if(!h_cal) {
        apiService.get(['event'], {view: 'short'}).then(
            function (res) {
                $scope.events = res;
            }
        )
    }

    $scope.tasks = null;

    apiService.get('task/counters').then(
        function(data){
            $scope.tasks = data;

        }
    )

    $scope.loadSpaces();
   $scope.loadContent();

})


