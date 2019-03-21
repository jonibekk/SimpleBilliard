<?php
App::import('Lib/DataStructure/Tree', 'BinaryTree');
App::import('Lib/Paging', 'PointerTree');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:20
 */
class PagingRequest
{
    const PAGE_ORDER_ASC = 'asc';
    const PAGE_ORDER_DESC = 'desc';

    /**
     * DB query ordering
     *
     * @var array
     *      [['id','DESC'], ['name' , 'ASC'], ...]
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
     * Binary Tree for saving the pointers
     *
     * @var PointerTree
     */
    private $pointerTree = null;

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
     * PagingRequest constructor.
     *
     * @param array               $conditions    Conditions for the search, e.g. SQL query
     * @param array | PointerTree $pointerValues Pointer to mark start / end point of search
     *                                           [$column_name] => [$math_operator, $value]
     * @param array               $order         Order of the query sorting
     */
    public function __construct(
        array $conditions = [],
        $pointerValues = [],
        array $order = []
    )
    {
        if (!empty($conditions)) {
            $this->conditions = $conditions;
        }

        $this->pointerTree = new PointerTree();

        if (!empty($pointerValues)) {
            if ($pointerValues instanceof PointerTree) {
                $this->pointerTree = $pointerValues;
            }
            if (is_array($pointerValues)) {
                if (count($pointerValues) == 3 && is_string($pointerValues[0]) && !is_array($pointerValues[1])) {
                    $this->pointerTree->addPointer($pointerValues);
                } else {
                    $this->pointerTree->populateTree($pointerValues);
                }
            }
        }

        if (!empty($order)) {
            foreach ($order as $key => $value) {
                if (is_numeric($key)) {
                    //If $value is [[$key => $value]]
                    $this->order[] = $value;
                } else {
                    //If $value is [$key => $value]
                    $this->order[] = [$key, $value];
                }
            }
        }
    }

    /**
     * Create next cursor for API requests
     *
     * @param array               $conditions    Conditions for the search, e.g. SQL query
     * @param array | PointerTree $pointerValues Pointer to mark start / end point of search
     *                                           [$column_name] => [$math_operator, $value]
     * @param array               $order         Order of the query sorting
     *
     * @return string Encoded next paging cursor
     */
    public static function createPageCursor(
        array $conditions = [],
        $pointerValues = null,
        array $order = []
    ): string
    {

        $array = array();

        if (!empty($conditions)) {
            $array['conditions'] = $conditions;
        }

        if (!empty($pointerValues)) {
            if (is_array($pointerValues)) {
                $array['pointer'] = (new PointerTree($pointerValues))->toArray();
            } elseif ($pointerValues instanceof PointerTree) {
                if (!$pointerValues->isEmpty()) {
                    $array['pointer'] = $pointerValues->toArray();
                }
            }
        }
        if (!empty($order)) {
            $array['order'] = $order;
        }

        if (empty($array)) {
            return "";
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
            $self = new self(
                $values['conditions'] ?? [],
                $values['pointer'] ?? [],
                $values['order'] ?? []);
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
        if (empty($decodedString)) {
            throw new RuntimeException("Failed in parsing cursor from base64 encoding");
        }
        $pagingRequest = json_decode($decodedString, true);
        if (empty($pagingRequest)) {
            throw new RuntimeException("Failed in parsing cursor from json");
        }
        return $pagingRequest;
    }

    /**
     * Add new ordering
     *
     * @param string $key
     * @param string $order
     *
     * @return bool TRUE on successful addition
     */
    public function addOrder(string $key, string $order = self::PAGE_ORDER_DESC): bool
    {
        //Each key can only be added once
        if (!in_array($key, Hash::extract($this->order, '{n}.0'))) {
            $this->order[] = [$key, $order];
            return true;
        }
        return false;
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
        if (empty($pointer)) {
            return true;
        }
        //If added as ['key', 'operator', 'value']
        if (count($pointer) == 3 && !is_array($pointer[0])) {
            $this->addPointer($pointer[0], $pointer[1], $pointer[2]);
            return true;
        }
        //If added as [['key', 'operator', 'value'], ['key', 'operator', 'value'],...]
        if (is_int(array_keys($pointer)[0])) {
            foreach ($pointer as $element) {
                if (count($element) == 3) {
                    $this->addPointer($element[0], $element[1], $element[2]);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Add new pointer
     *
     * @param string $key
     * @param string $operator
     * @param mixed  $value
     *
     * @return bool True on successful addition
     */
    public function addPointer(string $key, string $operator, $value): bool
    {
        return $this->pointerTree->addPointer([$key, $operator, $value]);
    }

    /**
     * Overwrite current pointer with new one
     *
     * @param PointerTree $pointer New pointer
     */
    public function setPointer(PointerTree $pointer)
    {
        $this->pointerTree = $pointer;
    }

    /**
     * Get the first node containing pointer with given key
     *
     * @param string $key
     *
     * @return array
     */
    public function getPointer(string $key): array
    {
        $node = $this->pointerTree->searchTree($key);

        if (empty($node) || $node->isEmpty()) {
            return [];
        } else {
            return $node->getValue();
        }
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

        if (!empty($this->order)) {
            foreach ($this->order as $value) {
                $result[] = [$value[0] => $value[1]];
            }
        }

        return $result;
    }

    /**
     * Get all stored pointers in CakePHP SQL query condition format
     *
     * @return array
     */
    public function getPointersAsQueryOption()
    {
        return $this->pointerTree->toCondition();
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
     * Get specific stored condition
     *
     * @param string $key                Resource array key
     * @param bool   $includedResourceId Whether should include resource ID
     *
     * @return mixed
     */
    public function getCondition(string $key, bool $includedResourceId = false)
    {
        $resources = $this->getConditions($includedResourceId);

        return Hash::get($resources, $key) ?? null;
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
        return self::createPageCursor($this->conditions, $this->pointerTree, $this->order);
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
     * @return mixed
     */
    public function getQuery($keys = null)
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
     * @param mixed $keys
     */
    public function addQueriesToCondition($keys = null)
    {
        if (empty($keys)) {
            return;
        }
        if (is_string($keys) && key_exists($keys, $this->queries)) {
            $this->conditions[$keys] = $this->getQuery($keys);
            return;
        }
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (key_exists($key, $this->queries)) {
                    $this->conditions[$key] = $this->getQuery($key);
                }
            }
        }
    }

    /**
     * Get resource ID in the URL
     *
     * @return int Return positive number on success, 0 if not exist
     */
    public function getResourceId(): int
    {
        //If not exist, return -1
        return Hash::get($this->resources, 'res_id', 0);
    }

    /**
     * Set resource ID in the URL
     *
     * @param int $resourceId
     */
    public function setResourceId(int $resourceId)
    {
        $this->addResource('res_id', $resourceId);
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
     * Set logged in user's ID.
     *
     * @param int $userId
     */
    public function setCurrentUserId(int $userId)
    {
        $this->addResource('current_user_id', $userId);
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

    /**
     * Set logged in user's current team ID
     *
     * @param int $teamId
     */
    public function setCurrentTeamId(int $teamId)
    {
        $this->addResource('current_team_id', $teamId);
    }
}