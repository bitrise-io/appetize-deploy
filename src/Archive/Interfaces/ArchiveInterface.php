<?php
namespace DAG\Appetize\Deploy\Archive\Interfaces;

/**
 * Interface ArchiveInterface
 */
interface ArchiveInterface
{
    /**
     * @param string $appFilePath
     *
     * @return string
     */
    public function create($appFilePath);
}
