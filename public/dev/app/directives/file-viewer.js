app.directive('lkrFileViewer', function($modal) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/file-viewer.html',
        replace : true,
        scope: {
            data: '=data',
            contentid : '=contentid'
        },
        link: function(scope, element, attrs) {

            scope.show = false;
            scope.openImg = function(id)
            {
                scope.image2View = apiUrl+'/userfile/'+id;

                var modalInstance = $modal.open({
                    templateUrl: 'app/templates/lightbox.html',
                    scope: scope,
                    windowClass: 'img-modal'

                });
            }

            scope.hasImages = function()
            {
                var i = _.findWhere(scope.data, {file_type: 'image'});
                return angular.isDefined(i);

            }

            scope.hasFiles = function()
            {
                var i = _.findWhere(scope.data, {file_type: 'file'});
                return angular.isDefined(i);

            }
            scope.formatDesc = function(description, size, date)
            {
                res = '';
                if(description != '')
                {
                    res = description + ' - '
                }
                res = res + scope.formatDate(date) + ' (' + scope.formatFileSize(size)+ ')';
                return res;
            }

            scope.formatDate = function(dte) {
                return moment(dte).format('lll');;
            }

            scope.formatFileSize = function(asize){
                if(asize == '' || asize == null) return '';
                var asize = parseInt(asize);
                if(asize > 1000000) return (asize/1000000).toFixed(2) + ' MB';
                if(asize > 1000) return (asize/1000).toFixed(2) + ' KB';

                return asize + ' Bytes';
            }

            scope.getPreviewImg = function(code)
            {
                return apiUrl+'/file/'+code+'/preview';
            }

            scope.getUrlToFile = function(id)
            {
                return apiUrl+'/file/'+id;
            }
        }

    }
})