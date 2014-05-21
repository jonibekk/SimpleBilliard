<?php
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{

    var $actsAs = [
        'ExtAddValidationRule',
        'ExtValidationErrorI18n',
        'ExtValidationPatterns',
    ];

    //全てのモデルでデフォルトで再起的にjoinするのをやめる。個別に指定する。
    public $recursive = -1;

}
