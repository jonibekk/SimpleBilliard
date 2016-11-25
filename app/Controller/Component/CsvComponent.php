<?php

class CsvComponent extends CakeObject
{

    public $name = "Csv";

    function initialize()
    {
    }

    function startup($controller)
    {
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    function convertCsvToArray($tmp_file_name)
    {
        //Stores CSV to array
        $res = [];
        $fileName = CACHE . 'new_add_member_' . md5(microtime()) . '.csv';
        if ($this->is_uploaded_file($tmp_file_name)) {
            $this->move_uploaded_file($tmp_file_name, $fileName);
            $pre_data = file_get_contents($fileName);
            //unify the new line code
            //convert encoding
            //bomの除去
            $pre_data = hex2bin(preg_replace("/^fffe/", "", bin2hex($pre_data)));
            //改行の変換
//            $pre_data = preg_replace("/\r\n|\r|\n/", "\n", $pre_data);

            file_put_contents($fileName, mb_convert_encoding($pre_data, "UTF-8", "UTF-16LE"));
            // during empty data,read csv every one line.
            $fp = fopen($fileName, 'r');
            //Japanese disappears when read the multi-byte string using fgetcsv in PHP5,
            // or because some disappear phenomenon occurs the measures.
            setlocale(LC_ALL, 'ja_JP.UTF-8');
            $l_no = 0;
            while ($ret_csv = fgetcsv($fp, null, "\t")) {
                for ($i = 0; $i < count($ret_csv); ++$i) {
                    $res[$l_no][] = $ret_csv[$i];
                }
                $l_no++;
            }
            fclose($fp);
            unlink($fileName);
        }

        return $res;
    }

    function is_uploaded_file($file)
    {
        return is_uploaded_file($file);
    }

    function move_uploaded_file($file_name, $description)
    {
        return move_uploaded_file($file_name, $description);
    }

}
