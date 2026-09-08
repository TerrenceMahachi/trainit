<?php
namespace App\Controllers;

use App\Models\Servicerequestclosure;
use App\Models\Database;

class ServicerequestclosuresController
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
            $search .= "(`closure_notes` LIKE $escapedTerm OR `satisfaction_rating` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequestclosure::page($selected_page, $page_size, $search, $order_by);
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
        $closure_notes = $_POST["closure_notes"];
        $satisfaction_rating = $_POST["satisfaction_rating"];
        
       
        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($closure_notes == "" && (!$_error)) {  $_result .= "<br>Error: closure_notes cannot be blank"; $_error = true; }
        if ($satisfaction_rating == "" && (!$_error)) {  $_result .= "<br>Error: satisfaction_rating cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequestclosure WHERE name=?";
            $records = Servicerequestclosure::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequestclosure();
            $record->reg_by = $_COOKIE['user'];
            
            $record->servicerequest = $servicerequest;
            $record->closure_notes = $closure_notes;
            $record->satisfaction_rating = $satisfaction_rating;
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
        $closure_notes = $_POST["closure_notes"];
        $satisfaction_rating = $_POST["satisfaction_rating"];



        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($closure_notes == "" && (!$_error)) {  $_result .= "<br>Error: closure_notes cannot be blank"; $_error = true; }
        if ($satisfaction_rating == "" && (!$_error)) {  $_result .= "<br>Error: satisfaction_rating cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequestclosure WHERE name=? AND iD!=?";
            $records = Servicerequestclosure::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequestclosure::where('iD', $recordiD)[0];
            
            $record->servicerequest = $servicerequest;
            $record->closure_notes = $closure_notes;
            $record->satisfaction_rating = $satisfaction_rating;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}