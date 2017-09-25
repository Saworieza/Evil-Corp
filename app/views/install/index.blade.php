<?php
  $public =  $_ENV['ROOTED_PUBLIC'] ? 'public/' : '';

?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <meta charset="utf-8">
    <title>Linkr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <%HTML::style($public.'build/bootstrap.css')%>
    <%HTML::script($public.'build/basiclib.min.js')%>
    <%HTML::style($public.'build/font-awesome.css')%>
<style>
    .form-control {
        margin-bottom: 10px;
    }
    .alert {
        padding: 0px;
    }
    .alert-warning, .alert-success {
        padding: 5px;
        font-size: 13px;
    }
    h5{
        border-bottom: 1px solid #bbb;
        padding-bottom: 4px;
        padding-top: 9px;
    }
    .alert-warning pre {
        margin-bottom: 0px;
        margin-top: 5px;
        background-color: transparent;
        color: #fff;
        padding: 0px 0px 10px 0px;
        border: none;

    }
    .alert-warning a {
        color : #fff;
        text-decoration: underline;
    }
</style>
</head>
<body style="padding: 10px" ng-controller="mainCtrl">
<div class="col-md-offset-3">
    <h2>Linkr Installer</h2>
    <br>

</div>
<div class="animate-switch-container" ng-switch on="selection">
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="0">
        <h4>Server Requirements</h4>

        <h6>&nbsp;</h6>
        <h6><i class="fa fa-fw" ng-class="serverCheck.php ? 'fa-check-square-o' : 'fa-square-o'"></i>PHP >= 5.4</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.php">
            Linkr requires at least php 5.4.0
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.pdo ? 'fa-check-square-o' : 'fa-square-o'"></i>PDO PHP Extension</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.pdo">
            PDO is required, please refer to this <a href="http://php.net/manual/en/pdo.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.pdo_mysql ? 'fa-check-square-o' : 'fa-square-o'"></i>PDO MySQL Driver</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.pdo">
            PDO MySQL driver is required, please refer to this <a href="http://php.net/manual/en/pdo.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.mcrypt ? 'fa-check-square-o' : 'fa-square-o'"></i>MCrypt PHP Extension</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.mcrypt">
            MCrypt PHP extension is required, please refer to this <a href="http://php.net/manual/en/mcrypt.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.curl ? 'fa-check-square-o' : 'fa-square-o'"></i>cURL PHP Extension</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.curl">
            cURL PHP extension is required, please refer to this <a href="http://php.net/manual/en/curl.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.json ? 'fa-check-square-o' : 'fa-square-o'"></i>JSON PHP Extension</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.json">
            JSON PHP extension is required, please refer to this <a href="http://php.net/manual/en/json.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.gd ? 'fa-check-square-o' : 'fa-square-o'"></i>GD PHP Extension</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.gd">
            GD PHP extension is required, please refer to this <a href="http://php.net/manual/en/image.installation.php" target="_blank">link</a> for instructions.
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.gd ? 'fa-check-square-o' : 'fa-square-o'"></i>Writable configuration file</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.env">
            <strong>{{serverCheck.env_file}} can not be created or is not writable by the server.</strong><br>
            If the file exists, set read and write permissions for everyone:<br>
            <pre>chmod 666 {{serverCheck.env_file}}</pre>
            If the file does not exists, create en empty one and give it the required permissions:<br>
            <pre>touch {{serverCheck.env_file}}
