<?php
require_once __DIR__ . '/../app/auth.php';
logout_admin();
redirect('login.php');
