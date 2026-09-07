<?php
namespace App\Controllers;

use App\Models\Rosterskill;
use App\Models\Database;

class RosterskillsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['rosterapplication']) && $_POST['rosterapplication'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' rosterapplication=' . $_POST['rosterapplication'];
}
if (isset($_POST['servicefunction']) && $_POST['servicefunction'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' servicefunction=' . $_POST['servicefunction'];
}
if (isset($_POST['skillitem']) && $_POST['skillitem'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' skillitem=' . $_POST['skillitem'];
}
if (isset($_POST['proficiencylevel']) && $_POST['proficiencylevel'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' proficiencylevel=' . $_POST['proficiencylevel'];
}

        if (isset($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            if ($search != "") {  $search .= " AND ";  }
            $search .= "()";
        }
        $selected_page = isset($_POST['page']) ? $_POST['page'] : 1;

        $page_size = (isset($_POST['page_size']) && $_POST['page_size'] != "") ? $_POST['page_size'] : 10;
        $order_by = (isset($_POST['order_by']) && $_POST['order_by'] != "") ? $_POST['order_by'] : 'reg_date DESC';
        $pagination_data = Rosterskill::page($selected_page, $page_size, $search, $order_by);
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
        $servicefunction = $_POST["servicefunction"];
        $skillitem = $_POST["skillitem"];
        $proficiencylevel = $_POST["proficiencylevel"];
        
       
        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($servicefunction == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicefunction "; $_error = true; }
        if ($skillitem == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_skillitem "; $_error = true; }
        if ($proficiencylevel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_proficiencylevel "; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM rosterskill WHERE name=?";
            $records = Rosterskill::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Rosterskill();
            $record->reg_by = $_COOKIE['user'];
            
            $record->rosterapplication = $rosterapplication;
            $record->servicefunction = $servicefunction;
            $record->skillitem = $skillitem;
            $record->proficiencylevel = $proficiencylevel;
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
        $servicefunction = $_POST["servicefunction"];
        $skillitem = $_POST["skillitem"];
        $proficiencylevel = $_POST["proficiencylevel"];



        
        if ($rosterapplication == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_rosterapplication "; $_error = true; }
        if ($servicefunction == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_servicefunction "; $_error = true; }
        if ($skillitem == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_skillitem "; $_error = true; }
        if ($proficiencylevel == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_proficiencylevel "; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM rosterskill WHERE name=? AND iD!=?";
            $records = Rosterskill::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Rosterskill::where('iD', $recordiD)[0];
            
            $record->rosterapplication = $rosterapplication;
            $record->servicefunction = $servicefunction;
            $record->skillitem = $skillitem;
            $record->proficiencylevel = $proficiencylevel;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}