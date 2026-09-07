<?php
namespace App\Controllers;

use App\Models\Rosterqualification;
use App\Models\Database;

class RosterqualificationsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['qualificationtype']) && $_POST['qualificationtype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' qualificationtype=' . $_POST['qualificationtype'];
}
if (isset($_POST['professionalbody']) && $_POST['professionalbody'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' professionalbody=' . $_POST['professionalbody'];
}
if (isset($_POST['qualificationstatus']) && $_POST['qualificationstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' qualificationstatus=' . $_POST['qualificationstatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`title` LIKE $escapedTerm OR `institution_name` LIKE $escapedTerm OR `field_of_study` LIKE $escapedTerm OR `date_obtained` LIKE $escapedTerm OR `expiry_date` LIKE $escapedTerm OR `certificate_doc` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterqualification::page($selected_page, $page_size, $search, $order_by);
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
        $qualificationtype = $_POST["qualificationtype"];
        $professionalbody = $_POST["professionalbody"];
        $qualificationstatus = $_POST["qualificationstatus"];
        $title = $_POST["title"];
        $institution_name = $_POST["institution_name"];
        $field_of_study = $_POST["field_of_study"];
        $date_obtained = $_POST["date_obtained"];
        $expiry_date = $_POST["expiry_date"];
        $certificate_doc = $_POST["certificate_doc"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($qualificationtype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_qualificationtype "; $_error = true; }
        if ($professionalbody == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_professionalbody "; $_error = true; }
        if ($qualificationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_qualificationstatus "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($institution_name == "" && (!$_error)) {  $_result .= "<br>Error: institution_name cannot be blank"; $_error = true; }
        if ($field_of_study == "" && (!$_error)) {  $_result .= "<br>Error: field_of_study cannot be blank"; $_error = true; }
        if ($date_obtained == "" && (!$_error)) {  $_result .= "<br>Error: date_obtained cannot be blank"; $_error = true; }
        if ($expiry_date == "" && (!$_error)) {  $_result .= "<br>Error: expiry_date cannot be blank"; $_error = true; }
        if ($certificate_doc == "" && (!$_error)) {  $_result .= "<br>Error: certificate_doc cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterqualification WHERE name=?";
            $records = Rosterqualification::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterqualification();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->qualificationtype = $qualificationtype;
            $record->professionalbody = $professionalbody;
            $record->qualificationstatus = $qualificationstatus;
            $record->title = $title;
            $record->institution_name = $institution_name;
            $record->field_of_study = $field_of_study;
            $record->date_obtained = $date_obtained;
            $record->expiry_date = $expiry_date;
            $record->certificate_doc = $certificate_doc;
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
        $qualificationtype = $_POST["qualificationtype"];
        $professionalbody = $_POST["professionalbody"];
        $qualificationstatus = $_POST["qualificationstatus"];
        $title = $_POST["title"];
        $institution_name = $_POST["institution_name"];
        $field_of_study = $_POST["field_of_study"];
        $date_obtained = $_POST["date_obtained"];
        $expiry_date = $_POST["expiry_date"];
        $certificate_doc = $_POST["certificate_doc"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($qualificationtype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_qualificationtype "; $_error = true; }
        if ($professionalbody == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_professionalbody "; $_error = true; }
        if ($qualificationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_qualificationstatus "; $_error = true; }
        if ($title == "" && (!$_error)) {  $_result .= "<br>Error: title cannot be blank"; $_error = true; }
        if ($institution_name == "" && (!$_error)) {  $_result .= "<br>Error: institution_name cannot be blank"; $_error = true; }
        if ($field_of_study == "" && (!$_error)) {  $_result .= "<br>Error: field_of_study cannot be blank"; $_error = true; }
        if ($date_obtained == "" && (!$_error)) {  $_result .= "<br>Error: date_obtained cannot be blank"; $_error = true; }
        if ($expiry_date == "" && (!$_error)) {  $_result .= "<br>Error: expiry_date cannot be blank"; $_error = true; }
        if ($certificate_doc == "" && (!$_error)) {  $_result .= "<br>Error: certificate_doc cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterqualification WHERE name=? AND iD!=?";
            $records = Rosterqualification::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterqualification::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->qualificationtype = $qualificationtype;
            $record->professionalbody = $professionalbody;
            $record->qualificationstatus = $qualificationstatus;
            $record->title = $title;
            $record->institution_name = $institution_name;
            $record->field_of_study = $field_of_study;
            $record->date_obtained = $date_obtained;
            $record->expiry_date = $expiry_date;
            $record->certificate_doc = $certificate_doc;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}