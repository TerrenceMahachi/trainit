<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use Exception;
use JsonSerializable;

class Model implements JsonSerializable
{
    protected $table;
    protected $pdo;
    protected $primaryKey = 'iD';
    protected $attributes = [];
    protected $queryConditions = []; // Store conditions for WHERE and OR WHERE clauses
    protected $queryParams = [];     // Store the parameters for the conditions

    protected $appendedRelations = []; // holds relationship name => value
    public function __construct($attributes = [], $pdo = null)
    {
        if ($pdo) {
            // If a PDO instance is provided, use it
            $this->pdo = $pdo;
        } else {
            // Use the application-wide shared connection (WAL, foreign keys,
            // busy_timeout are configured centrally in Database::open()).
            // Previously every model instantiation opened its own PDO handle.
            $this->pdo = Database::sharedPdo();
        }
        //  $this->attributes = $attributes;
        $this->attributes = is_array($attributes) ? $attributes : [];

        $this->ensureTableExists();
    }
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return null;
    }

    public function ___get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
    public static function create($attributes = [])
    {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }
    public function update()
    {
        // Fetch the current values from the database
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $currentRecord = $this->query($sql, [$this->attributes[$this->primaryKey]])->fetch(PDO::FETCH_ASSOC);

        $changedColumns = [];
        $changedColumnsText = "";

        foreach ($this->attributes as $key => $value) {
            if (array_key_exists($key, $currentRecord)) {
                $currentValue = $currentRecord[$key];

                // Cast both values to strings for comparison, to avoid issues with type differences
                if ((string) $currentValue !== (string) $value) {
                    $changedColumns[$key] = [
                        'old_value' => $currentValue,
                        'new_value' => $value
                    ];
                    $changedColumnsText .= "$key changed; <br>";
                }
            }
        }

        // Proceed with the update if there are changes
        if (!empty($changedColumns)) {
            $fields = array_keys($this->attributes);
            $placeholders = implode(' = ?, ', $fields) . ' = ?';
            $values = array_values($this->attributes);
            $values[] = $this->attributes[$this->primaryKey];

            $sql = "UPDATE {$this->table} SET $placeholders WHERE {$this->primaryKey} = ?";
            $this->query($sql, $values);
        }

        if (empty($changedColumnsText)) {
            $changedColumnsText = "No changes made";
        }

        return $changedColumnsText;
    }



    public static function all()
    {
        $instance = new static;
        $query = "SELECT * FROM {$instance->table} ";
        $results = $instance->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        foreach ($results as $result) {
            $instances[] = new static($result);
        }

        return $instances;
    }
    public static function item_list()
    {
        $instance = new static;
        $query = "SELECT * FROM {$instance->table} ";
        $results = $instance->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $options = "";

        $instances = [];
        foreach ($results as $result) {
            // $instances[] = new static($result);
            $options .= "<option value='" . htmlspecialchars($result['iD'], ENT_QUOTES) . "'>"
                . htmlspecialchars($result['name'], ENT_QUOTES) . "</option>";
        }

        return $options;
    }

    public static function item_list_where($column, $value, $view, $selected = 1)
    {
        $instance = new static;
        $options = "";
        $query = "SELECT * FROM {$instance->table} WHERE {$column} = ? ORDER BY name ASC ";
        $results = $instance->query($query, [$value])->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $isSelected = ($result['iD'] == $selected) ? "selected" : "";
            $options .= "<option value='" . htmlspecialchars($result['iD'], ENT_QUOTES) . "' $isSelected>"
                . htmlspecialchars($result[$view], ENT_QUOTES) . "</option>";
        }

        return $options;
    }


    /**
     * Whitelist an ORDER BY clause. Accepts one or more comma-separated
     * "column" or "column ASC|DESC" terms with plain identifiers only.
     * Anything else is rejected (returns '') so it can't inject SQL.
     */
    protected static function sanitizeOrderBy($order_by)
    {
        $order_by = trim((string) $order_by);
        if ($order_by === '') {
            return '';
        }

        $clean = [];
        foreach (explode(',', $order_by) as $term) {
            $term = trim($term);
            if (preg_match('/^`?[A-Za-z_][A-Za-z0-9_]*`?(\s+(ASC|DESC))?$/i', $term)) {
                $clean[] = $term;
            }
        }

        return implode(', ', $clean);
    }

    public static function page($selected_page, $rows_per_page, $searchString = '', $order_by = '', $joins = '', $columns = '*')
    {
        $instance = new static;

        $offset = ($selected_page - 1) * $rows_per_page;
        $query = "SELECT $columns FROM {$instance->table}";
        // Add the JOIN clause if provided
        if ($joins) {
            $query .= " $joins";
        }

        if ($searchString) {
            $query .= " WHERE $searchString";
        }

        // $order_by often comes from $_POST — only allow safe
        // "column [ASC|DESC]" expressions to avoid SQL injection.
        $order_by = self::sanitizeOrderBy($order_by);
        if ($order_by) {
            $query .= " ORDER BY $order_by";
        }

        $rows_per_page = (int) $rows_per_page;
        $offset = (int) $offset;
        $query .= " LIMIT $rows_per_page OFFSET $offset";
        //  echo $query;

        $results = $instance->query($query)->fetchAll(PDO::FETCH_ASSOC);

        // Get the total number of records
        $total_records_query = "SELECT COUNT(*) FROM {$instance->table}";
        // Add the JOIN clause to the count query if provided
        if ($joins) {
            $total_records_query .= " $joins";
        }

        if ($searchString) {
            $total_records_query .= " WHERE $searchString";
        }
        $total_records = $instance->query($total_records_query)->fetchColumn();

        // Calculate the total number of pages
        $total_pages = ceil($total_records / $rows_per_page);

        $instances = [];
        foreach ($results as $result) {
            $instances[] = new static($result);
        }

        return [
            'data' => $instances,
            'selected_page' => $selected_page,
            'rows_per_page' => $rows_per_page,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'query' => $query
        ];
    }
    /*
    $page_data = Document::page(2, 10); // Fetch the 2nd page with 10 rows per page

$documents = $page_data['data']; // The documents for the current page
$current_page = $page_data['selected_page']; // The current page number
$rows_per_page = $page_data['rows_per_page']; // The number of rows per page
*/
    public function save()
    {
        $fields = array_keys($this->attributes);
        $values = array_values($this->attributes);
        $placeholders = implode(',', array_fill(0, count($fields), '?'));

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $this->query($sql, $values);

        // Retrieve the last inserted ID and set it in the attributes
        $this->attributes[$this->primaryKey] = $this->pdo->lastInsertId();
    }

    public static function find($id)
    {
        $instance = new static;
        $result = $instance->query("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        return new static($result);
    }

    public static function latest()
    {
        $instance = new static;
        $result = $instance->query("SELECT * FROM {$instance->table} ORDER BY reg_date DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        return new static($result);
    }
    public static function latestWhere($column, $value)
    {
        $instance = new static;
        // $result = $instance->query("SELECT * FROM {$instance->table} WHERE {$column} = ? ORDER BY reg_date DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $query = "SELECT * FROM {$instance->table} WHERE {$column} = ? ORDER BY reg_date DESC LIMIT 1";
        $result = $instance->query($query, [$value])->fetch(PDO::FETCH_ASSOC);
        //  return $result;
        return new static($result);
    }
    public function toArray()
    {
        return get_object_vars($this);
    }

    public static function findByQuery($query, $params = [])
    {
        $instance = new static;
        $results = $instance->query($query, $params)->fetchAll(PDO::FETCH_ASSOC);



        $instances = [];
        foreach ($results as $result) {
            $instances[] = new static($result);
        }

        return $instances;
    }






    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $this->query($sql, [$this->attributes[$this->primaryKey]]);
    }
    public static function whereMulti(array $conditions)
    {
        $instance = new static;

        // Start the query
        $query = "SELECT * FROM {$instance->table} WHERE ";

        // Build the WHERE clause with placeholders
        $whereClauses = [];
        $values = [];
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value; // Bind the value for each placeholder
        }

        // Join the conditions using AND and append to the query
        $query .= implode(' AND ', $whereClauses);

        // Execute the query and fetch all results
        $results = $instance->query($query, $values)->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        if ($results) {
            // Loop through each result and instantiate a new object
            foreach ($results as $result) {
                $instances[] = new static($result);
            }
        }

        return $instances;
    }

    public static function make()
    {
        return new static;
    }
    public static function where($column, $value)
    {
        $instance = new static;
        $query = "SELECT * FROM {$instance->table} WHERE {$column} = ?";
        $results = $instance->query($query, [$value])->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        foreach ($results as $result) {
            $instances[] = new static($result);
        }

        return $instances;
    }
    /*
    $videos = EventVideo::where('compressed', '=', 0)
                    ->orWhere('framed', '=', 0)
                    ->get();
    */
    public  function were($column, $operator, $value)
    {
        $instance = $this;
        $instance->queryConditions[] = "$column $operator ?";
        $instance->queryParams[] = $value;
        return $instance; // Enable method chaining
    }

    // New method to add an OR WHERE condition
    public  function orWhere($column, $operator, $value)
    {
        $instance = $this;
        $instance->queryConditions[] = "OR $column $operator ?";
        $instance->queryParams[] = $value;
        return $instance; // Enable method chaining
    }
    // New method to add an OR WHERE condition
    public  function andWhere($column, $operator, $value)
    {
        $instance = $this;
        $instance->queryConditions[] = "AND $column $operator ?";
        $instance->queryParams[] = $value;
        return $instance; // Enable method chaining
    }

    // Method to execute the query based on the built conditions
    /* =========================
 * Simple aggregates
 * ========================= */
    public static function min($column, $where = '', $params = [])
    {
        $instance = new static;
        $sql = "SELECT MIN($column) AS v FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        return $instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['v'] ?? null;
    }
    public static function max($column, $where = '', $params = [])
    {
        $instance = new static;
        $sql = "SELECT MAX($column) AS v FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        return $instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['v'] ?? null;
    }
    public static function avg($column, $where = '', $params = [])
    {
        $instance = new static;
        $sql = "SELECT AVG($column) AS v FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        $v = $instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['v'] ?? null;
        return $v !== null ? (float)$v : null;
    }
    public static function sum($column, $where = '', $params = [])
    {
        $instance = new static;
        $sql = "SELECT SUM($column) AS v FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        $v = $instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['v'] ?? null;
        return $v !== null ? (float)$v : null;
    }

    /* =========================
 * Raw count helpers
 * ========================= */
    public static function countRaw($where = '', $params = [])
    {
        $instance = new static;
        $sql = "SELECT COUNT(iD) AS cnt FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        return (int)($instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
    }

    /* e.g. count rows where request IN (SELECT ... ) */
    public static function countWhereInSubquery($column, $subSql, $params = [])
    {
        $instance = new static;
        $sql = "SELECT COUNT(iD) AS cnt FROM {$instance->table} WHERE {$column} IN ($subSql)";
        return (int)($instance->query($sql, $params)->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
    }

    /* =========================
 * Quick list helpers
 * ========================= */
    public static function latestN($limit = 5, $orderBy = 'reg_date DESC')
    {
        $instance = new static;
        $limit = (int)$limit;
        $sql = "SELECT * FROM {$instance->table} ORDER BY {$orderBy} LIMIT {$limit}";
        $rows = $instance->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new static($r), $rows);
    }

    /* With simple WHERE, ORDER, LIMIT */
    public static function getWhereOrderLimit($where = '', $params = [], $orderBy = 'reg_date DESC', $limit = 10)
    {
        $instance = new static;
        $sql = "SELECT * FROM {$instance->table}";
        if ($where) $sql .= " WHERE $where";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        $sql .= " LIMIT " . (int)$limit;
        $rows = $instance->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new static($r), $rows);
    }

    /* =========================
 * Tiny builder extensions (optional)
 * ========================= */
    protected $queryOrderBy = '';
    protected $queryLimit   = 0;

    public function orderBy($column, $dir = 'DESC')
    {
        $this->queryOrderBy = trim($column . ' ' . $dir);
        return $this;
    }
    public function limit($n)
    {
        $this->queryLimit = (int)$n;
        return $this;
    }
    public function whereRaw($clause, $params = [])
    {
        // Allow adding arbitrary WHERE chunks (AND’ed)
        if ($clause) {
            if (!empty($this->queryConditions)) $this->queryConditions[] = "AND ($clause)";
            else $this->queryConditions[] = "($clause)";
            foreach ((array)$params as $p) $this->queryParams[] = $p;
        }
        return $this;
    }
    /* Update get() to honor ORDER BY + LIMIT */
    public function get()
    {
        $instance = $this;
        $query = "SELECT * FROM {$instance->table}";
        if (!empty($instance->queryConditions)) {
            $query .= " WHERE " . implode(' ', $instance->queryConditions);
        }
        if ($instance->queryOrderBy) {
            $query .= " ORDER BY {$instance->queryOrderBy}";
        }
        if ($instance->queryLimit > 0) {
            $query .= " LIMIT {$instance->queryLimit}";
        }
        $stmt = $instance->query($query, $instance->queryParams);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // reset builder state
        $instance->queryConditions = [];
        $instance->queryParams = [];
        $instance->queryOrderBy = '';
        $instance->queryLimit = 0;

        return array_map(fn($r) => new static($r), $results);
    }

    // Method to reset query conditions after each query
    protected function resetQuery()
    {
        $instance = new static;

        $instance->queryConditions = [];
        $instance->queryParams = [];
    }
    public static function countAll()
    {
        $instance = new static;
        $query = "SELECT COUNT(iD) AS cnt FROM {$instance->table} ";
        $result = $instance->query($query)->fetch(PDO::FETCH_ASSOC)['cnt'];
        return $result;
    }
    public static function countWhere($column, $value)
    {
        $instance = new static;
        $query = "SELECT COUNT(iD) AS cnt FROM {$instance->table} WHERE {$column} = ?";
        $result = $instance->query($query, [$value])->fetch(PDO::FETCH_ASSOC)['cnt'];
        return $result;
    }
    public static function countMulti(array $conditions)
    {
        $instance = new static;

        // Start the query
        $query = "SELECT COUNT(iD) AS cnt FROM {$instance->table} WHERE ";

        // Build the WHERE clause with placeholders
        $whereClauses = [];
        $values = [];
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value; // Bind the value for each placeholder
        }

        // Join the conditions using AND and append to the query
        $query .= implode(' AND ', $whereClauses);

        // Execute the query
        $result = $instance->query($query, $values)->fetch(PDO::FETCH_ASSOC)['cnt'];
        return $result;
    }
    public function belongsTo($relatedModel, $foreignKey, $localKey = 'iD', $relationName = null)
    {
        $relatedInstance = new $relatedModel;
        $relatedTable = $relatedInstance->table;

        $query = "SELECT * FROM {$relatedTable} WHERE {$localKey} = ?";
        $result = $this->query($query, [$this->attributes[$foreignKey]])->fetch(PDO::FETCH_ASSOC);

        $related = new $relatedModel($result);

        // Store relation for serialization
        if ($relationName) {
            $this->appendedRelations[$relationName] = $related;
        }

        return $related;
    }

    public function hasMany($relatedModel, $foreignKey, $localKey = 'iD', $relationName = null)
    {
        $relatedInstance = new $relatedModel;
        $relatedTable = $relatedInstance->table;

        $query = "SELECT * FROM {$relatedTable} WHERE {$foreignKey} = ?";
        $results = $this->query($query, [$this->attributes[$localKey]])->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        foreach ($results as $result) {
            $instances[] = new $relatedModel($result);
        }

        if ($relationName) {
            $this->appendedRelations[$relationName] = $instances;
        }

        return $instances;
    }
    public function jsonSerialize(): mixed
    {
        $data = $this->attributes;

        foreach ($this->appendedRelations as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_map(function ($item) {
                    return ($item instanceof JsonSerializable) ? $item->jsonSerialize() : $item;
                }, $value);
            } elseif ($value instanceof JsonSerializable) {
                $data[$key] = $value->jsonSerialize();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
    public function toJSONArray(): array
    {
        return $this->jsonSerialize();
    }
    public function _belongsTo($relatedModel, $foreignKey, $localKey = 'iD')
    {
        $relatedInstance = new $relatedModel;
        $relatedTable = $relatedInstance->table;

        $query = "SELECT * FROM {$relatedTable} WHERE {$localKey} = ?";
        $result = $this->query($query, [$this->attributes[$foreignKey]])->fetch(PDO::FETCH_ASSOC);

        return new $relatedModel($result);
    }

    public function _hasMany($relatedModel, $foreignKey, $localKey = 'iD')
    {
        $relatedInstance = new $relatedModel;
        $relatedTable = $relatedInstance->table;

        $query = "SELECT * FROM {$relatedTable} WHERE {$foreignKey} = ?";
        $results = $this->query($query, [$this->attributes[$localKey]])->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        foreach ($results as $result) {
            $instances[] = new $relatedModel($result);
        }

        return $instances;
    }

    protected function getTableCreationQuery()
    {
        throw new Exception('getTableCreationQuery method not defined in the model');
    }
    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    /** Tables already verified this request, so we don't re-check every time. */
    protected static $verifiedTables = [];

    protected function ensureTableExists()
    {
        // Every model instantiation used to run a sqlite_master query; since
        // find()/where()/all() each create a new instance, that was a lot of
        // redundant round-trips. Verify each table at most once per request.
        if (isset(self::$verifiedTables[$this->table])) {
            return;
        }

        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name=:table";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['table' => $this->table]);

        if ($stmt->fetch() === false) {
            // If the table doesn't exist, create it
            error_log("table created");
            $this->pdo->exec($this->getTableCreationQuery());

            // Insert default data
            foreach ($this->getDefaultDataInsertionQuery() as $insertQuery) {
                $this->pdo->exec($insertQuery);
            }
        }

        self::$verifiedTables[$this->table] = true;
    }
}
