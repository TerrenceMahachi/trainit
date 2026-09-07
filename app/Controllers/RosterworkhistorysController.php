<?php
namespace App\Controllers;

use App\Models\Rosterworkhistory;
use App\Models\Database;

class RosterworkhistorysController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['sectortype']) && $_POST['sectortype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' sectortype=' . $_POST['sectortype'];
}
if (isset($_POST['engagementbasis']) && $_POST['engagementbasis'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' engagementbasis=' . $_POST['engagementbasis'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`organization_name` LIKE $escapedTerm OR `position_title` LIKE $escapedTerm OR `start_date` LIKE $escapedTerm OR `end_date` LIKE $escapedTerm OR `is_current` LIKE $escapedTerm OR `key_deliverables` LIKE $escapedTerm OR `reason_for_leaving` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterworkhistory::page($selected_page, $page_size, $search, $order_by);
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
        $sectortype = $_POST["sectortype"];
        $engagementbasis = $_POST["engagementbasis"];
        $organization_name = $_POST["organization_name"];
        $position_title = $_POST["position_title"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $is_current = $_POST["is_current"];
        $key_deliverables = $_POST["key_deliverables"];
        $reason_for_leaving = $_POST["reason_for_leaving"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($sectortype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_sectortype "; $_error = true; }
        if ($engagementbasis == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_engagementbasis "; $_error = true; }
        if ($organization_name == "" && (!$_error)) {  $_result .= "<br>Error: organization_name cannot be blank"; $_error = true; }
        if ($position_title == "" && (!$_error)) {  $_result .= "<br>Error: position_title cannot be blank"; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($is_current == "" && (!$_error)) {  $_result .= "<br>Error: is_current cannot be blank"; $_error = true; }
        if ($key_deliverables == "" && (!$_error)) {  $_result .= "<br>Error: key_deliverables cannot be blank"; $_error = true; }
        if ($reason_for_leaving == "" && (!$_error)) {  $_result .= "<br>Error: reason_for_leaving cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterworkhistory WHERE name=?";
            $records = Rosterworkhistory::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterworkhistory();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->sectortype = $sectortype;
            $record->engagementbasis = $engagementbasis;
            $record->organization_name = $organization_name;
            $record->position_title = $position_title;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->is_current = $is_current;
            $record->key_deliverables = $key_deliverables;
            $record->reason_for_leaving = $reason_for_leaving;
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
        $sectortype = $_POST["sectortype"];
        $engagementbasis = $_POST["engagementbasis"];
        $organization_name = $_POST["organization_name"];
        $position_title = $_POST["position_title"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $is_current = $_POST["is_current"];
        $key_deliverables = $_POST["key_deliverables"];
        $reason_for_leaving = $_POST["reason_for_leaving"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($sectortype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_sectortype "; $_error = true; }
        if ($engagementbasis == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_engagementbasis "; $_error = true; }
        if ($organization_name == "" && (!$_error)) {  $_result .= "<br>Error: organization_name cannot be blank"; $_error = true; }
        if ($position_title == "" && (!$_error)) {  $_result .= "<br>Error: position_title cannot be blank"; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($is_current == "" && (!$_error)) {  $_result .= "<br>Error: is_current cannot be blank"; $_error = true; }
        if ($key_deliverables == "" && (!$_error)) {  $_result .= "<br>Error: key_deliverables cannot be blank"; $_error = true; }
        if ($reason_for_leaving == "" && (!$_error)) {  $_result .= "<br>Error: reason_for_leaving cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterworkhistory WHERE name=? AND iD!=?";
            $records = Rosterworkhistory::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterworkhistory::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->sectortype = $sectortype;
            $record->engagementbasis = $engagementbasis;
            $record->organization_name = $organization_name;
            $record->position_title = $position_title;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->is_current = $is_current;
            $record->key_deliverables = $key_deliverables;
            $record->reason_for_leaving = $reason_for_leaving;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}