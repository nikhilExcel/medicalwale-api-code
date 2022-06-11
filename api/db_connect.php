<?php
/*
Author         : Sumit Shinde
Author Email   : sumit@hostingduty.com
Author Website : Hostingduty.com
Licence        : GPL V2
*/

//$mysqli = new mysqli("localhost","root","root","users");

/* Database Configuration - PDO */
$dbhost   = 'medicalwale-prod-1-cluster.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com';
$dbuser   = 'mw_aegis_app';
$dbpass   = 'L_DW49KulmwMpXzkuk&p7G[]jej-Z)Dt';
$dbname   = 'medicalw_medicalwaledbc';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
