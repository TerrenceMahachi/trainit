<?php
namespace App\Controllers;

use App\Models\Userprofile;
use App\Models\Database;

class UserprofilesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['user']) && $_POST['user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' user=' . $_POST['user'];
}
if (isset($_POST['profiletype']) && $_POST['profiletype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' profiletype=' . $_POST['profiletype'];
}
if (isset($_POST['profilestatus']) && $_POST['profilestatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' profilestatus=' . $_POST['profilestatus'];
}
if (isset($_POST['reviewed_by']) && $_POST['reviewed_by'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' reviewed_by=' . $_POST['reviewed_by'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`display_title` LIKE $escapedTerm OR `is_default` LIKE $escapedTerm OR `request_notes` LIKE $escapedTerm OR `reviewer_notes` LIKE $escapedTerm OR `reviewed_at` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Userprofile::page($selected_page, $page_size, $search, $order_by);
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
        
        $user = $_POST["user"];
        $profiletype = $_POST["profiletype"];
        $profilestatus = $_POST["profilestatus"];
        $display_title = $_POST["display_title"];
        $is_default = $_POST["is_default"];
        $request_notes = $_POST["request_notes"];
        $reviewer_notes = $_POST["reviewer_notes"];
        $reviewed_by = $_POST["reviewed_by"];
        $reviewed_at = $_POST["reviewed_at"];
        
       
        
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($profiletype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_profiletype "; $_error = true; }
        if ($profilestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_profilestatus "; $_error = true; }
        if ($display_title == "" && (!$_error)) {  $_result .= "<br>Error: display_title cannot be blank"; $_error = true; }
        if ($is_default == "" && (!$_error)) {  $_result .= "<br>Error: is_default cannot be blank"; $_error = true; }
        if ($request_notes == "" && (!$_error)) {  $_result .= "<br>Error: request_notes cannot be blank"; $_error = true; }
        if ($reviewer_notes == "" && (!$_error)) {  $_result .= "<br>Error: reviewer_notes cannot be blank"; $_error = true; }
        if ($reviewed_by == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_reviewed_by "; $_error = true; }
        if ($reviewed_at == "" && (!$_error)) {  $_result .= "<br>Error: reviewed_at cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM userprofile WHERE name=?";
            $records = Userprofile::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Userprofile();
            $record->reg_by = $_COOKIE['user'];
            
            $record->user = $user;
            $record->profiletype = $profiletype;
            $record->profilestatus = $profilestatus;
            $record->display_title = $display_title;
            $record->is_default = $is_default;
            $record->request_notes = $request_notes;
            $record->reviewer_notes = $reviewer_notes;
            $record->reviewed_by = $reviewed_by;
            $record->reviewed_at = $reviewed_at;
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
        
        $user = $_POST["user"];
        $profiletype = $_POST["profiletype"];
        $profilestatus = $_POST["profilestatus"];
        $display_title = $_POST["display_title"];
        $is_default = $_POST["is_default"];
        $request_notes = $_POST["request_notes"];
        $reviewer_notes = $_POST["reviewer_notes"];
        $reviewed_by = $_POST["reviewed_by"];
        $reviewed_at = $_POST["reviewed_at"];



        
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($profiletype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_profiletype "; $_error = true; }
        if ($profilestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_profilestatus "; $_error = true; }
        if ($display_title == "" && (!$_error)) {  $_result .= "<br>Error: display_title cannot be blank"; $_error = true; }
        if ($is_default == "" && (!$_error)) {  $_result .= "<br>Error: is_default cannot be blank"; $_error = true; }
        if ($request_notes == "" && (!$_error)) {  $_result .= "<br>Error: request_notes cannot be blank"; $_error = true; }
        if ($reviewer_notes == "" && (!$_error)) {  $_result .= "<br>Error: reviewer_notes cannot be blank"; $_error = true; }
        if ($reviewed_by == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_reviewed_by "; $_error = true; }
        if ($reviewed_at == "" && (!$_error)) {  $_result .= "<br>Error: reviewed_at cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM userprofile WHERE name=? AND iD!=?";
            $records = Userprofile::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Userprofile::where('iD', $recordiD)[0];
            
            $record->user = $user;
            $record->profiletype = $profiletype;
            $record->profilestatus = $profilestatus;
            $record->display_title = $display_title;
            $record->is_default = $is_default;
            $record->request_notes = $request_notes;
            $record->reviewer_notes = $reviewer_notes;
            $record->reviewed_by = $reviewed_by;
            $record->reviewed_at = $reviewed_at;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}