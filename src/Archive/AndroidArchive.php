<?php
namespace DAG\Appetize\Deploy\Archive;

use DAG\Appetize\Deploy\Archive\Abstracts\AbstractArchive;
use DAG\Appetize\Deploy\Archive\Interfaces\ArchiveInterface;

/**
 * Class AndroidArchive
 */
final class AndroidArchive extends AbstractArchive implements ArchiveInterface
{
    /**
     * @param string $appFilePath
     *
     * @return string
     *
     * @throws \Exception
     */
    public function create($appFilePath)
    {
        if (!file_exists($appFilePath) || !is_readable($appFilePath) || !is_file($appFilePath)) {
            throw new \Exception(sprintf('Invalid file provided "%s"', $appFilePath));
        }

        $zip = $this->createArchive();
        $filePath = $zip->filename;

        $zip->addFile($appFilePath, basename($appFilePath));

        if (!$zip->close()) {
            throw new \Exception('Can not create the ZIP file');
        }

        return $filePath;
    }
}
