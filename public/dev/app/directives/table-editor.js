app.directive('lkrTableEditor', function() {
    return {
        restrict: 'EA',
        replace: true,
        templateUrl: 'directives/table-editor.html',
        scope: {
            tableData: '=data'
        },
        link: function(scope, element, attrs) {
            scope.showPaste = false;
            scope.pasteData = '';
            scope.newRowClick = function()
            {
                var newRow = [];
                for(var i=0; i<scope.tableData.cols.length; i++){
                    newRow.push('');
                }
                scope.tableData.rows.push( newRow );

            }
            scope.delColClick = function()
            {
                if(scope.tableData.cols.length > 1)
                {
                    scope.tableData.cols.splice(scope.tableData.cols.length-1,1);
                    for(var i=0; i<scope.tableData.rows.length;++i){
                        scope.tableData.rows[i].splice(scope.tableData.rows[i].length-1,1);
                    }
                }
            }

            scope.delRowClick = function(index)
            {
                scope.tableData.rows.splice(index,1);
            }

            scope.processPaste = function(){
                var lines = scope.pasteData.split("\n");
                var data = [];
                for(var i=0; i<lines.length;i++){
                    data.push( lines[i].split('\t'));
                }

                scope.tableData.cols = [];
                scope.tableData.rows = [];

                if(data.length>0)
                {
                    for(i=0; i<data[0].length; i++){
                        scope.tableData.cols.push({type:'text', title:data[0][i]})
                    }

                    for(i=1; i<data.length; i++){
                        var row=[];
                        for(j=0;j<data[i].length;j++)
                        {
                            row.push(data[i][j]);
                        }
                        scope.tableData.rows.push(row);

                    }

                }
                if(scope.tableData.rows.length == 0)
                {
                    scope.tableData.rows.push([]);
                    for(i=0; i<scope.tableData.cols.length;i++){
                        scope.tableData.rows[0].push('');
                    }
                }

                scope.showPaste = false;
                scope.pasteData = '';
                scope.$apply();
            }

            scope.handlePaste = function(event){
                setTimeout(function() {
                    scope.processPaste();
                }, 100);
            }

            scope.newColClick = function()
            {
                scope.tableData.cols.push({ type: 'text', title: translations['NEW_COLUMN']});
                for(var i=0; i<scope.tableData.rows.length; i++){
                    scope.tableData.rows[i].push('');
                }

            }
        }
    }
});