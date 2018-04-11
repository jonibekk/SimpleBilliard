<?php
App::uses('AppModel', 'Model');
App::import('User', 'Model');
class PlainUser extends AppModel
{
  private $dispatcher;
  public $useTable = 'users';
  public function setUsername($row) {
    // just dispatch
    // do not extend User or you could be in big trouble.
    if (!isset($this->dispatcher)) {
      $this->dispatcher = new User();
    }
    return $this->dispatcher->setUsername($row);
  }
  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $result) {
      $results[$key] = $this->setUsername($result);
    }
    return $results;
  }
}
