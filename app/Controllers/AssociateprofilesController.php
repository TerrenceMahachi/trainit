<?php
namespace App\Controllers;

use App\Models\Associateprofile;
use App\Models\Database;

class AssociateprofilesController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['employmentstatus']) && $_POST['employmentstatus'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' employmentstatus=' . $_POST['employmentstatus'];
}
if (isset($_POST['invoiceentitytype']) && $_POST['invoiceentitytype'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' invoiceentitytype=' . $_POST['invoiceentitytype'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`years_experience` LIKE $escapedTerm OR `donor_experience_years` LIKE $escapedTerm OR `donors_worked_with` LIKE $escapedTerm OR `largest_budget_handled` LIKE $escapedTerm OR `largest_team_supervised` LIKE $escapedTerm OR `largest_endpoints_supported` LIKE $escapedTerm OR `largest_dataset_managed` LIKE $escapedTerm OR `supervised_juniors_before` LIKE $escapedTerm OR `led_audits_or_evaluations` LIKE $escapedTerm OR `rejected_work_experience` LIKE $escapedTerm OR `day_rate_expectation` LIKE $escapedTerm OR `capacity_days_per_month` LIKE $escapedTerm OR `notice_period` LIKE $escapedTerm OR `has_tax_clearance_itf263` LIKE $escapedTerm OR `zimra_bp_number` LIKE $escapedTerm OR `tax_clearance_doc` LIKE $escapedTerm OR `is_vat_registered` LIKE $escapedTerm OR `vat_number` LIKE $escapedTerm OR `has_indemnity_insurance` LIKE $escapedTerm OR `insurance_cover_amount` LIKE $escapedTerm OR `conflict_of_interest` LIKE $escapedTerm OR `moonlighting_restrictions` LIKE $escapedTerm OR `cv_bid_consent` LIKE $escapedTerm OR `restricted_sectors_or_donors` LIKE $escapedTerm OR `public_website_listing_consent` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Associateprofile::page($selected_page, $page_size, $search, $order_by);
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
        $employmentstatus = $_POST["employmentstatus"];
        $years_experience = $_POST["years_experience"];
        $donor_experience_years = $_POST["donor_experience_years"];
        $donors_worked_with = $_POST["donors_worked_with"];
        $largest_budget_handled = $_POST["largest_budget_handled"];
        $largest_team_supervised = $_POST["largest_team_supervised"];
        $largest_endpoints_supported = $_POST["largest_endpoints_supported"];
        $largest_dataset_managed = $_POST["largest_dataset_managed"];
        $supervised_juniors_before = $_POST["supervised_juniors_before"];
        $led_audits_or_evaluations = $_POST["led_audits_or_evaluations"];
        $rejected_work_experience = $_POST["rejected_work_experience"];
        $day_rate_expectation = $_POST["day_rate_expectation"];
        $capacity_days_per_month = $_POST["capacity_days_per_month"];
        $notice_period = $_POST["notice_period"];
        $invoiceentitytype = $_POST["invoiceentitytype"];
        $has_tax_clearance_itf263 = $_POST["has_tax_clearance_itf263"];
        $zimra_bp_number = $_POST["zimra_bp_number"];
        $tax_clearance_doc = $_POST["tax_clearance_doc"];
        $is_vat_registered = $_POST["is_vat_registered"];
        $vat_number = $_POST["vat_number"];
        $has_indemnity_insurance = $_POST["has_indemnity_insurance"];
        $insurance_cover_amount = $_POST["insurance_cover_amount"];
        $conflict_of_interest = $_POST["conflict_of_interest"];
        $moonlighting_restrictions = $_POST["moonlighting_restrictions"];
        $cv_bid_consent = $_POST["cv_bid_consent"];
        $restricted_sectors_or_donors = $_POST["restricted_sectors_or_donors"];
        $public_website_listing_consent = $_POST["public_website_listing_consent"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($employmentstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_employmentstatus "; $_error = true; }
        if ($years_experience == "" && (!$_error)) {  $_result .= "<br>Error: years_experience cannot be blank"; $_error = true; }
        if ($donor_experience_years == "" && (!$_error)) {  $_result .= "<br>Error: donor_experience_years cannot be blank"; $_error = true; }
        if ($donors_worked_with == "" && (!$_error)) {  $_result .= "<br>Error: donors_worked_with cannot be blank"; $_error = true; }
        if ($largest_budget_handled == "" && (!$_error)) {  $_result .= "<br>Error: largest_budget_handled cannot be blank"; $_error = true; }
        if ($largest_team_supervised == "" && (!$_error)) {  $_result .= "<br>Error: largest_team_supervised cannot be blank"; $_error = true; }
        if ($largest_endpoints_supported == "" && (!$_error)) {  $_result .= "<br>Error: largest_endpoints_supported cannot be blank"; $_error = true; }
        if ($largest_dataset_managed == "" && (!$_error)) {  $_result .= "<br>Error: largest_dataset_managed cannot be blank"; $_error = true; }
        if ($supervised_juniors_before == "" && (!$_error)) {  $_result .= "<br>Error: supervised_juniors_before cannot be blank"; $_error = true; }
        if ($led_audits_or_evaluations == "" && (!$_error)) {  $_result .= "<br>Error: led_audits_or_evaluations cannot be blank"; $_error = true; }
        if ($rejected_work_experience == "" && (!$_error)) {  $_result .= "<br>Error: rejected_work_experience cannot be blank"; $_error = true; }
        if ($day_rate_expectation == "" && (!$_error)) {  $_result .= "<br>Error: day_rate_expectation cannot be blank"; $_error = true; }
        if ($capacity_days_per_month == "" && (!$_error)) {  $_result .= "<br>Error: capacity_days_per_month cannot be blank"; $_error = true; }
        if ($notice_period == "" && (!$_error)) {  $_result .= "<br>Error: notice_period cannot be blank"; $_error = true; }
        if ($invoiceentitytype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_invoiceentitytype "; $_error = true; }
        if ($has_tax_clearance_itf263 == "" && (!$_error)) {  $_result .= "<br>Error: has_tax_clearance_itf263 cannot be blank"; $_error = true; }
        if ($zimra_bp_number == "" && (!$_error)) {  $_result .= "<br>Error: zimra_bp_number cannot be blank"; $_error = true; }
        if ($tax_clearance_doc == "" && (!$_error)) {  $_result .= "<br>Error: tax_clearance_doc cannot be blank"; $_error = true; }
        if ($is_vat_registered == "" && (!$_error)) {  $_result .= "<br>Error: is_vat_registered cannot be blank"; $_error = true; }
        if ($vat_number == "" && (!$_error)) {  $_result .= "<br>Error: vat_number cannot be blank"; $_error = true; }
        if ($has_indemnity_insurance == "" && (!$_error)) {  $_result .= "<br>Error: has_indemnity_insurance cannot be blank"; $_error = true; }
        if ($insurance_cover_amount == "" && (!$_error)) {  $_result .= "<br>Error: insurance_cover_amount cannot be blank"; $_error = true; }
        if ($conflict_of_interest == "" && (!$_error)) {  $_result .= "<br>Error: conflict_of_interest cannot be blank"; $_error = true; }
        if ($moonlighting_restrictions == "" && (!$_error)) {  $_result .= "<br>Error: moonlighting_restrictions cannot be blank"; $_error = true; }
        if ($cv_bid_consent == "" && (!$_error)) {  $_result .= "<br>Error: cv_bid_consent cannot be blank"; $_error = true; }
        if ($restricted_sectors_or_donors == "" && (!$_error)) {  $_result .= "<br>Error: restricted_sectors_or_donors cannot be blank"; $_error = true; }
        if ($public_website_listing_consent == "" && (!$_error)) {  $_result .= "<br>Error: public_website_listing_consent cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM associateprofile WHERE name=?";
            $records = Associateprofile::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Associateprofile();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->employmentstatus = $employmentstatus;
            $record->years_experience = $years_experience;
            $record->donor_experience_years = $donor_experience_years;
            $record->donors_worked_with = $donors_worked_with;
            $record->largest_budget_handled = $largest_budget_handled;
            $record->largest_team_supervised = $largest_team_supervised;
            $record->largest_endpoints_supported = $largest_endpoints_supported;
            $record->largest_dataset_managed = $largest_dataset_managed;
            $record->supervised_juniors_before = $supervised_juniors_before;
            $record->led_audits_or_evaluations = $led_audits_or_evaluations;
            $record->rejected_work_experience = $rejected_work_experience;
            $record->day_rate_expectation = $day_rate_expectation;
            $record->capacity_days_per_month = $capacity_days_per_month;
            $record->notice_period = $notice_period;
            $record->invoiceentitytype = $invoiceentitytype;
            $record->has_tax_clearance_itf263 = $has_tax_clearance_itf263;
            $record->zimra_bp_number = $zimra_bp_number;
            $record->tax_clearance_doc = $tax_clearance_doc;
            $record->is_vat_registered = $is_vat_registered;
            $record->vat_number = $vat_number;
            $record->has_indemnity_insurance = $has_indemnity_insurance;
            $record->insurance_cover_amount = $insurance_cover_amount;
            $record->conflict_of_interest = $conflict_of_interest;
            $record->moonlighting_restrictions = $moonlighting_restrictions;
            $record->cv_bid_consent = $cv_bid_consent;
            $record->restricted_sectors_or_donors = $restricted_sectors_or_donors;
            $record->public_website_listing_consent = $public_website_listing_consent;
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
        $employmentstatus = $_POST["employmentstatus"];
        $years_experience = $_POST["years_experience"];
        $donor_experience_years = $_POST["donor_experience_years"];
        $donors_worked_with = $_POST["donors_worked_with"];
        $largest_budget_handled = $_POST["largest_budget_handled"];
        $largest_team_supervised = $_POST["largest_team_supervised"];
        $largest_endpoints_supported = $_POST["largest_endpoints_supported"];
        $largest_dataset_managed = $_POST["largest_dataset_managed"];
        $supervised_juniors_before = $_POST["supervised_juniors_before"];
        $led_audits_or_evaluations = $_POST["led_audits_or_evaluations"];
        $rejected_work_experience = $_POST["rejected_work_experience"];
        $day_rate_expectation = $_POST["day_rate_expectation"];
        $capacity_days_per_month = $_POST["capacity_days_per_month"];
        $notice_period = $_POST["notice_period"];
        $invoiceentitytype = $_POST["invoiceentitytype"];
        $has_tax_clearance_itf263 = $_POST["has_tax_clearance_itf263"];
        $zimra_bp_number = $_POST["zimra_bp_number"];
        $tax_clearance_doc = $_POST["tax_clearance_doc"];
        $is_vat_registered = $_POST["is_vat_registered"];
        $vat_number = $_POST["vat_number"];
        $has_indemnity_insurance = $_POST["has_indemnity_insurance"];
        $insurance_cover_amount = $_POST["insurance_cover_amount"];
        $conflict_of_interest = $_POST["conflict_of_interest"];
        $moonlighting_restrictions = $_POST["moonlighting_restrictions"];
        $cv_bid_consent = $_POST["cv_bid_consent"];
        $restricted_sectors_or_donors = $_POST["restricted_sectors_or_donors"];
        $public_website_listing_consent = $_POST["public_website_listing_consent"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($employmentstatus == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_employmentstatus "; $_error = true; }
        if ($years_experience == "" && (!$_error)) {  $_result .= "<br>Error: years_experience cannot be blank"; $_error = true; }
        if ($donor_experience_years == "" && (!$_error)) {  $_result .= "<br>Error: donor_experience_years cannot be blank"; $_error = true; }
        if ($donors_worked_with == "" && (!$_error)) {  $_result .= "<br>Error: donors_worked_with cannot be blank"; $_error = true; }
        if ($largest_budget_handled == "" && (!$_error)) {  $_result .= "<br>Error: largest_budget_handled cannot be blank"; $_error = true; }
        if ($largest_team_supervised == "" && (!$_error)) {  $_result .= "<br>Error: largest_team_supervised cannot be blank"; $_error = true; }
        if ($largest_endpoints_supported == "" && (!$_error)) {  $_result .= "<br>Error: largest_endpoints_supported cannot be blank"; $_error = true; }
        if ($largest_dataset_managed == "" && (!$_error)) {  $_result .= "<br>Error: largest_dataset_managed cannot be blank"; $_error = true; }
        if ($supervised_juniors_before == "" && (!$_error)) {  $_result .= "<br>Error: supervised_juniors_before cannot be blank"; $_error = true; }
        if ($led_audits_or_evaluations == "" && (!$_error)) {  $_result .= "<br>Error: led_audits_or_evaluations cannot be blank"; $_error = true; }
        if ($rejected_work_experience == "" && (!$_error)) {  $_result .= "<br>Error: rejected_work_experience cannot be blank"; $_error = true; }
        if ($day_rate_expectation == "" && (!$_error)) {  $_result .= "<br>Error: day_rate_expectation cannot be blank"; $_error = true; }
        if ($capacity_days_per_month == "" && (!$_error)) {  $_result .= "<br>Error: capacity_days_per_month cannot be blank"; $_error = true; }
        if ($notice_period == "" && (!$_error)) {  $_result .= "<br>Error: notice_period cannot be blank"; $_error = true; }
        if ($invoiceentitytype == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_invoiceentitytype "; $_error = true; }
        if ($has_tax_clearance_itf263 == "" && (!$_error)) {  $_result .= "<br>Error: has_tax_clearance_itf263 cannot be blank"; $_error = true; }
        if ($zimra_bp_number == "" && (!$_error)) {  $_result .= "<br>Error: zimra_bp_number cannot be blank"; $_error = true; }
        if ($tax_clearance_doc == "" && (!$_error)) {  $_result .= "<br>Error: tax_clearance_doc cannot be blank"; $_error = true; }
        if ($is_vat_registered == "" && (!$_error)) {  $_result .= "<br>Error: is_vat_registered cannot be blank"; $_error = true; }
        if ($vat_number == "" && (!$_error)) {  $_result .= "<br>Error: vat_number cannot be blank"; $_error = true; }
        if ($has_indemnity_insurance == "" && (!$_error)) {  $_result .= "<br>Error: has_indemnity_insurance cannot be blank"; $_error = true; }
        if ($insurance_cover_amount == "" && (!$_error)) {  $_result .= "<br>Error: insurance_cover_amount cannot be blank"; $_error = true; }
        if ($conflict_of_interest == "" && (!$_error)) {  $_result .= "<br>Error: conflict_of_interest cannot be blank"; $_error = true; }
        if ($moonlighting_restrictions == "" && (!$_error)) {  $_result .= "<br>Error: moonlighting_restrictions cannot be blank"; $_error = true; }
        if ($cv_bid_consent == "" && (!$_error)) {  $_result .= "<br>Error: cv_bid_consent cannot be blank"; $_error = true; }
        if ($restricted_sectors_or_donors == "" && (!$_error)) {  $_result .= "<br>Error: restricted_sectors_or_donors cannot be blank"; $_error = true; }
        if ($public_website_listing_consent == "" && (!$_error)) {  $_result .= "<br>Error: public_website_listing_consent cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM associateprofile WHERE name=? AND iD!=?";
            $records = Associateprofile::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Associateprofile::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->employmentstatus = $employmentstatus;
            $record->years_experience = $years_experience;
            $record->donor_experience_years = $donor_experience_years;
            $record->donors_worked_with = $donors_worked_with;
            $record->largest_budget_handled = $largest_budget_handled;
            $record->largest_team_supervised = $largest_team_supervised;
            $record->largest_endpoints_supported = $largest_endpoints_supported;
            $record->largest_dataset_managed = $largest_dataset_managed;
            $record->supervised_juniors_before = $supervised_juniors_before;
            $record->led_audits_or_evaluations = $led_audits_or_evaluations;
            $record->rejected_work_experience = $rejected_work_experience;
            $record->day_rate_expectation = $day_rate_expectation;
            $record->capacity_days_per_month = $capacity_days_per_month;
            $record->notice_period = $notice_period;
            $record->invoiceentitytype = $invoiceentitytype;
            $record->has_tax_clearance_itf263 = $has_tax_clearance_itf263;
            $record->zimra_bp_number = $zimra_bp_number;
            $record->tax_clearance_doc = $tax_clearance_doc;
            $record->is_vat_registered = $is_vat_registered;
            $record->vat_number = $vat_number;
            $record->has_indemnity_insurance = $has_indemnity_insurance;
            $record->insurance_cover_amount = $insurance_cover_amount;
            $record->conflict_of_interest = $conflict_of_interest;
            $record->moonlighting_restrictions = $moonlighting_restrictions;
            $record->cv_bid_consent = $cv_bid_consent;
            $record->restricted_sectors_or_donors = $restricted_sectors_or_donors;
            $record->public_website_listing_consent = $public_website_listing_consent;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}