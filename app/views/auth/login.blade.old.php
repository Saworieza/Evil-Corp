@extends('../layout')
@section('head')

@stop
@section('body')
    <div class="row">
        <div style="width:440px; margin: auto; padding: 20px">

            <div class="text-center" style="margin-bottom: 30px">
                <div style="font-size: 64px; color:#fff; font-weight: 200; text-shadow: 0px 0px 4px #555;"><%Config::get('app.app_title')%></div>
                <div style="font-size: 24px; color:#fff; font-weight: 200; text-shadow: 0px 0px 2px #555;"><%Config::get('app.app_subtitle')%></div>
<!--                <img src="logo.png">-->
            </div>
            <div style="border: 1px solid #ddd; padding: 10px; background-color: #fff">

            <div class="animate-switch-container" ng-switch on="selection">
                <div class="animate-switch" ng-switch-when="login">
                    <div class="center-block " >
                        <div class="text-mb">
                            <% trans('client.SIGN_IN_WITH_YOUR_ACCOUNT') %>
                        </div>
                        <form name="form" class="ng-pristine ng-invalid ng-invalid-required">
                            <div class="form-group m-b-xs">
                                <input id="email" name="email"  type="text" ng-model='user.email' ng-keypress="keyPress('email')" autofocus placeholder="<% trans('client.E_MAIL') %>" class="form-control input-md">
                                <label style="color: #ff0000" ng-show="errors['email'][0]">{{errors['email']}}</label>

                            </div>
                            <div class="form-group m-b-xs">
                                <input id="password" placeholder="<% trans('client.PASSWORD') %>" ng-keypress="keyPress('password')" name="password" type="password" ng-model='user.password' class="form-control input-md">
                                <label style="color: #ff0000" ng-show="errors['password'][0]">{{errors['password']}}</label>
                            </div>
                            <div class="checkbox no-margin">
                                <label class="ui-checks">
                                    <input ng-model="user.remember" type="checkbox"><i></i> <% trans('client.KEEP_ME_SIGNED_IN') %>
                                </label>
                            </div>
                            <button  class="btn btn-info"  ng-click='loginClick()' style="margin-bottom: 10px"><% trans('client.SIGN_IN') %></button>
                            <p class="text-xs"><a href="javascript:void(0)" ng-click="viewClick('forgot')"><% trans('client.FORGOT_PASSWORD') %></a></p>
                            @IF(Config::get('app.self_registration_domain') != '')
                            <p class="m-v-lg text-sm"><% trans('client.DO_NOT_HAVE_AN_ACCOUNT') %> <a ui-sref="signup" href="javascript:void(0)" ng-click="viewClick('register')"><% trans('client.CREATE_AN_ACCOUNT') %></a></p>
                            @ENDIF
                        </form>
                        @IF(Config::get('app.demo'))
                        <div class="text-mb">
                            Demo login
                        </div>
                        <div class="clearfix"></div>
                        <ul style="list-style: none; padding-left: 0px">
                            <li ng-repeat="user in demousers" style="float: left">
                                <a href="javascript:void(0)" ng-click='demoClick($index)'><img class=" thumb-sm avatar m-r"  ng-src="{{getAvatar(user.id)}}"></a>
                            </li>
                        </ul>
                        @ENDIF
                    </div>
                </div>
                <div class="animate-switch" ng-switch-when="forgot">
                    <div class="center-block " >
                        <div class="text-mb">
                            <% trans('client.FORGOT_PASSWORD') %>
                            <small><br><br><% trans('client.CHANGE_YOUR_PASSWORD_MSG') %></small>
                        </div>
                        <form name="form">
                            <div class="form-group m-b-xs">
                                <input type="email" placeholder="eMail" class="form-control" ng-model='forgot.email' required="" style="">
                            </div>

                            <button ng-click='forgotClick()' class="btn btn-info" style="margin-bottom: 10px"><% trans('client.SEND') %></button>
                            <p class="text-xs"><a href="javascript:void(0)" ng-click="viewClick('login')"><% trans('client.RETURN_TO_SIGN_IN') %></a></p>
                        </form>
                    </div>
                </div>
                @IF(Config::get('app.self_registration_domain') != '')
                <div class="animate-switch" ng-switch-when="register">
                    <div class="center-block " >
                        <div class="text-mb">
                            <% trans('client.SIGN_UP_TO_YOUR_ACCOUNT', ['title'=> Config::get('app.app_title')]) %>
                            <small>
                                <br><br>
                                @IF(Config::get('app.self_registration_domain') != '*')
                                <% trans('client.ONLY_USERS_AT', ['domain'=> Config::get('app.self_registration_domain')])%>
                                @ENDIF
                            </small>
                        </div>
                        <form name="form" class="ng-pristine ng-invalid ng-invalid-required">
                            <div class="form-group m-b-xs">
                                <input type="email" placeholder="<% trans('client.E_MAIL') %>" class="form-control" ng-model='register.email' >
                            </div>
                            <button ng-click='registerClick()'  class="btn btn-info" style="margin-bottom: 10px"><% trans('client.SIGN_UP') %></button>
                            <p class="text-xs"><a href="javascript:void(0)" ng-click="viewClick('login')"><% trans('client.RETURN_TO_SIGN_IN') %></a></p>

                        </form>
                    </div>

                </div>
                @ENDIF
                <div class="animate-switch" ng-switch-default></div>
            </div>
        </div>
            <div class="text-center" style="margin-top: 30px">
                <small class="text-muted" style="font-weight:200; color: #e3e3e3"><%Config::get('app.app_footer')%></small>
            </div>
    </div>
