<?php
class ScriptGenerator extends CodeGenerator
{
    private function generateDisplayColumns()
    {
        $columns = '';
        foreach ($this->columns as $column) {
            $label = str_replace('_', ' ', $column->cleaned_name);
            $columns .= "    { key: '{$column->cleaned_name}', label: '{$label}' },\n";
        }
        return rtrim($columns, "\n");
    }

    private function generateSearchFieldsJs()
    {
        $searchFields = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $fieldName = $column->isForeignKey ? $column->cleaned_name : $column->name;
                $searchFields .= "    var n_{$fieldName} = $('.search').find('[name={$fieldName}]').val();\n";

            }
        }
        return $searchFields;
    }

    private function generateFindSearchFieldsJs()
    {
        $findSearchFields = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $fieldName = $column->isForeignKey ? $column->cleaned_name : $column->name;
                $findSearchFields .= "{$fieldName}: n_{$fieldName},\n            ";
            }
        }
        return rtrim($findSearchFields, ",\n            ");
    }

    public function generate()
    {
        $template = file_get_contents(GEN_TPL . '/scripts.template.js');
        $content = str_replace(
            ['{{MODEL_NAME}}', '{{MODEL_NAME_LOWERCASE}}', '{{DISPLAY_COLUMNS}}', '{{SEARCH_FIELDS_JS}}', '{{FIND_SEARCH_FIELDS_JS}}'],
            [$this->modelName, $this->modelLower, $this->generateDisplayColumns(), $this->generateSearchFieldsJs(), $this->generateFindSearchFieldsJs()],
            $template
        );
        $scriptsDir = GEN_ROOT . '/assets/scripts';
        if (!is_dir($scriptsDir)) {
            mkdir($scriptsDir, 0777, true);
        }
        file_put_contents("$scriptsDir/{$this->modelLower}.js", $content);
    }
}
