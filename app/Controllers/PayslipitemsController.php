<?php
namespace App\Controllers;

use App\Models\Payslipitem;
use App\Models\Database;

class PayslipitemsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['payslip']) && $_POST['payslip'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payslip=' . $_POST['payslip'];
}
if (isset($_POST['payrollitemtype']) && $_POST['payrollitemtype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payrollitemtype=' . $_POST['payrollitemtype'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`item_name` LIKE $escapedTerm OR `amount` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Payslipitem::page($selected_page, $page_size, $search, $order_by);
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
        
        $payslip = $_POST["payslip"];
        $payrollitemtype = $_POST["payrollitemtype"];
        $item_name = $_POST["item_name"];
        $amount = $_POST["amount"];
        
       
        
        if ($payslip == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payslip "; $_error = true; }
        if ($payrollitemtype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollitemtype "; $_error = true; }
        if ($item_name == "" && (!$_error)) {  $_result .= "<br>Error: item_name cannot be blank"; $_error = true; }
        if ($amount == "" && (!$_error)) {  $_result .= "<br>Error: amount cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM payslipitem WHERE name=?";
            $records = Payslipitem::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Payslipitem();
            $record->reg_by = $_COOKIE['user'];
            
            $record->payslip = $payslip;
            $record->payrollitemtype = $payrollitemtype;
            $record->item_name = $item_name;
            $record->amount = $amount;
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
        
        $payslip = $_POST["payslip"];
        $payrollitemtype = $_POST["payrollitemtype"];
        $item_name = $_POST["item_name"];
        $amount = $_POST["amount"];



        
        if ($payslip == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payslip "; $_error = true; }
        if ($payrollitemtype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollitemtype "; $_error = true; }
        if ($item_name == "" && (!$_error)) {  $_result .= "<br>Error: item_name cannot be blank"; $_error = true; }
        if ($amount == "" && (!$_error)) {  $_result .= "<br>Error: amount cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM payslipitem WHERE name=? AND iD!=?";
            $records = Payslipitem::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Payslipitem::where('iD', $recordiD)[0];
            
            $record->payslip = $payslip;
            $record->payrollitemtype = $payrollitemtype;
            $record->item_name = $item_name;
            $record->amount = $amount;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}