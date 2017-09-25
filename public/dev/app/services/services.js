app.factory('spaceFactory', function($q, $http) {
    var currentSpace = null;
    return {
        setCurrent : function(spaceCode)
        {
            $http.get(apiUrl+'/space/')
        },

        list: function() {
            var deferred = $q.defer();
            if (spaces.length == 0) {
                $http.get(apiUrl+'/space/userspaces').then(function(response) {
                    if(response.data.success)
                    {
                        spaces = response.data.spaces;
                        deferred.resolve(response.data.spaces);
                    }
                });
            } else {
                deferred.resolve(spaces);
            }
            return deferred.promise;
        },

        get: function(spaceCode) {
            var deferred = $q.defer();
            if(spaces.length == 0){
                this.list().then(function(response){
                    deferred.resolve(spaces[findWithAttr(spaces, 'code', spaceCode)]);
                });
            } else {
                deferred.resolve(spaces[findWithAttr(spaces, 'code', spaceCode)]);
            }

            return deferred.promise;
        },

        setCurrent: function(spaceCode) {
            var deferred = $q.defer();

            this.get(spaceCode).then(function(response){
                $http.get(apiUrl+'/space/'+spaceCode+'/users').success(function(res){
                    if(res.success){
                        spaceUsers = res.users;
                        currentSpace = spaces[findWithAttr(spaces, 'code', spaceCode)];
                        deferred.resolve(response);
                    }
                })
            });

            return deferred.promise;
        },

        getSpaceUsers: function() {
            var deferred = $q.defer();
            deferred.resolve(spaceUsers);
            return deferred.promise;
        }

    }
});

app.factory('contentFactory', function($http){
    return {
        content : [],
        addElement : function(ele){
            this.contents.push(ele);
        },
        showElements: function(){

        },
        getChilds : function(contentId) {
            return $http.get(apiUrl+'/content/'+contentId+'/childs');
        },
        getEvents : function(params) {
            if(params) {
                params = {params: params};
            }
            return $http.get(apiUrl+'/content/'+space.id+'/events', params);
        },
        getStream : function(spaceCode, params){
            if(params) {
                params = {params: params};
            }
            return $http.get(apiUrl+'/stream/'+spaceCode, params);
        },
        deleteContent : function(contentId){
            return $http.delete(apiUrl+'/content/'+contentId);
        },
        saveContent : function(spaceCode, className, payLoad) {
            return $http.post(apiUrl+'/content/'+spaceCode+'/'+className, payLoad);
        },
        likeContent : function(contentId) {
            return $http.get(apiUrl+'/content/'+contentId+'/like');
        },
        setFavorite : function(contentId) {
            return $http.get(apiUrl+'/content/'+contentId+'/favorite');
        },
        getFiles : function(params) {
            if(params) {
                params = {params: params};
            }
            return $http.get(apiUrl+'/content/'+space.id+'/files', params);
        },
        getLatestFiles : function() {
            return $http.get(apiUrl+'/content/'+space.id+'/latestfiles');
        },
        getWikis : function(params) {
            if(params.length > 0) {
                params = {params: {tags: params}};
            }
            return $http.get(apiUrl+'/content/'+space.id+'/wikis', params);
        },
        getEntry : function(contentId) {
            return $http.get(apiUrl+'/content/entry/'+contentId);
        },
        setWikiOrder : function(id, order) {
            return $http.get(apiUrl+'/content/wiki/'+id+'/order/'+order);
        }
    }

})
