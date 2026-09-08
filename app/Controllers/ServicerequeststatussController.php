<?php
namespace App\Controllers;

use App\Models\Servicerequeststatus;
use App\Models\Database;

class ServicerequeststatussController
{
    public function index()
    {
        $search = "";

        

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`code` LIKE $escapedTerm OR `name` LIKE $escapedTerm OR `description` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Servicerequeststatus::page($selected_page, $page_size, $search, $order_by);
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
        
       
        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM servicerequeststatus WHERE name=?";
            $records = Servicerequeststatus::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Servicerequeststatus();
            $record->reg_by = $_COOKIE['user'];
            
            $record->code = $code;
            $record->name = $name;
            $record->description = $description;
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



        
        if ($code == "" && (!$_error)) {  $_result .= "<br>Error: code cannot be blank"; $_error = true; }
        if ($name == "" && (!$_error)) {  $_result .= "<br>Error: name cannot be blank"; $_error = true; }
        if ($description == "" && (!$_error)) {  $_result .= "<br>Error: description cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM servicerequeststatus WHERE name=? AND iD!=?";
            $records = Servicerequeststatus::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Servicerequeststatus::where('iD', $recordiD)[0];
            
            $record->code = $code;
            $record->name = $name;
            $record->description = $description;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}