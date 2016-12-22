<?php
App::uses('AppModel', 'Model');

/**
 * KrProgressLog Model
 *
 * @property ActionResult $ActionResult
 */
class KrProgressLog extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'ActionResult',
    ];
}
