<?php
namespace App\Controllers;

use App\Models\UserSession;
use App\Models\Database;

class UserSessionsController
{
    public function index()
    {
        $search = "";

         if (isset($_POST['user']) && $_POST['user'] != '') {
                if ($search != '') {  $search .= ' AND '; }
                $search .= ' user=' . $_POST['user'];
            }

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`token` LIKE $escapedTerm OR `start` LIKE $escapedTerm OR `end` LIKE $escapedTerm OR `ip_address` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = UserSession::page($selected_page, $page_size, $search, $order_by);
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
        
        $token = $_POST["token"];
        $user = $_POST["user"];
        $start = $_POST["start"];
        $end = $_POST["end"];
        $ip_address = $_POST["ip_address"];
        
       
        
        if ($token == "" && (!$_error)) {  $_result .= "<br>Error: token cannot be blank"; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: user cannot be blank"; $_error = true; }
        if ($start == "" && (!$_error)) {  $_result .= "<br>Error: start cannot be blank"; $_error = true; }
        if ($end == "" && (!$_error)) {  $_result .= "<br>Error: end cannot be blank"; $_error = true; }
        if ($ip_address == "" && (!$_error)) {  $_result .= "<br>Error: ip_address cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM usersession WHERE token=?";
            $records = UserSession::findByQuery($sql, [$token]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            //name, form, interest_rate, min_amount, max_amount, min_income, max_repayment_period, currency, description
            $record = new UserSession();
            $record->reg_by = $_COOKIE['user'];
            
            $record->token = $token;
            $record->user = $user;
            $record->start = $start;
            $record->end = $end;
            $record->ip_address = $ip_address;
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
        
        $token = $_POST["token"];
        $user = $_POST["user"];
        $start = $_POST["start"];
        $end = $_POST["end"];
        $ip_address = $_POST["ip_address"];



        
        if ($token == "" && (!$_error)) {  $_result .= "<br>Error: token cannot be blank"; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: user cannot be blank"; $_error = true; }
        if ($start == "" && (!$_error)) {  $_result .= "<br>Error: start cannot be blank"; $_error = true; }
        if ($end == "" && (!$_error)) {  $_result .= "<br>Error: end cannot be blank"; $_error = true; }
        if ($ip_address == "" && (!$_error)) {  $_result .= "<br>Error: ip_address cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM usersession WHERE name=? AND iD!=?";
            $records = UserSession::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            //name, form, interest_rate, min_amount, max_amount, min_income, max_repayment_period, currency, description
            $record = UserSession::where('iD', $recordiD)[0];
            
            $record->token = $token;
            $record->user = $user;
            $record->start = $start;
            $record->end = $end;
            $record->ip_address = $ip_address;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}