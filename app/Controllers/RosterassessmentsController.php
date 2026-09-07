<?php
namespace App\Controllers;

use App\Models\Rosterassessment;
use App\Models\Database;

class RosterassessmentsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['reviewer']) && $_POST['reviewer'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' reviewer=' . $_POST['reviewer'];
}
if (isset($_POST['vettingrecommendation']) && $_POST['vettingrecommendation'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' vettingrecommendation=' . $_POST['vettingrecommendation'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`eligibility_gate_passed` LIKE $escapedTerm OR `technical_fit_score` LIKE $escapedTerm OR `evidence_score` LIKE $escapedTerm OR `judgement_score` LIKE $escapedTerm OR `availability_score` LIKE $escapedTerm OR `motivation_score` LIKE $escapedTerm OR `total_score` LIKE $escapedTerm OR `automated_red_flags` LIKE $escapedTerm OR `interview_notes` LIKE $escapedTerm OR `technical_test_result` LIKE $escapedTerm OR `vetted_at` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterassessment::page($selected_page, $page_size, $search, $order_by);
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
        $reviewer = $_POST["reviewer"];
        $vettingrecommendation = $_POST["vettingrecommendation"];
        $eligibility_gate_passed = $_POST["eligibility_gate_passed"];
        $technical_fit_score = $_POST["technical_fit_score"];
        $evidence_score = $_POST["evidence_score"];
        $judgement_score = $_POST["judgement_score"];
        $availability_score = $_POST["availability_score"];
        $motivation_score = $_POST["motivation_score"];
        $total_score = $_POST["total_score"];
        $automated_red_flags = $_POST["automated_red_flags"];
        $interview_notes = $_POST["interview_notes"];
        $technical_test_result = $_POST["technical_test_result"];
        $vetted_at = $_POST["vetted_at"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($reviewer == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_reviewer "; $_error = true; }
        if ($vettingrecommendation == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_vettingrecommendation "; $_error = true; }
        if ($eligibility_gate_passed == "" && (!$_error)) {  $_result .= "<br>Error: eligibility_gate_passed cannot be blank"; $_error = true; }
        if ($technical_fit_score == "" && (!$_error)) {  $_result .= "<br>Error: technical_fit_score cannot be blank"; $_error = true; }
        if ($evidence_score == "" && (!$_error)) {  $_result .= "<br>Error: evidence_score cannot be blank"; $_error = true; }
        if ($judgement_score == "" && (!$_error)) {  $_result .= "<br>Error: judgement_score cannot be blank"; $_error = true; }
        if ($availability_score == "" && (!$_error)) {  $_result .= "<br>Error: availability_score cannot be blank"; $_error = true; }
        if ($motivation_score == "" && (!$_error)) {  $_result .= "<br>Error: motivation_score cannot be blank"; $_error = true; }
        if ($total_score == "" && (!$_error)) {  $_result .= "<br>Error: total_score cannot be blank"; $_error = true; }
        if ($automated_red_flags == "" && (!$_error)) {  $_result .= "<br>Error: automated_red_flags cannot be blank"; $_error = true; }
        if ($interview_notes == "" && (!$_error)) {  $_result .= "<br>Error: interview_notes cannot be blank"; $_error = true; }
        if ($technical_test_result == "" && (!$_error)) {  $_result .= "<br>Error: technical_test_result cannot be blank"; $_error = true; }
        if ($vetted_at == "" && (!$_error)) {  $_result .= "<br>Error: vetted_at cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterassessment WHERE name=?";
            $records = Rosterassessment::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterassessment();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->reviewer = $reviewer;
            $record->vettingrecommendation = $vettingrecommendation;
            $record->eligibility_gate_passed = $eligibility_gate_passed;
            $record->technical_fit_score = $technical_fit_score;
            $record->evidence_score = $evidence_score;
            $record->judgement_score = $judgement_score;
            $record->availability_score = $availability_score;
            $record->motivation_score = $motivation_score;
            $record->total_score = $total_score;
            $record->automated_red_flags = $automated_red_flags;
            $record->interview_notes = $interview_notes;
            $record->technical_test_result = $technical_test_result;
            $record->vetted_at = $vetted_at;
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
        $reviewer = $_POST["reviewer"];
        $vettingrecommendation = $_POST["vettingrecommendation"];
        $eligibility_gate_passed = $_POST["eligibility_gate_passed"];
        $technical_fit_score = $_POST["technical_fit_score"];
        $evidence_score = $_POST["evidence_score"];
        $judgement_score = $_POST["judgement_score"];
        $availability_score = $_POST["availability_score"];
        $motivation_score = $_POST["motivation_score"];
        $total_score = $_POST["total_score"];
        $automated_red_flags = $_POST["automated_red_flags"];
        $interview_notes = $_POST["interview_notes"];
        $technical_test_result = $_POST["technical_test_result"];
        $vetted_at = $_POST["vetted_at"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($reviewer == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_reviewer "; $_error = true; }
        if ($vettingrecommendation == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_vettingrecommendation "; $_error = true; }
        if ($eligibility_gate_passed == "" && (!$_error)) {  $_result .= "<br>Error: eligibility_gate_passed cannot be blank"; $_error = true; }
        if ($technical_fit_score == "" && (!$_error)) {  $_result .= "<br>Error: technical_fit_score cannot be blank"; $_error = true; }
        if ($evidence_score == "" && (!$_error)) {  $_result .= "<br>Error: evidence_score cannot be blank"; $_error = true; }
        if ($judgement_score == "" && (!$_error)) {  $_result .= "<br>Error: judgement_score cannot be blank"; $_error = true; }
        if ($availability_score == "" && (!$_error)) {  $_result .= "<br>Error: availability_score cannot be blank"; $_error = true; }
        if ($motivation_score == "" && (!$_error)) {  $_result .= "<br>Error: motivation_score cannot be blank"; $_error = true; }
        if ($total_score == "" && (!$_error)) {  $_result .= "<br>Error: total_score cannot be blank"; $_error = true; }
        if ($automated_red_flags == "" && (!$_error)) {  $_result .= "<br>Error: automated_red_flags cannot be blank"; $_error = true; }
        if ($interview_notes == "" && (!$_error)) {  $_result .= "<br>Error: interview_notes cannot be blank"; $_error = true; }
        if ($technical_test_result == "" && (!$_error)) {  $_result .= "<br>Error: technical_test_result cannot be blank"; $_error = true; }
        if ($vetted_at == "" && (!$_error)) {  $_result .= "<br>Error: vetted_at cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterassessment WHERE name=? AND iD!=?";
            $records = Rosterassessment::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterassessment::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->reviewer = $reviewer;
            $record->vettingrecommendation = $vettingrecommendation;
            $record->eligibility_gate_passed = $eligibility_gate_passed;
            $record->technical_fit_score = $technical_fit_score;
            $record->evidence_score = $evidence_score;
            $record->judgement_score = $judgement_score;
            $record->availability_score = $availability_score;
            $record->motivation_score = $motivation_score;
            $record->total_score = $total_score;
            $record->automated_red_flags = $automated_red_flags;
            $record->interview_notes = $interview_notes;
            $record->technical_test_result = $technical_test_result;
            $record->vetted_at = $vetted_at;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}