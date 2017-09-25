<?php

define('ROLE_NONE', -1);
define('ROLE_SUSPENDED', 0);
define('ROLE_INVITED', 1);
define('ROLE_MEMBER', 2);
define('ROLE_MODERATOR', 3);

define('USER_STATE_SUSPENDED',0);
define('USER_STATE_INVITED',1);
define('USER_STATE_ACTIVE',2);
define('USER_STATE_DELETED',3);

define('ALERT_SUCCESS', 'success');
define('ALERT_INFO', 'info');
define('ALERT_WARNING', 'warning');
define('ALERT_DANGER', 'danger');

define('CONTENT_MESSAGE', 0);
define('CONTENT_LINK', 1);
define('CONTENT_TABLE', 2);
define('CONTENT_CHART', 3);
define('CONTENT_POLL', 4);
define('CONTENT_EVENT', 5);
define('CONTENT_LOCATION', 6);
define('CONTENT_WIKI', 7);

define('TASK_ROLE_NONE',0);
define('TASK_ROLE_ASSIGNED',1);
define('TASK_ROLE_CREATOR',2);

define('MSG_PRIVATE',0);
define('MSG_NOTIF_NOSEQ',2);
