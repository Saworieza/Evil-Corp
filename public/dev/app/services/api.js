app.service('apiService', function($http, $q, usSpinnerService, growl){

    var resFn = function(deferred){

        return function(resData, status, headers, config) {
            usSpinnerService.stop('appSpinner');

            if(resData.message) {
                if(resData.alert){
                    growl[resData.alert](resData.message);
                } else {
                    growl.warning(resData.message);
                }
            };

            if(resData._debug)
            {
                console.debug(
                    resData._debug._SERVER.REQUEST_METHOD + ':' + resData._debug._SERVER.REQUEST_URI
                    ,resData);
            }

            if(resData.success) {

                if(resData.extra)
                {
                    deferred.resolve({data:resData.data, extra: resData.extra});

                } else {
                    deferred.resolve(resData.data)

                }

            } else {
                var err = angular.isDefined(resData.errors) ? resData.errors : null;
                deferred.reject(err);
            }

        }
    }

    var errFn = function(deferred) {
        return function(data, status, headers, config) {
            growl.error("Error: "+ status);
            deferred.notify(status);
            usSpinnerService.stop('appSpinner');

        }
    }

    var api = {
        promise: function(method, uri, params, cache, data){

            if(uri!='mainpool' && uri[0]!=='chat')
                usSpinnerService.spin('appSpinner');

            var deferred = $q.defer();
            head = '';
            cache = angular.isDefined(cache);
            if(uri instanceof Array) uri = uri.join('/');

            if(method !== 'get' && method !== 'post')
            {
                head = {'X-HTTP-Method-Override' : method};
                method = 'post';
            }

            $http({
                method: method,
                cache: cache,
                url: apiUrl+'/'+uri,
                params: params,
                headers: head,
                data: data
            }).success(resFn(deferred)).error(errFn(deferred));


            return deferred.promise;
        },

        get: function(uri, params)
        {
            return this.promise('get', uri, params)
        },

        getCached: function(uri, params)
        {
            return this.promise('get', uri, params, true)
        },

        post: function(uri, data)
        {
            return this.promise('post', uri, null, false, data);
        },

        put: function(uri, data)
        {
            return this.promise('put', uri, null, false, data);
        },

        delete: function(uri, params)
        {
            return this.promise('delete', uri, params);
        }

    }

    return api;
})