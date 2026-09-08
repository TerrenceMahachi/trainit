<?php
namespace App\Controllers;

use App\Models\Clientserviceplan;
use App\Models\Database;

class ClientserviceplansController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['clientorganization']) && $_POST['clientorganization'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientorganization=' . $_POST['clientorganization'];
}
if (isset($_POST['serviceoffering']) && $_POST['serviceoffering'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' serviceoffering=' . $_POST['serviceoffering'];
}
if (isset($_POST['service_manager']) && $_POST['service_manager'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' service_manager=' . $_POST['service_manager'];
}
if (isset($_POST['billing_owner']) && $_POST['billing_owner'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' billing_owner=' . $_POST['billing_owner'];
}
if (isset($_POST['excesspolicy']) && $_POST['excesspolicy'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' excesspolicy=' . $_POST['excesspolicy'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`plan_name` LIKE $escapedTerm OR `currency` LIKE $escapedTerm OR `monthly_fee` LIKE $escapedTerm OR `included_hours` LIKE $escapedTerm OR `associate_rate` LIKE $escapedTerm OR `apprentice_rate` LIKE $escapedTerm OR `billing_cycle_day` LIKE $escapedTerm OR `start_date` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Clientserviceplan::page($selected_page, $page_size, $search, $order_by);
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
        
        $clientorganization = $_POST["clientorganization"];
        $serviceoffering = $_POST["serviceoffering"];
        $plan_name = $_POST["plan_name"];
        $currency = $_POST["currency"];
        $monthly_fee = $_POST["monthly_fee"];
        $included_hours = $_POST["included_hours"];
        $associate_rate = $_POST["associate_rate"];
        $apprentice_rate = $_POST["apprentice_rate"];
        $billing_cycle_day = $_POST["billing_cycle_day"];
        $service_manager = $_POST["service_manager"];
        $billing_owner = $_POST["billing_owner"];
        $excesspolicy = $_POST["excesspolicy"];
        $start_date = $_POST["start_date"];
        
       
        
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($serviceoffering == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_serviceoffering "; $_error = true; }
        if ($plan_name == "" && (!$_error)) {  $_result .= "<br>Error: plan_name cannot be blank"; $_error = true; }
        if ($currency == "" && (!$_error)) {  $_result .= "<br>Error: currency cannot be blank"; $_error = true; }
        if ($monthly_fee == "" && (!$_error)) {  $_result .= "<br>Error: monthly_fee cannot be blank"; $_error = true; }
        if ($included_hours == "" && (!$_error)) {  $_result .= "<br>Error: included_hours cannot be blank"; $_error = true; }
        if ($associate_rate == "" && (!$_error)) {  $_result .= "<br>Error: associate_rate cannot be blank"; $_error = true; }
        if ($apprentice_rate == "" && (!$_error)) {  $_result .= "<br>Error: apprentice_rate cannot be blank"; $_error = true; }
        if ($billing_cycle_day == "" && (!$_error)) {  $_result .= "<br>Error: billing_cycle_day cannot be blank"; $_error = true; }
        if ($service_manager == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_service_manager "; $_error = true; }
        if ($billing_owner == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_billing_owner "; $_error = true; }
        if ($excesspolicy == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_excesspolicy "; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM clientserviceplan WHERE name=?";
            $records = Clientserviceplan::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Clientserviceplan();
            $record->reg_by = $_COOKIE['user'];
            
            $record->clientorganization = $clientorganization;
            $record->serviceoffering = $serviceoffering;
            $record->plan_name = $plan_name;
            $record->currency = $currency;
            $record->monthly_fee = $monthly_fee;
            $record->included_hours = $included_hours;
            $record->associate_rate = $associate_rate;
            $record->apprentice_rate = $apprentice_rate;
            $record->billing_cycle_day = $billing_cycle_day;
            $record->service_manager = $service_manager;
            $record->billing_owner = $billing_owner;
            $record->excesspolicy = $excesspolicy;
            $record->start_date = $start_date;
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
        
        $clientorganization = $_POST["clientorganization"];
        $serviceoffering = $_POST["serviceoffering"];
        $plan_name = $_POST["plan_name"];
        $currency = $_POST["currency"];
        $monthly_fee = $_POST["monthly_fee"];
        $included_hours = $_POST["included_hours"];
        $associate_rate = $_POST["associate_rate"];
        $apprentice_rate = $_POST["apprentice_rate"];
        $billing_cycle_day = $_POST["billing_cycle_day"];
        $service_manager = $_POST["service_manager"];
        $billing_owner = $_POST["billing_owner"];
        $excesspolicy = $_POST["excesspolicy"];
        $start_date = $_POST["start_date"];



        
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($serviceoffering == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_serviceoffering "; $_error = true; }
        if ($plan_name == "" && (!$_error)) {  $_result .= "<br>Error: plan_name cannot be blank"; $_error = true; }
        if ($currency == "" && (!$_error)) {  $_result .= "<br>Error: currency cannot be blank"; $_error = true; }
        if ($monthly_fee == "" && (!$_error)) {  $_result .= "<br>Error: monthly_fee cannot be blank"; $_error = true; }
        if ($included_hours == "" && (!$_error)) {  $_result .= "<br>Error: included_hours cannot be blank"; $_error = true; }
        if ($associate_rate == "" && (!$_error)) {  $_result .= "<br>Error: associate_rate cannot be blank"; $_error = true; }
        if ($apprentice_rate == "" && (!$_error)) {  $_result .= "<br>Error: apprentice_rate cannot be blank"; $_error = true; }
        if ($billing_cycle_day == "" && (!$_error)) {  $_result .= "<br>Error: billing_cycle_day cannot be blank"; $_error = true; }
        if ($service_manager == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_service_manager "; $_error = true; }
        if ($billing_owner == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_billing_owner "; $_error = true; }
        if ($excesspolicy == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_excesspolicy "; $_error = true; }
        if ($start_date == "" && (!$_error)) {  $_result .= "<br>Error: start_date cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM clientserviceplan WHERE name=? AND iD!=?";
            $records = Clientserviceplan::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Clientserviceplan::where('iD', $recordiD)[0];
            
            $record->clientorganization = $clientorganization;
            $record->serviceoffering = $serviceoffering;
            $record->plan_name = $plan_name;
            $record->currency = $currency;
            $record->monthly_fee = $monthly_fee;
            $record->included_hours = $included_hours;
            $record->associate_rate = $associate_rate;
            $record->apprentice_rate = $apprentice_rate;
            $record->billing_cycle_day = $billing_cycle_day;
            $record->service_manager = $service_manager;
            $record->billing_owner = $billing_owner;
            $record->excesspolicy = $excesspolicy;
            $record->start_date = $start_date;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}