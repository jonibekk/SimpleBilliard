<?php
App::uses('AppHelper', 'View/Helper');
App::import('Service', 'ExperimentService');

/**
 * 実験のためのヘルパー
 * このヘルパーはイレギュラーだが利便性を優先し、Serviceを利用する。
 * 本来、ヘルパーからServiceを参照することは許されない。
 *
 * @author daikihirakata
 * @property ExperimentService $Experiment
 */
class ExptHelper extends AppHelper
{
    public function __construct(View $view, $settings = array())
    {
        parent::__construct($view, $settings);

        $this->Experiment = ClassRegistry::init('Experiment');
    }

    /**
     * 実験が存在するかチェック
     *
     * @param $name
     *
     * @return bool
     */
    function is($name)
    {
        return $this->Experiment->isDefined($name);
    }
}
