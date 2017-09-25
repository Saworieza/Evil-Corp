<h3><%trans('email.welcome',  ['site_name'=> Config::get('app.app_title', 'Linkr')])%></h3>
<div>
    <?php
        $time = env('invitation_expire',1) * 24;
    ?>
    <%trans('email.register_msg')%><br/><br/><% URL::to('activation', array($token)) %><br/>
    <%trans('email.expire_msg', [ 'time'=> $time])%>
</div>
