<?php
App::uses('AppModel', 'Model');
App::import('User', 'Model');
class PlainUser extends AppModel
{
  private $dispatcher;
  public $useTable = 'users';
  public function setUsername($row) {
    return $this->dispatcher->setUsername($row);
  }
  public function afterFind($results, $primary = false) {
    // just dispatch
    // do not extend User or you could be in big trouble.
    // and basically do not use this with DB access
    if (!isset($this->dispatcher)) {
      $this->dispatcher = ClassRegistry::init('User');;
    }
    $this->dispatcher->afterFind($results, $primary);
    foreach ($results as $key => $result) {
      $results[$key] = $this->setUsername($result);
    }
    return $results;
  }
}
