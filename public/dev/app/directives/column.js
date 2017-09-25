app.directive('lkrColumn', function(){

    return {
        restrict: 'EA',
        replace : true,
        template: '<strong><a href="javascript:void(0)" ng-click="setOrder()">{{fieldCaption}} </a>' +
            '<i class="fa fa-chevron-down" style="color:#318BBD;" ng-show="sortOptions.column == fieldName && sortOptions.asc"></i>'+
            '<i class="fa fa-chevron-up" style="color:#318BBD;" ng-show="sortOptions.column == fieldName && !sortOptions.asc"></i></strong>',

        scope : {
            fieldCaption: '@',
            fieldName: '@',
            sortFunction: '&',
            sortOptions: '='
        },
        controller: function($scope, $element, $attrs) {
            $scope.setOrder = function() {
                $scope.sortFunction({colName: $scope.fieldName});
            }
        }

    }

});
