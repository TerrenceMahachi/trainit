<?php
namespace App\Controllers;

use App\Models\Staffleave;
use App\Models\Database;

class StaffleavesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffprofile']) && $_POST['staffprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffprofile=' . $_POST['staffprofile'];
}
if (isset($_POST['leavetype']) && $_POST['leavetype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' leavetype=' . $_POST['leavetype'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`start_date` LIKE $escapedTerm OR `end_date` LIKE $escapedTerm OR `days_requested` LIKE $escapedTerm OR `reason` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Staffleave::page($selected_page, $page_size, $search, $order_by);
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
        $leavetype = $_POST["leavetype"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $days_requested = $_POST["days_requested"];
        $reason = $_POST["reason"];
        
       
        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($leavetype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_leavetype "; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($days_requested == "" && (!$_error)) {  $_result .= "<br>Error: days_requested cannot be blank"; $_error = true; }
        if ($reason == "" && (!$_error)) {  $_result .= "<br>Error: reason cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM staffleave WHERE name=?";
            $records = Staffleave::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Staffleave();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffprofile = $staffprofile;
            $record->leavetype = $leavetype;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->days_requested = $days_requested;
            $record->reason = $reason;
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
        $leavetype = $_POST["leavetype"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $days_requested = $_POST["days_requested"];
        $reason = $_POST["reason"];



        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($leavetype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_leavetype "; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($days_requested == "" && (!$_error)) {  $_result .= "<br>Error: days_requested cannot be blank"; $_error = true; }
        if ($reason == "" && (!$_error)) {  $_result .= "<br>Error: reason cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM staffleave WHERE name=? AND iD!=?";
            $records = Staffleave::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Staffleave::where('iD', $recordiD)[0];
            
            $record->staffprofile = $staffprofile;
            $record->leavetype = $leavetype;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->days_requested = $days_requested;
            $record->reason = $reason;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}