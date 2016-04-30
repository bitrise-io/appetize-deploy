#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use DAG\Appetize\Deploy\Command\UploadAppCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$inputArg = [
    basename(__FILE__),
    'upload',
    isset($_SERVER['app_path']) ? $_SERVER['app_path'] : null,
    isset($_SERVER['platform']) ? $_SERVER['platform'] : null,
    isset($_SERVER['appetize_token']) ? $_SERVER['appetize_token'] : null,
];

if (isset($_SERVER['public_key'])) {
    $inputArg[] = '--public-key='.$_SERVER['public_key'];
}

$input = new ArgvInput($inputArg);

$application = new Application(
    'Deploy an app on Appetize.io',
    '@package_version@'
);
$application->add(new UploadAppCommand());
$application->run($input);
