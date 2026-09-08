<?php
namespace App\Controllers;

use App\Models\Clientserviceplantermination;
use App\Models\Database;

class ClientserviceplanterminationsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['clientserviceplan']) && $_POST['clientserviceplan'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientserviceplan=' . $_POST['clientserviceplan'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`end_date` LIKE $escapedTerm OR `reason` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Clientserviceplantermination::page($selected_page, $page_size, $search, $order_by);
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
        
        $clientserviceplan = $_POST["clientserviceplan"];
        $end_date = $_POST["end_date"];
        $reason = $_POST["reason"];
        
       
        
        if ($clientserviceplan == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientserviceplan "; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($reason == "" && (!$_error)) {  $_result .= "<br>Error: reason cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM clientserviceplantermination WHERE name=?";
            $records = Clientserviceplantermination::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Clientserviceplantermination();
            $record->reg_by = $_COOKIE['user'];
            
            $record->clientserviceplan = $clientserviceplan;
            $record->end_date = $end_date;
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
        
        $clientserviceplan = $_POST["clientserviceplan"];
        $end_date = $_POST["end_date"];
        $reason = $_POST["reason"];



        
        if ($clientserviceplan == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientserviceplan "; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($reason == "" && (!$_error)) {  $_result .= "<br>Error: reason cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM clientserviceplantermination WHERE name=? AND iD!=?";
            $records = Clientserviceplantermination::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Clientserviceplantermination::where('iD', $recordiD)[0];
            
            $record->clientserviceplan = $clientserviceplan;
            $record->end_date = $end_date;
            $record->reason = $reason;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}