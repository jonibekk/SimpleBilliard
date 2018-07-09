<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:20
 */

class PagingRequest
{
    const DEFAULT_PAGE_LIMIT = 20;
    const MAX_PAGE_LIMIT = 100;

    const PAGE_ORDER_ASC = 'asc';
    const PAGE_ORDER_DESC = 'desc';

    /**
     * DB query ordering
     *
     * @var array
     *      ['$column_name'] => 'ASC/DESC'
     */
    private $order = [];

    /**
     * Array of pointer for next / prev paging
     *
     * @var array
     *      [$column_name] => [$math_operator, $value]
     */
    private $pointerValues = [];

    /**
     * Array for search query from URL
     *
     * @var array
     */
    private $queries = [];

    /**
     * DB query parameters, follow DB query structure
     *
     * @var array
     */
    private $conditions = [];

    /**
     * Add resource ID from the URL. Will not be included in cursor
     *
     * @var array
     */
    private $resources = [];

    /**
     * Request object
     *
     * @var CakeRequest
     */
    private $request;

    /**
     * PagingRequest constructor.
     *
     * @param array $conditions    Conditions for the search, e.g. SQL query
     * @param array $pointerValues Pointer to mark start / end point of search
     *                             [$column_name] => [$math_operator, $value]
     * @param array $order         Order of the query sorting
     */
    public function __construct(
        array $conditions = [],
        array $pointerValues = [],
        array $order = []
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
     * Create next cursor for API requests
     *
     * @param array $conditions    Conditions for the search, e.g. SQL query
     * @param array $pointerValues Pointer to mark start / end point of search
     *                             [$column_name] => [$math_operator, $value]
     * @param array $order         Order of the query sorting
     *
     * @return string Encoded next paging cursor
     */
    public static function createPageCursor(
        array $conditions = [],
        array $pointerValues = [],
        array $order = []
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
     * Decode a cursor into object
     *
     * @param string $cursor
     *
     * @throws RuntimeException When failed parsing cursor
     * @return PagingRequest
     */
    public static function decodeCursorToObject(string $cursor)
    {
        try {
            $values = self::decodeCursorToArray($cursor);
            $self = new self($values['conditions'], $values['pointer'], $values['order']);
        } catch (RuntimeException $e) {
            throw $e;
        }

        return $self;
    }

    /**
     * Decode a cursor into multi-dimensional array
     *
     * @param string $cursor
     *
     * @return array
     * @throws RuntimeException
     */
    public static function decodeCursorToArray(string $cursor): array
    {
        if (empty($cursor)) {
            throw new InvalidArgumentException("Cursor can't be empty");
        }
        $decodedString = base64_decode($cursor);
        if ($decodedString === false || empty($decodedString)) {
            throw new RuntimeException("Failed in parsing cursor from base64 encoding");
        }
        $jsonDecodedString = json_decode($decodedString, true);
        if ($jsonDecodedString === false || empty($jsonDecodedString)) {
            throw new RuntimeException("Failed in parsing cursor from json");
        }
        return $jsonDecodedString;
    }

    /**
     * Add new ordering
     *
     * @param string $key
     * @param string $order
     */
    public function addOrder(string $key, string $order = self::PAGE_ORDER_DESC)
    {
        $this->order[$key] = $order;
    }

    /**
     * Add new pointer using array
     *
     * @param array $pointer
     *
     * @return bool True on successful addition
     */
    public function addPointerArray(array $pointer)
    {
        if (count($pointer) != 3) {
            return false;
        }
        $this->addPointer($pointer[0], $pointer[1], $pointer[2]);

        return true;
    }

    /**
     * Add new pointer
     *
     * @param string $key
     * @param string $operator
     * @param mixed  $value
     */
    public function addPointer(string $key, string $operator = '<', $value)
    {
        $this->pointerValues[$key] = [$operator, $value];
    }

    /**
     * Overwrite current pointer with new one
     *
     * @param array $pointer New pointer
     */
    public function setPointer(array $pointer)
    {
        $this->pointerValues = $pointer;
    }

    /**
     * Add new condition
     *
     * @param array $conditions
     * @param bool  $overwrite If same key exist, whether to overwrite or not
     */
    public function addCondition(array $conditions, bool $overwrite = false)
    {
        if ($overwrite) {
            $this->conditions = array_merge($this->conditions, $conditions);
        } else {
            if (!array_key_exists(key($conditions), $this->conditions)) {
                $this->conditions = array_merge($this->conditions, $conditions);
            }
        }
    }

    /**
     * Get all stored ordering
     *
     * @return array
     */
    public function getOrders()
    {
        $result = [];

        if (empty($this->order)) {
            return $result;
        }

        foreach ($this->order as $key => $order) {
            $result[] = [$key => $order];
        }
        return $result;
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

        if (!empty ($this->pointerValues)) {
            foreach ($this->pointerValues as $key => $row) {
                $result[] = "$key  $row[0]  $row[1]";
            }
        }
        return $result;
    }

    /**
     * Get all stored conditions
     *
     * @param bool $includeResourceId Whether should include resource ID
     *
     * @return array
     */
    public function getConditions(bool $includeResourceId = false)
    {
        return ($includeResourceId) ? array_merge($this->conditions, $this->resources) : $this->conditions;
    }

    /**
     * Add a resource ID to the cursor. Will always overwrite existing one
     *
     * @param string $key
     * @param int    $id
     */
    public function addResource(string $key, int $id)
    {
        $this->resources[$key] = $id;
    }

    /**
     * Create cursor string from this cursor object
     *
     * @return string
     */
    public function returnCursor()
    {
        return ($this->isEmpty()) ? "" :
            base64_encode(json_encode([
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
     * Get saved URL queries
     *
     * @param null $keys
     *
     * @return array
     */
    public function getQuery($keys = null): array
    {
        if (empty($keys)) {
            return [];
        }
        if (is_string($keys)) {
            return Hash::get($this->queries, $keys);
        }
        if (is_array($keys)) {
            $result = [];
            foreach ($keys as $key) {
                $result[$key] = Hash::get($this->queries, $key);
            }
            return $result;
        }
        return [];
    }

    /**
     * Insert URL queries
     *
     * @param array $query
     * @param bool  $overwriteFlag Overwrite elements with same key name
     */
    public function addQueries(array $query, bool $overwriteFlag = false)
    {
        if ($overwriteFlag) {
            $this->queries = array_merge($this->queries, $query);
        } else {
            $this->queries += $query;
        }
    }

    /**
     * Add saved queries into condition, which will be included in cursor
     *
     * @param null $keys
     */
    public function addQueriesToCondition($keys = null)
    {
        if (empty($keys)) {
            return;
        }
        if (is_string($keys) && key_exists($keys, $this->queries)) {
            $this->conditions[$keys] = Hash::get($this->queries, $keys);
        }
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (key_exists($key, $this->queries)) {
                    $this->conditions[$key] = Hash::get($this->queries, $key);
                }
            }
        }
    }

    /**
     * Set CakeRequest data to PagingRequest
     *
     * @param CakeRequest $request
     */
    public function setCakeRequest(CakeRequest $request)
    {
        if (empty($request)) {
            throw new InvalidArgumentException("Request can't be empty");
        }
        $this->request = $request;
    }

    /**
     * Get stored CakeRequest data
     *
     * @return CakeRequest
     */
    public function getCakeRequest(): CakeRequest
    {
        return $this->request ?? null;
    }

    /**
     * Get resource ID in the URL
     *
     * @return int Return positive number on success, 0 if not exist
     */
    public function getResourceId(): int
    {
        if (!empty($this->request->params['id'])) {
            return $this->request->params['id'];
        }
        //If resource ID not included in request, will try to get from resources.
        //If not exist, return -1
        return Hash::get($this->resources, 'res_id', 0);
    }

    /**
     * Get logged in user's ID.
     *
     * @return int Return 0 if not exist
     */
    public function getCurrentUserId(): int
    {
        return Hash::get($this->resources, 'current_user_id', 0);
    }

    /**
     * Get logged in user's current team ID
     *
     * @return int Return 0 if not exist
     */
    public function getCurrentTeamId(): int
    {
        return Hash::get($this->resources, 'current_team_id', 0);
    }
}