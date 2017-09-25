<?php
  $public =  $_ENV['ROOTED_PUBLIC'] ? 'public/' : '';
  $ran = rand(1,999999);
?>
<!DOCTYPE html>
<html lang="en" ng-app="app" ng-cloak>
  <head>
    <meta charset="utf-8">
    <title><% Config::get('app.app_title', 'Linkr')%></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <%HTML::style($public.'build/'.env('THEME', 'default').'.min.css?'.$ran)%>
      <%HTML::style($public.'build/font-awesome.css')%>

      @if(file_exists($public.('build/custom.css')))
      <%HTML::style($public.'build/custom.css')%>
      @endif

  </head>
  <style>
      .aside-chat-panel {
          max-height: calc(100% - 75px);
          height: calc(100% - 75px);
          overflow-y: scroll !important;
          max-width: 400px;
          padding: 5px;
      }
  </style>
<body ng-controller="mainCtrl">
<div id="mask" data-ng-show="false">
    <div id="loader">
    </div>
</div>

<div growl></div>
<header class="navbar navbar-inverse navbar-fixed-top bs-docs-nasv" role="banner">
    <div class="container-fluid">
        <div class="navbar-header">

            <button class="navbar-toggle hidden-lg"
                    type="button"
                    data-template="partials/aside.html"
                    data-animation="am-fade-and-slide-left" data-placement="left" title="Menu" data-container="body" bs-aside="">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="./" class="navbar-brand"><% Config::get('app.app_title', 'Linkr')%></a>


        </div>
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <ul class="nav navbar-nav hidden-sm hidden-md">

                <li>
                    <a ui-sref="home"><i class="fa fa-home"></i> {{'HOME' | translate}}</a>
                </li>
                <li>
                    <a href="javascript:void(0)"
                       data-template="partials/aside-spaces.html"
                       data-animation="am-fade-and-slide-left" data-placement="left"
                       data-container="body" bs-aside=""><i class="fa fa-comments-o"></i> {{'Groups' | translate}}
                    </a>
                </li>
                @IF(!env('HIDE_PEOPLE', false))
                <li>
                    <a ui-sref="people"><i class="fa fa-users"></i> {{'PEOPLE' | translate}}</a>
                </li>
                @ENDIF
                @IF(!env('HIDE_TASKS', false))
                <li>
                    <a ui-sref="tasks"><i class="fa fa-tasks"></i> {{'TASKS' | translate}}</a>
                </li>
                @ENDIF
                @IF(!env('HIDE_CALENDAR', false))
                <li>
                    <a ui-sref="calendar"><i class="fa fa-calendar"></i> {{'CALENDAR' | translate}}</a>
                </li>
                @ENDIF
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <span us-spinner="{width:2, length:4, shadow:on, color:'#fff', hwaccel:on,radius:7}" spinner-key="appSpinner"></span>
                </li>

                <li>
                    <input type="search" class="form-control" id='search-box' placeholder=" {{'SEARCH' | translate}}" ng-model="search.text" ng-enter="doSearch()">

                </li>
                <li>
                    <a ui-sref="messages">&nbsp;<i class="fa fa-envelope"></i>&nbsp;<span ng-if='msgCount>0' class="badge badge-white">{{msgCount}}</span></a>
                </li>
                <li>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"><i class='fa fa-user'></i> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @if(Auth::user()->admin)
                        <li><a ui-sref="admin">{{'SITE_ADMIN' | translate}}</a></li>
                        @endif
                        <li><a ui-sref="profile">{{'MY_PROFILE' | translate}}</a></li>
                        <li class="divider"></li>
                        <li><a href="<%URL::to('/logout')%>">{{'LOGOUT' | translate}}</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</header>
<div ui-view></div>
<%HTML::script($public.'build/lib.min.js')%>
@if(App::environment()=='local')
<!-- DEV MODE--------------------------------- -->
<%HTML::script($public.'dev/app/util.js')%>
<%HTML::script($public.'dev/app/app.js')%>
<%HTML::scripts($public.'dev/app/controllers')%>
<%HTML::scripts($public.'dev/app/directives')%>
<%HTML::scripts($public.'dev/app/services')%>
<%HTML::scripts($public.'dev/app/filters')%>
<!-- END DEV MODE--------------------------------- -->
@else
 <%HTML::script($public.'build/app.min.js')%>
@endif

<%HTML::script($public.'build/app.tpls.min.js')%>
<%HTML::script('lang')%>

<script type="text/javascript">
    var user = {id:<%Auth::user()->id%>, fullname:'<%addslashes(Auth::user()->full_name)%>', cs: <% Auth::user()->create_spaces;%>};
    var apiUrl = "<%URL::to('api/v1');%>";
    var siteUrl = "<%URL::to('/');%>";
    var assetsUrl = "<%URL::to($public.'assets');%>";
    var token = "<%csrf_token() %>";
    var modules = [];
    var hm = <% intval( env('HIDE_MODERATORS', false)); %>;
    var showPhone = <% intval( env('PEOPLE_SHOW_PHONE', false)); %>;
    var showOrg = <% intval( env('PEOPLE_SHOW_ORG', false)); %>;
    var profileFields = <% json_encode(["facebook"=> env('PROFILE_FACEBOOK', true), "google"=> env('PROFILE_GOOGLE', true),
        "linkedin"=> env('PROFILE_LINKEDIN', true), "twitter"=> env('PROFILE_TWITTER', true), "github"=> env('PROFILE_GITHUB', true), "skype"=> env('PROFILE_SKYPE', true)]);
    %>;
    var locale = "<%Config::get('app.locale')%>";
    var snd = new Audio("<%URL::to($public.'assets/sounds/click.mp3');%>");
    var sndBell = new Audio("<%URL::to($public.'assets/sounds/bell.mp3');%>");
    var appVersion = "<%Config::get('app.version')%>";
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
</script>
<%HTML::script('modules')%>

</body>
</html>