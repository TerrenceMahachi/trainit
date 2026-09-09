<?php
namespace App\Controllers;

use App\Models\Rosterdocument;
use App\Models\Database;

class RosterdocumentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['documenttype']) && $_POST['documenttype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' documenttype=' . $_POST['documenttype'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`file_path` LIKE $escapedTerm OR `original_name` LIKE $escapedTerm OR `file_size_kb` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterdocument::page($selected_page, $page_size, $search, $order_by);
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
        
        $rosterapplication = $_POST["rosterapplication"];
        $documenttype = $_POST["documenttype"];
        $file_path = $_POST["file_path"];
        $original_name = $_POST["original_name"];
        $file_size_kb = $_POST["file_size_kb"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($documenttype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_documenttype "; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($original_name == "" && (!$_error)) {  $_result .= "<br>Error: original_name cannot be blank"; $_error = true; }
        if ($file_size_kb == "" && (!$_error)) {  $_result .= "<br>Error: file_size_kb cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterdocument WHERE name=?";
            $records = Rosterdocument::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterdocument();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->documenttype = $documenttype;
            $record->file_path = $file_path;
            $record->original_name = $original_name;
            $record->file_size_kb = $file_size_kb;
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
        
        $rosterapplication = $_POST["rosterapplication"];
        $documenttype = $_POST["documenttype"];
        $file_path = $_POST["file_path"];
        $original_name = $_POST["original_name"];
        $file_size_kb = $_POST["file_size_kb"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($documenttype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_documenttype "; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($original_name == "" && (!$_error)) {  $_result .= "<br>Error: original_name cannot be blank"; $_error = true; }
        if ($file_size_kb == "" && (!$_error)) {  $_result .= "<br>Error: file_size_kb cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterdocument WHERE name=? AND iD!=?";
            $records = Rosterdocument::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterdocument::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->documenttype = $documenttype;
            $record->file_path = $file_path;
            $record->original_name = $original_name;
            $record->file_size_kb = $file_size_kb;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}