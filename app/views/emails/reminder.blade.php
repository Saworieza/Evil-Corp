<h3><%trans('email.password_reset',  ['site_name'=> Config::get('app.app_title', 'Linkr')])%></h3>
<div>
    <%trans('email.password_reset_msg')%><br/><br/><% URL::to('password', array($token)) %><br/>
    <%trans('email.expire_msg', [ 'time'=> 1])%>

</div>
