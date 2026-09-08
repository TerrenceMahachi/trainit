<?php
namespace App\Controllers;

use App\Models\Servicerequesttriage;
use App\Models\Database;

class ServicerequesttriagesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['servicerequest']) && $_POST['servicerequest'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicerequest=' . $_POST['servicerequest'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`sla_due_date` LIKE $escapedTerm OR `triage_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequesttriage::page($selected_page, $page_size, $search, $order_by);
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
        $sla_due_date = $_POST["sla_due_date"];
        $triage_notes = $_POST["triage_notes"];
        
       
        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($sla_due_date == "" && (!$_error)) {  $_result .= "<br>Error: sla_due_date cannot be blank"; $_error = true; }
        if ($triage_notes == "" && (!$_error)) {  $_result .= "<br>Error: triage_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequesttriage WHERE name=?";
            $records = Servicerequesttriage::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequesttriage();
            $record->reg_by = $_COOKIE['user'];
            
            $record->servicerequest = $servicerequest;
            $record->sla_due_date = $sla_due_date;
            $record->triage_notes = $triage_notes;
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
        $sla_due_date = $_POST["sla_due_date"];
        $triage_notes = $_POST["triage_notes"];



        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($sla_due_date == "" && (!$_error)) {  $_result .= "<br>Error: sla_due_date cannot be blank"; $_error = true; }
        if ($triage_notes == "" && (!$_error)) {  $_result .= "<br>Error: triage_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequesttriage WHERE name=? AND iD!=?";
            $records = Servicerequesttriage::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequesttriage::where('iD', $recordiD)[0];
            
            $record->servicerequest = $servicerequest;
            $record->sla_due_date = $sla_due_date;
            $record->triage_notes = $triage_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}