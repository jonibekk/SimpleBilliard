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
if (!empty($td)) {
    foreach ($td as $td_v) {
        foreach ($td_v as $v) {
            $this->Csv->addField($v);
        }
        $this->Csv->endRow();
    }
}

$this->Csv->setFilename($filename);
echo $this->Csv->render(true, 'SJIS-win', 'UTF-8');
