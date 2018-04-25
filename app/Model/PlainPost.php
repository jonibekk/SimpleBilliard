<?php
App::uses('AppModel', 'Model');
/**
 * PlainPost Model
 *
 * This class should be used for a specific situation
 * when there is no way to get data WITHOUT any associations in Cake standard ways.
 * because there could be a problem with CakePHP itself or ExtContainableBehavior.
 */
class PlainPost extends AppModel {
    public $useTable = 'posts';
}
