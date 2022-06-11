<?php
/*
Author         : Sumit Shinde
Author Email   : sumit@hostingduty.com
Author Website : Hostingduty.com
Licence        : GPL V2
*/

//$mysqli = new mysqli("localhost","root","root","users");

/* Database Configuration - PDO */

/*$dbhost   = 'localhost';
$dbuser   = 'recharge';
$dbpass   = 'welcome@123';
$dbname   = 'medicalw_medicalwaledbc';
$dbmethod = 'mysql:dbname=';*/

//DATABAS  DETAILS FOR LIVE

/*$dbhost   = 'localhost';
$dbuser   = 'Med_App';
$dbpass   = 'aeda3Wee';
$dbname   = 'medicalw_medicalwaledbc';
$dbmethod = 'mysql:dbname=';*/

//DATABAS  DETAILS FOR SANDBOX

/*$dbhost   = 'localhost';
$dbuser   = 'medicalw_sandusr';
$dbpass   = '*QR1o%oCY2}n';
$dbname   = 'medicalw_sandbox';
$dbmethod = 'mysql:dbname=';*/

$dbhost   = 'medicalwale-prod-1-cluster.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com';
$dbuser   = 'mw_aegis_app';
$dbpass   = 'L_DW49KulmwMpXzkuk&p7G[]jej-Z)Dt';
$dbname   = 'medicalw_medicalwaledbc';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
// $pdo = new PDO($dsn, $dbuser, $dbpass,"charset=utf8");

// $pdo = new PDO("mysql:host=localhost;dbname=medicalw_medicalwaledbc;charset=utf8");

$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
