@extends('../layout')
@section('body')
<div class="row">
    <div class="col-md-4 col-md-offset-4" style="padding: 50px">

        <div class="text-center" style="margin-bottom: 30px">
            <div style="font-size: 64px; color:#fff; font-weight: 200; text-shadow: 0px 0px 4px #555;"><%Config::get('app.app_title')%></div>
            <div style="font-size: 24px; color:#fff; font-weight: 200; text-shadow: 0px 0px 2px #555;"><%Config::get('app.app_subtitle')%></div>
        </div>
        <div class="animate-switch-container" style="height: auto">
            <div class="animate-switch" >
                <div class="center-block " >
                    <div class="text-mb">
                        Password reset

                        <br>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="passwordinput"><% trans('client.NEW_PASSWORD') %></label>
                        <input id="password" ng-keypress="keyPress('password')" autofocus name="password" type="password" ng-model='user.password' class="form-control input-md">
                        <label style="color: #ff0000" ng-show="errors['password'][0]">{{errors['password']}}</label>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="passwordinput"><% trans('client.CONFIRM_NEW_PASSWORD') %></label>
                        <input id="password_confirmation" ng-keypress="keyPress('password_confirmation')" name="password_confirmation" ng-model="user.password_confirmation" type="password" class="form-control input-md">
                        <label style="color: #ff0000" ng-show="errors['password_confirmation'][0]">{{errors['password_confirmation']}}</label>

                    </div>
                    <br>
                    <div class="form-group">
                         <button id="singlebutton" name="singlebutton" ng-click='activateClick()' class="btn btn-primary"><% trans('client.SUBMIT') %></button>
                    </div>
                </div>
            </div>


        </div>
    </div>
 </div>

@stop

@section('footer')
<script>
app.controller('mainCtrl', function($scope, $http ){

    $scope.user= <% json_encode($user) %>;
    $scope.errors = [];
    $scope.user['_token'] = "<%csrf_token() %>";

    $scope.keyPress = function(field)
    {
        delete($scope.errors[field]);
    }

    $scope.activateClick = function()
    {
        $http.post(apiUrl+'/password', $scope.user).success(function(res){
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