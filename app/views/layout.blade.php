<?php
  $public =  $_ENV['ROOTED_PUBLIC'] ? 'public/' : '';
  
?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <meta charset="utf-8">
    <title><%Config::get('app.app_title')%></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <%HTML::style($public.'build/'.env('THEME', 'default').'.min.css?')%>
    @if(file_exists($public.('build/custom.css')))
    <%HTML::style($public.'build/custom.css')%>
    @endif
    <%HTML::script($public.'build/basiclib.min.js')%>
    <style>
        .animate-switch-container {
            position:relative;
            background-color: #fff;
            height:<%330+(Config::get('app.demo') ? 80 : 0)+(Config::get('app.self_registration_domain')!='' ? 25:0)%>px;
            overflow:hidden;
            padding: 20px;
        }

        .animate-switch.ng-animate {
            -webkit-transition:all cubic-bezier(0.250, 0.460, 0.450, 0.940) 0.8s;
            transition:all cubic-bezier(0.250, 0.460, 0.450, 0.940) 0.8s;
            position:absolute;
            top:0;
            left:0;
            right:0;
            bottom:0;
        }


    </style>
    @yield('head')
</head>
<body class="auth-body" ng-controller="mainCtrl">
<div growl></div>
@yield('body')
</body>
<script>
    var app = angular.module('app', [ 'ngAnimate', 'angular-growl']);
    app.config(['growlProvider', function (growlProvider) {
        growlProvider.globalTimeToLive(3000);
    }]);
    var assetsUrl = "<%URL::to($public.'assets');%>";
    var apiUrl = "<%URL::to('/')%>";
    var _token = "<%csrf_token() %>";
</script>

@yield('footer')

</html>
