<?php
namespace App\Controllers;

use App\Models\Documentvalidity;
use App\Models\Database;

class DocumentvaliditysController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffdocument']) && $_POST['staffdocument'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffdocument=' . $_POST['staffdocument'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`issue_date` LIKE $escapedTerm OR `expiry_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Documentvalidity::page($selected_page, $page_size, $search, $order_by);
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
        
        $staffdocument = $_POST["staffdocument"];
        $issue_date = $_POST["issue_date"];
        $expiry_date = $_POST["expiry_date"];
        
       
        
        if ($staffdocument == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffdocument "; $_error = true; }
        if ($issue_date == "" && (!$_error)) {  $_result .= "<br>Error: issue_date cannot be blank"; $_error = true; }
        if ($expiry_date == "" && (!$_error)) {  $_result .= "<br>Error: expiry_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM documentvalidity WHERE name=?";
            $records = Documentvalidity::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Documentvalidity();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffdocument = $staffdocument;
            $record->issue_date = $issue_date;
            $record->expiry_date = $expiry_date;
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
        
        $staffdocument = $_POST["staffdocument"];
        $issue_date = $_POST["issue_date"];
        $expiry_date = $_POST["expiry_date"];



        
        if ($staffdocument == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffdocument "; $_error = true; }
        if ($issue_date == "" && (!$_error)) {  $_result .= "<br>Error: issue_date cannot be blank"; $_error = true; }
        if ($expiry_date == "" && (!$_error)) {  $_result .= "<br>Error: expiry_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM documentvalidity WHERE name=? AND iD!=?";
            $records = Documentvalidity::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Documentvalidity::where('iD', $recordiD)[0];
            
            $record->staffdocument = $staffdocument;
            $record->issue_date = $issue_date;
            $record->expiry_date = $expiry_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}