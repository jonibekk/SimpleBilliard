<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/02
 * Time: 15:26
 */

interface StorageClient
{
    /**
     * Save file into specified bucket
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function save(UploadedFile $file): string;

    /**
     * Delete file from a bucket
     *
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(string $fileName): bool;

    /**
     * Get a file from a bucket
     *
     * @param string $fileName
     *
     * @return UploadedFile
     */
    public function get(string $fileName): UploadedFile;
}