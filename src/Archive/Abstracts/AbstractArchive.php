<?php
namespace DAG\Appetize\Deploy\Archive\Abstracts;

use ZipArchive;

/**
 * Class AbstractArchive
 */
abstract class AbstractArchive
{
    /**
     * @return ZipArchive
     *
     * @throws \Exception
     */
    protected function createArchive()
    {
        // Create file
        $filename = $this->generateZipFilename();
        touch($filename);
        $zip = new ZipArchive();

        if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception(sprintf('Can not create file "%s"', $filename));
        }

        return $zip;
    }

    /**
     * @return string
     */
    protected function generateZipFilename()
    {
        return sys_get_temp_dir().'/app'.rand(1000, 9999).'.zip';
    }
}
