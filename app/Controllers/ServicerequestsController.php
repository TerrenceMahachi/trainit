<?php
namespace App\Controllers;

use App\Models\Servicerequest;
use App\Models\Database;

class ServicerequestsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['clientorganization']) && $_POST['clientorganization'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientorganization=' . $_POST['clientorganization'];
}
if (isset($_POST['clientserviceplan']) && $_POST['clientserviceplan'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientserviceplan=' . $_POST['clientserviceplan'];
}
if (isset($_POST['requester']) && $_POST['requester'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' requester=' . $_POST['requester'];
}
if (isset($_POST['prioritylevel']) && $_POST['prioritylevel'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' prioritylevel=' . $_POST['prioritylevel'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`request_number` LIKE $escapedTerm OR `title` LIKE $escapedTerm OR `description` LIKE $escapedTerm OR `desired_due_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequest::page($selected_page, $page_size, $search, $order_by);
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
        
        $request_number = $_POST["request_number"];
        $clientorganization = $_POST["clientorganization"];
        $clientserviceplan = $_POST["clientserviceplan"];
        $requester = $_POST["requester"];
        $prioritylevel = $_POST["prioritylevel"];
        $title = $_POST["title"];
        $description = $_POST["description"];
        $desired_due_date = $_POST["desired_due_date"];
        
       
        
        if ($request_number == "" && (!$_error)) {  $_result .= "<br>Error: request_number cannot be blank"; $_error = true; }
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($clientserviceplan == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientserviceplan "; $_error = true; }
        if ($requester == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_requester "; $_error = true; }
        if ($prioritylevel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_prioritylevel "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }
        if ($desired_due_date == "" && (!$_error)) {  $_result .= "<br>Error: desired_due_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequest WHERE name=?";
            $records = Servicerequest::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequest();
            $record->reg_by = $_COOKIE['user'];
            
            $record->request_number = $request_number;
            $record->clientorganization = $clientorganization;
            $record->clientserviceplan = $clientserviceplan;
            $record->requester = $requester;
            $record->prioritylevel = $prioritylevel;
            $record->title = $title;
            $record->description = $description;
            $record->desired_due_date = $desired_due_date;
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
        
        $request_number = $_POST["request_number"];
        $clientorganization = $_POST["clientorganization"];
        $clientserviceplan = $_POST["clientserviceplan"];
        $requester = $_POST["requester"];
        $prioritylevel = $_POST["prioritylevel"];
        $title = $_POST["title"];
        $description = $_POST["description"];
        $desired_due_date = $_POST["desired_due_date"];



        
        if ($request_number == "" && (!$_error)) {  $_result .= "<br>Error: request_number cannot be blank"; $_error = true; }
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($clientserviceplan == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientserviceplan "; $_error = true; }
        if ($requester == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_requester "; $_error = true; }
        if ($prioritylevel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_prioritylevel "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }
        if ($desired_due_date == "" && (!$_error)) {  $_result .= "<br>Error: desired_due_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequest WHERE name=? AND iD!=?";
            $records = Servicerequest::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequest::where('iD', $recordiD)[0];
            
            $record->request_number = $request_number;
            $record->clientorganization = $clientorganization;
            $record->clientserviceplan = $clientserviceplan;
            $record->requester = $requester;
            $record->prioritylevel = $prioritylevel;
            $record->title = $title;
            $record->description = $description;
            $record->desired_due_date = $desired_due_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}