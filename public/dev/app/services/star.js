app.service('starService', function(apiService){
    return {
        toggle : function(className, objId){

            return apiService.get(['star',className,objId]);

        }
    }
})
