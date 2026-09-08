<?php
namespace App\Controllers;

use App\Models\Stafftimeapproval;
use App\Models\Database;

class StafftimeapprovalsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['stafftimeentry']) && $_POST['stafftimeentry'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' stafftimeentry=' . $_POST['stafftimeentry'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`is_approved` LIKE $escapedTerm OR `review_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Stafftimeapproval::page($selected_page, $page_size, $search, $order_by);
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
        
        $stafftimeentry = $_POST["stafftimeentry"];
        $is_approved = $_POST["is_approved"];
        $review_notes = $_POST["review_notes"];
        
       
        
        if ($stafftimeentry == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_stafftimeentry "; $_error = true; }
        if ($is_approved == "" && (!$_error)) {  $_result .= "<br>Error: is_approved cannot be blank"; $_error = true; }
        if ($review_notes == "" && (!$_error)) {  $_result .= "<br>Error: review_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM stafftimeapproval WHERE name=?";
            $records = Stafftimeapproval::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Stafftimeapproval();
            $record->reg_by = $_COOKIE['user'];
            
            $record->stafftimeentry = $stafftimeentry;
            $record->is_approved = $is_approved;
            $record->review_notes = $review_notes;
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
        
        $stafftimeentry = $_POST["stafftimeentry"];
        $is_approved = $_POST["is_approved"];
        $review_notes = $_POST["review_notes"];



        
        if ($stafftimeentry == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_stafftimeentry "; $_error = true; }
        if ($is_approved == "" && (!$_error)) {  $_result .= "<br>Error: is_approved cannot be blank"; $_error = true; }
        if ($review_notes == "" && (!$_error)) {  $_result .= "<br>Error: review_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM stafftimeapproval WHERE name=? AND iD!=?";
            $records = Stafftimeapproval::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Stafftimeapproval::where('iD', $recordiD)[0];
            
            $record->stafftimeentry = $stafftimeentry;
            $record->is_approved = $is_approved;
            $record->review_notes = $review_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}