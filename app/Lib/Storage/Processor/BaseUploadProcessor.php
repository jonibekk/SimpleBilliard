<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 10:40
 */

abstract class BaseUploadProcessor
{
    /**
     * Process input file
     *
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    abstract public function process(UploadedFile $file): UploadedFile;
}