<?php
/*
*
* Logovanje grešaka u fajl
*
*/
require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;


    $log = new Monolog\Logger('name');


    // create writing handler
    $log->pushHandler(new StreamHandler(__DIR__ . "/errors/log.log", Logger::DEBUG));

    // inject monolog's error/exception handler
    $errorHandler = new ErrorHandler($log);
    $errorHandler->registerErrorHandler();
    $errorHandler->registerExceptionHandler();
    $errorHandler->registerFatalHandler();


