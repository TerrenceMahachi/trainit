<?php
namespace App\Controllers;

use App\Models\Staffdepartmentassignment;
use App\Models\Database;

class StaffdepartmentassignmentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffprofile']) && $_POST['staffprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffprofile=' . $_POST['staffprofile'];
}
if (isset($_POST['department']) && $_POST['department'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' department=' . $_POST['department'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`is_head` LIKE $escapedTerm OR `effective_from` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Staffdepartmentassignment::page($selected_page, $page_size, $search, $order_by);
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
        $department = $_POST["department"];
        $is_head = $_POST["is_head"];
        $effective_from = $_POST["effective_from"];
        
       
        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($department == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_department "; $_error = true; }
        if ($is_head == "" && (!$_error)) {  $_result .= "<br>Error: is_head cannot be blank"; $_error = true; }
        if ($effective_from == "" && (!$_error)) {  $_result .= "<br>Error: effective_from cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM staffdepartmentassignment WHERE name=?";
            $records = Staffdepartmentassignment::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Staffdepartmentassignment();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffprofile = $staffprofile;
            $record->department = $department;
            $record->is_head = $is_head;
            $record->effective_from = $effective_from;
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
        $department = $_POST["department"];
        $is_head = $_POST["is_head"];
        $effective_from = $_POST["effective_from"];



        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($department == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_department "; $_error = true; }
        if ($is_head == "" && (!$_error)) {  $_result .= "<br>Error: is_head cannot be blank"; $_error = true; }
        if ($effective_from == "" && (!$_error)) {  $_result .= "<br>Error: effective_from cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM staffdepartmentassignment WHERE name=? AND iD!=?";
            $records = Staffdepartmentassignment::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Staffdepartmentassignment::where('iD', $recordiD)[0];
            
            $record->staffprofile = $staffprofile;
            $record->department = $department;
            $record->is_head = $is_head;
            $record->effective_from = $effective_from;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}