chmod 666 {{serverCheck.env_file}}</pre>
            You may need to use sudo
        </div>
        <h6><i class="fa fa-fw" ng-class="serverCheck.avatar ? 'fa-check-square-o' : 'fa-square-o'"></i>Writable avatars folder</h6>
        <div class="alert alert-warning" ng-show="! serverCheck.avatar">
            <strong>{{serverCheck.avatar_dir}} is not writable by the server.</strong><br>
            Please, give it read and write permissions for everyone:<br>
            <pre>chmod -R 0777 {{serverCheck.avatar_dir}}</pre>
            You may need to use sudo


        </div>

        <br>
    </div>
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="1">
        <h4>Site Info</h4>
        <h6>&nbsp;</h6>
        <div class="form-group">
            <label class="control-label">Title</label>
            <input class="form-control" ng-model="conf.app_title">
            <label class="control-label">Subtitle</label>
            <input class="form-control" ng-model="conf.app_subtitle">
            <label class="control-label">Copyright information</label>
            <input class="form-control" ng-model="conf.app_copyright">
        </div><br>
        <small>All fields are required</small>

    </div>
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="2">
        <h4>Database Setup</h4>
        <h6>&nbsp;</h6>
        <div class="form-group">
            <label class="control-label">Host</label>
            <input class="form-control" ng-model="conf.db_host" ng-change="dbChange()">
            <label class="control-label">Database</label>
            <input class="form-control" ng-model="conf.db_database" ng-change="dbChange()">
            <label class="control-label">User name</label>
            <input class="form-control" ng-model="conf.db_username" ng-change="dbChange()">
            <label class="control-label">Password</label>
            <input type="password" ng-model="conf.db_password" class="form-control" ng-change="dbChange()">
            <label class="control-label">Table prefix</label>
            <input class="form-control" ng-model="db_prefix">
        </div>
        <button class="btn btn-info btn-sm" ng-click="testDB()">Test connection</button>
        <br><br>
        <div class="alert alert-success" ng-show="dbTest.db == true">
            Successfully connected to the database.
        </div>
        <div class="alert alert-warning" ng-show="dbTest.db == false">
            {{dbTest.msg}}
        </div>

    </div>
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="3">
        <h4>Files Folder</h4>
        <div class="alert">
            Folder to store user-uploaded files.<br>
            Make sure that the webserver has read/write access to this folder.
            For security reasons, it's advisable to locate this folder outside of the www root.

        </div>
        <div class="form-group">

            <label class="control-label">Folder</label>
            <input class="form-control" ng-model="conf.files_folder" ng-change="folderChanged()">
            <br>
            <button class="btn btn-info btn-sm" ng-click="testFilesFolder()">Test folder</button>
            <br><br>
            <div class="alert alert-warning" ng-show="filesTest == false">
                <strong>Folder not found or is not writable by the server.</strong><br>
                For example, to create a folder at the root level named "linkrfiles, execute these commands:<br>
                <pre>mkdir /linkrfiles
