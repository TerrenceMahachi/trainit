<?php
namespace App\Controllers;

use App\Models\Clientmembership;
use App\Models\Database;

class ClientmembershipsController
{
    public function index()
    {
        $search = "";

        
if (isset($_POST['clientorganization']) && $_POST['clientorganization'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientorganization=' . $_POST['clientorganization'];
}
if (isset($_POST['user']) && $_POST['user'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' user=' . $_POST['user'];
}
if (isset($_POST['clientmemberrole']) && $_POST['clientmemberrole'] != '') {
    if ($search != '') {  $search .= ' AND '; }
    $search .= ' clientmemberrole=' . $_POST['clientmemberrole'];
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
        $pagination_data = Clientmembership::page($selected_page, $page_size, $search, $order_by);
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
        $user = $_POST["user"];
        $clientmemberrole = $_POST["clientmemberrole"];
        
       
        
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($clientmemberrole == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientmemberrole "; $_error = true; }

        if (!$_error) {
            $sql = "SELECT * FROM clientmembership WHERE name=?";
            $records = Clientmembership::findByQuery($sql, [$name]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = new Clientmembership();
            $record->reg_by = $_COOKIE['user'];
            
            $record->clientorganization = $clientorganization;
            $record->user = $user;
            $record->clientmemberrole = $clientmemberrole;
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
        $user = $_POST["user"];
        $clientmemberrole = $_POST["clientmemberrole"];



        
        if ($clientorganization == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientorganization "; $_error = true; }
        if ($user == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_user "; $_error = true; }
        if ($clientmemberrole == "" && (!$_error)) {  $_result .= "<br>Error: Please provide a valid value for fk_clientmemberrole "; $_error = true; }
        if (!$_error) {
            $sql = "SELECT * FROM clientmembership WHERE name=? AND iD!=?";
            $records = Clientmembership::findByQuery($sql, [$name, $recordiD]);
            if (count($records) > 0) {
                $_error = true;
                $_result .= "Error: Record name already used, try a different name";
            }
        }
         if (!$_error) {
            $record = Clientmembership::where('iD', $recordiD)[0];
            
            $record->clientorganization = $clientorganization;
            $record->user = $user;
            $record->clientmemberrole = $clientmemberrole;
            $record->status = $status;
            $_result = $record->update();

        }/* */
        return ['status' => $_error ? 0 : 1, 'msg' => $_result];
        
    }
}