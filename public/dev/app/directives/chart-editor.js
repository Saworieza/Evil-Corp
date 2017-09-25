app.directive('lkrChartEditor', function(){
    return {
        scope: {
            chartData : '=data'
        },
        restrict: 'EA',
        templateUrl: 'directives/chart-editor.html',
        replace: true,
        link: function(scope, iElm, iAttrs, controller) {


            scope.delSerieClick = function()
            {
                if(scope.chartData.labels.length > 1)
                {

                    scope.chartData.labels.pop();
                    var key = 's'+scope.chartData.skeys.pop();
                    for (var i = 0; i < scope.chartData.data.length; i++) {
                        delete scope.chartData.data[i][key];
                    };

                }
            }

            scope.delRowClick = function(index) {
                scope.chartData.data.splice(index,1);
            }

            scope.addRowClick = function(){
                var newRow = {'xlabel': translations['LABEL']};
                for(var i=0;i<scope.chartData.skeys.length;i++){
                    newRow['s'+scope.chartData.skeys[i]] = 0;
                }
                scope.chartData.data.push(newRow);

            }

            scope.addSerieClick = function(){
                scope.chartData.labels.push(translations['SERIE']);
                var c = Math.max.apply(Math, scope.chartData.skeys)+1;
                scope.chartData.skeys.push(c);
                var indx = 's' + c;
                for(var i=0; i<scope.chartData.data.length; i++){
                    scope.chartData.data[i][indx]=0;
                }

            }


        }
    };
});
