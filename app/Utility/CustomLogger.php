<?php
App::uses('AppUtil', 'Util');
App::import('Utility', 'CustomLogger');

class CustomLogger {
    /** Singleton instance of CustomLogger */
    private static $instance = NULL;
    /** @var array */
    protected $controllerData = [];
    /** @var array */
    protected $metadata = [];

    private function __construct() {}

    /**
     * return singleton instance of CustomLogger
     */
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

    /**
     * sets metadata within the current transaction, this will be automatically appended to logs and forwarded to NewRelic
     */
    public function setMetadata(array $newMetadata)
    {
        $this->metadata[] = $newMetadata;

        if (extension_loaded('newrelic')) {
            $flattenedNewMetadata = AppUtil::flattenArrayPath($newMetadata);

            foreach ($flattenedNewMetadata as $key => $value) {
                newrelic_add_custom_parameter($key, $value);
            }
        }
    }

    /**
     * sends exception with stacktrace to Newrelic, also adds to error log
     */
    public function logException(Exception $exception)
    {
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($exception);
        }

        $data = [ 'exception' => $exception->__toString()];
        GoalousLog::error('Exception raised', $this->appendMetadata($data));
    }

    /**
     * 
     */
    public function logEvent(string $name, array $data = [])
    {
        $loggedData = $this->appendMetadata($data);

        if (extension_loaded('newrelic')) {
            $flattenedData = AppUtil::flattenArrayPath($loggedData);
            newrelic_record_custom_event($name, $flattenedData);
        }

        GoalousLog::info($name, $loggedData);
    }

    public function info(string $msg, array $data = [])
    {
        GoalousLog::info($msg, $this->appendMetadata($data));
    }

    public function warning(string $msg, array $data = [])
    {
        GoalousLog::warning($msg, $this->appendMetadata($data));
    }

    public function error(string $msg, array $data = [])
    {
        GoalousLog::error($msg, $this->appendMetadata($data));
    }

    public function emergency(string $msg, array $data = [])
    {
        GoalousLog::emergency($msg, $this->appendMetadata($data));
    }

    private function appendMetadata($data) 
    {
        $data['controllerData'] = $this->controllerData;
        $data['metadata'] = $this->metadata;

        if (extension_loaded('newrelic')) {
            return array_merge($data, newrelic_get_linking_metadata());
        }

        return $data;
    }
}
