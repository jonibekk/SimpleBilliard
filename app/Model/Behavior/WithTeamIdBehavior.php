<?php

App::uses('ContainableBehavior', 'Model/Behavior');

/** @noinspection PhpUndefinedClassInspection */
class WithTeamIdBehavior extends ModelBehavior
{
    /**
     * Contain settings indexed by model name.
     *
     * @var array
     * @access private
     */

    var $__settings = array();

    /**
     * Initiate behaviour for the model using settings.
     *
     * @param \Model|object $Model    Model using the behaviour
     * @param array         $settings Settings to override for model.
     *
     * @access public
     */
    function setup(Model $Model, $settings = array())
    {
        $default = array(
            'field'  => 'team_id',
            'enable' => true,
        );

        if (!isset($this->__settings[$Model->alias])) {
            $this->__settings[$Model->alias] = $default;
        }

        $settings = (is_array($settings)) ? $settings : array();

        $this->__settings[$Model->alias] = am($this->__settings[$Model->alias], $settings);
    }

    /**
     * Set if the beforeFind() or beforeDelete() should be overriden for specific model.
     *
     * @param \Model|object $Model  Model about to be deleted.
     * @param boolean       $enable If specified method should be overriden.
     *
     * @access public
     */
    function changeEnable(Model $Model, $enable = true)
    {
        $this->__settings[$Model->alias]['enable'] = $enable;
    }

    /**
     * Run before a model is about to be find, used only fetch for non-deleted records.
     *
     * @param \Model|object $Model     Model about to be deleted.
     * @param array         $queryData Data used to execute this query, i.e. conditions, order, etc.
     *
     * @return mixed Set to false to abort find operation, or return an array with data used to execute query
     * @access public
     */
    function beforeFind(Model $Model, $queryData)
    {
        if (!$Model->current_team_id) {
            return $queryData;
        }
        if (!$this->__settings[$Model->alias]['enable']) {
            return $queryData;
        }
        if (!$Model->hasField($this->__settings[$Model->alias]['field'])) {
            return $queryData;
        }

        $Db = ConnectionManager::getDataSource($Model->useDbConfig);
        $include = false;
        if (!empty($queryData['conditions']) && is_string($queryData['conditions'])) {
            $include = true;

            /** @noinspection PhpUndefinedMethodInspection */
            $fields = array(
                $Db->name($Model->alias) . '.' . $Db->name($this->__settings[$Model->alias]['field']),
                $Db->name($this->__settings[$Model->alias]['field']),
                $Model->alias . '.' . $this->__settings[$Model->alias]['field'],
                $this->__settings[$Model->alias]['field']
            );

            foreach ($fields as $field) {
                if (preg_match('/^' . preg_quote($field) . '[\s=!]+/i', $queryData['conditions'])
                    || preg_match('/\\x20+' . preg_quote($field) . '[\s=!]+/i', $queryData['conditions'])
                ) {
                    $include = false;
                    break;
                }
            }
        }
        else {

            $has_field_in_conditions = in_array($this->__settings[$Model->alias]['field'],
                                                array_keys($queryData['conditions']));
            $has_alias_field_in_conditions = in_array($Model->alias . '.' . $this->__settings[$Model->alias]['field'],
                                                      array_keys($queryData['conditions']));
            if (empty($queryData['conditions'])
                || (!$has_field_in_conditions && !$has_alias_field_in_conditions)
            ) {
                $include = true;
            }
        }

        if ($include) {
            if (empty($queryData['conditions'])) {
                $queryData['conditions'] = array();
            }

            if (is_string($queryData['conditions'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $queryData['conditions'] = $Db->name($Model->alias) . '.'
                    . $Db->name($this->__settings[$Model->alias]['field']) . "= {$Model->current_team_id} AND "
                    . $queryData['conditions'];
            }
            else {
                $queryData['conditions'][$Model->alias . '.' . $this->__settings[$Model->alias]['field']] = $Model->current_team_id;
            }
        }
        return $queryData;
    }
}
