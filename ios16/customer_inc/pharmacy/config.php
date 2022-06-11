<?php

// Define database connection constants

/*define('DB_HOST', 'localhost');
define('DB_USER', 'medicalw_sandusr');
define('DB_PASSWORD', '*QR1o%oCY2}n');
define('DB_NAME', 'medicalw_sandbox');
$connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$con = mysqli_connect("localhost", "medicalw_sandusr", "*QR1o%oCY2}n")or die(mysqli_error());
mysqli_select_db($con, "medicalw_sandbox") or die(mysql_error());
define('HDB_HOST', 'localhost');
define('HDB_USER', 'medicalw_sandusr');
define('HDB_PASSWORD', '*QR1o%oCY2}n');
define('HDB_NAME', 'medicalw_sandbox');
$hconnection = @mysqli_connect(HDB_HOST, HDB_USER, HDB_PASSWORD, HDB_NAME);
$hconn = @mysqli_connect(HDB_HOST, HDB_USER, HDB_PASSWORD, HDB_NAME);*/

//LIVE CONNECTION

define('DB_HOST', 'medicalwale-prod-db.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com');
define('DB_USER', 'Med_App');
define('DB_PASSWORD', 'aeda3Wee');
define('DB_NAME', 'medicalw_medicalwaledbc');
$connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$con = mysqli_connect("medicalwale-prod-db.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com", "Med_App", "aeda3Wee")or die(mysqli_error());
mysqli_select_db($con, "medicalw_medicalwaledbc") or die(mysql_error());
define('HDB_HOST', 'medicalwale-prod-db.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com');
define('HDB_USER', 'Med_App');
define('HDB_PASSWORD', 'aeda3Wee');
define('HDB_NAME', 'medicalw_medicalwaledbc');
$hconnection = @mysqli_connect(HDB_HOST, HDB_USER, HDB_PASSWORD, HDB_NAME);
$hconn = @mysqli_connect(HDB_HOST, HDB_USER, HDB_PASSWORD, HDB_NAME);

?>