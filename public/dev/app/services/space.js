app.factory('spaceFactory', function($q, $http,  $cacheFactory, $state, apiService, $cacheFactory) {
    var currentSpace = null;
    var spaces = [];
    return {
        current: function()
        {
            return currentSpace;
        },
        invalidateSpaceCache : function()
        {
            if(currentSpace)
                $cacheFactory.get('$http').remove(apiUrl+'/space/'+currentSpace.code);

        },
        spaceList : function(data)
        {
            if(angular.isDefined(data))
            {
                spaces = data;
            } else {
                return spaces;
            }
        },

        loadSpaces: function(invalidateCache)
        {
            if(invalidateCache)  $cacheFactory.get('$http').remove(apiUrl+'/space');
            var deferred = $q.defer();
            apiService.getCached('space').then(
                function(res){
                    spaces = res;
                    deferred.resolve(res);
                }, function(err){
                    deferred.reject(err);
                }
            )
            return deferred.promise;
        },
        loadTags : function(){
            if(currentSpace)
            {
                apiService.get(['tag', currentSpace.code]).then(function(res){
                    currentSpace.tags = res;
                });
            }
        },
        accessDesc : function(){
            if(currentSpace)
                return currentSpace.access == 'PU' ? translations['PUBLIC'] : translations['PRIVATE'];
        },
        access : function(){
            if(currentSpace)
                return currentSpace.access;
        },
        title : function()
        {
            if(currentSpace)
                return currentSpace.title;
        },

        active : function(){
            if(currentSpace)
                return currentSpace.active;

        },
        options : function()
        {
            if(currentSpace)
                return currentSpace.options;
        },
        tasks: function(){
            if(currentSpace)
                return currentSpace.options.features.indexOf('tasks')>=0;
        },
        featuresDesc : function()
        {
            if(currentSpace)
            {
                var features = angular.copy(currentSpace.options.features);
                for(var i=0; i<features.length; i++){
                    if( translations[features[i].toUpperCase()] ) features[i] = translations[features[i].toUpperCase()] ;

                }
                return features.join(', ');
            }

        },
        members: function()
        {
            if(currentSpace)
                return currentSpace.users;

        },
        tags: function()
        {
            if(currentSpace)
                return currentSpace.tags;
        },

        description : function(){
            if(currentSpace)
                return currentSpace.description;
        },

        isDefault : function()
        {
            if(currentSpace)
          return currentSpace.default_space;
        },
        code: function()
        {
            if(currentSpace)
          return currentSpace.code;
        },
        role: function()
        {
            if(currentSpace)
               return currentSpace.role;
        },

        isMine: function(id)
        {
            return id == user.id;
        },

        isLoaded: function() {
          return currentSpace !== null;
        },

        setCurrent: function(spaceCode) {
            var deferred = $q.defer();
            currentSpace = null;

            apiService.getCached(['space', spaceCode]).then(
                function(res){
                    currentSpace = res;
                    deferred.resolve(res);
                }, function(err){
                    $state.go('home');
                })

            return deferred.promise;

        }


    }
});
