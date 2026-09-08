<?php
namespace App\Controllers;

use App\Models\Staffdocument;
use App\Models\Database;

class StaffdocumentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffprofile']) && $_POST['staffprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffprofile=' . $_POST['staffprofile'];
}
if (isset($_POST['documenttype']) && $_POST['documenttype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' documenttype=' . $_POST['documenttype'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`title` LIKE $escapedTerm OR `file_path` LIKE $escapedTerm OR `file_size` LIKE $escapedTerm OR `mime_type` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Staffdocument::page($selected_page, $page_size, $search, $order_by);
        $data = [
            "order_by" => $order_by,
            "status" => "success",
            "response_code" => "002",
            "records" => $pagination_data['data'],
            "pagination" => [
                "current_page" => $pagination_data['selected_page'],
                "rows_per_page" => $pagination_data['rows_per_page'],
                "total_records" => $pagination_data['total_records'],
                "total_pages" => $pagination_data['total_pages'],
            ]
        ];
        return $data;
    }

    public function create()
    { 
        $_error = false;
        $_result = "";
        
        $staffprofile = $_POST["staffprofile"];
        $documenttype = $_POST["documenttype"];
        $title = $_POST["title"];
        $file_path = $_POST["file_path"];
        $file_size = $_POST["file_size"];
        $mime_type = $_POST["mime_type"];
        
       
        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($documenttype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_documenttype "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($file_size == "" && (!$_error)) {  $_result .= "<br>Error: file_size cannot be blank"; $_error = true; }
        if ($mime_type == "" && (!$_error)) {  $_result .= "<br>Error: mime_type cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM staffdocument WHERE name=?";
            $records = Staffdocument::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Staffdocument();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffprofile = $staffprofile;
            $record->documenttype = $documenttype;
            $record->title = $title;
            $record->file_path = $file_path;
            $record->file_size = $file_size;
            $record->mime_type = $mime_type;
            $record->save();
            $_result = "Record $name added successfully.";
        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
    }
    public function edit($recordiD)
    {
        $_error = false;
        $_result = "";
        $status = $_POST["status"];
        
        $staffprofile = $_POST["staffprofile"];
        $documenttype = $_POST["documenttype"];
        $title = $_POST["title"];
        $file_path = $_POST["file_path"];
        $file_size = $_POST["file_size"];
        $mime_type = $_POST["mime_type"];



        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($documenttype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_documenttype "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($file_size == "" && (!$_error)) {  $_result .= "<br>Error: file_size cannot be blank"; $_error = true; }
        if ($mime_type == "" && (!$_error)) {  $_result .= "<br>Error: mime_type cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM staffdocument WHERE name=? AND iD!=?";
            $records = Staffdocument::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Staffdocument::where('iD', $recordiD)[0];
            
            $record->staffprofile = $staffprofile;
            $record->documenttype = $documenttype;
            $record->title = $title;
            $record->file_path = $file_path;
            $record->file_size = $file_size;
            $record->mime_type = $mime_type;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}