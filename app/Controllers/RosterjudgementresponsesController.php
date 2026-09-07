<?php
namespace App\Controllers;

use App\Models\Rosterjudgementresponse;
use App\Models\Database;

class RosterjudgementresponsesController
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
            $search .= "(`motivation_narrative` LIKE $escapedTerm OR `primary_function_evidence` LIKE $escapedTerm OR `shared_client_management_plan` LIKE $escapedTerm OR `error_discovery_resolution` LIKE $escapedTerm OR `urgent_friday_deadline_dilemma` LIKE $escapedTerm OR `associate_apprentice_qa_methodology` LIKE $escapedTerm OR `associate_unethical_client_solution` LIKE $escapedTerm OR `apprentice_twelve_month_goal` LIKE $escapedTerm OR `additional_notes` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterjudgementresponse::page($selected_page, $page_size, $search, $order_by);
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
        $motivation_narrative = $_POST["motivation_narrative"];
        $primary_function_evidence = $_POST["primary_function_evidence"];
        $shared_client_management_plan = $_POST["shared_client_management_plan"];
        $error_discovery_resolution = $_POST["error_discovery_resolution"];
        $urgent_friday_deadline_dilemma = $_POST["urgent_friday_deadline_dilemma"];
        $associate_apprentice_qa_methodology = $_POST["associate_apprentice_qa_methodology"];
        $associate_unethical_client_solution = $_POST["associate_unethical_client_solution"];
        $apprentice_twelve_month_goal = $_POST["apprentice_twelve_month_goal"];
        $additional_notes = $_POST["additional_notes"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($motivation_narrative == "" && (!$_error)) {  $_result .= "<br>Error: motivation_narrative cannot be blank"; $_error = true; }
        if ($primary_function_evidence == "" && (!$_error)) {  $_result .= "<br>Error: primary_function_evidence cannot be blank"; $_error = true; }
        if ($shared_client_management_plan == "" && (!$_error)) {  $_result .= "<br>Error: shared_client_management_plan cannot be blank"; $_error = true; }
        if ($error_discovery_resolution == "" && (!$_error)) {  $_result .= "<br>Error: error_discovery_resolution cannot be blank"; $_error = true; }
        if ($urgent_friday_deadline_dilemma == "" && (!$_error)) {  $_result .= "<br>Error: urgent_friday_deadline_dilemma cannot be blank"; $_error = true; }
        if ($associate_apprentice_qa_methodology == "" && (!$_error)) {  $_result .= "<br>Error: associate_apprentice_qa_methodology cannot be blank"; $_error = true; }
        if ($associate_unethical_client_solution == "" && (!$_error)) {  $_result .= "<br>Error: associate_unethical_client_solution cannot be blank"; $_error = true; }
        if ($apprentice_twelve_month_goal == "" && (!$_error)) {  $_result .= "<br>Error: apprentice_twelve_month_goal cannot be blank"; $_error = true; }
        if ($additional_notes == "" && (!$_error)) {  $_result .= "<br>Error: additional_notes cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterjudgementresponse WHERE name=?";
            $records = Rosterjudgementresponse::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterjudgementresponse();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->motivation_narrative = $motivation_narrative;
            $record->primary_function_evidence = $primary_function_evidence;
            $record->shared_client_management_plan = $shared_client_management_plan;
            $record->error_discovery_resolution = $error_discovery_resolution;
            $record->urgent_friday_deadline_dilemma = $urgent_friday_deadline_dilemma;
            $record->associate_apprentice_qa_methodology = $associate_apprentice_qa_methodology;
            $record->associate_unethical_client_solution = $associate_unethical_client_solution;
            $record->apprentice_twelve_month_goal = $apprentice_twelve_month_goal;
            $record->additional_notes = $additional_notes;
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
        $motivation_narrative = $_POST["motivation_narrative"];
        $primary_function_evidence = $_POST["primary_function_evidence"];
        $shared_client_management_plan = $_POST["shared_client_management_plan"];
        $error_discovery_resolution = $_POST["error_discovery_resolution"];
        $urgent_friday_deadline_dilemma = $_POST["urgent_friday_deadline_dilemma"];
        $associate_apprentice_qa_methodology = $_POST["associate_apprentice_qa_methodology"];
        $associate_unethical_client_solution = $_POST["associate_unethical_client_solution"];
        $apprentice_twelve_month_goal = $_POST["apprentice_twelve_month_goal"];
        $additional_notes = $_POST["additional_notes"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($motivation_narrative == "" && (!$_error)) {  $_result .= "<br>Error: motivation_narrative cannot be blank"; $_error = true; }
        if ($primary_function_evidence == "" && (!$_error)) {  $_result .= "<br>Error: primary_function_evidence cannot be blank"; $_error = true; }
        if ($shared_client_management_plan == "" && (!$_error)) {  $_result .= "<br>Error: shared_client_management_plan cannot be blank"; $_error = true; }
        if ($error_discovery_resolution == "" && (!$_error)) {  $_result .= "<br>Error: error_discovery_resolution cannot be blank"; $_error = true; }
        if ($urgent_friday_deadline_dilemma == "" && (!$_error)) {  $_result .= "<br>Error: urgent_friday_deadline_dilemma cannot be blank"; $_error = true; }
        if ($associate_apprentice_qa_methodology == "" && (!$_error)) {  $_result .= "<br>Error: associate_apprentice_qa_methodology cannot be blank"; $_error = true; }
        if ($associate_unethical_client_solution == "" && (!$_error)) {  $_result .= "<br>Error: associate_unethical_client_solution cannot be blank"; $_error = true; }
        if ($apprentice_twelve_month_goal == "" && (!$_error)) {  $_result .= "<br>Error: apprentice_twelve_month_goal cannot be blank"; $_error = true; }
        if ($additional_notes == "" && (!$_error)) {  $_result .= "<br>Error: additional_notes cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterjudgementresponse WHERE name=? AND iD!=?";
            $records = Rosterjudgementresponse::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterjudgementresponse::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->motivation_narrative = $motivation_narrative;
            $record->primary_function_evidence = $primary_function_evidence;
            $record->shared_client_management_plan = $shared_client_management_plan;
            $record->error_discovery_resolution = $error_discovery_resolution;
            $record->urgent_friday_deadline_dilemma = $urgent_friday_deadline_dilemma;
            $record->associate_apprentice_qa_methodology = $associate_apprentice_qa_methodology;
            $record->associate_unethical_client_solution = $associate_unethical_client_solution;
            $record->apprentice_twelve_month_goal = $apprentice_twelve_month_goal;
            $record->additional_notes = $additional_notes;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}