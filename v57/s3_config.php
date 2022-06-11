<?php

// Bucket Name
$bucket = "medicalwale";
if (!class_exists('S3'))
    require_once('S3.php');

//AWS access info
if (!defined('awsAccessKey'))
    define('awsAccessKey', 'AKIAJNIZJVO2QJMFTL5Q');
if (!defined('awsSecretKey'))
    define('awsSecretKey', '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');

//instantiate the class
$s3 = new S3(awsAccessKey, awsSecretKey);

$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
?>