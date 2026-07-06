<?php
require_once 'config.php';
require_once 'db.php';
require_once 'auth.php';

$auth->logout();
redirect(BASE_URL);
