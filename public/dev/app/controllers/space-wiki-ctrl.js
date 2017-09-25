app.controller('spaceWikiCtrl', function($scope, $state, $stateParams, $previousState, $sce,$upload,
                                          apiService, growl, spaceFactory, formatterService ){

    $scope.currentSpace = spaceFactory;
    $scope.formatter = formatterService;
    $scope.wikis = [];
    $scope.menuIndex = -1;
    $scope.editing = false;
    $scope.editWiki = {};
    $scope.newFiles = [];
    $scope.fileIndex = 0;
    $scope.$parent.selected = 2;
    $scope.searchText = '';

    $scope.searching = false;

    $scope.searchCancelClick = function()
    {
        $scope.searchText = '';
        $scope.loadWikis();

    }

    $scope.uploadClick = function(){
        $('#_fileInput').click();
    }
    $scope.saveClick = function(){

        if(!$scope.editing) return;

        if($scope.editWiki.id > 0){
            apiService.put(['wiki',$scope.editWiki.id], {
                title: $scope.editWiki.title,
                access: $scope.editWiki.access,
                body: $('#htmleditor').code()
            }).then(
                function(res){
                    $scope.wikis[$scope.menuIndex] = res;
                    $scope.wikis[$scope.menuIndex].body = $sce.trustAsHtml( res.body );
                    $scope.editing = false;
                }
            )
        } else if($scope.editWiki.id == 0){
            apiService.post(['wiki'], {
                title: $scope.editWiki.title,
                access: $scope.editWiki.access,
                space_code: $stateParams.spaceCode,
                body: $('#htmleditor').code()
            }).then(
                function(res){
                    $scope.wikis.push(res);
                    $scope.editing = false;
                    $scope.menuClick($scope.wikis.length -1);


                }
            )
        }
    }

    $scope.confirmDeleteClick = function()
    {
        apiService.delete(['wiki', $scope.wikis[$scope.menuIndex].id]).then(
            function(res){
                $scope.wikis.splice($scope.menuIndex,1);
                if($scope.wikis.length > 0 )
                    $scope.menuIndex = 0;
            }
        )
    }

    $scope.confirmDeleteFileClick = function(fileCode)
    {
        apiService.delete(['file', fileCode]).then(
            function(res)
            {
               var i = findWithAttr( $scope.wikis[$scope.menuIndex].attachments, 'code',  fileCode);
                if(i >= 0){
                    $scope.wikis[$scope.menuIndex].attachments.splice(i,1);
                }
            }
        )
    }

    $scope.newWikiClick = function()
    {
        $scope.editWiki = {id:0, title:'', body:'', access:'PU'};

        $('#htmleditor').code('');
        $scope.editing = true;
        $scope.newFiles = [];

    }

    $scope.delteWikiClick = function()
    {

    }

    $scope.editWikiClick = function()
    {
        if($scope.menuIndex < 0) return;

        $scope.editing = true;
        $scope.editWiki = {};
        $scope.editWiki = JSON.parse( JSON.stringify($scope.wikis[$scope.menuIndex]) );

        $('#htmleditor').code($scope.editWiki.editBody);

    }

    $scope.loadWikis = function()
    {
        var params = {space_code: $stateParams.spaceCode};
        $scope.searching = $scope.searchText !== '';
        if($scope.searchText !== '')
            params['s'] = $scope.searchText;
        apiService.get(['wiki'],params ).then(
            function(res){
                $scope.wikis = res;
                if(res.length > 0 ) $scope.menuClick(0);
            }
        )
    }

    $scope.menuClick = function(index)
    {
        $scope.menuIndex = index;
        $scope.newFiles = [];
        if($scope.wikis.length >0)
        {
            if(! $scope.wikis[index].body ){
                apiService.get(['wiki', $scope.wikis[index].id, 'body']).then(
                    function(res){
                        $scope.wikis[index].body = $sce.trustAsHtml( res );
                        $scope.wikis[index].editBody = res;
                    }
                )
            }
        }
    }

    $scope.onFileSelect = function($files){

        var fileId = '';
        for (var i = 0; i < $files.length; i++) {
            $scope.fileIndex++;
            fileId = $scope.fileIndex+'-'+i;
            $scope.newFiles.push({original_name: $files[i].name, id: fileId, file_size: $files[i].size, status:'w'});

            var file = $files[i];

            file['id']= i;
            $scope.upload = $upload.upload({
                url: apiUrl + '/file',
                data: {id: fileId, filedate: file.lastModifiedDate, wiki_id: $scope.wikis[$scope.menuIndex].id},
                file: file})
                .success(function(res, status, headers, config) {
                    var e = _.findWhere($scope.newFiles, {id: res.data.id});

                    if(res.success)
                    {
                        if(e.status != 'deleted') e.status ='done';
                        e.id = res.data.code;
                        $scope.wikis[$scope.menuIndex].attachments.push(res.data);
                    } else {
                        e.status = 'error';
                    }

                }).error(function(res, data, status, fileInfo ){
                    var e = _.findWhere($scope.newFiles, {id: fileInfo.file.id});
                    e.status = 'error';
                })

        }
    }

    $scope.getUrlToFile = function(id)
    {
        return apiUrl+'/file/'+id;
    }
    $scope.loadWikis();

})


app.filter('highlight', function ($sce) {
    'use strict';

    return function (text, search, enabled) {

        if(! enabled ) return text;
        if (text && (search || angular.isNumber(search))) {
            text = text.toString();
            search = search.toString();

            return $sce.trustAsHtml(text.replace(new RegExp(search, 'gi'), '<span class="ui-match">$&</span>'));

        } else {
            return text;
        }
    };

});