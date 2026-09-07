<?php
namespace App\Controllers;

use App\Models\UserProfile;
use App\Models\Database;

class UserProfilesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['user']) && $_POST['user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' user=' . $_POST['user'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`name` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = UserProfile::page($selected_page, $page_size, $search, $order_by);
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
        
        $name = $_POST["name"];
        $user = $_POST["user"];
        
       
        
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM userprofile WHERE name=?";
            $records = UserProfile::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new UserProfile();
            $record->reg_by = $_COOKIE['user'];
            
            $record->name = $name;
            $record->user = $user;
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
        
        $name = $_POST["name"];
        $user = $_POST["user"];



        
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM userprofile WHERE name=? AND iD!=?";
            $records = UserProfile::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = UserProfile::where('iD', $recordiD)[0];
            
            $record->name = $name;
            $record->user = $user;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}