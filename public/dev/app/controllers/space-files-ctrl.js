app.controller('spaceFilesCtrl', function($scope, $state, $stateParams, $previousState,
                                          apiService, growl, spaceFactory, formatterService,$popover ){

    $scope.folders = [];
    $scope.menuIndex = -1;
    $scope.appending = false;
    $scope.editing = false;
    $scope.editFolder = '';
    $scope.newFiles = [];
    $scope.list = {sortField : "1"};
    $scope.sort = {column:'file_name', asc: true};
    $scope.filter = '';
    $scope.busy = true;
    $scope.page = 0;
    $scope.currentSpace = spaceFactory;
    $scope.formatter = formatterService;
    $scope.viewer = {mode: 'table'};

    $scope.realFolders = [];

    $scope.setOrder = function(colName)
    {
        if($scope.sort.column == colName) {
            $scope.sort.asc = !$scope.sort.asc;
        } else {
            $scope.sort.column = colName;
            $scope.sort.asc = false;
        }
        $scope.loadFiles(true);
    }

    $scope.menuIndex = -1;
    //$scope.menuClick = function(index){
    //    $scope.menuIndex = index;
    //    $scope.selectedFolder = $scope.folders[index];
    //    $scope.loadFiles(true);
    //}

    $scope.cancelClick = function()
    {
        $scope.appending = false;
    }

    $scope.confirmRenameClick = function(txt)
    {
        if(txt == '') return;
        apiService.put(['folder', $scope.selectedFolder.id], {name: txt}).then(
            function(res){
                var id =  angular.copy($scope.selectedFolder.id);
                $scope.loadFolders(id);
                //$scope.selectedFolder.name = txt;
                //$scope.selectedFolder

            }
        )
    }


    $scope.batchDeleteClick = function()
    {
        angular.forEach($scope.files, function(file){

            if(file.checked=='1' && (formatterService.isMe(file.user_id) || $scope.currentSpace.role() >2 )  )
            {
                apiService.delete(['file',file.code]).then(
                    function(res){
                        $scope.loadFiles(true);
                    }
                )

            }
        })

    }

    $scope.confirmDeleteFileClick = function(code)
    {
        apiService.delete(['file',code]).then(
            function(res){
                $scope.loadFiles(true);
            }

        )
    }

    $scope.getUrlToFile = function(id)
    {
        return apiUrl+'/file/'+id;
    }

    $scope.confirmDeleteClick = function()
    {
        apiService.delete(['folder', $scope.selectedFolder.id]).then(
            function(res){
                $scope.loadFolders();
                $scope.selectedFolder = $scope.folders[0];
                $scope.menuIndex = 0;
            }
        )
    }

    $scope.$on('fileUpoadPostEvent', function(e){

        apiService.post(['folder', $scope.selectedFolder.id ], {files: $scope.newFiles}).then(
            function(res){
                $scope.newFiles= [];
                $scope.loadFiles(true);


            }
        )
    })

    $scope.okClick = function()
    {

        var parent_id = 0;

        if(angular.isDefined($scope.rootFolder) || $scope.selectedFolder.id > 0)
        {
            if($scope.rootFolder == 0 )
                parent_id = $scope.selectedFolder.id;
        }

        apiService.post(['folder'], {space_code: $stateParams.spaceCode, name: $scope.editFolder, parent_id: parent_id}).then(
            function(res){
                res['childs'] = [];

                if(parent_id > 0)
                {
                    res['path'] = $scope.selectedFolder['path']+'/'+$scope.editFolder;
                    $scope.selectedFolder.childs.push( res );
                } else {
                    res['path'] = $scope.editFolder;
                    $scope.folders.push( res );

                }
                $scope.realFolders = angular.copy($scope.folders);
                $scope.realFolders.shift();
                $scope.editFolder = '';
                $scope.rootFolder = 1;
            }
        )

        $scope.appending = false;
    }

    $scope.newFolderClick = function()
    {
        $scope.editFolder = '';
        $scope.appending = true;
    }


    $scope.loadFolders = function(selectId)
    {
        apiService.get(['folder'], {space_code: $stateParams.spaceCode}).then(
            function(data){
                $scope.folders = data;
                $scope.realFolders = angular.copy(data);
                $scope.folders.splice(0,0,{id:0, name: translations['STREAM_FILES'], childs: [], path:translations['STREAM_FILES']});
                $scope.selectedFolder = data[0];

            }
        )
    }
    $scope.paging = function()
    {
        if($scope.busy) return;

        if($scope.page < 0) return;

        $scope.page++;
        $scope.loadFiles(false);
    }

    $scope.filterKeyPress = function()
    {
        if($scope.filter == ''){
            $scope.loadFiles(true);
        }
    }

    $scope.search = function()
    {
        $scope.loadFiles(true);
    }

    $scope.checkAllClick = function(value)
    {
        console.log('check');
        angular.forEach($scope.files, function(f){
            f.checked = value;
        })
    }

    $scope.confirmBatchCopy = function(folder)
    {
        console.log('mcopy', folder);

        if(angular.isUndefined(folder)) return;
        if(folder == $scope.selectedFolder.id) return;
        angular.forEach($scope.files, function(file){

         console.log('f', file);
            if(file.checked=='1')
            {
                apiService.put(['file', file.code, 'copy', folder]).then(
                    function(res){
                        growl.success(file.file_name + ' ' + translations['DONE']);
                    }
                )

            }
        })

    }
    $scope.confirmBatchMove = function(folder)
    {
        if(angular.isUndefined(folder)) return;
        if(folder == $scope.selectedFolder.id) return;


        angular.forEach($scope.files, function(file){
            if(file.checked=='1' && (formatterService.isMe(file.user_id) || $scope.currentSpace.role() >2 )  )
            {
                apiService.put(['file', file.code, 'move', folder]).then(
                    function(res){
                        growl.success(file.file_name+ ' ' + translations['DONE']);
                        $scope.loadFiles(true);

                    }
                )

            }
        })

    }


    $scope.batchCopyClick = function(event)
    {
        console.log(event.target);

        var count = _.filter($scope.files, function(f){
           return f.checked=="1";
        });
        if(! count.length ) return;
        var pop = $popover(angular.element(event.target), {
            title: translations['COPY_TO'],
            content: count.length,
            template:"partials/pop-copy-multiple-file.html",
            placement:"bottom",
            container:'body',
            animation:"am-fade",
            trigger: 'manual',
            autoClose: true,
            show: true
        });

        pop.$scope.okCaption = translations['COPY'];
        pop.$scope.folders = $scope.realFolders;


        pop.$scope.treeOptions = $scope.treeOptions;
        pop.$scope.okClick = $scope.confirmBatchCopy;
        pop.$promise.then(pop.show);

    }

    $scope.batchMoveClick = function(event)
    {

        var count = _.filter($scope.files, function(f){
           return f.checked=="1";
        });
        if(! count.length ) return;
        var pop = $popover(angular.element(event.target), {
            title: translations['MOVE_TO'],
            content: count.length,
            template:"partials/pop-copy-multiple-file.html",
            placement:"bottom",
            container:'body',
            animation:"am-fade",
            trigger: 'manual',
            autoClose: true,
            show: true
        });

        pop.$scope.okCaption = translations['MOVE'];
        pop.$scope.folders = $scope.realFolders;
        pop.$scope.treeOptions = $scope.treeOptions;
        pop.$scope.okClick = $scope.confirmBatchMove;
        pop.$promise.then(pop.show);

    }

    $scope.confirmFileCopy = function(file, folder){

        if(angular.isUndefined(file) || angular.isUndefined(folder)) return;
        if(folder == $scope.selectedFolder.id) return;

        apiService.put(['file', file.code, 'copy', folder]).then(
            function(res){
                growl.success(translations['DONE']);
            }
        )
    }
    $scope.confirmFileMove = function(file, folder){

        if(angular.isUndefined(file) || angular.isUndefined(folder)) return;
        if(folder == $scope.selectedFolder.id) return;
        apiService.put(['file', file.code, 'move', folder]).then(
            function(res){
                growl.success(translations['DONE']);

                $scope.loadFiles(true);

            }
        )
    }

    $scope.loadFiles = function(resetPage)
    {
        if(resetPage)
        {
            $scope.page= 0;
        } else if($scope.page < 0 ) {
            return;
        }

        $scope.busy = true;
        if($scope.selectedFolder.id > 0 )
        {
            var params = {folder_id: $scope.selectedFolder.id};

        } else {
            var params = {space_code: $stateParams.spaceCode};
        }
        params.sort = $scope.sort.column;
        params.asc = $scope.sort.asc;
        params.filter = $scope.filter;
        params.page = $scope.page;
        apiService.get(['file'], params ).then(
            function(data){
                if($scope.page ==0) {
                    $scope.files = data;
                } else {
                    for(i=0; i<data.length; i++){
                        $scope.files.push(data[i]);
                    }
                }
                if(data.length < 50) $scope.page = -1;

                $scope.toFolders = JSON.parse( JSON.stringify($scope.folders));
                if($scope.menuIndex > 0 )
                    $scope.toFolders.splice($scope.menuIndex, 1);
                $scope.toFolders.shift();


                $scope.busy = false;

            }, function(err){
                $scope.busy = false;
            }
        )
    }

    $scope.$watch('selectedFolder', function(v){
        if(! angular.isDefined(v)) return;
        $scope.menuIndex = v.id;
        $scope.loadFiles(true);
    });

    $scope.treeOptions = {
        nodeChildren: "childs",
        dirSelectable: true,
        injectClasses: {
            ul: "a1",
            li: "a2",
            liSelected: "a7",
            iExpanded: "a3",
            iCollapsed: "a4",
            iLeaf: "a5",
            label: "a6",
            labelSelected: "a8"
        }
    }


    $scope.loadFolders(false);
    $scope.$parent.selected = 1;
})