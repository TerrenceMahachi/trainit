<?php
namespace App\Controllers;

use App\Models\Profilestatus;
use App\Models\Database;

class ProfilestatussController
{
    public function index()
    {
        $search = "";

        

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`code` LIKE $escapedTerm OR `name` LIKE $escapedTerm OR `badge_class` LIKE $escapedTerm OR `can_access_portal` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Profilestatus::page($selected_page, $page_size, $search, $order_by);
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
        
        $code = $_POST["code"];
        $name = $_POST["name"];
        $badge_class = $_POST["badge_class"];
        $can_access_portal = $_POST["can_access_portal"];
        
       
        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($badge_class == "" && (!$_error)) {  $_result .= "<br>Error: badge_class cannot be blank"; $_error = true; }
        if ($can_access_portal == "" && (!$_error)) {  $_result .= "<br>Error: can_access_portal cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM profilestatus WHERE name=?";
            $records = Profilestatus::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Profilestatus();
            $record->reg_by = $_COOKIE['user'];
            
            $record->code = $code;
            $record->name = $name;
            $record->badge_class = $badge_class;
            $record->can_access_portal = $can_access_portal;
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
        
        $code = $_POST["code"];
        $name = $_POST["name"];
        $badge_class = $_POST["badge_class"];
        $can_access_portal = $_POST["can_access_portal"];



        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($badge_class == "" && (!$_error)) {  $_result .= "<br>Error: badge_class cannot be blank"; $_error = true; }
        if ($can_access_portal == "" && (!$_error)) {  $_result .= "<br>Error: can_access_portal cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM profilestatus WHERE name=? AND iD!=?";
            $records = Profilestatus::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Profilestatus::where('iD', $recordiD)[0];
            
            $record->code = $code;
            $record->name = $name;
            $record->badge_class = $badge_class;
            $record->can_access_portal = $can_access_portal;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}