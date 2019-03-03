<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/04
 * Time: 13:32
 */

abstract class BaseEntity implements ArrayAccess, IteratorAggregate
{
    /**
     * Original Model name where the object is made from
     *
     * @var string
     */
    protected $modelName = '';

    /**
     * Properties hold by the model
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Defaults that should be used if the property doesn't exist
     *
     * @var array
     */
    protected $defaults = [];

    public function __construct(array $rawData = [], string $modelName = null)
    {
        $this->setModelName($modelName);

        $this->setProperty($rawData);

        $this->initializeDefaults();
    }

    /**
     * Set original Model name
     *
     * @param string|null $modelName
     */
    public function setModelName(string $modelName = null)
    {
        $this->modelName = $modelName ?: substr(get_class($this), 0, -6);
    }

    /**
     * Set properties
     *
     * @param      $data
     * @param null $value
     */
    public function setProperty($data, $value = null)
    {
        if (count($data) == 1 && key($data) === $this->modelName) {
            $this->appendData($this->properties, $data[$this->modelName], $value);
        } else {
            $this->appendData($this->properties, $data, $value);
        }
    }

    /**
     * Generic function to add data to array
     *
     * @param array $array
     * @param       $data
     * @param       $value
     */
    protected function appendData(array &$array, $data, $value)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $array[$key] = $value;
            }
        } elseif (!empty($value)) {
            $array[$data] = $value;
        }
    }

    /**
     * Initialize defaults during object creation
     */
    protected function initializeDefaults()
    {
        $this->defaults['del_flg'] = false;
    }

    public function offsetExists($offset)
    {
        return isset($properties[$offset]);
    }

    public function offsetGet($offset)
    {
        return Hash::get($this->properties, $offset, Hash::get($this->defaults, $offset, null));
    }

    public function offsetSet($offset, $value)
    {
        $this->properties[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * Set default value
     *
     * @param      $data
     * @param null $value
     */
    public function setDefault($data, $value = null)
    {
        $this->appendData($this->defaults, $data, $value);
    }

    /**
     * Check whether the object is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->properties);
    }

    /**
     * Convert the object to array
     *
     * @param bool $includeModelName
     *
     * @return array
     */
    public function toArray(bool $includeModelName = false): array
    {
        if ($includeModelName && !empty($this->modelName)) {
            $result[$this->modelName] = $this->properties ?: [];
            return $result;
        } else {
            return $this->properties ?: [];
        }
    }

    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }

}