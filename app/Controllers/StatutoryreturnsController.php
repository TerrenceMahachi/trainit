<?php
namespace App\Controllers;

use App\Models\Statutoryreturn;
use App\Models\Database;

class StatutoryreturnsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['payrollperiod']) && $_POST['payrollperiod'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payrollperiod=' . $_POST['payrollperiod'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`return_type` LIKE $escapedTerm OR `reference_number` LIKE $escapedTerm OR `total_contribution` LIKE $escapedTerm OR `submission_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Statutoryreturn::page($selected_page, $page_size, $search, $order_by);
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
        
        $payrollperiod = $_POST["payrollperiod"];
        $return_type = $_POST["return_type"];
        $reference_number = $_POST["reference_number"];
        $total_contribution = $_POST["total_contribution"];
        $submission_date = $_POST["submission_date"];
        
       
        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($return_type == "" && (!$_error)) {  $_result .= "<br>Error: return_type cannot be blank"; $_error = true; }
        if ($reference_number == "" && (!$_error)) {  $_result .= "<br>Error: reference_number cannot be blank"; $_error = true; }
        if ($total_contribution == "" && (!$_error)) {  $_result .= "<br>Error: total_contribution cannot be blank"; $_error = true; }
        if ($submission_date == "" && (!$_error)) {  $_result .= "<br>Error: submission_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM statutoryreturn WHERE name=?";
            $records = Statutoryreturn::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Statutoryreturn();
            $record->reg_by = $_COOKIE['user'];
            
            $record->payrollperiod = $payrollperiod;
            $record->return_type = $return_type;
            $record->reference_number = $reference_number;
            $record->total_contribution = $total_contribution;
            $record->submission_date = $submission_date;
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
        
        $payrollperiod = $_POST["payrollperiod"];
        $return_type = $_POST["return_type"];
        $reference_number = $_POST["reference_number"];
        $total_contribution = $_POST["total_contribution"];
        $submission_date = $_POST["submission_date"];



        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($return_type == "" && (!$_error)) {  $_result .= "<br>Error: return_type cannot be blank"; $_error = true; }
        if ($reference_number == "" && (!$_error)) {  $_result .= "<br>Error: reference_number cannot be blank"; $_error = true; }
        if ($total_contribution == "" && (!$_error)) {  $_result .= "<br>Error: total_contribution cannot be blank"; $_error = true; }
        if ($submission_date == "" && (!$_error)) {  $_result .= "<br>Error: submission_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM statutoryreturn WHERE name=? AND iD!=?";
            $records = Statutoryreturn::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Statutoryreturn::where('iD', $recordiD)[0];
            
            $record->payrollperiod = $payrollperiod;
            $record->return_type = $return_type;
            $record->reference_number = $reference_number;
            $record->total_contribution = $total_contribution;
            $record->submission_date = $submission_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}