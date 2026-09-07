<?php
class ModelGenerator extends CodeGenerator {
    private function generateFieldsSql() {
        $fieldsSql = '';
        foreach ($this->columns as $column) {
          
                $fieldsSql .= "`{$column->cleaned_name}` " . strtoupper($column->type) . " NOT NULL,\n                ";
           
        }
        return rtrim($fieldsSql, ",\n                ");
    }

    private function generateForeignKeySql() {
        $foreignKeySql = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $field = $column->cleaned_name;
                $table = $column->referencedTable;
                $foreignKeySql .= "\n                 ,FOREIGN KEY(`$field`) REFERENCES `$table`(iD)";
            }
        }
        return rtrim($foreignKeySql, ",\n                ");
    }

    private function generateHasFields() {
        $hasFields = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $field = $column->cleaned_name;
                $relatedModel = $column->relatedModel;
                $hasFields .= "public function {$field}()\n{\n    return \$this->belongsTo({$relatedModel}::class, '{$field}');\n}\n";
            }
        }
        return $hasFields;
    }

    public function generate() {
        $template = file_get_contents(GEN_TPL . '/model.template.php');
        $content = str_replace(
            ['{{MODEL_NAME}}', '{{TABLE_NAME}}', '{{FIELDS_SQL}}', '{{FOREIGN_KEYS_SQL}}', '{{HAS_FIELDS}}'],
            [$this->modelName, $this->tableName, $this->generateFieldsSql(), $this->generateForeignKeySql(), $this->generateHasFields()],
            $template
        );
        file_put_contents(GEN_ROOT . "/app/Models/{$this->modelName}.php", $content);
    }
}