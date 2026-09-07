<?php
class RouterGenerator extends CodeGenerator {
    private function generateRecordColumns() {
        $recordColumns = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $fieldName = $column->cleaned_name;
                $recordColumns .= ",\n         '{$column->cleaned_name}' => \$record->{$fieldName}()->name";
            } else {
                $recordColumns .= ",\n         '{$column->name}' => \$record->{$column->name}";
            }
        }
        return $recordColumns;
    }

    public function generate() {
        $template = file_get_contents(GEN_TPL . '/router.template.php');
        $content = str_replace(
            ['{{MODEL_NAME}}', '{{MODEL_NAME_LOWERCASE}}', '{{RECORD_COLUMNS}}'],
            [$this->modelName, $this->modelLower, $this->generateRecordColumns()],
            $template
        );
        file_put_contents(GEN_ROOT . "/routes/{$this->modelLower}_routes.php", $content);
    }
}