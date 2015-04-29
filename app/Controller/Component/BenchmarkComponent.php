<?php

//App::uses('Component', 'Controller');
class BenchmarkComponent extends Component
{

    protected $_marks = array();
    protected $_start = null;

    public $name = "Benchmark";

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        parent::__construct($collection, $settings);
        $this->_start = microtime(true);
        $this->mark(__FILE__, __LINE__, 0);

    }

    public function __destruct()
    {
        $this->mark(__FILE__, __LINE__);
        foreach ($this->_marks as $v) {
            $this->log(sprintf('[%01.2fms][%01.2fMB] %s:%d %s', $v['time'], $v['mem'], $v['file'], $v['no'],
                               $v['msg']));
        }

    }

    public function mark($file, $no, $time = null, $msg = null)
    {
        if (!$file || !$no) {
            return;
        }
        if (is_null($time)) {
            $time = microtime(true) - $this->_start;
        }
        //ファイル名を整形
        $file = substr($file, strpos($file, 'app/') + 3, strlen($file));
        $mem = memory_get_usage() / 1024 / 1024; //mega
        $time *= 1000;//convert to ms from s.
        $this->_marks[] = array(
            'time' => $time,
            'file' => $file,
            'no'   => $no,
            'mem'  => $mem,
            'msg'  => $msg,
        );
    }

    function initialize(Controller $controller)
    {
    }

    function startup(Controller $controller)
    {

    }

    function beforeRender(Controller $controller)
    {
    }

    function shutdown(Controller $controller)
    {
    }

    function beforeRedirect(Controller $controller, $url, $status = null, $exit = true)
    {
    }

}
