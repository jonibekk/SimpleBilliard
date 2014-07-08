<?php
/* SVN FILE: $Id: soft_deletable.php 38 2007-11-26 19:36:27Z mgiglesias $ */

/**
 * SoftDeletable Behavior class file.
 *
 * @filesource
 * @author     Mariano Iglesias
 * @link       http://cake-syrup.sourceforge.net/ingredients/soft-deletable-behavior/
 * @version    $Revision: 38 $
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package    app
 * @subpackage app.models.behaviors
 */

/**
 * Model behavior to support soft deleting records.
 *
 * @package    app
 * @subpackage app.models.behaviors
 */

App::uses('ContainableBehavior', 'Model/Behavior');

/** @noinspection PhpUndefinedClassInspection */
class SoftDeletableBehavior extends ModelBehavior
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
            'field'      => 'deleted',
            'field_date' => 'deleted_date',
            'delete'     => true,
            'find'       => true
        );

        if (!isset($this->__settings[$Model->alias])) {
            $this->__settings[$Model->alias] = $default;
        }

        $settings = (is_array($settings)) ? $settings : array();

        $this->__settings[$Model->alias] = am($this->__settings[$Model->alias], $settings);
    }

    /**
     * Run before a model is deleted, used to do a soft delete when needed.
     *
     * @param \Model|object $Model   Model about to be deleted
     * @param boolean       $cascade If true records that depend on this record will also be deleted
     *
     * @return boolean Set to true to continue with delete, false otherwise
     * @access public
     */
    function beforeDelete(Model $Model, $cascade = true)
    {
        if ($this->__settings[$Model->alias]['delete']
            && $Model->hasField($this->__settings[$Model->alias]['field'])
        ) {
            $attributes = $this->__settings[$Model->alias];
            $id = $Model->id;

            $data = array(
                $Model->alias => array(
                    $attributes['field'] => true
                )
            );

            if (isset($attributes['field_date']) && $Model->hasField($attributes['field_date'])) {
                $data[$Model->alias][$attributes['field_date']] = time();
            }

            foreach (am(array_keys($data[$Model->alias]),
                        array(
                            'field',
                            'field_date',
                            'find',
                            'delete'
                        )) as $field) {
                unset($attributes[$field]);
            }

            if (!empty($attributes)) {
                $data[$Model->alias] = am($data[$Model->alias], $attributes);
            }

            $Model->id = $id;
            $deleted = $Model->save($data, false, array_keys($data[$Model->alias]));

            if ($deleted && $cascade) {
                $Model->_deleteDependent($id, $cascade);
                $Model->_deleteLinks($id);
            }

            return false;
        }

        return true;
    }

    /**
     * Permanently deletes a record.
     *
     * @param \Model|object $Model   Model from where the method is being executed.
     * @param mixed         $id      ID of the soft-deleted record.
     * @param boolean       $cascade Also delete dependent records
     *
     * @return boolean Result of the operation.
     * @access public
     */
    function hardDelete(Model $Model, $id, $cascade = true)
    {
        $onFind = $this->__settings[$Model->alias]['find'];
        $onDelete = $this->__settings[$Model->alias]['delete'];
        $this->enableSoftDeletable($Model, false);

        $deleted = $Model->del($id, $cascade);

        $this->enableSoftDeletable($Model, 'delete', $onDelete);
        $this->enableSoftDeletable($Model, 'find', $onFind);

        return $deleted;
    }

    /**
     * Permanently deletes all records that were soft deleted.
     *
     * @param \Model|object $Model   Model from where the method is being executed.
     * @param boolean       $cascade Also delete dependent records
     *
     * @return boolean Result of the operation.
     * @access public
     */
    function purge(Model $Model, $cascade = true)
    {
        $purged = false;

        if ($Model->hasField($this->__settings[$Model->alias]['field'])) {
            $onFind = $this->__settings[$Model->alias]['find'];
            $onDelete = $this->__settings[$Model->alias]['delete'];
            $this->enableSoftDeletable($Model, false);

            $purged = $Model
                ->deleteAll(
                array(
                    $this->__settings[$Model->alias]['field'] => true
                ), $cascade);

            $this->enableSoftDeletable($Model, 'delete', $onDelete);
            $this->enableSoftDeletable($Model, 'find', $onFind);
        }

        return $purged;
    }

    /**
     * Restores a soft deleted record, and optionally change other fields.
     *
     * @param \Model|object $Model      Model from where the method is being executed.
     * @param mixed         $id         ID of the soft-deleted record.
     * @param array|\Other  $attributes Other fields to change (in the form of field => value)
     *
     * @return boolean Result of the operation.
     * @access public
     */
    /** @noinspection SpellCheckingInspection */
    function undelete(Model $Model, $id = null, $attributes = array())
    {
        if ($Model->hasField($this->__settings[$Model->alias]['field'])) {
            if (empty($id)) {
                $id = $Model->id;
            }

            $data = array(
                $Model->alias => array(
                    $Model->primaryKey                        => $id,
                    $this->__settings[$Model->alias]['field'] => false
                )
            );

            if (isset($this->__settings[$Model->alias]['field_date'])
                && $Model->hasField($this->__settings[$Model->alias]['field_date'])
            ) {
                $data[$Model->alias][$this->__settings[$Model->alias]['field_date']] = null;
            }

            if (!empty($attributes)) {
                $data[$Model->alias] = am($data[$Model->alias], $attributes);
            }

            $onFind = $this->__settings[$Model->alias]['find'];
            $onDelete = $this->__settings[$Model->alias]['delete'];
            $this->enableSoftDeletable($Model, false);

            $Model->id = $id;
            $result = $Model->save($data, false, array_keys($data[$Model->alias]));

            $this->enableSoftDeletable($Model, 'find', $onFind);
            $this->enableSoftDeletable($Model, 'delete', $onDelete);

            return ($result !== false);
        }

        return false;
    }

    /**
     * Set if the beforeFind() or beforeDelete() should be overriden for specific model.
     *
     * @param \Model|object $Model   Model about to be deleted.
     * @param mixed         $methods If string, method (find / delete) to enable on, if array array of method names, if boolean, enable it for find method
     * @param boolean       $enable  If specified method should be overriden.
     *
     * @access public
     */
    function enableSoftDeletable(Model $Model, $methods, $enable = true)
    {
        if (is_bool($methods)) {
            $enable = $methods;
            $methods = array(
                'find',
                'delete'
            );
        }

        if (!is_array($methods)) {
            $methods = array(
                $methods
            );
        }

        foreach ($methods as $method) {
            $this->__settings[$Model->alias][$method] = $enable;
        }
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
        /*
        $contain = new ContainableBehavior();
        $queryData = $contain -> beforeFind($Model, $queryData);
         */
        if ($this->__settings[$Model->alias]['find']
            && $Model->hasField($this->__settings[$Model->alias]['field'])
        ) {
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
                if (empty($queryData['conditions'])
                    || (!in_array($this->__settings[$Model->alias]['field'], array_keys($queryData['conditions']))
                        && !in_array($Model->alias . '.' . $this->__settings[$Model->alias]['field'],
                                     array_keys($queryData['conditions'])))
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
                        . $Db->name($this->__settings[$Model->alias]['field']) . '= false AND '
                        . $queryData['conditions'];
                }
                else {
                    $queryData['conditions'][$Model->alias . '.' . $this->__settings[$Model->alias]['field']] = false;
                }
            }
        }
        return $queryData;
    }

    /**
     * Run before a model is saved, used to disable beforeFind() override.
     *
     * @param \Model|object $Model Model about to be saved.
     * @param array         $options
     *
     * @return boolean True if the operation should continue, false if it should abort
     * @access public
     */
    function beforeSave(Model $Model, $options = [])
    {
        if ($this->__settings[$Model->alias]['find']) {
            if (!isset($this->__backAttributes)) {
                $this->__backAttributes = array(
                    $Model->alias => array()
                );
            }
            else {
                if (!isset($this->__backAttributes[$Model->alias])) {
                    $this->__backAttributes[$Model->alias] = array();
                }
            }

            $this->__backAttributes[$Model->alias]['find'] = $this->__settings[$Model->alias]['find'];
            $this->__backAttributes[$Model->alias]['delete'] = $this->__settings[$Model->alias]['delete'];
            $this->enableSoftDeletable($Model, false);
        }

        return true;
    }

    /**
     * Run after a model has been saved, used to enable beforeFind() override.
     *
     * @param \Model|object $Model   Model just saved.
     * @param boolean       $created True if this save created a new record
     * @param array         $options
     *
     * @return bool|void
     * @access       public
     * @noinspection PhpUndefinedFieldInspection
     */
    function afterSave(Model $Model, $created, $options = [])
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->__backAttributes[$Model->alias]['find'])) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->enableSoftDeletable($Model, 'find', $this->__backAttributes[$Model->alias]['find']);
            /** @noinspection PhpUndefinedFieldInspection */
            $this->enableSoftDeletable($Model, 'delete', $this->__backAttributes[$Model->alias]['delete']);
            /** @noinspection PhpUndefinedFieldInspection */
            unset($this->__backAttributes[$Model->alias]['find']);
            /** @noinspection PhpUndefinedFieldInspection */
            unset($this->__backAttributes[$Model->alias]['delete']);
        }
    }
}
