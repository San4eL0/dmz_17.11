<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/validation.php';

session_start_safe();
adminLogout();
header('Location: login.php');
exit;