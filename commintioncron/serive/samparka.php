<?php
	$conn = mysqli_connect('localhost', 'lottersi_selimxpro', 'lottersi_selimxpro', 'lottersi_selimxpro');
	
	if (!$conn) {
		echo "Error: " . mysqli_connect_error();
		exit();
	}
	
	date_default_timezone_set("Asia/Kolkata"); 
?>