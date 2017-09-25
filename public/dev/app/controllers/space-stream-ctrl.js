app.controller('spaceStreamCtrl', function($scope, $state, $timeout, apiService, formatterService,
                                           $stateParams, spaceFactory, contentService, $q, $http ){

    $scope.people = null;

    $scope.hm = hm;

    $scope.currentSpace = spaceFactory;

    contentService.setSpace($stateParams.spaceCode);


    $scope.stream = [];
    $scope.formatter = formatterService;

    $scope.shareType = 0;
    $scope.streamFilter = null;
    $scope.filter = null;
    $scope.filterIndex = 0;
    $scope.busy = false;
    $scope.limitTags = 15;
    $scope.filterTag = 0;
    $scope.$parent.selected = 0;


    $scope.newPosts = false;

    $scope.lastPostDate = 0;

    $scope.viewAllTagsClick = function(){
        $scope.limitTags = 100;
    }



    $scope.filterClick = function(index){
        $scope.filterIndex = index;
        var filters = [null, {starred: true}, 0,1,2,3,4,5,6 ];
        if(index == 0 )
            $scope.filter = null;
        else if(index == 1 )
            $scope.filter = {key: 'starred', value: true};
        else
            $scope.filter = {key: 'class_id', value: index-2}

        $scope.loadStream(true);
    }

    $scope.newContent = contentService.prepareNewContent();


    $scope.$on('contentDeletedEvent', function(ev, id){
            var i = findWithAttr($scope.stream, 'id', id);
            if(i>=0){
                $scope.currentSpace.loadTags();
                $scope.loadEvents();
                $scope.stream.splice(i,1);
            }
        }
    );

    $scope.deleteClick = function(contentId)
    {

        var contentIndex = findWithAttr($scope.stream, {id: contentId});
        if(contentIndex >= 0 )
        {
            $scope.loadStream();
        }
    }

    $scope.loadFiles = function()
    {
        apiService.get(['file'], {asc: false, space_code:$stateParams.spaceCode, view:1, sort:'created_at', page:0 }).then(
            function(res){
                $scope.latestFiles = res;

            }
        )
    }

    $scope.shareTypeClick = function(stype)
    {
        $scope.shareType  = stype;
        $scope.newContent = contentService.prepareNewContent($scope.newContent.content_text);
    }

    $scope.getTagText = function(item) {
        return '#' + item.tag ;
    };

    $scope.paging = function()
    {
        if($scope.busy ) return;

        $scope.loadStream(false);
    }

    $scope.searchTag = function(term) {
        var tagList = $scope.currentSpace.tags();
            var tagList = _(tagList).reject(function(i)
            {
                return i.tag.toLowerCase().indexOf(term.toLowerCase()) != 0;
            })

        tagList.sort(function(a,b) {
            if (a.tag.toLowerCase() < b.tag.toLowerCase())
                return -1;
            if (a.tag.toLowerCase() > b.tag.toLowerCase())
                return 1;
            return 0;
        });

        if(tagList.length > 10) tagList = tagList.slice(0,10);
        $scope.products = tagList;
        return $q.when(tagList);

    };

    $scope.confirmQuit = function()
    {
        apiService.get(['quit', $stateParams.spaceCode]).then(
            function(res){
                $state.go('home');
            })
    }

    $scope.peopleClick = function(id)
    {
        $scope.$emit('peopleDialogEvent', id);
    }

    $scope.getMemberTooltip = function(member) {
        if(member.role < 3)
            return member.full_name;
        else
            return member.full_name + '<br>' + translations['MODERATOR'];
    }

    $scope.getPeopleTextRaw = function(item){
            return '@' + item.code;
    }

    $scope.searchPeople = function(term) {
        if(isEmpty(term)) return;
        var peopleList = [];
        var params = {params:{'ini': term}};

        return $http.get(apiUrl+'/space/'+$scope.currentSpace.code()+ '/users', params).then(function (response) {
            if(response.data.success) {
                angular.forEach(response.data.data, function(item) {
                        peopleList.push(item);
                });

                $scope.people = peopleList;
                return $q.when(peopleList);
            }
        });
    };

    $scope.pool = function()
    {
        contentService.pool($scope.lastPostDate).then(
            function(res){
                if(! $scope.newPosts && res) snd.play();

                $scope.newPosts = res > 0;
                poolTimeout = $timeout($scope.pool, 60000);
            }
        )
    }
    $scope.events = [];


    $scope.loadEvents = function(){
        apiService.get(['event'], {space_code: $stateParams.spaceCode, view: 'short'}).then(
            function(res){
                $scope.events = res;
            }
        )
    }




    $scope.inviteClick = function()
    {
        $scope.$parent.inviteUsers();

    }

    $scope.postClick = function()
    {

        contentService.post($scope.newContent, $scope.shareType).then(function(res){

            if(res.content_text.indexOf('"hashtag"')>=0  )
                $scope.currentSpace.loadTags();


            $scope.stream.splice(0,0,res);

            $scope.errors = null;

            if($scope.shareType == 5)
                $scope.loadEvents();

            $scope.shareType = 0;
            if($scope.newContent.files.length > 0) $scope.loadFiles();
            $scope.newContent = contentService.prepareNewContent();

        }, function(err){
            $scope.errors = err;


        }, function(notif){
        });

    }

    $scope.filterTagClick = function(tagId){

        if($scope.filterTag == tagId)
            $scope.filterTag = 0
        else
            $scope.filterTag = tagId;

        $scope.loadStream(true);
    }

    $scope.poolClick = function(){

        $scope.filterTag = 0;
        $scope.filter = null;
        $scope.filterIndex = 0;
        $scope.stream = [];
        $timeout(function(){
            $scope.loadStream(true);

        });
    }

    $scope.deleteall = function()
    {

    }

    $scope.loadStream = function(resetPage)
    {

        if(resetPage)
        {
            $scope.fromDate = moment().utc().add('1','hours').format('YYYY-MM-DD HH:mm:ss');
            $scope.newPosts = 0;
        } else if($scope.fromDate==-1){
            return;
        }



        $scope.busy = true;

        var query = {from_date: $scope.fromDate};

        if($scope.filter)
            query[$scope.filter.key] = $scope.filter.value;

        if($scope.filterTag > 0)
            query.tag = $scope.filterTag;

        contentService.getStream(query).then(
            function(data) {

                if(resetPage) {
                    $scope.stream = []
                    $scope.newPosts = false;

                }
                for(i=0; i<data.length; i++){
                    if($scope.lastPostDate < parseInt(moment.utc(data[i].updated_at).format('X'))){
                        $scope.lastPostDate = parseInt(moment.utc(data[i].updated_at).format('X'));
                    }
                    $scope.stream.push(data[i]);
                }

                if(data.length < 10)
                {
                    $scope.fromDate = -1;
                } else {
                    $scope.fromDate = $scope.stream[$scope.stream.length -1].updated_at;


                }
                $scope.busy = false;

            },function(err){
                $scope.busy = false;

            })
    }

    $scope.getUrlToFile = function(id)
    {
        return apiUrl+'/file/'+id;
    }

    $scope.loadStream(true);

    var poolTimeout = $timeout($scope.pool, 60000);



    $scope.loadEvents();
    $scope.loadFiles();

    $scope.$on('$destroy', function(){
        $timeout.cancel(poolTimeout);

    });

    $scope.getAvatar = function(id) {

        return assetsUrl + '/avatar/' + id + '.jpg';
    }

    $scope.formatDesc = function(description, size, date)
    {
        res = '';
        if(description != '')
        {
            res = description + ' - '
        }

        res = res + $scope.formatter.formatDate(date) + ' (' + $scope.formatter.formatFileSize(size)+ ')';
        return res;
    }

    $scope.$on('commentNewFiles', function(ev){
        $scope.loadFiles();
    })


})