<?php
namespace App\Controllers;

use App\Models\Servicerequestattachment;
use App\Models\Database;

class ServicerequestattachmentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['servicerequest']) && $_POST['servicerequest'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicerequest=' . $_POST['servicerequest'];
}
if (isset($_POST['messagevisibility']) && $_POST['messagevisibility'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' messagevisibility=' . $_POST['messagevisibility'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`file_path` LIKE $escapedTerm OR `file_name` LIKE $escapedTerm OR `file_size` LIKE $escapedTerm OR `mime_type` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequestattachment::page($selected_page, $page_size, $search, $order_by);
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
        
        $servicerequest = $_POST["servicerequest"];
        $file_path = $_POST["file_path"];
        $file_name = $_POST["file_name"];
        $file_size = $_POST["file_size"];
        $mime_type = $_POST["mime_type"];
        $messagevisibility = $_POST["messagevisibility"];
        
       
        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($file_name == "" && (!$_error)) {  $_result .= "<br>Error: file_name cannot be blank"; $_error = true; }
        if ($file_size == "" && (!$_error)) {  $_result .= "<br>Error: file_size cannot be blank"; $_error = true; }
        if ($mime_type == "" && (!$_error)) {  $_result .= "<br>Error: mime_type cannot be blank"; $_error = true; }
        if ($messagevisibility == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_messagevisibility "; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequestattachment WHERE name=?";
            $records = Servicerequestattachment::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequestattachment();
            $record->reg_by = $_COOKIE['user'];
            
            $record->servicerequest = $servicerequest;
            $record->file_path = $file_path;
            $record->file_name = $file_name;
            $record->file_size = $file_size;
            $record->mime_type = $mime_type;
            $record->messagevisibility = $messagevisibility;
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
        
        $servicerequest = $_POST["servicerequest"];
        $file_path = $_POST["file_path"];
        $file_name = $_POST["file_name"];
        $file_size = $_POST["file_size"];
        $mime_type = $_POST["mime_type"];
        $messagevisibility = $_POST["messagevisibility"];



        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($file_path == "" && (!$_error)) {  $_result .= "<br>Error: file_path cannot be blank"; $_error = true; }
        if ($file_name == "" && (!$_error)) {  $_result .= "<br>Error: file_name cannot be blank"; $_error = true; }
        if ($file_size == "" && (!$_error)) {  $_result .= "<br>Error: file_size cannot be blank"; $_error = true; }
        if ($mime_type == "" && (!$_error)) {  $_result .= "<br>Error: mime_type cannot be blank"; $_error = true; }
        if ($messagevisibility == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_messagevisibility "; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequestattachment WHERE name=? AND iD!=?";
            $records = Servicerequestattachment::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequestattachment::where('iD', $recordiD)[0];
            
            $record->servicerequest = $servicerequest;
            $record->file_path = $file_path;
            $record->file_name = $file_name;
            $record->file_size = $file_size;
            $record->mime_type = $mime_type;
            $record->messagevisibility = $messagevisibility;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}