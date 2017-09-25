<?php

Artisan::add(new MaintenanceTasks);
Artisan::add(new MailerCommand);
Artisan::add(new UpgradeCommand);
Artisan::add(new VersionCommand);

