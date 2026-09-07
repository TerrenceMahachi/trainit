<?php
namespace App\Helpers;

use PDO;
use PDOException;
use App\Models\Fund;
use App\Models\FundContributor;

use DateTime;
function get_conn()
{
    $dbFile = defined('_DB_PATH') ? _DB_PATH : __DIR__ . '/../../database/app.db';
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    $pdo->exec("PRAGMA cache_size = 10000;");  // Increase cache size (e.g., 10MB)
    $pdo->exec("PRAGMA temp_store = MEMORY;"); // Store temporary tables in memory instead of disk

    return $pdo;
}
function createThumbnail($sourceFile, $targetFile, $thumbWidth) {
    // Get image dimensions and type
    list($originalWidth, $originalHeight, $imageType) = getimagesize($sourceFile);

    // Calculate new height maintaining aspect ratio
    $thumbHeight = (int)(($originalHeight / $originalWidth) * $thumbWidth); // Cast to int

    // Create a new true color image for the thumbnail
    $thumbImage = imagecreatetruecolor((int)$thumbWidth, $thumbHeight); // Cast to int

    // Create an image resource from the source file
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourceFile);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourceFile);
            // Preserve transparency for PNG
            imagealphablending($thumbImage, false);
            imagesavealpha($thumbImage, true);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourceFile);
            break;
        default:
            return false; // Unsupported image type
    }

    // Resize the source image to the thumbnail
    if (imagecopyresampled(
        $thumbImage,
        $sourceImage,
        0, 0, 0, 0,
        (int)$thumbWidth, $thumbHeight, // Cast dimensions to int
        (int)$originalWidth, (int)$originalHeight // Cast dimensions to int
    )) {
        // Save the thumbnail
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbImage, $targetFile, 90); // Quality: 90
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbImage, $targetFile);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbImage, $targetFile);
                break;
        }

        // Free up memory
        imagedestroy($thumbImage);
        imagedestroy($sourceImage);

        return true;
    }

    // Free up memory on failure
    imagedestroy($thumbImage);
    imagedestroy($sourceImage);

    return false;
}
function update_item($table, $column, $value, $selector_column, $item)
{
    global $conn;
    try {
        $sql = "UPDATE " . $table . " SET `" . $column . "`='" . $value . "' WHERE " . $selector_column . "=" . $item;
        $data = $conn->query($sql);
        return $data->rowCount();
    } catch (PDOException $e) {
        return "Update failed: " . $e->getMessage();
    }

}

function get_item_list($table, $index, $column, $conn)
{

    $result = "";

    $sql = "SELECT * FROM " . $table . "";
    $data = $conn->query($sql);
    while ($row = $data->fetch()) {
        $result .= "<option value='" . $row[$index] . "'>" . $row[$column] . "</option>";
    }

    return $result;
}


function formatDate($dateString)
{
    // Convert the date string into a DateTime object
    $date = new DateTime($dateString);

    // Format the date as 'd M Y'
    return $date->format('d M Y');
}
function rename_func($frameDir)
{
    // Get all the frame files
    $frameFiles = glob($frameDir . 'frame_*.png');

    foreach ($frameFiles as $frameFile) {
        // Extract the frame number from the file name
        if (preg_match('/frame_(\d+).png/', basename($frameFile), $matches)) {
            $frameNumber = intval($matches[1]);

            // Calculate the timestamp
            $hours = str_pad(floor($frameNumber / 3600), 2, '0', STR_PAD_LEFT);
            $minutes = str_pad(floor(($frameNumber % 3600) / 60), 2, '0', STR_PAD_LEFT);
            $seconds = str_pad($frameNumber % 60, 2, '0', STR_PAD_LEFT);
            $timestamp = "{$hours}_{$minutes}_{$seconds}";

            // Create the new file name with timestamp
            $newFileName = "{$frameDir}{$timestamp}.png";

            // Rename the file
            rename($frameFile, $newFileName);
        }
    }
}
function formatDateTime($dateString)
{
    // Convert the date string into a DateTime object
    $date = new DateTime($dateString);

    // Format the date as 'd M Y'
    return $date->format('d M Y H:i');
}

/* $validationRules = [
    'institution' => ['required' => true, 'label' => 'Institution'],
    'pos' => ['required' => true, 'label' => 'Position'],
    'form' => ['required' => true, 'label' => 'Form'],
    'name' => ['required' => true, 'label' => 'Name'],
    'type' => ['required' => true, 'label' => 'Type'],
    'required' => ['required' => true, 'label' => 'Required'],
    'max_length' => ['required' => true, 'label' => 'Max Length']
];

// Get form data
$formData = $_POST;  // or $_REQUEST, depending on how you're receiving form data

// Validate form data
$errors = validateForm($formData, $validationRules); */