chmod -R 0777 /linkrfiles
</pre>
                You may need to use sudo
            </div>
            <small>Your PHP working path is <% getcwd()%><br>
            You can also specify a relative path to it (ie, ../../linkrfiles)
            </small>
            <div class="alert alert-success" ng-show="filesTest == true">
               Folder OK.
            </div>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="4">
        <h4>eMail</h4>
        <div class="form-group">
            <div class="alert">
                Linkr (Laravel) supports SMTP and PHP's "mail" function as drivers for the
                sending of e-mail as well as dedicated email services like Mandrill and Mailgun.
                You may specify which one you're using throughout your application here.<br>
                We strongly recommend the use of a dedicated service insted of a smtp account.
            </div>
            <label class="control-label">Driver</label>
            <select class="form-control" ng-model="conf.mail_driver">
                <option value="mandrill">Mandrill</option>
                <option value="mailgun">Mailgun</option>
                <option value="smtp">SMTP</option>
                <option value="mail">mail</option>
                <option value="sendmail">sendmail</option>
            </select>
            <label class="control-label">"From" Address</label>
            <input class="form-control" ng-model="conf.mail_from_address">

            <label class="control-label">"From" Name</label>
            <input class="form-control" ng-model="conf.mail_from_name">

            <div ng-show="conf.mail_driver=='smtp'">
                <label class="control-label">SMTP Host Address</label>
                <input class="form-control" ng-model="conf.mail_host">

                <label class="control-label">SMTP Host Port</label>
                <input class="form-control" ng-model="conf.mail_port">

                <label class="control-label">E-Mail Encryption Protocol</label>
                <input class="form-control" ng-model="conf.mail_encryption">

                <label class="control-label">SMTP Server Username</label>
                <input class="form-control" ng-model="conf.mail_username">

                <label class="control-label">SMTP Server Password</label>
                <input class="form-control" type="password" ng-model="conf.mail_password">

                <label class="control-label">Confirm SMTP Server Password</label>
                <input class="form-control" type="password" ng-model="conf.mail_password_conf">
            </div>
            <div ng-show="conf.mail_driver=='mandrill'">
                <label class="control-label">Mandrill API KEY</label>
                <input class="form-control" ng-model="conf.mail_mandrill_secret">
            </div>
            <div ng-show="conf.mail_driver=='mailgun'">
                <label class="control-label">Mailgun Domain</label>
                <input class="form-control" ng-model="conf.mail_mailgun_domain">

                <label class="control-label">Mailgun API KEY</label>
                <input class="form-control" ng-model="conf.mail_mailgun_secret">
            </div>



            <div ng-show="conf.mail_driver=='sendmail'">
                <label class="control-label">Sendmail System Path (Only when using the "sendmail" driver)</label>
                <input class="form-control" ng-model="conf.mail_sendmail">
            </div>
        </div>
        <br>
        <input class="form-control" ng-model="conf.mail_to" placeholder="EMail test destination address">

        <button class="btn btn-info btn-sm" ng-click="testEMail()">Test eMail</button>
        <br><br>
        <div ng-show="emailTesting == true">Testing...</div>
        <div class="alert alert-warning" ng-show="emailTest.success == false">
            {{emailTest.msg}}
        </div>

        <div class="alert alert-success" ng-show="emailTest.success == true">
            eMail sent.
        </div>
    </div>

    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="5">
        <h4>Options</h4>
        <div class="form-group">
            <div class="col-md-12">
               <h5>Self Registration Domain</h5>
                <div class="alert">
                    Self registration may be restricted to a specific domain.
                    Only users with email addresses from that domain will be allowed to self register (if you need more than one domain, separate them with commas).
                    You can also allow any domain name for self-registration entering "*" (asterisk).
                    Otherwise leave it blank to dsisable self-registration.
                </div>
                <label class="control-label">Domain</label>
                <input class="form-control" ng-model="conf.self_registration" placeholder="mycompany.com">
            </div>
            <div class="col-md-12">
                <h5>Localization</h5>
                <div class="alert">
                    Set to "en" for english, "es" for spanish or refer to readme.md for instructions on how to make your own translation.
                </div>
                    <label class="control-label" >Locale</label>
                    <input class="form-control" ng-model="conf.locale">
            </div>
            <div class="col-md-12">
                <h5>Image Resizing</h5>
                <div class="alert">
                    Image resize after uploading or set width = 0 and height = 0 to leave images untouched.

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label">Width</label>
                        <input class="form-control" ng-model="conf.image_w">
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">Height</label>
                        <input class="form-control" ng-model="conf.image_h">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="6">
        <h4>Administrator</h4>
        <div class="form-group">
            <label class="control-label">Full name</label>
            <input class="form-control" ng-model="conf.admin_fullname">
            <label class="control-label">eMail</label>
            <input class="form-control" ng-model="conf.admin_email">
            <label class="control-label">Password</label>
            <input class="form-control" type="password" ng-model="conf.admin_password">
            <label class="control-label">Confirm password</label>
            <input class="form-control" type="password" ng-model="conf.admin_password_conf">
            <label ng-show="admin_password_error">Password and password confirmation does not match.</label>
        </div>
        <small>All fields are required</small>
    </div>

    <div class="col-md-6 col-md-offset-3 well" ng-switch-when="7">
        <h4>Installing Linkr</h4>
        <h5 ng-show="installing == true">Please wait</h5>
        <h5 ng-show="installed.success == true">Done. <%link_to('/','Visit your site')%></h5>
        <div class="alert alert-warning" ng-show="installed.success == false">{{installed.msg}}</div>

    </div>

    <div class="col-md-6 col-md-offset-3"  style="padding-left: 0px">
        <button class="btn btn-info" ng-if='selection > 0 && selection < 7' ng-click="prevClick()">Previous</button>

        <button class="btn btn-info" ng-show = 'canNext && selection < 6' ng-click="nextClick()">Next</button>
        <button class="btn btn-info" ng-show = 'selection == 6' ng-click="InstallClick()">Install</button>
        <button class="btn btn-default" ng-show = '! canNext' ">Next</button>
    </div>


</div>
<div class="col-md-12">&nbsp;</div>
</body>

