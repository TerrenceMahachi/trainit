<?php
namespace App\Controllers;

use App\Models\Workassignment;
use App\Models\Database;

class WorkassignmentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['servicerequest']) && $_POST['servicerequest'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicerequest=' . $_POST['servicerequest'];
}
if (isset($_POST['user']) && $_POST['user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' user=' . $_POST['user'];
}
if (isset($_POST['assigned_role']) && $_POST['assigned_role'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' assigned_role=' . $_POST['assigned_role'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`rate_currency` LIKE $escapedTerm OR `hourly_rate_snapshot` LIKE $escapedTerm OR `due_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Workassignment::page($selected_page, $page_size, $search, $order_by);
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
        $user = $_POST["user"];
        $assigned_role = $_POST["assigned_role"];
        $rate_currency = $_POST["rate_currency"];
        $hourly_rate_snapshot = $_POST["hourly_rate_snapshot"];
        $due_date = $_POST["due_date"];
        
       
        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($assigned_role == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_assigned_role "; $_error = true; }
        if ($rate_currency == "" && (!$_error)) {  $_result .= "<br>Error: rate_currency cannot be blank"; $_error = true; }
        if ($hourly_rate_snapshot == "" && (!$_error)) {  $_result .= "<br>Error: hourly_rate_snapshot cannot be blank"; $_error = true; }
        if ($due_date == "" && (!$_error)) {  $_result .= "<br>Error: due_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM workassignment WHERE name=?";
            $records = Workassignment::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Workassignment();
            $record->reg_by = $_COOKIE['user'];
            
            $record->servicerequest = $servicerequest;
            $record->user = $user;
            $record->assigned_role = $assigned_role;
            $record->rate_currency = $rate_currency;
            $record->hourly_rate_snapshot = $hourly_rate_snapshot;
            $record->due_date = $due_date;
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
        $user = $_POST["user"];
        $assigned_role = $_POST["assigned_role"];
        $rate_currency = $_POST["rate_currency"];
        $hourly_rate_snapshot = $_POST["hourly_rate_snapshot"];
        $due_date = $_POST["due_date"];



        
        if ($servicerequest == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicerequest "; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($assigned_role == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_assigned_role "; $_error = true; }
        if ($rate_currency == "" && (!$_error)) {  $_result .= "<br>Error: rate_currency cannot be blank"; $_error = true; }
        if ($hourly_rate_snapshot == "" && (!$_error)) {  $_result .= "<br>Error: hourly_rate_snapshot cannot be blank"; $_error = true; }
        if ($due_date == "" && (!$_error)) {  $_result .= "<br>Error: due_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM workassignment WHERE name=? AND iD!=?";
            $records = Workassignment::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Workassignment::where('iD', $recordiD)[0];
            
            $record->servicerequest = $servicerequest;
            $record->user = $user;
            $record->assigned_role = $assigned_role;
            $record->rate_currency = $rate_currency;
            $record->hourly_rate_snapshot = $hourly_rate_snapshot;
            $record->due_date = $due_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}