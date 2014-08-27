<?php
/**
 * Data Shell
 */
App::uses('AppShell', 'Console/Command');
App::uses('File', "Core");

class DataShell extends AppShell
{
    protected $directory;
    protected $connection = 'default';

    public function startup()
    {
        parent::startup();

        if (!empty($this->params['directory'])) {
            $this->directory = $this->params['directory'];
        }
        else {
            $this->directory = APP . 'Config' . DS . 'Schema' . DS . 'data';
        }
        $this->directory .= DS;

        if (!empty($this->params['connection'])) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $connection = $this->params['connection'];
        }

        if (empty($this->params['name']) && !empty($this->args[0])) {
            $this->params['name'] = $this->args[0];
        }
    }

    public function main()
    {
        $this->help();
    }

    public function help()
    {
        $help = <<<TEXT
<info>Usage:</info>
	cake data export <name>
	cake data import <name>
	cake data local_test_import <name>
	cake data display

<info>Option:</info>
	--directory <directory>  :   Export/Import Directory.
	                             Default: `APP/config/schema/data'
	--connection <connection>:   Choise to connection configure.
	                             Default: `default'
TEXT;
        $this->out(__d("cake_console", $help));
        $this->_stop();
    }

    public function display()
    {
        $modelNames = App::objects('model');

        $this->out(__d('cake_console', sprintf("<info>%13s : %5s</info>", "Model Name", "Count")));

        foreach ($modelNames as $modelName) {
            $Model = ClassRegistry::init($modelName);
            $Model->useDbConfig = $this->connection;

            try {
                $count = $Model->find('count', array("callbacks" => false));
                $this->out(__d('cake_console', sprintf("%13s : %5s", $modelName, $count)));
            } catch (Exception $e) {
                // pass
            }
        }
        $this->_stop();
    }

    public function export()
    {
        if (!empty($this->params['name'])) {
            $modelNames = array(Inflector::camelize(Inflector::singularize($this->params['name'])));
        }
        else {
            $modelNames = App::objects('model');
        }

        foreach ($modelNames as $modelName) {
            $Model = ClassRegistry::init($modelName);
            $Model->useDbConfig = $this->connection;

            $records = $Model->find('all', array('recursive' => -1, 'callbacks' => false));

            if (empty($records)) {
                continue;
            }

            $content = "<?php\n";
            $content .= "class {$Model->name}Data {\n";
            $content .= "\tpublic \$name = '{$Model->name}';\n\n";
            $content .= "\tpublic \$records = array(";

            foreach ($records as $record) {
                $values = array("\n\t\tarray(");
                foreach ($record[$Model->alias] as $field => $value) {
                    if (is_null($value)) {
                        $values[] = "\t'$field' => null,";
                    }
                    elseif (is_numeric($value)) {
                        $values[] = "\t'$field' => $value,";
                    }
                    else {
                        $values[] = "\t'$field' => '$value',";
                    }
                }
                $values[] = "),";
                $content .= implode("\n\t\t", $values);
            }
            $content .= "\n\t);\n";
            $content .= '}';

            $filePath = $this->directory . $Model->name . 'Data.php';
            $file = new File($filePath, true);
            $file->write($content);
            $file->close($filePath);

            $this->out('Data exported: ' . $Model->name);
        }
    }

    public function import()
    {
        if (isset($this->params['name'])) {
            $dataObjects = array(Inflector::camelize(Inflector::singularize($this->params['name'])) . 'Data');
        }
        else {
            $dataObjects = App::objects('class', $this->directory);
        }

        $passFields = null;
        if (array_key_exists('pass', $this->params)) {
            $passFields = array('created', 'updated', 'modified');
        }

        foreach ($dataObjects as $data) {
            // App::uses($data, "Config/Schema/data");
            // require_once($this->directory."/".$data);
            // App::import('Config/Schema/data', $data, false, $this->directory);
            // var_dump($this);
            App::uses($data, "Config/Schema/data/");
            $vars = get_class_vars($data);
            if (!$vars) {
                throw new Exception("Not found " . $data);
            }
            // var_dump(extract($data));
            extract(get_class_vars($data));

            if (empty($records) || !is_array($records)) {
                continue;
            }

            /** @noinspection PhpUndefinedVariableInspection */
            $Model = ClassRegistry::init($name);
            $Model->useDbConfig = $this->connection;
            if ($passFields) {
                foreach ($records as &$record) {
                    foreach ($passFields as $field) {
                        unset($record[$field]);
                    }
                }
            }
            $Model->query("TRUNCATE `$Model->table`");

            $success = 'Failed';
            if ($Model->saveAll($records, array('validate' => false))) {
                $success = 'Success';
            }

            $this->out("Data imported: $Model->name [$success]");
        }
    }

    public function local_test_import()
    {
        //既にテストデータが存在する場合は処理しない
        $Model = ClassRegistry::init('User');
        $result = $Model->findById(1);
        if (!empty($result)) {
            $this->out(__d('global', '既にテストデータが存在するため、テストデータのインポートをキャンセルしました。'));
            return;
        }

        $this->directory = APP . 'Config' . DS . 'Schema' . DS . 'local_test_data';

        if (isset($this->params['name'])) {
            $dataObjects = array(Inflector::camelize(Inflector::singularize($this->params['name'])) . 'Data');
        }
        else {
            $dataObjects = App::objects('class', $this->directory);
        }

        $passFields = null;
        if (array_key_exists('pass', $this->params)) {
            $passFields = array('created', 'updated', 'modified');
        }

        foreach ($dataObjects as $data) {
            App::uses($data, "Config/Schema/local_test_data/");
            $vars = get_class_vars($data);
            if (!$vars) {
                throw new Exception("Not found " . $data);
            }
            // var_dump(extract($data));
            extract(get_class_vars($data));

            if (empty($records) || !is_array($records)) {
                continue;
            }

            /** @noinspection PhpUndefinedVariableInspection */
            $Model = ClassRegistry::init($name);
            $Model->useDbConfig = $this->connection;
            if ($passFields) {
                foreach ($records as &$record) {
                    foreach ($passFields as $field) {
                        unset($record[$field]);
                    }
                }
            }
            $Model->query("TRUNCATE `$Model->table`");

            $success = 'Failed';
            if ($Model->saveAll($records, array('validate' => false))) {
                $success = 'Success';
            }

            $this->out("Data imported: $Model->name [$success]");
        }
    }
}