<?php


$conn=mysqli_connect("localhost", "root", "3Bsgmh8Tzb");

$db_name=mysqli_select_db($conn, "speaker");

mysqli_query($conn, "SET character_set_client='utf8'");
mysqli_query($conn, "SET character_set_connection='utf8'");
mysqli_query($conn, 
"SET character_set_results='utf8'");

//?>