function generate_code($table, $column, $size)
{
    $found = true;
    global $conn;
    while ($found) {
        $token = generate_number($size);
        $sql = "SELECT * FROM `$table` WHERE `$column`='" . $token . "'";
        if (count($conn->query($sql)->fetchAll(PDO::FETCH_ASSOC)) == 0) {
            $found = false;
        }
    }
    return $token;

}

function generate_number($size)
{
    $characters1 = '9318062735';
    $randstring = '';

    for ($i = 0; $i < $size; $i++) {
        $randstring .= $characters1[rand(0, (strlen($characters1) - 1))];
    }

    return $randstring;
}


function generate_string($size)
{
    $characters1 = '09A1F6IJ4G8L5MnoCpDq2H7z3rNstUuxYZcBEdKeVWabXfghiPjvOwQRkTlmSy';
    $randstring = '';

    for ($i = 0; $i < $size; $i++) {
        $randstring .= $characters1[rand(0, (strlen($characters1) - 1))];
    }

    return $randstring;
}
function validateForm($formData, $rules)
{
    $errors = [];
    foreach ($rules as $field => $rule) {
        $value = isset($formData[$field]) ? $formData[$field] : '';

        if ($rule['required'] && ($value == '' || $value == '-1')) {
            $errors[] = "Error: {$rule['label']} is required";
        }

        if (isset($rule['maxLength']) && strlen($value) > $rule['maxLength']) {
            $errors[] = "Error: {$rule['label']} cannot be longer than {$rule['maxLength']} characters";
        }

        // Add more validation checks as needed
    }

    return $errors;
}

function insert_record($table, $data)
{
    global $conn;
    // Ensure the table name is safe to use in SQL by whitelisting allowed table names
    /* $allowedTables = ['table1', 'table2', 'images']; // Add your allowed table names here
     if (!in_array($table, $allowedTables)) {
         throw new Exception("Invalid table name");
     }
     */

    // Build the SQL statement
    $columns = implode(", ", array_keys($data));
    $values = ":" . implode(", :", array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

    // Get the last inserted ID
    $lastInsertId = $conn->lastInsertId();

    return $lastInsertId;
}
function get_records($tableName)
{
    global $conn;
    // Prepare SQL query
    $sql = "$tableName";

    // Execute query
    $stmt = $conn->query($sql);

    // Fetch data as objects
    //$result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the result
    return $result;
}

/*
  $data = [
                    'institution' => $k,
                    'position' => $cnt,
                    'name' => $frm['name'],
                    'description' => $frm['description'],
                    'reg_by' => $user
                ];
                $infm = insert_record('institution_form', $data);
                $qns = get_records("form_question WHERE status=1 && form=" . $frm['iD']);
                foreach ($qns as $qn) {
                    $data = [
                        'institution' => $k,
                        'pos' => $qn['pos'],
                        'name' => $qn['name'],
                        'options' => $qn['options'],
                        'type' => $qn['type'],
                        'required' => $qn['required'],
                        'max_length' => $qn['max_length'],
                        'form' => $infm,
                        'reg_by' => $user
                    ];
                    $infqn = insert_record('institution_form_question', $data);

                }
                */

function record_page_view($p_name)
{
    $pages = get_records("SELECT * FROM `page` WHERE `name`='$p_name' ");
    if (count($pages) == 0) {
        $data = [
            'name' => $p_name
        ];
        $page = insert_record('page', $data);
    } else {
        $page = $pages[0]['iD'];
        $views = get_records("SELECT * FROM `page` WHERE `name`='$p_name' ")[0]['views'] + 1;
        update_item("page", "views", $views, "iD", $page);
    }

    $data = [
        'page' => $page,
        'reg_by' => 1
    ];
    $page_view = insert_record('page_view', $data);

    if (str_contains($p_name, 'hymn/')) {
        $pv = explode("/", $p_name)[3];
        //echo "bk: " . $pv;
        $hymn = get_records("SELECT * FROM `hymn` WHERE `slug`='$pv' ");
        if (count($hymn) > 0) {
            update_item("hymn", "views", ($hymn[0]['views'] + 1), "iD", $hymn[0]['iD']);
        }

    }


}
function sendmail($email, $subject, $msg)
{
    global $siteConfig;
    $siteName = $siteConfig->siteName ?? 'Trainit';
    return \App\Helpers\Mailer::send(
        to: $email,
        subject: $subject,
        bodyHtml: $msg,
        fromEmail: \App\Helpers\Mailer::HELLO,
        fromName: $siteName
    );
}