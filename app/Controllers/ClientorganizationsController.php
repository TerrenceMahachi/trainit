<?php
namespace App\Controllers;

use App\Models\Clientorganization;
use App\Models\Database;

class ClientorganizationsController
{
    public function index()
    {
        $search = "";

        

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`legal_name` LIKE $escapedTerm OR `trading_name` LIKE $escapedTerm OR `registration_number` LIKE $escapedTerm OR `tax_number` LIKE $escapedTerm OR `billing_email` LIKE $escapedTerm OR `address` LIKE $escapedTerm OR `city` LIKE $escapedTerm OR `country` LIKE $escapedTerm OR `primary_phone` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Clientorganization::page($selected_page, $page_size, $search, $order_by);
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
        
        $legal_name = $_POST["legal_name"];
        $trading_name = $_POST["trading_name"];
        $registration_number = $_POST["registration_number"];
        $tax_number = $_POST["tax_number"];
        $billing_email = $_POST["billing_email"];
        $address = $_POST["address"];
        $city = $_POST["city"];
        $country = $_POST["country"];
        $primary_phone = $_POST["primary_phone"];
        
       
        
        if ($legal_name == "" && (!$_error)) {  $_result .= "<br>Error: legal_name cannot be blank"; $_error = true; }
        if ($trading_name == "" && (!$_error)) {  $_result .= "<br>Error: trading_name cannot be blank"; $_error = true; }
        if ($registration_number == "" && (!$_error)) {  $_result .= "<br>Error: registration_number cannot be blank"; $_error = true; }
        if ($tax_number == "" && (!$_error)) {  $_result .= "<br>Error: tax_number cannot be blank"; $_error = true; }
        if ($billing_email == "" && (!$_error)) {  $_result .= "<br>Error: billing_email cannot be blank"; $_error = true; }
        if ($address == "" && (!$_error)) {  $_result .= "<br>Error: address cannot be blank"; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($primary_phone == "" && (!$_error)) {  $_result .= "<br>Error: primary_phone cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM clientorganization WHERE name=?";
            $records = Clientorganization::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Clientorganization();
            $record->reg_by = $_COOKIE['user'];
            
            $record->legal_name = $legal_name;
            $record->trading_name = $trading_name;
            $record->registration_number = $registration_number;
            $record->tax_number = $tax_number;
            $record->billing_email = $billing_email;
            $record->address = $address;
            $record->city = $city;
            $record->country = $country;
            $record->primary_phone = $primary_phone;
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
        
        $legal_name = $_POST["legal_name"];
        $trading_name = $_POST["trading_name"];
        $registration_number = $_POST["registration_number"];
        $tax_number = $_POST["tax_number"];
        $billing_email = $_POST["billing_email"];
        $address = $_POST["address"];
        $city = $_POST["city"];
        $country = $_POST["country"];
        $primary_phone = $_POST["primary_phone"];



        
        if ($legal_name == "" && (!$_error)) {  $_result .= "<br>Error: legal_name cannot be blank"; $_error = true; }
        if ($trading_name == "" && (!$_error)) {  $_result .= "<br>Error: trading_name cannot be blank"; $_error = true; }
        if ($registration_number == "" && (!$_error)) {  $_result .= "<br>Error: registration_number cannot be blank"; $_error = true; }
        if ($tax_number == "" && (!$_error)) {  $_result .= "<br>Error: tax_number cannot be blank"; $_error = true; }
        if ($billing_email == "" && (!$_error)) {  $_result .= "<br>Error: billing_email cannot be blank"; $_error = true; }
        if ($address == "" && (!$_error)) {  $_result .= "<br>Error: address cannot be blank"; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($primary_phone == "" && (!$_error)) {  $_result .= "<br>Error: primary_phone cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM clientorganization WHERE name=? AND iD!=?";
            $records = Clientorganization::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Clientorganization::where('iD', $recordiD)[0];
            
            $record->legal_name = $legal_name;
            $record->trading_name = $trading_name;
            $record->registration_number = $registration_number;
            $record->tax_number = $tax_number;
            $record->billing_email = $billing_email;
            $record->address = $address;
            $record->city = $city;
            $record->country = $country;
            $record->primary_phone = $primary_phone;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}