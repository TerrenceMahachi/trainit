<?php
namespace App\Controllers;

use App\Models\Payrollperiodapproval;
use App\Models\Database;

class PayrollperiodapprovalsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['payrollperiod']) && $_POST['payrollperiod'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payrollperiod=' . $_POST['payrollperiod'];
}
if (isset($_POST['payperiodstatus']) && $_POST['payperiodstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' payperiodstatus=' . $_POST['payperiodstatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Payrollperiodapproval::page($selected_page, $page_size, $search, $order_by);
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
        $payperiodstatus = $_POST["payperiodstatus"];
        $notes = $_POST["notes"];
        
       
        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($payperiodstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payperiodstatus "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM payrollperiodapproval WHERE name=?";
            $records = Payrollperiodapproval::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Payrollperiodapproval();
            $record->reg_by = $_COOKIE['user'];
            
            $record->payrollperiod = $payrollperiod;
            $record->payperiodstatus = $payperiodstatus;
            $record->notes = $notes;
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
        $payperiodstatus = $_POST["payperiodstatus"];
        $notes = $_POST["notes"];



        
        if ($payrollperiod == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payrollperiod "; $_error = true; }
        if ($payperiodstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_payperiodstatus "; $_error = true; }
        if ($notes == "" && (!$_error)) {  $_result .= "<br>Error: notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM payrollperiodapproval WHERE name=? AND iD!=?";
            $records = Payrollperiodapproval::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Payrollperiodapproval::where('iD', $recordiD)[0];
            
            $record->payrollperiod = $payrollperiod;
            $record->payperiodstatus = $payperiodstatus;
            $record->notes = $notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}