</div>


@stop

@section('footer')
<script>
    function shuffle(o){ //v1.0
        for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        return o;
    };

    app.controller('mainCtrl', function($scope, $http, growl ){

        $scope.user = {'email': '', 'password':''};
        $scope.errors = {'email': null, password:null};
        $scope.forgot = {'email': ''};
        $scope.register = {'email': ''};
        $scope.selection = 'login';
        @IF(Config::get('app.demo'))
        $scope.demousers = shuffle([
            {id: 1, email:'demo3@mail.com', password:'123456'},
            {id: 2, email:'demo1@mail.com', password:'123456'},
            {id: 3, email:'demo2@mail.com', password:'123456'},
            {id: 4, email:'demo4@mail.com', password:'123456'},
            {id: 5, email:'demo5@mail.com', password:'123456'},
            {id: 6, email:'demo6@mail.com', password:'123456'},
            {id: 7, email:'demo7@mail.com', password:'123456'}
        ]);
        $scope.getAvatar = function(id) {

            return assetsUrl + '/avatar/' + id + '.jpg';
        }

        $scope.demoClick = function(id)
        {
            $scope.user.email = $scope.demousers[id].email;
            $scope.user.password = $scope.demousers[id].password;
            $scope.loginClick();

        }
        @ENDIF

        $scope.keyPress = function(field)
        {
            $scope.errors[field] = null;
        }

        $scope.forgotClick = function()
        {
            var data = {'_token': _token}
            growl.info("<% trans('client.PLEASE_WAIT') %>");
            $http.put(apiUrl+'/password/'+$scope.forgot['email'], data).success(function(res){
                if(! res.success)
                {
                    growl.warning(res.message);
                } else {
                    growl.success(res.message);
                    $scope.selection = 'login';
                }
            })
        }

        $scope.viewClick = function(view){

            $scope.selection = view;
        }

        $scope.loginClick = function()
        {
            var data = $scope.user;

            data['_token'] = _token;
            $http.post(apiUrl+'/login', data).success(function(res){
                if(! res.success)
                {
                    if(res.errors)
                        $scope.errors = res.errors;
                    if(res.message)
                       growl.warning(res.message);

                } else {
                    document.location.href= '<%URL::to('/')%>';

                }

            })
        }

        $scope.registerClick = function()
        {
            var data = {'_token': _token, 'email': $scope.register['email']};
            growl.info("<% trans('client.PLEASE_WAIT') %>");

            $http.post(apiUrl+'/register', data).success(function(res){
                if(! res.success)
                {
                    if(res.errors)
                       growl.warning( res.errors['email'] );

                    if(res.message)
                        growl.warning( res.message );

                } else {
                    growl.info("<% trans('client.MAIL_SENT') %>");
                    $scope.selection = 'login';

                }

            })
        }

    });

</script>
@stop