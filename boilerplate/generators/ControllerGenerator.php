<?php
class ControllerGenerator extends CodeGenerator {
    private function generateColumns() {
        $columns = '';
        foreach ($this->columns as $column) {
            $columns .= "\n            \$record->{$column->cleaned_name} = \${$column->cleaned_name};";
        }
        return $columns;
    }

    private function generateValidation() {
        $validation = '';
        foreach ($this->columns as $column) {
            if (!$column->isForeignKey) {
                $validation .= "\n        if (\${$column->name} == \"\" && (!\$_error)) {  \$_result .= \"<br>Error: {$column->name} cannot be blank\"; \$_error = true; }";
            }else{
                $validation .= "\n        if (\${$column->cleaned_name} == \"\" && (!\$_error)) {  \$_result .= \"<br>Error: Please provide a valid value for {$column->name} \"; \$_error = true; }";
            }
        }
        return $validation;
    }

    private function generateSetFields() {
        $setFields = '';
        foreach ($this->columns as $column) {
            $fieldName = $column->cleaned_name;
            $setFields .= "\n        \${$column->cleaned_name} = \$_POST[\"{$column->cleaned_name}\"];";
        }
        return $setFields;
    }

    private function generateSearchString() {
        $searchString = '';
        foreach ($this->columns as $column) {
            if (!$column->isForeignKey) {
                if ($searchString) $searchString .= " OR ";
                $searchString .= "`{$column->cleaned_name}` LIKE \$escapedTerm";
            }
        }
        return $searchString;
    }

    private function generateSearchList() {
        $searchList = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $fieldName = $column->cleaned_name;
                $searchList .= "\nif (isset(\$_POST['$fieldName']) && \$_POST['$fieldName'] != '') {\n    if (\$search != '') {  \$search .= ' AND '; }\n    \$search .= ' $fieldName=' . \$_POST['$fieldName'];\n}";
            }
        }
        return $searchList;
    }

    public function generate() {
        $template = file_get_contents(GEN_TPL . '/controller.template.php');
        $content = str_replace(
            ['{{MODEL_NAME}}', '{{MODEL_NAME_LOWERCASE}}', '{{COLUMNS}}', '{{COLUMNS_VALIDATION}}', '{{COLUMNS_SET}}', '{{SEARCH_STRING}}', '{{SEARCH_LIST}}'],
            [$this->modelName, $this->modelLower, $this->generateColumns(), $this->generateValidation(), $this->generateSetFields(), $this->generateSearchString(), $this->generateSearchList()],
            $template
        );
        file_put_contents(GEN_ROOT . "/app/Controllers/{$this->modelName}sController.php", $content);
    }
}