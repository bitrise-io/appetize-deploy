<?php
namespace DAG\Appetize\Deploy\Archive;

use DAG\Appetize\Deploy\Archive\Abstracts\AbstractArchive;
use DAG\Appetize\Deploy\Archive\Interfaces\ArchiveInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

/**
 * Class IOSArchive
 */
final class IOSArchive extends AbstractArchive implements ArchiveInterface
{
    /**
     * @param string $appFilePath
     *
     * @return string The file path to the archive
     *
     * @throws \Exception
     */
    public function create($appFilePath)
    {
        if (!file_exists($appFilePath) || !is_readable($appFilePath) || !is_dir($appFilePath)) {
            throw new \Exception(sprintf('Invalid file provided "%s"', $appFilePath));
        }

        $zip = $this->createArchive();
        $filePath = $zip->filename;

        $this->addFolderToZip($appFilePath, $zip);

        if (!$zip->close()) {
            throw new \Exception('Can not create the ZIP file');
        }

        return $filePath;
    }

    /**
     * @param string     $appBundle
     * @param ZipArchive $zip
     *
     * @throws \Exception
     */
    private function addFolderToZip($appBundle, ZipArchive $zip)
    {
        $rootPath = realpath($appBundle);

        $appBundleBaseName = basename($appBundle);

        $zip->addEmptyDir($appBundleBaseName);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = $appBundleBaseName.'/'.substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                if (!$zip->addFile($filePath, $relativePath)) {
                    throw new \Exception(sprintf('Can not add file "%s"', $filePath));
                }
            }
        }
    }
}
