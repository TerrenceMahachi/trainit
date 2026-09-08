<?php
namespace App\Controllers;

use App\Models\Payslipdisbursement;
use App\Models\Database;

class PayslipdisbursementsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['payslip']) && $_POST['payslip'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payslip=' . $_POST['payslip'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`payment_method` LIKE $escapedTerm OR `transaction_reference` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Payslipdisbursement::page($selected_page, $page_size, $search, $order_by);
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
        $payment_method = $_POST["payment_method"];
        $transaction_reference = $_POST["transaction_reference"];
        
       
        
        if ($payslip == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payslip "; $_error = true; }
        if ($payment_method == "" && (!$_error)) {  $_result .= "<br>Error: payment_method cannot be blank"; $_error = true; }
        if ($transaction_reference == "" && (!$_error)) {  $_result .= "<br>Error: transaction_reference cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM payslipdisbursement WHERE name=?";
            $records = Payslipdisbursement::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Payslipdisbursement();
            $record->reg_by = $_COOKIE['user'];
            
            $record->payslip = $payslip;
            $record->payment_method = $payment_method;
            $record->transaction_reference = $transaction_reference;
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
        $payment_method = $_POST["payment_method"];
        $transaction_reference = $_POST["transaction_reference"];



        
        if ($payslip == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payslip "; $_error = true; }
        if ($payment_method == "" && (!$_error)) {  $_result .= "<br>Error: payment_method cannot be blank"; $_error = true; }
        if ($transaction_reference == "" && (!$_error)) {  $_result .= "<br>Error: transaction_reference cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM payslipdisbursement WHERE name=? AND iD!=?";
            $records = Payslipdisbursement::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Payslipdisbursement::where('iD', $recordiD)[0];
            
            $record->payslip = $payslip;
            $record->payment_method = $payment_method;
            $record->transaction_reference = $transaction_reference;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}