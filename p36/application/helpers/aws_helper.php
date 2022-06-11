<?php
require_once('aws-autoloader.php');
use Aws\S3\S3Client;


function DeleteFromToS3($filename){
    $bucket='medicalwale';
    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => 'us-east-1',
        'credentials' => [
            'key'    => 'AKIAJNIZJVO2QJMFTL5Q',
            'secret' => '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',
        ]
    ]);
    $s3->deleteObject([
        'Bucket' => $bucket,
        'Key'    => $filename,
    ]);
}

function getExtension($str)
{
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l   = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
}


?>