<?php
class Column {
    public $name;          // internal name — FK columns are normalized to "fk_<field>"
    public $cleaned_name;  // the actual DB column / form field name (no fk_ prefix)
    public $type;          // SQL-ish type; FK columns are forced to "integer"
    public $isForeignKey;
    public $relatedModel;      // e.g. "Gender" (the model a FK points at)
    public $referencedTable;   // e.g. "gender" (the table a FK points at)

    /**
     * A foreign key can be declared three ways (all equivalent):
     *   fk_gender:integer   — legacy: name prefixed with fk_
     *   gender:fk           — type "fk"; related model inferred as ucfirst(name)
     *   gender:fk:Gender    — type "fk" with an explicit related model
     */
    public function __construct($name, $type, $relatedModel = null) {
        $byName = str_starts_with($name, 'fk_');
        $byType = strtolower($type) === 'fk';
        $this->isForeignKey = $byName || $byType;

        if ($this->isForeignKey) {
            $field = $byName ? substr($name, 3) : $name;
            $this->cleaned_name    = $field;
            $this->name            = 'fk_' . $field;      // normalize so generators can rely on it
            $this->relatedModel    = ucfirst($relatedModel ?: $field);
            $this->referencedTable = strtolower($this->relatedModel);
            $this->type            = 'integer';           // FK values are integer ids
        } else {
            $this->name         = $name;
            $this->cleaned_name = $name;
            $this->type         = $type;
        }
    }
}
