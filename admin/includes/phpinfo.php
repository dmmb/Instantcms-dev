<?php

    session_start();

    if (!isset($_SESSION['user'])){ exit; }
    if (!isset($_SESSION['user']['is_admin'])) { exit; }
    if (!$_SESSION['user']['is_admin']) { exit; }

    phpinfo();

?>
