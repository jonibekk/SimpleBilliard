<?php
App::uses('AppModel', 'Model');

/**
 * MemberType Model
 *
 * @property Team       $Team
 * @property TeamMember $TeamMember
 */
class MemberType extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'name'       => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'active_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'del_flg'    => array(
            'boolean' => array(
                'rule' => array('boolean'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Team' => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'TeamMember' => array(
            'className'    => 'TeamMember',
            'foreignKey'   => 'member_type_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        )
    );

    function getByName($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'name'    => $name
            ]
        ];
        return $this->find('first', $options);
    }

    function saveNewType($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [
            'name'    => $name,
            'team_id' => $team_id
        ];
        //TODO create()
        //削除すべきと思われ、$team_idが指定されているusagesなし
        $this->create();
        return $this->save($data);
    }

    function getByNameIfNotExistsSave($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $member_type = $this->getByName($name, $team_id);
        if (!empty($member_type)) {
            return $member_type;
        }
        $res = $this->saveNewType($name);
        return $res;
    }
}
