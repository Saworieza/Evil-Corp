<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><% Config::get('config.app_title', 'Linkr')%></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <%HTML::style('build/bootstrap.css')%>
</head>
<body style="margin: 10px">
<div class="bs-component">
    <div class="alert alert-dismissable bg-info">
        <h4>404</h4>
        <h5><%trans('messages.page_not_found')%> / <%trans('messages.link_expired')%></h5>
    </div>
</div>

</body>
</html>
