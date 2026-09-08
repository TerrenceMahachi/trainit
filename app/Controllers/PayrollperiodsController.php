<?php
namespace App\Controllers;

use App\Models\Payrollperiod;
use App\Models\Database;

class PayrollperiodsController
{
    public function index()
    {
        $search = "";

        

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`period_code` LIKE $escapedTerm OR `period_name` LIKE $escapedTerm OR `start_date` LIKE $escapedTerm OR `end_date` LIKE $escapedTerm OR `pay_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Payrollperiod::page($selected_page, $page_size, $search, $order_by);
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
        
        $period_code = $_POST["period_code"];
        $period_name = $_POST["period_name"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $pay_date = $_POST["pay_date"];
        
       
        
        if ($period_code == "" && (!$_error)) {  $_result .= "<br>Error: period_code cannot be blank"; $_error = true; }
        if ($period_name == "" && (!$_error)) {  $_result .= "<br>Error: period_name cannot be blank"; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($pay_date == "" && (!$_error)) {  $_result .= "<br>Error: pay_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM payrollperiod WHERE name=?";
            $records = Payrollperiod::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Payrollperiod();
            $record->reg_by = $_COOKIE['user'];
            
            $record->period_code = $period_code;
            $record->period_name = $period_name;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->pay_date = $pay_date;
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
        
        $period_code = $_POST["period_code"];
        $period_name = $_POST["period_name"];
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $pay_date = $_POST["pay_date"];



        
        if ($period_code == "" && (!$_error)) {  $_result .= "<br>Error: period_code cannot be blank"; $_error = true; }
        if ($period_name == "" && (!$_error)) {  $_result .= "<br>Error: period_name cannot be blank"; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if ($end_date == "" && (!$_error)) {  $_result .= "<br>Error: end_date cannot be blank"; $_error = true; }
        if ($pay_date == "" && (!$_error)) {  $_result .= "<br>Error: pay_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM payrollperiod WHERE name=? AND iD!=?";
            $records = Payrollperiod::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Payrollperiod::where('iD', $recordiD)[0];
            
            $record->period_code = $period_code;
            $record->period_name = $period_name;
            $record->start_date = $start_date;
            $record->end_date = $end_date;
            $record->pay_date = $pay_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}