<?php
class BoilerplateGenerator {
    private $tableName;
    private $columns;

    public function __construct($tableName, $columnsString) {
        $this->tableName = $tableName;
        $this->columns = ColumnParser::parse($columnsString);
    }

    public function generate() {
        (new ModelGenerator($this->tableName, $this->columns))->generate();
        (new ControllerGenerator($this->tableName, $this->columns))->generate();
        (new ViewGenerator($this->tableName, $this->columns))->generate();
        (new ScriptGenerator($this->tableName, $this->columns))->generate();
        (new RouterGenerator($this->tableName, $this->columns))->generate();
        (new AdminLinkGenerator($this->tableName, $this->columns))->generate();
    }
}
