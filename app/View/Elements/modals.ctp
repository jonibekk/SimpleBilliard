<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/29
 * Time: 23:58
 *
 * @var View $this
 */
echo $this->element('modal_tutorial');
//チーム参加済みの場合
if ($this->Session->read('current_team_id')) {
    echo $this->element('modal_add_circle');
}
