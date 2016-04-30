<?php

use DAG\Appetize\Deploy\Archive\IOSArchive;

class BundleArchiveTest extends PHPUnit_Framework_TestCase
{
    public function testCreateAnArchiveWithAnAppleApp()
    {
        $appFilePath = __DIR__.'/samples/helloworld.app';

        $bundleArchive = new IOSArchive();
        $archiveFilePath = $bundleArchive->create($appFilePath);

        $this->assertFileExists($archiveFilePath);
    }
}
