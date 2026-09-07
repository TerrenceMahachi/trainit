<?php
namespace App\Controllers;

use App\Models\Rosterapplication;
use App\Models\Database;

class RosterapplicationsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['user']) && $_POST['user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' user=' . $_POST['user'];
}
if (isset($_POST['applicationtrack']) && $_POST['applicationtrack'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' applicationtrack=' . $_POST['applicationtrack'];
}
if (isset($_POST['applicationstatus']) && $_POST['applicationstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' applicationstatus=' . $_POST['applicationstatus'];
}
if (isset($_POST['primaryfunction']) && $_POST['primaryfunction'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' primaryfunction=' . $_POST['primaryfunction'];
}
if (isset($_POST['gender']) && $_POST['gender'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' gender=' . $_POST['gender'];
}
if (isset($_POST['zimprovince']) && $_POST['zimprovince'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' zimprovince=' . $_POST['zimprovince'];
}
if (isset($_POST['workrightstatus']) && $_POST['workrightstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' workrightstatus=' . $_POST['workrightstatus'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`legal_name` LIKE $escapedTerm OR `preferred_name` LIKE $escapedTerm OR `email` LIKE $escapedTerm OR `mobile_number` LIKE $escapedTerm OR `whatsapp_number` LIKE $escapedTerm OR `date_of_birth` LIKE $escapedTerm OR `city` LIKE $escapedTerm OR `suburb` LIKE $escapedTerm OR `country` LIKE $escapedTerm OR `nationality` LIKE $escapedTerm OR `work_permit_number` LIKE $escapedTerm OR `work_permit_expiry` LIKE $escapedTerm OR `has_disability_adjustment` LIKE $escapedTerm OR `adjustment_details` LIKE $escapedTerm OR `how_heard` LIKE $escapedTerm OR `referred_by` LIKE $escapedTerm OR `consent_version` LIKE $escapedTerm OR `consent_timestamp` LIKE $escapedTerm OR `consent_ip_address` LIKE $escapedTerm OR `e_signature` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterapplication::page($selected_page, $page_size, $search, $order_by);
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
        
        $user = $_POST["user"];
        $applicationtrack = $_POST["applicationtrack"];
        $applicationstatus = $_POST["applicationstatus"];
        $primaryfunction = $_POST["primaryfunction"];
        $legal_name = $_POST["legal_name"];
        $preferred_name = $_POST["preferred_name"];
        $email = $_POST["email"];
        $mobile_number = $_POST["mobile_number"];
        $whatsapp_number = $_POST["whatsapp_number"];
        $date_of_birth = $_POST["date_of_birth"];
        $gender = $_POST["gender"];
        $city = $_POST["city"];
        $suburb = $_POST["suburb"];
        $zimprovince = $_POST["zimprovince"];
        $country = $_POST["country"];
        $nationality = $_POST["nationality"];
        $workrightstatus = $_POST["workrightstatus"];
        $work_permit_number = $_POST["work_permit_number"];
        $work_permit_expiry = $_POST["work_permit_expiry"];
        $has_disability_adjustment = $_POST["has_disability_adjustment"];
        $adjustment_details = $_POST["adjustment_details"];
        $how_heard = $_POST["how_heard"];
        $referred_by = $_POST["referred_by"];
        $consent_version = $_POST["consent_version"];
        $consent_timestamp = $_POST["consent_timestamp"];
        $consent_ip_address = $_POST["consent_ip_address"];
        $e_signature = $_POST["e_signature"];
        
       
        
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($applicationtrack == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationtrack "; $_error = true; }
        if ($applicationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationstatus "; $_error = true; }
        if ($primaryfunction == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_primaryfunction "; $_error = true; }
        if ($legal_name == "" && (!$_error)) {  $_result .= "<br>Error: legal_name cannot be blank"; $_error = true; }
        if ($preferred_name == "" && (!$_error)) {  $_result .= "<br>Error: preferred_name cannot be blank"; $_error = true; }
        if ($email == "" && (!$_error)) {  $_result .= "<br>Error: email cannot be blank"; $_error = true; }
        if ($mobile_number == "" && (!$_error)) {  $_result .= "<br>Error: mobile_number cannot be blank"; $_error = true; }
        if ($whatsapp_number == "" && (!$_error)) {  $_result .= "<br>Error: whatsapp_number cannot be blank"; $_error = true; }
        if ($date_of_birth == "" && (!$_error)) {  $_result .= "<br>Error: date_of_birth cannot be blank"; $_error = true; }
        if ($gender == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_gender "; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($suburb == "" && (!$_error)) {  $_result .= "<br>Error: suburb cannot be blank"; $_error = true; }
        if ($zimprovince == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_zimprovince "; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($nationality == "" && (!$_error)) {  $_result .= "<br>Error: nationality cannot be blank"; $_error = true; }
        if ($workrightstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_workrightstatus "; $_error = true; }
        if ($work_permit_number == "" && (!$_error)) {  $_result .= "<br>Error: work_permit_number cannot be blank"; $_error = true; }
        if ($work_permit_expiry == "" && (!$_error)) {  $_result .= "<br>Error: work_permit_expiry cannot be blank"; $_error = true; }
        if ($has_disability_adjustment == "" && (!$_error)) {  $_result .= "<br>Error: has_disability_adjustment cannot be blank"; $_error = true; }
        if ($adjustment_details == "" && (!$_error)) {  $_result .= "<br>Error: adjustment_details cannot be blank"; $_error = true; }
        if ($how_heard == "" && (!$_error)) {  $_result .= "<br>Error: how_heard cannot be blank"; $_error = true; }
        if ($referred_by == "" && (!$_error)) {  $_result .= "<br>Error: referred_by cannot be blank"; $_error = true; }
        if ($consent_version == "" && (!$_error)) {  $_result .= "<br>Error: consent_version cannot be blank"; $_error = true; }
        if ($consent_timestamp == "" && (!$_error)) {  $_result .= "<br>Error: consent_timestamp cannot be blank"; $_error = true; }
        if ($consent_ip_address == "" && (!$_error)) {  $_result .= "<br>Error: consent_ip_address cannot be blank"; $_error = true; }
        if ($e_signature == "" && (!$_error)) {  $_result .= "<br>Error: e_signature cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterapplication WHERE name=?";
            $records = Rosterapplication::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterapplication();
            $record->reg_by = $_COOKIE['user'];
            
            $record->user = $user;
            $record->applicationtrack = $applicationtrack;
            $record->applicationstatus = $applicationstatus;
            $record->primaryfunction = $primaryfunction;
            $record->legal_name = $legal_name;
            $record->preferred_name = $preferred_name;
            $record->email = $email;
            $record->mobile_number = $mobile_number;
            $record->whatsapp_number = $whatsapp_number;
            $record->date_of_birth = $date_of_birth;
            $record->gender = $gender;
            $record->city = $city;
            $record->suburb = $suburb;
            $record->zimprovince = $zimprovince;
            $record->country = $country;
            $record->nationality = $nationality;
            $record->workrightstatus = $workrightstatus;
            $record->work_permit_number = $work_permit_number;
            $record->work_permit_expiry = $work_permit_expiry;
            $record->has_disability_adjustment = $has_disability_adjustment;
            $record->adjustment_details = $adjustment_details;
            $record->how_heard = $how_heard;
            $record->referred_by = $referred_by;
            $record->consent_version = $consent_version;
            $record->consent_timestamp = $consent_timestamp;
            $record->consent_ip_address = $consent_ip_address;
            $record->e_signature = $e_signature;
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
        
        $user = $_POST["user"];
        $applicationtrack = $_POST["applicationtrack"];
        $applicationstatus = $_POST["applicationstatus"];
        $primaryfunction = $_POST["primaryfunction"];
        $legal_name = $_POST["legal_name"];
        $preferred_name = $_POST["preferred_name"];
        $email = $_POST["email"];
        $mobile_number = $_POST["mobile_number"];
        $whatsapp_number = $_POST["whatsapp_number"];
        $date_of_birth = $_POST["date_of_birth"];
        $gender = $_POST["gender"];
        $city = $_POST["city"];
        $suburb = $_POST["suburb"];
        $zimprovince = $_POST["zimprovince"];
        $country = $_POST["country"];
        $nationality = $_POST["nationality"];
        $workrightstatus = $_POST["workrightstatus"];
        $work_permit_number = $_POST["work_permit_number"];
        $work_permit_expiry = $_POST["work_permit_expiry"];
        $has_disability_adjustment = $_POST["has_disability_adjustment"];
        $adjustment_details = $_POST["adjustment_details"];
        $how_heard = $_POST["how_heard"];
        $referred_by = $_POST["referred_by"];
        $consent_version = $_POST["consent_version"];
        $consent_timestamp = $_POST["consent_timestamp"];
        $consent_ip_address = $_POST["consent_ip_address"];
        $e_signature = $_POST["e_signature"];



        
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($applicationtrack == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationtrack "; $_error = true; }
        if ($applicationstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_applicationstatus "; $_error = true; }
        if ($primaryfunction == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_primaryfunction "; $_error = true; }
        if ($legal_name == "" && (!$_error)) {  $_result .= "<br>Error: legal_name cannot be blank"; $_error = true; }
        if ($preferred_name == "" && (!$_error)) {  $_result .= "<br>Error: preferred_name cannot be blank"; $_error = true; }
        if ($email == "" && (!$_error)) {  $_result .= "<br>Error: email cannot be blank"; $_error = true; }
        if ($mobile_number == "" && (!$_error)) {  $_result .= "<br>Error: mobile_number cannot be blank"; $_error = true; }
        if ($whatsapp_number == "" && (!$_error)) {  $_result .= "<br>Error: whatsapp_number cannot be blank"; $_error = true; }
        if ($date_of_birth == "" && (!$_error)) {  $_result .= "<br>Error: date_of_birth cannot be blank"; $_error = true; }
        if ($gender == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_gender "; $_error = true; }
        if ($city == "" && (!$_error)) {  $_result .= "<br>Error: city cannot be blank"; $_error = true; }
        if ($suburb == "" && (!$_error)) {  $_result .= "<br>Error: suburb cannot be blank"; $_error = true; }
        if ($zimprovince == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_zimprovince "; $_error = true; }
        if ($country == "" && (!$_error)) {  $_result .= "<br>Error: country cannot be blank"; $_error = true; }
        if ($nationality == "" && (!$_error)) {  $_result .= "<br>Error: nationality cannot be blank"; $_error = true; }
        if ($workrightstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_workrightstatus "; $_error = true; }
        if ($work_permit_number == "" && (!$_error)) {  $_result .= "<br>Error: work_permit_number cannot be blank"; $_error = true; }
        if ($work_permit_expiry == "" && (!$_error)) {  $_result .= "<br>Error: work_permit_expiry cannot be blank"; $_error = true; }
        if ($has_disability_adjustment == "" && (!$_error)) {  $_result .= "<br>Error: has_disability_adjustment cannot be blank"; $_error = true; }
        if ($adjustment_details == "" && (!$_error)) {  $_result .= "<br>Error: adjustment_details cannot be blank"; $_error = true; }
        if ($how_heard == "" && (!$_error)) {  $_result .= "<br>Error: how_heard cannot be blank"; $_error = true; }
        if ($referred_by == "" && (!$_error)) {  $_result .= "<br>Error: referred_by cannot be blank"; $_error = true; }
        if ($consent_version == "" && (!$_error)) {  $_result .= "<br>Error: consent_version cannot be blank"; $_error = true; }
        if ($consent_timestamp == "" && (!$_error)) {  $_result .= "<br>Error: consent_timestamp cannot be blank"; $_error = true; }
        if ($consent_ip_address == "" && (!$_error)) {  $_result .= "<br>Error: consent_ip_address cannot be blank"; $_error = true; }
        if ($e_signature == "" && (!$_error)) {  $_result .= "<br>Error: e_signature cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterapplication WHERE name=? AND iD!=?";
            $records = Rosterapplication::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterapplication::where('iD', $recordiD)[0];
            
            $record->user = $user;
            $record->applicationtrack = $applicationtrack;
            $record->applicationstatus = $applicationstatus;
            $record->primaryfunction = $primaryfunction;
            $record->legal_name = $legal_name;
            $record->preferred_name = $preferred_name;
            $record->email = $email;
            $record->mobile_number = $mobile_number;
            $record->whatsapp_number = $whatsapp_number;
            $record->date_of_birth = $date_of_birth;
            $record->gender = $gender;
            $record->city = $city;
            $record->suburb = $suburb;
            $record->zimprovince = $zimprovince;
            $record->country = $country;
            $record->nationality = $nationality;
            $record->workrightstatus = $workrightstatus;
            $record->work_permit_number = $work_permit_number;
            $record->work_permit_expiry = $work_permit_expiry;
            $record->has_disability_adjustment = $has_disability_adjustment;
            $record->adjustment_details = $adjustment_details;
            $record->how_heard = $how_heard;
            $record->referred_by = $referred_by;
            $record->consent_version = $consent_version;
            $record->consent_timestamp = $consent_timestamp;
            $record->consent_ip_address = $consent_ip_address;
            $record->e_signature = $e_signature;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}