<script>
    var apiUrl = "<%URL::to('/')%>";
    var app = angular.module('app', [ 'ngAnimate', 'angular-growl']);
    app.config(['growlProvider', function (growlProvider) {
        growlProvider.globalTimeToLive(3000);
    }]);

    var apiUrl = "<%URL::to('/')%>";
    var _token = "<%csrf_token() %>";

    app.controller('mainCtrl', function($scope, $http, growl ){

        $scope.canNext = false;
        $scope.dbTest = {};
        $scope.filesTest = {};
        $scope.dbRes = {};
        $scope.installing = false;

        $scope.admin_password_error = false;
        $scope.conf = {
            app_title     : 'Linkr',
            app_subtitle  : 'Enterprise Social Network',
            app_copyright : '© 2014 xnCode',
            db_host       : 'localhost',
            db_database   : 'linkr',
            db_username   : '',
            db_password   : '',
            db_prefix     : '',
            files_folder  : '/linkrfiles',
            mail_driver   : 'mandrill',
            mail_host     : 'smtp.gmail.com',
            mail_port     : 587,
            mail_encryption : 'tls',
            mail_username   : 'yourgmailusername',
            mail_password   : '',
            mail_from_address  : 'youraddress@gmail.com',
            mail_from_name  : 'Linkr',
            mail_to         : '',
            mail_mandrill_secret   : '',
            mail_mailgun_domain   : '',
            mail_mailgun_secret   : '',
            mail_sendmail   : '/usr/sbin/sendmail -bs',
            self_registration : '',
            locale          : 'en',
            image_w         : 960,
            image_h         : 540,
            admin_fullname : '',
            admin_email     : '',
            admin_password  : '',
            admin_password_conf : ''


        };

        $scope.InstallClick = function()
        {
            $scope.selection = 7;
            $scope.installing = true;
            $http.post(apiUrl + '/install', $scope.conf).success(
                function(res){
                    $scope.installed = res;
                    $scope.installing = false;
                }
            )
        }

        $scope.prevClick = function()
        {
            $scope.selection--;
            $scope.canNext = true;
            if($scope.selection == 2 || $scope.selection == 3)
            {
                $scope.canNext = false;
            }

        }
        $scope.nextClick = function()
        {
            if($scope.selection == 1)
            {
                if($scope.conf.app_title == '' || $scope.conf.app_subtitle == '' || $scope.conf.app_copyright == '') return;
                $scope.canNext = false;
            }
            if($scope.selection == 6)
            {
                $scope.admin_password_error = false;
                if($scope.conf.admin_fullname == '' || $scope.conf.admin_email == '' || $scope.conf.admin_password == '' || $scope.conf.admin_password_conf == '') return;
                if($scope.conf.admin_password != $scope.conf.admin_password_conf ){
                    $scope.admin_password_error = true;
                    return;
                }
            }

            if($scope.selection == 2)
            {
                $scope.canNext = false;
            }
            $scope.selection++;
        }

        $scope.testEMail = function(){
            $scope.emailTesting = true;
            $scope.emailTest = {};
            $http.get(apiUrl + '/install/testemail', {params: $scope.conf}).success(
                function(res){
                    $scope.emailTesting = false;
                    $scope.emailTest = res;
                }
            )
        }

        $scope.folderChanged = function()
        {
            $scope.canNext = false;
            $scope.filesTest = {};
        }

        $scope.testFilesFolder = function()
        {
            $scope.canNext = false;
            $scope.filesTest = {};

            $http.get(apiUrl + '/install/testfilesfolder', {params: $scope.conf}).success(
                function(res){
                    $scope.filesTest = res.success;
                    $scope.canNext = res.success;
                }
            )
        }

        $scope.testDB = function()
        {
            $scope.canNext = false;

            $http.get(apiUrl + '/install/testdb', {params: $scope.conf}).success(
                function(res){
                    $scope.dbTest = res;
                    $scope.canNext = res.db;
                }
            )
        }

        $scope.dbChange = function(){
            $scope.canNext = false;
            $scope.dbTest = {};
        }

        $scope.serverCheck = function() {
            $http.get(apiUrl+'/install/servercheck').success(
                function(res){
                    $scope.canNext = res.curl && res.gd && res.mcrypt && res.pdo && res.pdo_mysql && res.php && res.env;
                    $scope.serverCheck = res;
                    $scope.selection = 0
                    console.log(res);
                }
            )
        }

        $scope.serverCheck();
    });

</script>

</html>
