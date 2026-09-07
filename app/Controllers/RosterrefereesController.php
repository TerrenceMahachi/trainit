<?php
namespace App\Controllers;

use App\Models\Rosterreferee;
use App\Models\Database;

class RosterrefereesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['refereecontacttiming']) && $_POST['refereecontacttiming'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' refereecontacttiming=' . $_POST['refereecontacttiming'];
}
if (isset($_POST['refereeverificationstatus']) && $_POST['refereeverificationstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' refereeverificationstatus=' . $_POST['refereeverificationstatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`referee_name` LIKE $escapedTerm OR `organization` LIKE $escapedTerm OR `position` LIKE $escapedTerm OR `relationship` LIKE $escapedTerm OR `email` LIKE $escapedTerm OR `phone` LIKE $escapedTerm OR `verification_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterreferee::page($selected_page, $page_size, $search, $order_by);
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
        $refereecontacttiming = $_POST["refereecontacttiming"];
        $refereeverificationstatus = $_POST["refereeverificationstatus"];
        $referee_name = $_POST["referee_name"];
        $organization = $_POST["organization"];
        $position = $_POST["position"];
        $relationship = $_POST["relationship"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $verification_notes = $_POST["verification_notes"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($refereecontacttiming == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_refereecontacttiming "; $_error = true; }
        if ($refereeverificationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_refereeverificationstatus "; $_error = true; }
        if ($referee_name == "" && (!$_error)) {  $_result .= "<br>Error: referee_name cannot be blank"; $_error = true; }
        if ($organization == "" && (!$_error)) {  $_result .= "<br>Error: organization cannot be blank"; $_error = true; }
        if ($position == "" && (!$_error)) {  $_result .= "<br>Error: position cannot be blank"; $_error = true; }
        if ($relationship == "" && (!$_error)) {  $_result .= "<br>Error: relationship cannot be blank"; $_error = true; }
        if ($email == "" && (!$_error)) {  $_result .= "<br>Error: email cannot be blank"; $_error = true; }
        if ($phone == "" && (!$_error)) {  $_result .= "<br>Error: phone cannot be blank"; $_error = true; }
        if ($verification_notes == "" && (!$_error)) {  $_result .= "<br>Error: verification_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterreferee WHERE name=?";
            $records = Rosterreferee::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterreferee();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->refereecontacttiming = $refereecontacttiming;
            $record->refereeverificationstatus = $refereeverificationstatus;
            $record->referee_name = $referee_name;
            $record->organization = $organization;
            $record->position = $position;
            $record->relationship = $relationship;
            $record->email = $email;
            $record->phone = $phone;
            $record->verification_notes = $verification_notes;
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
        $refereecontacttiming = $_POST["refereecontacttiming"];
        $refereeverificationstatus = $_POST["refereeverificationstatus"];
        $referee_name = $_POST["referee_name"];
        $organization = $_POST["organization"];
        $position = $_POST["position"];
        $relationship = $_POST["relationship"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $verification_notes = $_POST["verification_notes"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($refereecontacttiming == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_refereecontacttiming "; $_error = true; }
        if ($refereeverificationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_refereeverificationstatus "; $_error = true; }
        if ($referee_name == "" && (!$_error)) {  $_result .= "<br>Error: referee_name cannot be blank"; $_error = true; }
        if ($organization == "" && (!$_error)) {  $_result .= "<br>Error: organization cannot be blank"; $_error = true; }
        if ($position == "" && (!$_error)) {  $_result .= "<br>Error: position cannot be blank"; $_error = true; }
        if ($relationship == "" && (!$_error)) {  $_result .= "<br>Error: relationship cannot be blank"; $_error = true; }
        if ($email == "" && (!$_error)) {  $_result .= "<br>Error: email cannot be blank"; $_error = true; }
        if ($phone == "" && (!$_error)) {  $_result .= "<br>Error: phone cannot be blank"; $_error = true; }
        if ($verification_notes == "" && (!$_error)) {  $_result .= "<br>Error: verification_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterreferee WHERE name=? AND iD!=?";
            $records = Rosterreferee::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterreferee::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->refereecontacttiming = $refereecontacttiming;
            $record->refereeverificationstatus = $refereeverificationstatus;
            $record->referee_name = $referee_name;
            $record->organization = $organization;
            $record->position = $position;
            $record->relationship = $relationship;
            $record->email = $email;
            $record->phone = $phone;
            $record->verification_notes = $verification_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}