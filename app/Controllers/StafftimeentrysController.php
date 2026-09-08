<?php
namespace App\Controllers;

use App\Models\Stafftimeentry;
use App\Models\Database;

class StafftimeentrysController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['staffprofile']) && $_POST['staffprofile'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' staffprofile=' . $_POST['staffprofile'];
}
if (isset($_POST['activitycategory']) && $_POST['activitycategory'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' activitycategory=' . $_POST['activitycategory'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "(`work_date` LIKE $escapedTerm OR `hours` LIKE $escapedTerm OR `task_summary` LIKE $escapedTerm)";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Stafftimeentry::page($selected_page, $page_size, $search, $order_by);
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
        
        $staffprofile = $_POST["staffprofile"];
        $activitycategory = $_POST["activitycategory"];
        $work_date = $_POST["work_date"];
        $hours = $_POST["hours"];
        $task_summary = $_POST["task_summary"];
        
       
        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($activitycategory == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_activitycategory "; $_error = true; }
        if ($work_date == "" && (!$_error)) {  $_result .= "<br>Error: work_date cannot be blank"; $_error = true; }
        if ($hours == "" && (!$_error)) {  $_result .= "<br>Error: hours cannot be blank"; $_error = true; }
        if ($task_summary == "" && (!$_error)) {  $_result .= "<br>Error: task_summary cannot be blank"; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM stafftimeentry WHERE name=?";
            $records = Stafftimeentry::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Stafftimeentry();
            $record->reg_by = $_COOKIE['user'];
            
            $record->staffprofile = $staffprofile;
            $record->activitycategory = $activitycategory;
            $record->work_date = $work_date;
            $record->hours = $hours;
            $record->task_summary = $task_summary;
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
        
        $staffprofile = $_POST["staffprofile"];
        $activitycategory = $_POST["activitycategory"];
        $work_date = $_POST["work_date"];
        $hours = $_POST["hours"];
        $task_summary = $_POST["task_summary"];



        
        if ($staffprofile == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_staffprofile "; $_error = true; }
        if ($activitycategory == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_activitycategory "; $_error = true; }
        if ($work_date == "" && (!$_error)) {  $_result .= "<br>Error: work_date cannot be blank"; $_error = true; }
        if ($hours == "" && (!$_error)) {  $_result .= "<br>Error: hours cannot be blank"; $_error = true; }
        if ($task_summary == "" && (!$_error)) {  $_result .= "<br>Error: task_summary cannot be blank"; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM stafftimeentry WHERE name=? AND iD!=?";
            $records = Stafftimeentry::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Stafftimeentry::where('iD', $recordiD)[0];
            
            $record->staffprofile = $staffprofile;
            $record->activitycategory = $activitycategory;
            $record->work_date = $work_date;
            $record->hours = $hours;
            $record->task_summary = $task_summary;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}