<?php
class ViewGenerator extends CodeGenerator {
    private function generateFormFields($forEdit = false) {
        $html = '';
        $skipFields = ['iD', 'reg_by', 'reg_date'];
        foreach ($this->columns as $column) {
            if (in_array($column->name, $skipFields)) continue;
            $inputType = match ($column->type) {
                'text' => 'text', 'integer' => 'number', 'decimal' => 'number', 'date' => 'date', 'datetime' => 'datetime-local', 'time' => 'time', default => 'text'
            };
            if ($column->isForeignKey) {
                $relatedModel = $column->relatedModel;
                $fieldName = $column->cleaned_name;
                $html .= "
                <div class=\"form-group mb-4 col-md-12\">
                    <label class=\"text-muted fw-lighter fs-6 mb-3 text-muted\"> " . ucfirst($fieldName) . ": </label>
                    <select class=\"form-select form-select-lg f-sel\" name=\"{$fieldName}\" required>
                        <option value=\"\">Select " . ucfirst($fieldName) . "</option>
                        <?php foreach ({$relatedModel}::all() as \$selector): ?>
                            " . ($forEdit ? "<option value=\"<?= \$selector->iD; ?>\" <?= \$data['record']->{$fieldName} == \$selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars(\$selector->name ?? ''); ?></option>" : "<option value=\"<?= \$selector->iD; ?>\"><?= htmlspecialchars(\$selector->name ?? ''); ?></option>") . "
                        <?php endforeach; ?>
                    </select>
                </div>";
            } else {
                $html .= "
                <div class=\"form-group col-md-12 mb-4\">
                    <label class=\"text-muted fw-lighter fs-6 mb-3 text-muted\"> {$column->name}: </label>
                    <input type=\"{$inputType}\" name=\"{$column->name}\" class=\"form-control form-control-lg\"" . ($forEdit ? " value=\"<?= htmlspecialchars(\$item->{$column->name} ?? '', ENT_QUOTES) ?>\"" : "") . " />
                </div>";
            }
        }
        return $html;
    }

    private function generateIncludeFields() {
        $includes = '';
        foreach ($this->columns as $column) {
            if ($column->isForeignKey) {
                $includes .= "use App\Models\\{$column->relatedModel};\n";
            }
        }
        return $includes;
    }

    private function generateListColumns() {
        $listColumns = '';
        $skipFields = ['iD', 'reg_by', 'reg_date'];
        foreach ($this->columns as $column) {
            if (!in_array($column->name, $skipFields)) {
                $listColumns .= "                 |  <span class=\"fw-normal text-black\">{$column->cleaned_name}:</span> @{$column->cleaned_name}_name\n";
            }
        }
        return $listColumns;
    }

    private function generateTableColumns() {
        $tableColumns = '';
        $skipFields = ['iD', 'reg_by', 'reg_date'];
        foreach ($this->columns as $column) {
            if (!in_array($column->name, $skipFields)) {
                $tableColumns .= "            <td>@{$column->cleaned_name}_name</td>\n";
            }
        }
        return $tableColumns;
    }

    private function generateSearchColumns() {
        $searchColumns = '';
        $skipFields = ['iD', 'reg_by', 'reg_date'];
        foreach ($this->columns as $column) {
            if (in_array($column->name, $skipFields)) continue;
            if ($column->isForeignKey) {
                $fieldName = $column->cleaned_name;
                $relatedModel = $column->relatedModel;
                $searchColumns .= "
                <div class='mb-3 col-auto '>
                    <select class='form-select select save-state f-sel' name='{$fieldName}'>
                        <option value=\"\">All " . ucfirst($fieldName) . "s</option>
                        <?php foreach ({$relatedModel}::all() as \$selector): ?>
                            <option value=\"<?= \$selector->iD; ?>\"><?= \$selector->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>";
            }
        }
        return $searchColumns;
    }

    private function generateViewFields() {
        $viewFields = '';
        $skipFields = ['iD', 'reg_by', 'reg_date'];
        foreach ($this->columns as $column) {
            if (!in_array($column->name, $skipFields)) {
                if ($column->isForeignKey) {
                    $fieldName = $column->cleaned_name;
                    $viewFields .= "
                    <tr>
                        <th class=\"fw-bold\">" . ucfirst($fieldName) . "</th>
                        <td class=\"text-muted\"><?= htmlspecialchars(\$item->{$fieldName}()->name ?? ''); ?></td>
                    </tr>";
                } else {
                    $viewFields .= "
                    <tr>
                        <th class=\"fw-bold\">" . ucfirst($column->name) . "</th>
                        <td class=\"text-muted\"><?= htmlspecialchars(\$item->{$column->name} ?? ''); ?></td>
                    </tr>";
                }
            }
        }
        return $viewFields;
    }

    public function generate() {
        $viewFolderName = GEN_ROOT . "/views/{$this->modelLower}s";
        if (!file_exists($viewFolderName)) {
            mkdir($viewFolderName, 0777, true);
        }

        // Index View
        $indexTemplate = file_get_contents(GEN_TPL . '/index.template.php');
        $indexContent = str_replace(
            ['{{MODEL_NAME}}', '{{MODEL_NAME_LOWERCASE}}', '{{LIST_COLUMNS}}', '{{TABLE_COLUMNS}}', '{{INCLUDE_FIELDS}}', '{{SEARCH_COLUMNS}}'],
            [$this->modelName, $this->modelLower, $this->generateListColumns(), $this->generateTableColumns(), $this->generateIncludeFields(), $this->generateSearchColumns()],
            $indexTemplate
        );
        file_put_contents("$viewFolderName/index.php", $indexContent);

        // Create View
        $createTemplate = file_get_contents(GEN_TPL . '/create.template.php');
        $createContent = str_replace(
            ['{{FORM_FIELDS}}', '{{INCLUDE_FIELDS}}'],
            [$this->generateFormFields(false), $this->generateIncludeFields()],
            $createTemplate
        );
        file_put_contents("$viewFolderName/create.php", $createContent);

        // Edit View
        $editTemplate = file_get_contents(GEN_TPL . '/edit.template.php');
        $editContent = str_replace(
            ['{{EDIT_FIELDS}}', '{{INCLUDE_FIELDS}}'],
            [$this->generateFormFields(true), $this->generateIncludeFields()],
            $editTemplate
        );
        file_put_contents("$viewFolderName/edit.php", $editContent);

        // View View
        $viewTemplate = file_get_contents(GEN_TPL . '/view.template.php');
        $viewContent = str_replace('{{VIEW_FIELDS}}', $this->generateViewFields(), $viewTemplate);
        file_put_contents("$viewFolderName/view.php", $viewContent);

        // Nav View
        $navTemplate = file_get_contents(GEN_TPL . '/nav.template.php');
        file_put_contents("$viewFolderName/nav.php", $navTemplate);

        // Header View
        $headerTemplate = file_get_contents(GEN_TPL . '/header.template.php');
        $headerContent = str_replace(
            ['{{MODEL_NAME}}', '{{MODEL_NAME_LOWERCASE}}'],
            [$this->modelName, $this->modelLower],
            $headerTemplate
        );
        file_put_contents("$viewFolderName/header.php", $headerContent);
    }
}