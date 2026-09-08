<?php
namespace App\Controllers;

use App\Models\Payslip;
use App\Models\Database;

class PayslipsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['payrollperiod']) && $_POST['payrollperiod'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payrollperiod=' . $_POST['payrollperiod'];
}
if (isset($_POST['staffprofile']) && $_POST['staffprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffprofile=' . $_POST['staffprofile'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`currency` LIKE $escapedTerm OR `gross_pay` LIKE $escapedTerm OR `total_deductions` LIKE $escapedTerm OR `net_pay` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Payslip::page($selected_page, $page_size, $search, $order_by);
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
        $staffprofile = $_POST["staffprofile"];
        $currency = $_POST["currency"];
        $gross_pay = $_POST["gross_pay"];
        $total_deductions = $_POST["total_deductions"];
        $net_pay = $_POST["net_pay"];
        
       
        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($currency == "" && (!$_error)) {  $_result .= "<br>Error: currency cannot be blank"; $_error = true; }
        if ($gross_pay == "" && (!$_error)) {  $_result .= "<br>Error: gross_pay cannot be blank"; $_error = true; }
        if ($total_deductions == "" && (!$_error)) {  $_result .= "<br>Error: total_deductions cannot be blank"; $_error = true; }
        if ($net_pay == "" && (!$_error)) {  $_result .= "<br>Error: net_pay cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM payslip WHERE name=?";
            $records = Payslip::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Payslip();
            $record->reg_by = $_COOKIE['user'];
            
            $record->payrollperiod = $payrollperiod;
            $record->staffprofile = $staffprofile;
            $record->currency = $currency;
            $record->gross_pay = $gross_pay;
            $record->total_deductions = $total_deductions;
            $record->net_pay = $net_pay;
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
        $staffprofile = $_POST["staffprofile"];
        $currency = $_POST["currency"];
        $gross_pay = $_POST["gross_pay"];
        $total_deductions = $_POST["total_deductions"];
        $net_pay = $_POST["net_pay"];



        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($currency == "" && (!$_error)) {  $_result .= "<br>Error: currency cannot be blank"; $_error = true; }
        if ($gross_pay == "" && (!$_error)) {  $_result .= "<br>Error: gross_pay cannot be blank"; $_error = true; }
        if ($total_deductions == "" && (!$_error)) {  $_result .= "<br>Error: total_deductions cannot be blank"; $_error = true; }
        if ($net_pay == "" && (!$_error)) {  $_result .= "<br>Error: net_pay cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM payslip WHERE name=? AND iD!=?";
            $records = Payslip::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Payslip::where('iD', $recordiD)[0];
            
            $record->payrollperiod = $payrollperiod;
            $record->staffprofile = $staffprofile;
            $record->currency = $currency;
            $record->gross_pay = $gross_pay;
            $record->total_deductions = $total_deductions;
            $record->net_pay = $net_pay;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}