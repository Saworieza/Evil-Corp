@extends('../layout')
@section('body')
<div class="row">
    <div class="col-md-4 col-md-offset-4" style="padding: 50px">

        <div class="text-center" style="margin-bottom: 30px">
            <div style="font-size: 64px; color:#fff; font-weight: 200; text-shadow: 0px 0px 4px #555;"><%Config::get('app.app_title')%></div>
            <div style="font-size: 24px; color:#fff; font-weight: 200; text-shadow: 0px 0px 2px #555;"><%Config::get('app.app_subtitle')%></div>
        </div>
        <br>
        <div class="animate-switch-container" style="height: auto" >
            <div class="animate-switch" >
                <div class="center-block " >
                    <div class="auth-title">
                        <% trans('client.ACTIVATE_ACCOUNT') %>

                        <br>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="textinput"><% trans('client.FULL_NAME') %></label>
                        <input id="first_name" autocomplete="off" autofocus ng-keypress="keyPress('full_name')" name="full_name" type="text" ng-model = 'user.full_name' class="form-control input-md">
                        <p class='inline-error' ng-show="errors['full_name']"            >{{errors['full_name'] | joinBy:'.'}}</p>

                    </div>

                    <div class="form-group">
                        <label class="control-label" for="textinput"><% trans('client.E_MAIL') %></label>
                        <input id="email" name="email" type="text" ng-model='user.email' disabled class="form-control input-md">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="passwordinput"><% trans('client.PASSWORD') %></label>
                            <input id="password" ng-keypress="keyPress('password')" name="password" type="password" ng-model='user.password' class="form-control input-md">
                            <p class='inline-error' ng-show="errors['password']">{{errors['password'] | joinBy:'.'}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="passwordinput"><% trans('client.CONFIRM_NEW_PASSWORD') %></label>
                            <input id="password_confirmation" ng-keypress="keyPress('password_confirmation')" name="password_confirmation" ng-model="user.password_confirmation" type="password" class="form-control input-md">
                            <p class='inline-error' ng-show="errors['password_confirmation']">{{errors['password_confirmation'] | joinBy:'.'}}</p>

                    </div>
                    <br>
                    <div class="form-group">
                        <label class="control-label" for="singlebutton"></label>
                            <button id="singlebutton" name="singlebutton" ng-click='activateClick()' class="btn btn-primary"><% trans('client.ACTIVATE_ACCOUNT') %></button>
                    </div>


                </div>
            </div>
       </div>
    </div>
    </div>

@stop

@section('footer')
<script>

app.filter('joinBy', function () {
    return function (input,delimiter) {
        if( Object.prototype.toString.call( input ) === '[object Array]' ) {
            return (input || []).join(delimiter || ',');
        } else {
            return input;
        }
    };
});
app.controller('mainCtrl', function($scope, $http ){

    $scope.user= <% json_encode($user) %>;
    $scope.user['_token'] = "<%csrf_token() %>";

    $scope.errors = {};

    $scope.keyPress = function(field)
    {
        delete($scope.errors[field]);
    }

    $scope.activateClick = function()
    {


        $http.post(apiUrl+'/activation', $scope.user).success(function(res){
            if(! res.success)
            {
                if(res.errors)
                    $scope.errors = res.errors;

            } else {
                document.location.href= '<%URL::to('/')%>';
            }

        })
    }

});

</script>
@stop