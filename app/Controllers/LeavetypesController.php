<?php
namespace App\Controllers;

use App\Models\Leavetype;
use App\Models\Database;

class LeavetypesController
{
    public function index()
    {
        $search = "";

        

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`code` LIKE $escapedTerm OR `name` LIKE $escapedTerm OR `description` LIKE $escapedTerm OR `is_paid` LIKE $escapedTerm OR `annual_days` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Leavetype::page($selected_page, $page_size, $search, $order_by);
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
        $description = $_POST["description"];
        $is_paid = $_POST["is_paid"];
        $annual_days = $_POST["annual_days"];
        
       
        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }
        if ($is_paid == "" && (!$_error)) {  $_result .= "<br>Error: is_paid cannot be blank"; $_error = true; }
        if ($annual_days == "" && (!$_error)) {  $_result .= "<br>Error: annual_days cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM leavetype WHERE name=?";
            $records = Leavetype::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Leavetype();
            $record->reg_by = $_COOKIE['user'];
            
            $record->code = $code;
            $record->name = $name;
            $record->description = $description;
            $record->is_paid = $is_paid;
            $record->annual_days = $annual_days;
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
        $description = $_POST["description"];
        $is_paid = $_POST["is_paid"];
        $annual_days = $_POST["annual_days"];



        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }
        if ($is_paid == "" && (!$_error)) {  $_result .= "<br>Error: is_paid cannot be blank"; $_error = true; }
        if ($annual_days == "" && (!$_error)) {  $_result .= "<br>Error: annual_days cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM leavetype WHERE name=? AND iD!=?";
            $records = Leavetype::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Leavetype::where('iD', $recordiD)[0];
            
            $record->code = $code;
            $record->name = $name;
            $record->description = $description;
            $record->is_paid = $is_paid;
            $record->annual_days = $annual_days;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}