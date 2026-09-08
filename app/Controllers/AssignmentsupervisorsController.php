<?php
namespace App\Controllers;

use App\Models\Assignmentsupervisor;
use App\Models\Database;

class AssignmentsupervisorsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['workassignment']) && $_POST['workassignment'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' workassignment=' . $_POST['workassignment'];
}
if (isset($_POST['supervisor_user']) && $_POST['supervisor_user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' supervisor_user=' . $_POST['supervisor_user'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`supervision_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Assignmentsupervisor::page($selected_page, $page_size, $search, $order_by);
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
        
        $workassignment = $_POST["workassignment"];
        $supervisor_user = $_POST["supervisor_user"];
        $supervision_notes = $_POST["supervision_notes"];
        
       
        
        if ($workassignment == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_workassignment "; $_error = true; }
        if ($supervisor_user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_supervisor_user "; $_error = true; }
        if ($supervision_notes == "" && (!$_error)) {  $_result .= "<br>Error: supervision_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM assignmentsupervisor WHERE name=?";
            $records = Assignmentsupervisor::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Assignmentsupervisor();
            $record->reg_by = $_COOKIE['user'];
            
            $record->workassignment = $workassignment;
            $record->supervisor_user = $supervisor_user;
            $record->supervision_notes = $supervision_notes;
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
        
        $workassignment = $_POST["workassignment"];
        $supervisor_user = $_POST["supervisor_user"];
        $supervision_notes = $_POST["supervision_notes"];



        
        if ($workassignment == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_workassignment "; $_error = true; }
        if ($supervisor_user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_supervisor_user "; $_error = true; }
        if ($supervision_notes == "" && (!$_error)) {  $_result .= "<br>Error: supervision_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM assignmentsupervisor WHERE name=? AND iD!=?";
            $records = Assignmentsupervisor::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Assignmentsupervisor::where('iD', $recordiD)[0];
            
            $record->workassignment = $workassignment;
            $record->supervisor_user = $supervisor_user;
            $record->supervision_notes = $supervision_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}