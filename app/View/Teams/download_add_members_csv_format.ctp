<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/11/12
 * Time: 19:27
 *
 * @var CodeCompletionView $this
 * @var                    $th
 * @var                    $td
 * @var                    $filename
 */
$this->Csv->addRow($th);
$this->Csv->setFilename($filename);
echo $this->Csv->render(true, 'SJIS-win', 'UTF-8');
