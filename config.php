<?php
  // Define database connection constants
  define('DB_HOST', 'medicalwale-prod-1-cluster.cluster-cowhfy9plswr.ap-south-1.rds.amazonaws.com');
  define('DB_USER', 'mw_aegis_cron');
  define('DB_PASSWORD', 'bD2}EpJ%a&x)~W#f-R[gOG;!');
  define('DB_NAME', 'medicalw_medicalwaledbc');
  $hconnection = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
  $connection = $conn = $hconn = $hconnection;
?>
