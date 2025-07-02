<?php
	$conn = new mysqli('localhost', 'root', '', 'db_kedai_djoenang');
	
	if(!$conn){
		die("Error: Failed to connect to database");
	}
?>