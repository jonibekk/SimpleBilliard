<?php
App::uses('AppModel', 'Model');

/**
 * KrWatchlist Model
 *
 * @property KeyResult       $KeyResult
 * @property Watchlist       $Watchlist
 */

class KrWatchlist extends AppModel
{
    public $validate = [
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    public $belongsTo = [
        'KeyResult',
        'Watchlist',
    ];
}