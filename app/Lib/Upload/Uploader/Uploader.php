<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/31
 * Time: 11:52
 */

interface Uploader
{
    /**
     * Store file temporarily in buffer storage. Will be deleted automatically
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function buffer(UploadedFile $file): string;

    /**
     * Save file to permanent storage
     *
     * @param string $uuid
     *
     * @return UploadedFile
     */
    public function getBuffer(string $uuid): UploadedFile;

    /**
     * Move file from temporary storage to permanent one.
     * Encapsulation for getBuffer() & save()
     *
     * @param string   $modelName
     * @param int      $modelId
     * @param string   $uuid
     * @param callable $preprocess Functions to be applied to UploadedFile before saving
     *
     * @return bool
     */
    public function move(string $modelName, int $modelId, string $uuid, callable $preprocess = null): bool;

    /**
     * Save file to permanent storage
     *
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function save(string $modelName, int $modelId, UploadedFile $file): bool;

    /**
     * Delete file from buffer. Only required for local storage
     *
     * @param string $uuid
     *
     * @return bool
     */
    public function deleteBuffer(string $uuid): bool;

    /**
     * Delete permanent file from storage
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $uuid
     *
     * @return bool
     */
    public function delete(string $modelName, int $modelId, string $uuid): bool;
}