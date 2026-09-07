<?php
namespace App\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Models\Role;
use App\Models\Login;
use App\Models\DocumentCategory;
use App\Models\Database;
//use function App\Helpers\view;
use function App\Helpers\get_records;
use function App\Helpers\sendmail;

class UserController
{
    public function index()
    {
       
        $search = "";//. $category[0]->iD;
        if (!empty($_GET['search'])) {
            $searchTerm = $_GET['search'];
            $escapedTerm = (new Database)->escape("%$searchTerm%");
            $search .= "  (`name` LIKE $escapedTerm  OR `email` LIKE $escapedTerm  )";
        }
        $orb = 'reg_date DESC';
        // $documents = Document::page(1, 2, $search, $orb);
        $selected_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page_size = isset($_COOKIE['page_size']) ? $_COOKIE['page_size'] : 10;
        $pagination_data = User::page($selected_page, $page_size, $search, $orb);
    
        $data = [
            "status" => "success",
            "response_code" => "002",
            "records" => $pagination_data['data'],  // the actual document data
            "pagination" => [
                "current_page" => $pagination_data['selected_page'],
                "rows_per_page" => $pagination_data['rows_per_page'],
                "total_records" => $pagination_data['total_records'],
                "total_pages" => $pagination_data['total_pages'],
            ]
        ];


        return $data;

    }

    function apicreate()
    {
        $_error = false;
        $_result = "";
    
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $role = $_POST["role"] ?? 2;
        $us = $_COOKIE['user'] ?? 1;
        $u=null;
      
        if ($name == "" && (!$_error)) {
            $_result .= "<br>Error: Name cannot be blank";
            $_error = true;
        }
    
        if ((strlen($password) < 4) && (!$_error)) {
            $_result .= "<br>Error: Password cannot be less than 4 characters";
            $_error = true;
        }
    
        if ($email == "" && (!$_error)) {
            $_result .= "Error: Email cannot be blank";
            $_error = true;
        }
    
        if ((!(filter_var($email, FILTER_VALIDATE_EMAIL))) && (!$_error)) {
            $_result .= "<br>Error: Invalid Email";
            $_error = true;
        }
    
        if (!$_error) {
            $sql = "SELECT * FROM user WHERE email=?";
            $users = User::findByQuery($sql, [$email]);
    
            if (count($users) > 0) {
                $_error = true;
                $_result .= "Error: Email already registered on this system ";
            }
        }
    
        if (!$_error) {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->role = $role;
            $user->reg_by = $us;
            $user->save();
            $u = $user->iD;

            $userrole = new Login();
            $userrole->user = $user->iD;
            $userrole->reg_by = $us;
            $userrole->password = password_hash($password, PASSWORD_BCRYPT);
            $userrole->save();
            $_result = "Account registered successfully.";

        }
    
        return [
            'status' => $_error ? 0 : 1,
            'msg' => $_result,
            'user' => $u
        ];
    }
    function create()
    {
        $_error = false;
        $_result = "";
    
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $role = $_POST["role"] ?? 2;
      
        if ($name == "" && (!$_error)) {
            $_result .= "<br>Error: Name cannot be blank";
            $_error = true;
        }
    
        if ((strlen($password) < 4) && (!$_error)) {
            $_result .= "<br>Error: Password cannot be less than 4 characters";
            $_error = true;
        }
    
        if ($email == "" && (!$_error)) {
            $_result .= "Error: Email cannot be blank";
            $_error = true;
        }
    
        if ((!(filter_var($email, FILTER_VALIDATE_EMAIL))) && (!$_error)) {
            $_result .= "<br>Error: Invalid Email";
            $_error = true;
        }
    
        if (!$_error) {
            $sql = "SELECT * FROM user WHERE email=?";
            $users = User::findByQuery($sql, [$email]);
    
            if (count($users) > 0) {
                $_error = true;
                $_result .= "Error: Email already registered on this system ";
            }
        }
    
        if (!$_error) {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->role = $role;
            $user->reg_by = 1;
            $user->save();

            $userrole = new Login();
            $userrole->user = $user->iD;
            $userrole->reg_by = $_COOKIE['user'];
            $userrole->password = password_hash($password, PASSWORD_BCRYPT);
            $userrole->save();
            $_result = "Account registered successfully.";

        }
    
        return [
            'status' => $_error ? 0 : 1,
            'msg' => $_result
        ];
    }
    
}