<?php
abstract class CodeGenerator {
    protected $tableName;
    protected $modelName;
    protected $modelLower;
    protected $columns;

    public function __construct($tableName, $columns) {
        $this->tableName = $tableName;
        $this->modelName = ucfirst($tableName);
        $this->modelLower = strtolower($tableName);
        $this->columns = $columns;
    }

    abstract public function generate();
}