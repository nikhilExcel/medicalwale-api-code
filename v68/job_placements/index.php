<?php

/*
Author         : Akshay Ravtole
Author Email   : akshay@hostingduty.com
Author Website : Hostingduty.com
Licence        : GPL V2
*/


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

require 'vendor/autoload.php';

//initialise the app
$app = new \Slim\App;

//loading app defination from different file
require 'app/user.php';


//running the app
$app->run();
