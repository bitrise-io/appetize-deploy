#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use DAG\Appetize\Deploy\Command\ProtectAllBuildsCommand;
use DAG\Appetize\Deploy\Command\UploadAppCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$inputArg = [
    basename(__FILE__),
    isset($_SERVER['command']) ? $_SERVER['command'] : 'upload',
    isset($_SERVER['app_path']) ? $_SERVER['app_path'] : null,
    isset($_SERVER['platform']) ? $_SERVER['platform'] : null,
    isset($_SERVER['appetize_token']) ? $_SERVER['appetize_token'] : null,
];

if (isset($_SERVER['public_key']) && $_SERVER['public_key']) {
    $inputArg[] = '--public-key='.$_SERVER['public_key'];
}

if (isset($_SERVER['protected_by_account']) && $_SERVER['protected_by_account']) {
    $inputArg[] = '--protected-by-account';
}

$input = new ArgvInput($inputArg);

$application = new Application(
    'Deploy an app on Appetize.io',
    '@package_version@'
);
$application->add(new UploadAppCommand());
$application->add(new ProtectAllBuildsCommand());
$application->run($input);
