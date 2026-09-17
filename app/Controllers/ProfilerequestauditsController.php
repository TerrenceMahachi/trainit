<?php
namespace App\Controllers;

use App\Models\Profilerequestaudit;
use App\Models\Database;

class ProfilerequestauditsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['userprofile']) && $_POST['userprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' userprofile=' . $_POST['userprofile'];
}
if (isset($_POST['from_status']) && $_POST['from_status'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' from_status=' . $_POST['from_status'];
}
if (isset($_POST['to_status']) && $_POST['to_status'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' to_status=' . $_POST['to_status'];
}
if (isset($_POST['performed_by']) && $_POST['performed_by'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' performed_by=' . $_POST['performed_by'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`action` LIKE $escapedTerm OR `notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Profilerequestaudit::page($selected_page, $page_size, $search, $order_by);
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
        
        $userprofile = $_POST["userprofile"];
        $action = $_POST["action"];
        $from_status = $_POST["from_status"];
        $to_status = $_POST["to_status"];
        $performed_by = $_POST["performed_by"];
        $notes = $_POST["notes"];
        
       
        
        if ($userprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_userprofile "; $_error = true; }
        if ($action == "" && (!$_error)) {  $_result .= "<br>Error: action cannot be blank"; $_error = true; }
        if ($from_status == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_from_status "; $_error = true; }
        if ($to_status == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_to_status "; $_error = true; }
        if ($performed_by == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_performed_by "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM profilerequestaudit WHERE name=?";
            $records = Profilerequestaudit::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Profilerequestaudit();
            $record->reg_by = $_COOKIE['user'];
            
            $record->userprofile = $userprofile;
            $record->action = $action;
            $record->from_status = $from_status;
            $record->to_status = $to_status;
            $record->performed_by = $performed_by;
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
        
        $userprofile = $_POST["userprofile"];
        $action = $_POST["action"];
        $from_status = $_POST["from_status"];
        $to_status = $_POST["to_status"];
        $performed_by = $_POST["performed_by"];
        $notes = $_POST["notes"];



        
        if ($userprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_userprofile "; $_error = true; }
        if ($action == "" && (!$_error)) {  $_result .= "<br>Error: action cannot be blank"; $_error = true; }
        if ($from_status == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_from_status "; $_error = true; }
        if ($to_status == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_to_status "; $_error = true; }
        if ($performed_by == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_performed_by "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM profilerequestaudit WHERE name=? AND iD!=?";
            $records = Profilerequestaudit::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Profilerequestaudit::where('iD', $recordiD)[0];
            
            $record->userprofile = $userprofile;
            $record->action = $action;
            $record->from_status = $from_status;
            $record->to_status = $to_status;
            $record->performed_by = $performed_by;
            $record->notes = $notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}