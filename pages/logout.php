<?php
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['user_type']);
session_destroy();
header("Location: /blipblop/index.php");
exit();
