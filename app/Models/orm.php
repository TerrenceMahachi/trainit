<?php

namespace App\Models;

use App\Models\Model;
use PDO;
use PDOException;
use DateTime;
// Database Connection

// Base Model

// Example Model


class FormSession extends Model
{
    protected $table = 'form_session';

}
class Currency extends Model
{
    protected $table = 'currency';
    public function goals()
    {
        return $this->hasMany(Goal::class, 'currency');
    }
    public function goal_targets()
    {
        $sum = 0; $goals = Goal::where('currency', $this->attributes[$this->primaryKey]);
        foreach ($goals as &$goal) { $sum += $goal->target_amount; }
        return $sum;
    }
    public function goal_balances()
    {
        $sum = 0; $goals = Goal::where('currency', $this->attributes[$this->primaryKey]);
        foreach ($goals as &$goal) { $sum += $goal->balance; }
        return $sum;
    }
    public function withdrawals()
    {
        $sum = 0; $records = Withdrawal::where('currency', $this->attributes[$this->primaryKey]);
        foreach ($records as &$record) { $sum += $record->amount; }
        return $sum;
    }
    public function payments()
    {
        $sum = 0; $records = Payment::where('currency', $this->attributes[$this->primaryKey]);
        foreach ($records as &$record) { $sum += $record->amount; }
        return $sum;
    }
    public function transactions()
    {
        $sum_dr = 0;  $sum_cr = 0; $records = Transaction::where('currency', $this->attributes[$this->primaryKey]);
        foreach ($records as &$record) { $sum_dr += $record->dr; $sum_cr += $record->cr; }
        return ['dr' => $sum_dr, 'cr' => $sum_cr];
    }

}

class PaymentMethod extends Model
{
    protected $table = 'payment_method';
}
class Goal extends Model
{
    protected $table = 'goal';
}
class Payment extends Model
{
    protected $table = 'goal_payment';
}
class Transaction extends Model
{
    protected $table = 'transaction';
}
class Withdrawal extends Model
{
    protected $table = 'withdrawal';
}
class KYC extends Model
{
    protected $table = 'kyc';
}


class Hymn extends Model
{
    protected $table = 'hymn';

}
class Hymnal extends Model
{
    protected $table = 'hymnl';

}

class Page extends Model
{
    protected $table = 'page';

}

class PageView extends Model
{
    protected $table = 'page_view';

}
/*
$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->password = password_hash('password123', PASSWORD_BCRYPT);
$user->save();

$users = User::findByQuery("SELECT * FROM users WHERE age > ? AND status = ?", [18, 'active']);
foreach ($users as $user) {
    echo $user->name;
}
// Usage



$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->password = password_hash('password123', PASSWORD_BCRYPT);
$user->save();

$users = User::findByQuery("SELECT * FROM users WHERE age > ? AND status = ?", [18, 'active']);
foreach ($users as $user) {
    echo $user->name;
}
---
$user = User::find(1);

if ($user) {
    $user->delete();
    echo 'User deleted';
} else {
    echo 'User not found';
}

--
// Find a user by ID
$user = User::find(1);

if ($user) {
    echo 'Name: ' . $user->name . '<br>';
    echo 'Email: ' . $user->email . '<br>';
} else {
    echo 'User not found';
}

--

// Find a user by ID
$user = User::find(1);

if ($user) {
    $user->name = 'Jane Doe';
    $user->email = 'jane@example.com';
    $user->save(); // or $user->update() if you have a separate update method
} else {
    echo 'User not found';
}
*/