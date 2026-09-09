<?php
namespace App\Controllers;

use App\Models\Rosterstatusevent;
use App\Models\Database;

class RosterstatuseventsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['applicationstatus']) && $_POST['applicationstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' applicationstatus=' . $_POST['applicationstatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`remarks` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterstatusevent::page($selected_page, $page_size, $search, $order_by);
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
        $applicationstatus = $_POST["applicationstatus"];
        $remarks = $_POST["remarks"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($applicationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationstatus "; $_error = true; }
        if ($remarks == "" && (!$_error)) {  $_result .= "<br>Error: remarks cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterstatusevent WHERE name=?";
            $records = Rosterstatusevent::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterstatusevent();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->applicationstatus = $applicationstatus;
            $record->remarks = $remarks;
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
        $applicationstatus = $_POST["applicationstatus"];
        $remarks = $_POST["remarks"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($applicationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationstatus "; $_error = true; }
        if ($remarks == "" && (!$_error)) {  $_result .= "<br>Error: remarks cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterstatusevent WHERE name=? AND iD!=?";
            $records = Rosterstatusevent::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterstatusevent::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->applicationstatus = $applicationstatus;
            $record->remarks = $remarks;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}