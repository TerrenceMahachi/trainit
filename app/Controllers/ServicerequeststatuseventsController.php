<?php
namespace App\Controllers;

use App\Models\Servicerequeststatusevent;
use App\Models\Database;

class ServicerequeststatuseventsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['servicerequest']) && $_POST['servicerequest'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicerequest=' . $_POST['servicerequest'];
}
if (isset($_POST['servicerequeststatus']) && $_POST['servicerequeststatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicerequeststatus=' . $_POST['servicerequeststatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequeststatusevent::page($selected_page, $page_size, $search, $order_by);
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
        $servicerequeststatus = $_POST["servicerequeststatus"];
        $notes = $_POST["notes"];
        
       
        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($servicerequeststatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequeststatus "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequeststatusevent WHERE name=?";
            $records = Servicerequeststatusevent::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequeststatusevent();
            $record->reg_by = $_COOKIE['user'];
            
            $record->servicerequest = $servicerequest;
            $record->servicerequeststatus = $servicerequeststatus;
            $record->notes = $notes;
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
        $servicerequeststatus = $_POST["servicerequeststatus"];
        $notes = $_POST["notes"];



        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($servicerequeststatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequeststatus "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequeststatusevent WHERE name=? AND iD!=?";
            $records = Servicerequeststatusevent::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequeststatusevent::where('iD', $recordiD)[0];
            
            $record->servicerequest = $servicerequest;
            $record->servicerequeststatus = $servicerequeststatus;
            $record->notes = $notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}