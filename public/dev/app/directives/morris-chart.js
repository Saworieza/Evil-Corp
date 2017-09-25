app.directive('morrisChart', function() {
    return {
        restrict: 'E',
        replace: true,
        template:'<div></div>',
        scope: {
            options: '='
        },
        link: function(scope, element, attrs) {

            scope.doChart = function(value)
            {
                if(!angular.isDefined(value)) return;
                var chart = angular.copy(value);
                element.empty();
                chart.element = element;
                chart.ykeys = [];
                for (var i = 0; i < chart.skeys.length; i++) {
                    chart.ykeys.push( 's'+chart.skeys[i]);
                };

                if(chart.chartType == 'bar')
                    var r = new Morris.Bar(chart);
                if(chart.chartType == 'line')
                    var r = new Morris.Line(chart);

                if(chart.chartType == 'area')
                    var r = new Morris.Area(chart);

                if(chart.chartType =='donut')
                {
                    var donutValues = [];
                    for (var i = 0; i < chart.data.length; i++) {
                        donutValues.push( {label: chart.data[i].xlabel, value: chart.data[i].s1});
                    }
                    chart.data = donutValues;
                    var r = Morris.Donut(chart);

                }


            }
            scope.$watch('options', function(value) {
                scope.doChart(value);
            }, true);


        }
    }
});
