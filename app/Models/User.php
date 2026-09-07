<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Database;
use App\Models\UserSession;
use DateTime;
use PDO;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'iD';
    protected function getTableCreationQuery()
    {
        return "
        CREATE TABLE IF NOT EXISTS `user` (
            `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `email` TEXT NOT NULL,
            `role` INTEGER NOT NULL DEFAULT '2',
            `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `reg_by` INTEGER NOT NULL DEFAULT '1',
            `status` INTEGER NOT NULL DEFAULT '1'
          )
        ";
    }
    protected function generateCode($table, $column, $length = 20)
    {
        // Cryptographically secure, unpredictable token. random_bytes is
        // suitable for session tokens; rand() was not.
        $randomString = substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);

        // Ensure the token is unique
        $conn = new Database();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $result = $conn->query($sql, [$randomString])->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            return $this->generateCode($table, $column, $length); // Recursively generate a new code if the one generated is not unique
        }

        return $randomString;
    }
    public function newSession()
    {

        $sessionToken = $this->generateCode("usersession", "token", 20);

        $currentDate = new DateTime();
        $startDate = $currentDate->format('Y-m-d H:i:s');
        $endDate = (clone $currentDate)->modify('+48 hours')->format('Y-m-d H:i:s');
        $ipAddress = $this->getIpAddress();
        // Assuming $conn is the database connection instance

        $existingSessions = $this->sessions();

        if (count($existingSessions) > 0) {
            // Update existing session
            $existingSession = $existingSessions[0];
            /*   $existingSession->token = $sessionToken;
            $existingSession->start = $startDate;
            $existingSession->end = $endDate;
            $existingSession->ip_address = $ipAddress; */
            $existingSession->status = "2";
            $existingSession->update();
        }
        // Create new session
        $this->createSession([
            'token' => $sessionToken,
            'user' => $this->attributes[$this->primaryKey],
            'start' => $startDate,
            'end' => $endDate,
            'ip_address' => $ipAddress
        ]);


        return $sessionToken; // Return the session token for further use if needed
    }



    protected function getIpAddress()
    {
        // Example method to get IP address. Adjust this to your context.
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    public function extendSession()
    {
        $sql = "SELECT * FROM usersession WHERE user = ? LIMIT 1";
        $records = UserSession::findByQuery($sql, [$this->attributes['iD']]);

        if (count($records) > 0) {
            $session = $records[0];
            $now = new DateTime();
            $end = new DateTime($session->end);

            if ($end > $now) {

                $currentDate = new DateTime();
                $newEndDate = $currentDate->modify("+48 hours")->format('Y-m-d H:i:s');

                $session->end = $newEndDate;
                $session->update();

                return true;
            }
        }

        return null; // No session or session expired
    }
    public function sessions()
    {
        return UserSession::where('user', $this->attributes['iD']);
    }
    public function currentSession()
    {
        $sql = "SELECT * FROM usersession WHERE user = ? LIMIT 1";
        $records = UserSession::findByQuery($sql, [$this->attributes['iD']]);

        if (count($records) > 0) {
            $session = $records[0];
            $now = new DateTime();
            $end = new DateTime($session->end);

            if ($end > $now) {
                return $session; // Session is still valid
            }
        }

        return null; // No session or session expired
    }
    public function createSession($data)
    {
        return UserSession::create($data); // $this->hasMany(UserSession::class, 'user');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }
    public function validate($password)
    {

        return (password_verify($password, $this->login()[0]->password));
    }

    public function login()
    {
        return $this->hasMany(Login::class, 'user');
    }

  
}
