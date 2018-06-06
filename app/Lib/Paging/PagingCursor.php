<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:20
 */

class PagingCursor
{
    const DEFAULT_PAGE_LIMIT = 20;
    const PAGE_ORDER_ASC = 'asc';
    const PAGE_ORDER_DESC = 'desc';

    /**
     * DB query ordering
     *
     * @var array
     *      ['$column_name' => 'ASC/DESC']
     */
    private $order;

    /**
     * Array of pointer for next / prev paging
     *
     * @var array
     *      [$column_name, $math_operator, $value]
     */
    private $pointerValues;

    /**
     * DB query parameters, follow DB query structure
     *
     * @var array
     */
    private $conditions;

    /**
     * PagingCursor constructor.
     *
     * @param array $conditions    Conditions for the search, e.g. SQL query
     * @param array $pointerValues Pointer to mark start / end point of search
     *                             [$column_name, $math_operator, $value]
     * @param array $order         Order of the query sorting
     */
    public function __construct(
        $conditions = [],
        $pointerValues = [],
        $order = []
    ) {
        if (!empty($conditions)) {
            $this->conditions = $conditions;
        }
        if (!empty($pointerValues)) {
            $this->pointerValues = $pointerValues;
        }
        if (!empty($order)) {
            $this->order = $order;
        }
    }

    /**
     * Add new ordering
     *
     * @param string $key
     * @param string $order
     */
    public function addOrder($key, $order = self::PAGE_ORDER_DESC)
    {
        $this->order[] = [$key => $order];
    }

    /**
     * Add new pointer
     *
     * @param string $key
     * @param string $operator
     * @param mixed  $value
     */
    public function addPointer($key, $operator = '<', $value)
    {
        $this->pointerValues[] = [$key, $operator, $value];
    }

    /**
     * Overwrite current pointer with new one
     *
     * @param array $pointer New pointer
     */
    public function setPointer($pointer)
    {
        $this->pointerValues = $pointer;
    }

    /**
     * Add new condition
     *
     * @param array $conditions
     */
    public function addCondition($conditions)
    {
        $this->conditions[] = $conditions;
    }

    /**
     * Get all stored ordering
     *
     * @return array
     */
    public function getOrders()
    {
        return $this->order;
    }

    /**
     * Get all stored pointers
     *
     * @return array
     */
    public function getPointers()
    {
        return $this->pointerValues;
    }

    /**
     * Get all stored pointers in CakePHP SQL query condition format
     *
     * @return array
     */
    public function getPointersAsQueryOption()
    {
        $result = array();

        foreach ($this->pointerValues as $row) {
            $result[] = [$row[0] . ' ' . $row[1] => $row[2]];
        }

        return $result;
    }

    /**
     * Get all stored conditions
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Create cursor string from this cursor object
     *
     * @return string
     */
    public function returnCursor()
    {
        return base64_encode(json_encode([
            'conditions' => $this->conditions,
            'pointer'    => $this->pointerValues,
            'order'      => $this->order
        ]));
    }

    /**
     * Check whether the cursor is empty or not
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->order) && empty($this->conditions) && empty($this->pointerValues);
    }

    /**
     * Create next cursor for API requests
     *
     * @param array $conditions    Conditions for the search, e.g. SQL query
     * @param array $pointerValues Pointer to mark start / end point of search
     *                             [$column_name, $math_operator, $value]
     * @param array $order         Order of the query sorting
     *
     * @return string Encoded next paging cursor
     */
    public static function createPageCursor(
        $conditions = [],
        $pointerValues = [],
        $order = []
    ): string {
        $array = array();

        if (!empty($conditions)) {
            $array['conditions'] = $conditions;
        }
        if (!empty($pointerValues)) {
            $array['pointer'] = $pointerValues;
        }
        if (!empty($order)) {
            $array['order'] = $order;
        }

        return base64_encode(json_encode($array));
    }

    /**
     * Decode a cursor into multi-dimensional array
     *
     * @param string $cursor
     *
     * @return array
     */
    public static function decodeCursorToArray(string $cursor): array
    {
        return json_decode(base64_decode($cursor), true);
    }

    /**
     * Decode a cursor into object
     *
     * @param string $cursor
     *
     * @return PagingCursor
     */
    public static function decodeCursorToObject(string $cursor)
    {
        $values = self::decodeCursorToArray($cursor);

        $self = new self($values['conditions'], $values['pointer'], $values['order']);

        return $self;
    }
}