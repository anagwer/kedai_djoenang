<?php 
require_once 'dbcon.php';

// if (isset($_POST['save'])) {

    session_start();
	session_destroy();
	header("Location:login.php");
	die();
    // }
?>
