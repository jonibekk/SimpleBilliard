<?php
/**
 * AppShell file
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 */

App::uses('Shell', 'Console');
App::uses('GoalousDateTime', 'DateTime');

/**
 * Application Shell
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell
{
    /**
     * @override
     */
    public function startup() {
        parent::startup();

        // set '--simulate_current_date="<php date format>"' option to
        // simulate current date time in the console command
        // as far as, using GoalousDateTime (except "www", "isao" environment)
        $canOverWriteCurrentDateTime = !in_array(ENV_NAME, ['www', 'isao']);
        $simulateCurrentDateTime  = Hash::get($this->params, 'simulate_current_date');
        $simulateCurrentTimestamp = Hash::get($this->params, 'simulate_current_timestamp');
        if (!$canOverWriteCurrentDateTime
            && (!empty($simulateCurrentDateTime) || !empty($simulateCurrentTimestamp))) {
            $this->out(sprintf("cant simulate current date in this env(%s)!", ENV_NAME));
            die();
        }
        // override current date by '--simulate_current_date'
        if (!empty($simulateCurrentDateTime)) {
            if (false === strtotime($simulateCurrentDateTime)) {
                $this->out("--simulate_current_date value must be date time format");
                die();
            }
            GoalousDateTime::setTestNow($simulateCurrentDateTime);
            $this->logInfo(sprintf('current date simulate as: %s', GoalousDateTime::now()->format('Y-m-d H:i:s')));
        }
        // override current date by '--simulate_current_timestamp'
        if (!empty($simulateCurrentTimestamp)) {
            if (false === AppUtil::isInt($simulateCurrentTimestamp)) {
                $this->out("--simulate_current_timestamp value must be integer");
                die();
            }
            GoalousDateTime::setTestNow(GoalousDateTime::createFromTimestamp($simulateCurrentTimestamp)->format('Y-m-d H:i:s'));
            $this->logInfo(sprintf('current date simulate as: %s (from timestamp)', GoalousDateTime::now()->format('Y-m-d H:i:s')));
        }
    }

    /**
     * @override
     * @return ConsoleOptionParser
     */
    function getOptionParser()
    {
        $parser = parent::getOptionParser();

        // take '--simulate_current_date' to simulate current date
        $options = [
            'simulate_current_date' => [
                'help'    => 'this batch simulate current date of option value',
                'default' => null,
            ],
            'simulate_current_timestamp' => [
                'help'    => 'this batch simulate current timestamp of option value',
                'default' => null,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * @override
     * @return bool|void
     */
    public function runCommand($command, $argv) {
        $isTask = $this->hasTask($command);
        $isMethod = $this->hasMethod($command);
        $isMain = $this->hasMethod('main');

        if ($isTask || $isMethod && $command !== 'execute') {
            array_shift($argv);
        }

        $this->OptionParser = $this->getOptionParser();
        try {
            list($this->params, $this->args) = $this->OptionParser->parse($argv, $command);
        } catch (ConsoleException $e) {
            $this->out($this->OptionParser->help($command));
            return false;
        }

        if (!empty($this->params['quiet'])) {
            $this->_useLogger(false);
        }
        if (!empty($this->params['plugin'])) {
            CakePlugin::load($this->params['plugin']);
        }
        $this->command = $command;
        if (!empty($this->params['help'])) {
            return $this->_displayHelp($command);
        }

        if (($isTask || $isMethod || $isMain) && $command !== 'execute') {
            $this->startup();
        }

        if ($isTask) {
            $command = Inflector::camelize($command);
            return $this->{$command}->runCommand('execute', $argv);
        }
        if ($isMethod) {
            return $this->{$command}();
        }
        if ($isMain) {
            // adding these changes to original runCommand()
            // 1. adding start/stop log before/after on main()
            // 2. try catch on main()
            $this->logInfo(sprintf('%s: start', $this->name), LOG_INFO);
            $result = false;
            try {
                $result = $this->main();
            } catch (Exception $e) {
                $this->logError($e->getMessage());
                $this->logError($e->getTraceAsString());
                return false;
            }
            $this->logInfo(sprintf('%s: stop', $this->name), LOG_INFO);
            return $result;
        }
        $this->out($this->OptionParser->help($command));
        return false;
    }

    /**
     * @override
     */
    public function logError(string $msg, $scope = null) {
        if (!is_string($msg)) {
            $msg = print_r($msg, true);
        }

        $msg = sprintf('[Shell:%s] %s', $this->name, $msg);
        return CakeLog::write(LOG_ERR, $msg, $scope);
    }

    /**
     * @override
     */
    public function logInfo(string $msg, $scope = null) {
        if (!is_string($msg)) {
            $msg = print_r($msg, true);
        }

        $msg = sprintf('[Shell:%s] %s', $this->name, $msg);
        return CakeLog::write(LOG_INFO, $msg, $scope);
    }
}
