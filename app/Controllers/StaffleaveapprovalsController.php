<?php
namespace App\Controllers;

use App\Models\Staffleaveapproval;
use App\Models\Database;

class StaffleaveapprovalsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffleave']) && $_POST['staffleave'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffleave=' . $_POST['staffleave'];
}
if (isset($_POST['leavestatus']) && $_POST['leavestatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' leavestatus=' . $_POST['leavestatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`decision_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Staffleaveapproval::page($selected_page, $page_size, $search, $order_by);
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
        
        $staffleave = $_POST["staffleave"];
        $leavestatus = $_POST["leavestatus"];
        $decision_notes = $_POST["decision_notes"];
        
       
        
        if ($staffleave == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffleave "; $_error = true; }
        if ($leavestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_leavestatus "; $_error = true; }
        if ($decision_notes == "" && (!$_error)) {  $_result .= "<br>Error: decision_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM staffleaveapproval WHERE name=?";
            $records = Staffleaveapproval::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Staffleaveapproval();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffleave = $staffleave;
            $record->leavestatus = $leavestatus;
            $record->decision_notes = $decision_notes;
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
        
        $staffleave = $_POST["staffleave"];
        $leavestatus = $_POST["leavestatus"];
        $decision_notes = $_POST["decision_notes"];



        
        if ($staffleave == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffleave "; $_error = true; }
        if ($leavestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_leavestatus "; $_error = true; }
        if ($decision_notes == "" && (!$_error)) {  $_result .= "<br>Error: decision_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM staffleaveapproval WHERE name=? AND iD!=?";
            $records = Staffleaveapproval::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Staffleaveapproval::where('iD', $recordiD)[0];
            
            $record->staffleave = $staffleave;
            $record->leavestatus = $leavestatus;
            $record->decision_notes = $decision_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}