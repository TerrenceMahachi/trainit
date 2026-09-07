<?php
class ColumnParser {
    /** Types that map to a form input / SQL column. */
    private const SCALAR_TYPES = ['text', 'integer', 'decimal', 'date', 'datetime', 'time', 'boolean'];

    public static function parse($columnsString) {
        $columns = [];

        foreach (explode(',', $columnsString) as $raw) {
            $spec = trim($raw);
            if ($spec === '') {
                continue;
            }

            $parts = explode(':', $spec);
            $name  = trim($parts[0]);
            $type  = isset($parts[1]) ? strtolower(trim($parts[1])) : 'text';
            $related = isset($parts[2]) ? trim($parts[2]) : null;

            self::validate($spec, $parts, $name, $type);

            $columns[] = new Column($name, $type, $related);
        }

        if (empty($columns)) {
            self::fail("No columns provided.");
        }

        return $columns;
    }

    /** Reject malformed specs with a clear message instead of silently misparsing. */
    private static function validate($spec, array $parts, $name, $type) {
        if (count($parts) > 3) {
            self::fail("Too many ':' in column \"$spec\".");
        }
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            self::fail("Invalid column name in \"$spec\".");
        }

        $isFk = str_starts_with($name, 'fk_') || $type === 'fk';

        // A third part is only meaningful as the related model of an fk column.
        if (isset($parts[2]) && $type !== 'fk') {
            self::fail("Unexpected \"{$parts[2]}\" in column \"$spec\". A third value is only allowed as the related model of an fk column (e.g. gender:fk:Gender).");
        }
        // Non-fk columns must use a known scalar type — this is what catches
        // mistakes like `gender:fk_gender:integer` (type "fk_gender").
        if (!$isFk && !in_array($type, self::SCALAR_TYPES, true)) {
            self::fail("Unknown type \"$type\" in column \"$spec\".");
        }
    }

    private static function fail($message) {
        fwrite(STDERR, "Column error: $message\n\n");
        fwrite(STDERR, "Column syntax:  name:type[,name:type ...]\n");
        fwrite(STDERR, "  Scalar types: " . implode(', ', self::SCALAR_TYPES) . "\n");
        fwrite(STDERR, "  Foreign keys: fk_<name>:integer   OR   <name>:fk   OR   <name>:fk:<Model>\n");
        fwrite(STDERR, "  Example: php boilerplate/generate_files.php driver \"name:text,age:integer,gender:fk:Gender\"\n");
        exit(1);
    }
}
