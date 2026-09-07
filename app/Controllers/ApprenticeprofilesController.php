<?php
namespace App\Controllers;

use App\Models\Apprenticeprofile;
use App\Models\Database;

class ApprenticeprofilesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['apprenticestatus']) && $_POST['apprenticestatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' apprenticestatus=' . $_POST['apprenticestatus'];
}
if (isset($_POST['engagementmodel']) && $_POST['engagementmodel'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' engagementmodel=' . $_POST['engagementmodel'];
}
if (isset($_POST['worklocationpreference']) && $_POST['worklocationpreference'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' worklocationpreference=' . $_POST['worklocationpreference'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`institution_name` LIKE $escapedTerm OR `degree_programme` LIKE $escapedTerm OR `study_level` LIKE $escapedTerm OR `student_reg_number` LIKE $escapedTerm OR `expected_completion_date` LIKE $escapedTerm OR `is_wrl_attachment` LIKE $escapedTerm OR `wrl_start_date` LIKE $escapedTerm OR `wrl_end_date` LIKE $escapedTerm OR `wrl_duration_months` LIKE $escapedTerm OR `wrl_coordinator_name` LIKE $escapedTerm OR `wrl_coordinator_email` LIKE $escapedTerm OR `wrl_coordinator_phone` LIKE $escapedTerm OR `requires_placement_letter` LIKE $escapedTerm OR `requires_host_mou` LIKE $escapedTerm OR `requires_logbook_visits` LIKE $escapedTerm OR `requires_host_insurance` LIKE $escapedTerm OR `min_stipend_required` LIKE $escapedTerm OR `proof_of_registration_doc` LIKE $escapedTerm OR `transcript_doc` LIKE $escapedTerm OR `current_average_grade` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Apprenticeprofile::page($selected_page, $page_size, $search, $order_by);
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
        $apprenticestatus = $_POST["apprenticestatus"];
        $institution_name = $_POST["institution_name"];
        $degree_programme = $_POST["degree_programme"];
        $study_level = $_POST["study_level"];
        $student_reg_number = $_POST["student_reg_number"];
        $expected_completion_date = $_POST["expected_completion_date"];
        $is_wrl_attachment = $_POST["is_wrl_attachment"];
        $wrl_start_date = $_POST["wrl_start_date"];
        $wrl_end_date = $_POST["wrl_end_date"];
        $wrl_duration_months = $_POST["wrl_duration_months"];
        $wrl_coordinator_name = $_POST["wrl_coordinator_name"];
        $wrl_coordinator_email = $_POST["wrl_coordinator_email"];
        $wrl_coordinator_phone = $_POST["wrl_coordinator_phone"];
        $requires_placement_letter = $_POST["requires_placement_letter"];
        $requires_host_mou = $_POST["requires_host_mou"];
        $requires_logbook_visits = $_POST["requires_logbook_visits"];
        $requires_host_insurance = $_POST["requires_host_insurance"];
        $min_stipend_required = $_POST["min_stipend_required"];
        $engagementmodel = $_POST["engagementmodel"];
        $worklocationpreference = $_POST["worklocationpreference"];
        $proof_of_registration_doc = $_POST["proof_of_registration_doc"];
        $transcript_doc = $_POST["transcript_doc"];
        $current_average_grade = $_POST["current_average_grade"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($apprenticestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_apprenticestatus "; $_error = true; }
        if ($institution_name == "" && (!$_error)) {  $_result .= "<br>Error: institution_name cannot be blank"; $_error = true; }
        if ($degree_programme == "" && (!$_error)) {  $_result .= "<br>Error: degree_programme cannot be blank"; $_error = true; }
        if ($study_level == "" && (!$_error)) {  $_result .= "<br>Error: study_level cannot be blank"; $_error = true; }
        if ($student_reg_number == "" && (!$_error)) {  $_result .= "<br>Error: student_reg_number cannot be blank"; $_error = true; }
        if ($expected_completion_date == "" && (!$_error)) {  $_result .= "<br>Error: expected_completion_date cannot be blank"; $_error = true; }
        if ($is_wrl_attachment == "" && (!$_error)) {  $_result .= "<br>Error: is_wrl_attachment cannot be blank"; $_error = true; }
        if ($wrl_start_date == "" && (!$_error)) {  $_result .= "<br>Error: wrl_start_date cannot be blank"; $_error = true; }
        if ($wrl_end_date == "" && (!$_error)) {  $_result .= "<br>Error: wrl_end_date cannot be blank"; $_error = true; }
        if ($wrl_duration_months == "" && (!$_error)) {  $_result .= "<br>Error: wrl_duration_months cannot be blank"; $_error = true; }
        if ($wrl_coordinator_name == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_name cannot be blank"; $_error = true; }
        if ($wrl_coordinator_email == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_email cannot be blank"; $_error = true; }
        if ($wrl_coordinator_phone == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_phone cannot be blank"; $_error = true; }
        if ($requires_placement_letter == "" && (!$_error)) {  $_result .= "<br>Error: requires_placement_letter cannot be blank"; $_error = true; }
        if ($requires_host_mou == "" && (!$_error)) {  $_result .= "<br>Error: requires_host_mou cannot be blank"; $_error = true; }
        if ($requires_logbook_visits == "" && (!$_error)) {  $_result .= "<br>Error: requires_logbook_visits cannot be blank"; $_error = true; }
        if ($requires_host_insurance == "" && (!$_error)) {  $_result .= "<br>Error: requires_host_insurance cannot be blank"; $_error = true; }
        if ($min_stipend_required == "" && (!$_error)) {  $_result .= "<br>Error: min_stipend_required cannot be blank"; $_error = true; }
        if ($engagementmodel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_engagementmodel "; $_error = true; }
        if ($worklocationpreference == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_worklocationpreference "; $_error = true; }
        if ($proof_of_registration_doc == "" && (!$_error)) {  $_result .= "<br>Error: proof_of_registration_doc cannot be blank"; $_error = true; }
        if ($transcript_doc == "" && (!$_error)) {  $_result .= "<br>Error: transcript_doc cannot be blank"; $_error = true; }
        if ($current_average_grade == "" && (!$_error)) {  $_result .= "<br>Error: current_average_grade cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM apprenticeprofile WHERE name=?";
            $records = Apprenticeprofile::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Apprenticeprofile();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->apprenticestatus = $apprenticestatus;
            $record->institution_name = $institution_name;
            $record->degree_programme = $degree_programme;
            $record->study_level = $study_level;
            $record->student_reg_number = $student_reg_number;
            $record->expected_completion_date = $expected_completion_date;
            $record->is_wrl_attachment = $is_wrl_attachment;
            $record->wrl_start_date = $wrl_start_date;
            $record->wrl_end_date = $wrl_end_date;
            $record->wrl_duration_months = $wrl_duration_months;
            $record->wrl_coordinator_name = $wrl_coordinator_name;
            $record->wrl_coordinator_email = $wrl_coordinator_email;
            $record->wrl_coordinator_phone = $wrl_coordinator_phone;
            $record->requires_placement_letter = $requires_placement_letter;
            $record->requires_host_mou = $requires_host_mou;
            $record->requires_logbook_visits = $requires_logbook_visits;
            $record->requires_host_insurance = $requires_host_insurance;
            $record->min_stipend_required = $min_stipend_required;
            $record->engagementmodel = $engagementmodel;
            $record->worklocationpreference = $worklocationpreference;
            $record->proof_of_registration_doc = $proof_of_registration_doc;
            $record->transcript_doc = $transcript_doc;
            $record->current_average_grade = $current_average_grade;
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
        $apprenticestatus = $_POST["apprenticestatus"];
        $institution_name = $_POST["institution_name"];
        $degree_programme = $_POST["degree_programme"];
        $study_level = $_POST["study_level"];
        $student_reg_number = $_POST["student_reg_number"];
        $expected_completion_date = $_POST["expected_completion_date"];
        $is_wrl_attachment = $_POST["is_wrl_attachment"];
        $wrl_start_date = $_POST["wrl_start_date"];
        $wrl_end_date = $_POST["wrl_end_date"];
        $wrl_duration_months = $_POST["wrl_duration_months"];
        $wrl_coordinator_name = $_POST["wrl_coordinator_name"];
        $wrl_coordinator_email = $_POST["wrl_coordinator_email"];
        $wrl_coordinator_phone = $_POST["wrl_coordinator_phone"];
        $requires_placement_letter = $_POST["requires_placement_letter"];
        $requires_host_mou = $_POST["requires_host_mou"];
        $requires_logbook_visits = $_POST["requires_logbook_visits"];
        $requires_host_insurance = $_POST["requires_host_insurance"];
        $min_stipend_required = $_POST["min_stipend_required"];
        $engagementmodel = $_POST["engagementmodel"];
        $worklocationpreference = $_POST["worklocationpreference"];
        $proof_of_registration_doc = $_POST["proof_of_registration_doc"];
        $transcript_doc = $_POST["transcript_doc"];
        $current_average_grade = $_POST["current_average_grade"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($apprenticestatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_apprenticestatus "; $_error = true; }
        if ($institution_name == "" && (!$_error)) {  $_result .= "<br>Error: institution_name cannot be blank"; $_error = true; }
        if ($degree_programme == "" && (!$_error)) {  $_result .= "<br>Error: degree_programme cannot be blank"; $_error = true; }
        if ($study_level == "" && (!$_error)) {  $_result .= "<br>Error: study_level cannot be blank"; $_error = true; }
        if ($student_reg_number == "" && (!$_error)) {  $_result .= "<br>Error: student_reg_number cannot be blank"; $_error = true; }
        if ($expected_completion_date == "" && (!$_error)) {  $_result .= "<br>Error: expected_completion_date cannot be blank"; $_error = true; }
        if ($is_wrl_attachment == "" && (!$_error)) {  $_result .= "<br>Error: is_wrl_attachment cannot be blank"; $_error = true; }
        if ($wrl_start_date == "" && (!$_error)) {  $_result .= "<br>Error: wrl_start_date cannot be blank"; $_error = true; }
        if ($wrl_end_date == "" && (!$_error)) {  $_result .= "<br>Error: wrl_end_date cannot be blank"; $_error = true; }
        if ($wrl_duration_months == "" && (!$_error)) {  $_result .= "<br>Error: wrl_duration_months cannot be blank"; $_error = true; }
        if ($wrl_coordinator_name == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_name cannot be blank"; $_error = true; }
        if ($wrl_coordinator_email == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_email cannot be blank"; $_error = true; }
        if ($wrl_coordinator_phone == "" && (!$_error)) {  $_result .= "<br>Error: wrl_coordinator_phone cannot be blank"; $_error = true; }
        if ($requires_placement_letter == "" && (!$_error)) {  $_result .= "<br>Error: requires_placement_letter cannot be blank"; $_error = true; }
        if ($requires_host_mou == "" && (!$_error)) {  $_result .= "<br>Error: requires_host_mou cannot be blank"; $_error = true; }
        if ($requires_logbook_visits == "" && (!$_error)) {  $_result .= "<br>Error: requires_logbook_visits cannot be blank"; $_error = true; }
        if ($requires_host_insurance == "" && (!$_error)) {  $_result .= "<br>Error: requires_host_insurance cannot be blank"; $_error = true; }
        if ($min_stipend_required == "" && (!$_error)) {  $_result .= "<br>Error: min_stipend_required cannot be blank"; $_error = true; }
        if ($engagementmodel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_engagementmodel "; $_error = true; }
        if ($worklocationpreference == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_worklocationpreference "; $_error = true; }
        if ($proof_of_registration_doc == "" && (!$_error)) {  $_result .= "<br>Error: proof_of_registration_doc cannot be blank"; $_error = true; }
        if ($transcript_doc == "" && (!$_error)) {  $_result .= "<br>Error: transcript_doc cannot be blank"; $_error = true; }
        if ($current_average_grade == "" && (!$_error)) {  $_result .= "<br>Error: current_average_grade cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM apprenticeprofile WHERE name=? AND iD!=?";
            $records = Apprenticeprofile::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Apprenticeprofile::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->apprenticestatus = $apprenticestatus;
            $record->institution_name = $institution_name;
            $record->degree_programme = $degree_programme;
            $record->study_level = $study_level;
            $record->student_reg_number = $student_reg_number;
            $record->expected_completion_date = $expected_completion_date;
            $record->is_wrl_attachment = $is_wrl_attachment;
            $record->wrl_start_date = $wrl_start_date;
            $record->wrl_end_date = $wrl_end_date;
            $record->wrl_duration_months = $wrl_duration_months;
            $record->wrl_coordinator_name = $wrl_coordinator_name;
            $record->wrl_coordinator_email = $wrl_coordinator_email;
            $record->wrl_coordinator_phone = $wrl_coordinator_phone;
            $record->requires_placement_letter = $requires_placement_letter;
            $record->requires_host_mou = $requires_host_mou;
            $record->requires_logbook_visits = $requires_logbook_visits;
            $record->requires_host_insurance = $requires_host_insurance;
            $record->min_stipend_required = $min_stipend_required;
            $record->engagementmodel = $engagementmodel;
            $record->worklocationpreference = $worklocationpreference;
            $record->proof_of_registration_doc = $proof_of_registration_doc;
            $record->transcript_doc = $transcript_doc;
            $record->current_average_grade = $current_average_grade;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}