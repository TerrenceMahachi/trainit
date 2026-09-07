<?php
namespace App\Controllers;

use App\Models\Rosteronboarding;
use App\Models\Database;

class RosteronboardingsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`national_id_number` LIKE $escapedTerm OR `national_id_doc` LIKE $escapedTerm OR `passport_number` LIKE $escapedTerm OR `passport_expiry` LIKE $escapedTerm OR `street_address` LIKE $escapedTerm OR `city` LIKE $escapedTerm OR `country` LIKE $escapedTerm OR `bank_name` LIKE $escapedTerm OR `bank_branch` LIKE $escapedTerm OR `account_name` LIKE $escapedTerm OR `account_number` LIKE $escapedTerm OR `bank_currency` LIKE $escapedTerm OR `emergency_contact_name` LIKE $escapedTerm OR `emergency_contact_phone` LIKE $escapedTerm OR `emergency_contact_relationship` LIKE $escapedTerm OR `nssa_number` LIKE $escapedTerm OR `police_clearance_doc` LIKE $escapedTerm OR `police_clearance_date` LIKE $escapedTerm OR `signed_contract_doc` LIKE $escapedTerm OR `signed_nda_doc` LIKE $escapedTerm OR `odoo_applicant_id` LIKE $escapedTerm OR `odoo_employee_id` LIKE $escapedTerm OR `synced_to_odoo_at` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosteronboarding::page($selected_page, $page_size, $search, $order_by);
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
        
        $rosterapplication = $_POST["rosterapplication"];
        $national_id_number = $_POST["national_id_number"];
        $national_id_doc = $_POST["national_id_doc"];
        $passport_number = $_POST["passport_number"];
        $passport_expiry = $_POST["passport_expiry"];
        $street_address = $_POST["street_address"];
        $city = $_POST["city"];
        $country = $_POST["country"];
        $bank_name = $_POST["bank_name"];
        $bank_branch = $_POST["bank_branch"];
        $account_name = $_POST["account_name"];
        $account_number = $_POST["account_number"];
        $bank_currency = $_POST["bank_currency"];
        $emergency_contact_name = $_POST["emergency_contact_name"];
        $emergency_contact_phone = $_POST["emergency_contact_phone"];
        $emergency_contact_relationship = $_POST["emergency_contact_relationship"];
        $nssa_number = $_POST["nssa_number"];
        $police_clearance_doc = $_POST["police_clearance_doc"];
        $police_clearance_date = $_POST["police_clearance_date"];
        $signed_contract_doc = $_POST["signed_contract_doc"];
        $signed_nda_doc = $_POST["signed_nda_doc"];
        $odoo_applicant_id = $_POST["odoo_applicant_id"];
        $odoo_employee_id = $_POST["odoo_employee_id"];
        $synced_to_odoo_at = $_POST["synced_to_odoo_at"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($national_id_number == "" && (!$_error)) {  $_result .= "<br>Error: national_id_number cannot be blank"; $_error = true; }
        if ($national_id_doc == "" && (!$_error)) {  $_result .= "<br>Error: national_id_doc cannot be blank"; $_error = true; }
        if ($passport_number == "" && (!$_error)) {  $_result .= "<br>Error: passport_number cannot be blank"; $_error = true; }
        if ($passport_expiry == "" && (!$_error)) {  $_result .= "<br>Error: passport_expiry cannot be blank"; $_error = true; }
        if ($street_address == "" && (!$_error)) {  $_result .= "<br>Error: street_address cannot be blank"; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($bank_name == "" && (!$_error)) {  $_result .= "<br>Error: bank_name cannot be blank"; $_error = true; }
        if ($bank_branch == "" && (!$_error)) {  $_result .= "<br>Error: bank_branch cannot be blank"; $_error = true; }
        if ($account_name == "" && (!$_error)) {  $_result .= "<br>Error: account_name cannot be blank"; $_error = true; }
        if ($account_number == "" && (!$_error)) {  $_result .= "<br>Error: account_number cannot be blank"; $_error = true; }
        if ($bank_currency == "" && (!$_error)) {  $_result .= "<br>Error: bank_currency cannot be blank"; $_error = true; }
        if ($emergency_contact_name == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_name cannot be blank"; $_error = true; }
        if ($emergency_contact_phone == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_phone cannot be blank"; $_error = true; }
        if ($emergency_contact_relationship == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_relationship cannot be blank"; $_error = true; }
        if ($nssa_number == "" && (!$_error)) {  $_result .= "<br>Error: nssa_number cannot be blank"; $_error = true; }
        if ($police_clearance_doc == "" && (!$_error)) {  $_result .= "<br>Error: police_clearance_doc cannot be blank"; $_error = true; }
        if ($police_clearance_date == "" && (!$_error)) {  $_result .= "<br>Error: police_clearance_date cannot be blank"; $_error = true; }
        if ($signed_contract_doc == "" && (!$_error)) {  $_result .= "<br>Error: signed_contract_doc cannot be blank"; $_error = true; }
        if ($signed_nda_doc == "" && (!$_error)) {  $_result .= "<br>Error: signed_nda_doc cannot be blank"; $_error = true; }
        if ($odoo_applicant_id == "" && (!$_error)) {  $_result .= "<br>Error: odoo_applicant_id cannot be blank"; $_error = true; }
        if ($odoo_employee_id == "" && (!$_error)) {  $_result .= "<br>Error: odoo_employee_id cannot be blank"; $_error = true; }
        if ($synced_to_odoo_at == "" && (!$_error)) {  $_result .= "<br>Error: synced_to_odoo_at cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosteronboarding WHERE name=?";
            $records = Rosteronboarding::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosteronboarding();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->national_id_number = $national_id_number;
            $record->national_id_doc = $national_id_doc;
            $record->passport_number = $passport_number;
            $record->passport_expiry = $passport_expiry;
            $record->street_address = $street_address;
            $record->city = $city;
            $record->country = $country;
            $record->bank_name = $bank_name;
            $record->bank_branch = $bank_branch;
            $record->account_name = $account_name;
            $record->account_number = $account_number;
            $record->bank_currency = $bank_currency;
            $record->emergency_contact_name = $emergency_contact_name;
            $record->emergency_contact_phone = $emergency_contact_phone;
            $record->emergency_contact_relationship = $emergency_contact_relationship;
            $record->nssa_number = $nssa_number;
            $record->police_clearance_doc = $police_clearance_doc;
            $record->police_clearance_date = $police_clearance_date;
            $record->signed_contract_doc = $signed_contract_doc;
            $record->signed_nda_doc = $signed_nda_doc;
            $record->odoo_applicant_id = $odoo_applicant_id;
            $record->odoo_employee_id = $odoo_employee_id;
            $record->synced_to_odoo_at = $synced_to_odoo_at;
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
        
        $rosterapplication = $_POST["rosterapplication"];
        $national_id_number = $_POST["national_id_number"];
        $national_id_doc = $_POST["national_id_doc"];
        $passport_number = $_POST["passport_number"];
        $passport_expiry = $_POST["passport_expiry"];
        $street_address = $_POST["street_address"];
        $city = $_POST["city"];
        $country = $_POST["country"];
        $bank_name = $_POST["bank_name"];
        $bank_branch = $_POST["bank_branch"];
        $account_name = $_POST["account_name"];
        $account_number = $_POST["account_number"];
        $bank_currency = $_POST["bank_currency"];
        $emergency_contact_name = $_POST["emergency_contact_name"];
        $emergency_contact_phone = $_POST["emergency_contact_phone"];
        $emergency_contact_relationship = $_POST["emergency_contact_relationship"];
        $nssa_number = $_POST["nssa_number"];
        $police_clearance_doc = $_POST["police_clearance_doc"];
        $police_clearance_date = $_POST["police_clearance_date"];
        $signed_contract_doc = $_POST["signed_contract_doc"];
        $signed_nda_doc = $_POST["signed_nda_doc"];
        $odoo_applicant_id = $_POST["odoo_applicant_id"];
        $odoo_employee_id = $_POST["odoo_employee_id"];
        $synced_to_odoo_at = $_POST["synced_to_odoo_at"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($national_id_number == "" && (!$_error)) {  $_result .= "<br>Error: national_id_number cannot be blank"; $_error = true; }
        if ($national_id_doc == "" && (!$_error)) {  $_result .= "<br>Error: national_id_doc cannot be blank"; $_error = true; }
        if ($passport_number == "" && (!$_error)) {  $_result .= "<br>Error: passport_number cannot be blank"; $_error = true; }
        if ($passport_expiry == "" && (!$_error)) {  $_result .= "<br>Error: passport_expiry cannot be blank"; $_error = true; }
        if ($street_address == "" && (!$_error)) {  $_result .= "<br>Error: street_address cannot be blank"; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($bank_name == "" && (!$_error)) {  $_result .= "<br>Error: bank_name cannot be blank"; $_error = true; }
        if ($bank_branch == "" && (!$_error)) {  $_result .= "<br>Error: bank_branch cannot be blank"; $_error = true; }
        if ($account_name == "" && (!$_error)) {  $_result .= "<br>Error: account_name cannot be blank"; $_error = true; }
        if ($account_number == "" && (!$_error)) {  $_result .= "<br>Error: account_number cannot be blank"; $_error = true; }
        if ($bank_currency == "" && (!$_error)) {  $_result .= "<br>Error: bank_currency cannot be blank"; $_error = true; }
        if ($emergency_contact_name == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_name cannot be blank"; $_error = true; }
        if ($emergency_contact_phone == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_phone cannot be blank"; $_error = true; }
        if ($emergency_contact_relationship == "" && (!$_error)) {  $_result .= "<br>Error: emergency_contact_relationship cannot be blank"; $_error = true; }
        if ($nssa_number == "" && (!$_error)) {  $_result .= "<br>Error: nssa_number cannot be blank"; $_error = true; }
        if ($police_clearance_doc == "" && (!$_error)) {  $_result .= "<br>Error: police_clearance_doc cannot be blank"; $_error = true; }
        if ($police_clearance_date == "" && (!$_error)) {  $_result .= "<br>Error: police_clearance_date cannot be blank"; $_error = true; }
        if ($signed_contract_doc == "" && (!$_error)) {  $_result .= "<br>Error: signed_contract_doc cannot be blank"; $_error = true; }
        if ($signed_nda_doc == "" && (!$_error)) {  $_result .= "<br>Error: signed_nda_doc cannot be blank"; $_error = true; }
        if ($odoo_applicant_id == "" && (!$_error)) {  $_result .= "<br>Error: odoo_applicant_id cannot be blank"; $_error = true; }
        if ($odoo_employee_id == "" && (!$_error)) {  $_result .= "<br>Error: odoo_employee_id cannot be blank"; $_error = true; }
        if ($synced_to_odoo_at == "" && (!$_error)) {  $_result .= "<br>Error: synced_to_odoo_at cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosteronboarding WHERE name=? AND iD!=?";
            $records = Rosteronboarding::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosteronboarding::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->national_id_number = $national_id_number;
            $record->national_id_doc = $national_id_doc;
            $record->passport_number = $passport_number;
            $record->passport_expiry = $passport_expiry;
            $record->street_address = $street_address;
            $record->city = $city;
            $record->country = $country;
            $record->bank_name = $bank_name;
            $record->bank_branch = $bank_branch;
            $record->account_name = $account_name;
            $record->account_number = $account_number;
            $record->bank_currency = $bank_currency;
            $record->emergency_contact_name = $emergency_contact_name;
            $record->emergency_contact_phone = $emergency_contact_phone;
            $record->emergency_contact_relationship = $emergency_contact_relationship;
            $record->nssa_number = $nssa_number;
            $record->police_clearance_doc = $police_clearance_doc;
            $record->police_clearance_date = $police_clearance_date;
            $record->signed_contract_doc = $signed_contract_doc;
            $record->signed_nda_doc = $signed_nda_doc;
            $record->odoo_applicant_id = $odoo_applicant_id;
            $record->odoo_employee_id = $odoo_employee_id;
            $record->synced_to_odoo_at = $synced_to_odoo_at;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}