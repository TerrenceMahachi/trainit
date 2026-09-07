<?php
// Absolute anchors so the generator works regardless of the current working
// directory and the new project layout (app/ + public/ web root).
define('GEN_ROOT', dirname(__DIR__));          // project root
define('GEN_TPL', __DIR__ . '/templates');     // boilerplate/templates

require_once __DIR__ . '/generators/Column.php';
require_once __DIR__ . '/generators/ColumnParser.php';
require_once __DIR__ . '/generators/CodeGenerator.php';
require_once __DIR__ . '/generators/ModelGenerator.php';
require_once __DIR__ . '/generators/ControllerGenerator.php';
require_once __DIR__ . '/generators/ViewGenerator.php';
require_once __DIR__ . '/generators/ScriptGenerator.php';
require_once __DIR__ . '/generators/RouterGenerator.php';
require_once __DIR__ . '/generators/AdminLinkGenerator.php';
require_once __DIR__ . '/generators/BoilerplateGenerator.php';

if ($argc !== 3) {
    echo "Usage: php generate_files.php <table_name> <columns_comma_separated>\n";
    exit(1);
}

$tableName = $argv[1];
$columnsString = $argv[2];
$modelLower = strtolower($tableName); // Lowercase for views and routes

$viewFolderPath = GEN_ROOT . "/views/{$modelLower}s";
$controllerPath = GEN_ROOT . "/app/Controllers/{$tableName}sController.php";
$modelPath = GEN_ROOT . "/app/Models/{$tableName}.php";

$existingPaths = [];

if (is_dir($viewFolderPath)) $existingPaths[] = $viewFolderPath;
if (file_exists($controllerPath)) $existingPaths[] = $controllerPath;
if (file_exists($modelPath)) $existingPaths[] = $modelPath;

if (!empty($existingPaths)) {
    echo "The following paths already exist and may be overwritten:\n";
    foreach ($existingPaths as $path) {
        echo "  - $path\n";
    }

    $confirm = readline("Do you want to continue and overwrite them? (yes/no): ");
    if (strtolower(trim($confirm)) !== 'yes') {
        echo "Operation cancelled.\n";
        exit(0);
    }
}

$generator = new BoilerplateGenerator($tableName, $columnsString);
$generator->generate();

echo "Files generated for $tableName!\n";
