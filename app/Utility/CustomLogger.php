<?php
App::uses('AppUtil', 'Util');
App::import('Utility', 'CustomLogger');

class CustomLogger {
    private static $instance = NULL;

    /**
     * @var array
     */
    protected $controllerData;

    private function __construct() {}

    public static function getInstance() {
        if(self::$instance === NULL) {
            self::$instance = new CustomLogger();
        }
        return self::$instance;
    }

    public function setControllerData($jwtData, $sessionData) 
    {
        $this->controllerData['jwt'] = $jwtData;
        $this->controllerData['session'] = $sessionData;
        $flattenedData = AppUtil::flattenArrayPath($this->controllerData);

        if (extension_loaded('newrelic')) {
            foreach ($flattenedData as $key => $value) {
                newrelic_add_custom_parameter($key, $value);
            }
        }
    }

    public function logException(Exception $exception)
    {
        $loggedData = [
            'exception' => $exception->__toString(),
            'controllerData' => $this->controllerData
        ];

        if (extension_loaded('newrelic')) {
            newrelic_notice_error($exception);
        }

        GoalousLog::error('Exception raised', $this->appendNewrelicMetadata($loggedData));
    }

    public function logEvent(string $name, array $data = [])
    {
        $loggedData = [
            'data' => $data,
            'controllerData' => $this->controllerData
        ];

        if (extension_loaded('newrelic')) {
            $flattenedData = AppUtil::flattenArrayPath($loggedData);
            newrelic_record_custom_event($name, $flattenedData);
        }

        GoalousLog::info($name, $this->appendNewrelicMetadata($loggedData));
    }

    private function appendNewrelicMetadata($data) 
    {
        if (extension_loaded('newrelic')) {
            return array_merge($data, newrelic_get_linking_metadata());
        }
        return $data;
    }
}
