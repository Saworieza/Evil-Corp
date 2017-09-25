app.directive('lkrFileEditor', function($upload, $timeout) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/file-editor.html',
        scope: {
            data: '=data',
            fileAccept: '@',
            fileEditorId: '=',
            caption: '@'
        },

        link: function(scope, element, attrs) {


            scope.showPostButton = angular.isDefined(attrs.showPostButton);

            scope.postButtonClick = function()
            {
                scope.$emit('fileUpoadPostEvent');
            }

            scope.fileIndex = 0;
            if(! angular.isDefined(scope.data)) {
                scope.data = [];
            }

            scope.showPost = (scope.showPost=='true');

            scope.selectClick = function(event){

                $timeout(function() {
                    $('#myFileInputAll'+scope.fileEditorId).click();
                })

            }

            scope.getPreviewImg = function(id)
            {
                if(id>0){
                    return apiUrl+'/userfile/'+id+'/preview';

                }
            }

            scope.removeClick = function(fileId){
                var e = _.findWhere(scope.data, {id: fileId});
                e.status ='deleted';
            }

            scope.filterFn = function(file) {
                return file.status !== 'deleted';
            }

            scope.formatFileSize = function(asize){
                if(asize == '' || asize == null) return '';
                var asize = parseInt(asize);
                if(asize > 1000000) return (asize/1000000).toFixed(2) + ' MB';
                if(asize > 1000) return (asize/1000).toFixed(2) + ' KB';

                return asize + ' Bytes';
            }

            scope.onFileSelect = function($files){

                var fileId = '';
                for (var i = 0; i < $files.length; i++) {
                    //var e = _.findWhere(scope.data, {file: $files[i].name});
                    scope.fileIndex++;
                    fileId = scope.fileIndex+'-'+i;
                    scope.data.push({original_name: $files[i].name, id: fileId, file_size: $files[i].size, status:'w'});

                    var file = $files[i];

                    file['id']= fileId;
                    scope.upload = $upload.upload({
                        url: apiUrl + '/file',
                        data: {id: fileId, filedate: file.lastModifiedDate},
                        file: file})
                        .success(function(res, status, headers, config) {
                            var e = _.findWhere(scope.data, {id: res.data.id});
                            if(res.success)
                            {
                                if(e.status != 'deleted') e.status ='done';
                                e.id = res.data.code;
                            } else {
                                e.status = 'error';
                            }

                        }).error(function(res, data, status, fileInfo ){
                            var e = _.findWhere(scope.data, {id: fileInfo.file.id});
                            e.status = 'error';
                        })

                }
            }
        }
    }

});