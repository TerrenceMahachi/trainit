<?php

namespace App\Helpers;

use PDO;
use PDOException;
class SiteConfig
{
    public $siteUrl;
    public $assetsUrl;
    public $siteName;
    public $allowReg;
    public $defaultEmail;
    public $uploadsFolder;
    public $dbName;
    public $dbUser;
    public $dbPassword;
    public $dbServer;
    public $assetsLoc;
    public $navLoc;
    
    public function __construct($siteUrl, $siteName, $assetsUrl, $assetsLoc="", $navLoc="", $dbName='', $dbUser='', $dbPassword='', $dbServer = 'localhost', $allowReg = true, $defaultEmail = 'hello@example.com', $uploadsFolder = '../uploads/')
    {
        $this->siteUrl = $siteUrl;
        $this->assetsUrl = $assetsUrl;
        $this->assetsLoc = $assetsLoc;
        $this->navLoc = $navLoc;
        $this->siteName = $siteName;
        $this->allowReg = $allowReg;
        $this->defaultEmail = $defaultEmail;
        $this->uploadsFolder = $uploadsFolder;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbServer = $dbServer;
    }

    public function getSiteUrl()
    {
        return $this->siteUrl;
    }
    public function getAssetsUrl()
    {
        return $this->assetsUrl;
    }

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function allowReg()
    {
        return $this->allowReg;
    }

    public function getDefaultEmail()
    {
        return $this->defaultEmail;
    }

    public function getUploadsFolder()
    {
        return $this->uploadsFolder;
    }

    public function getDatabaseName()
    {
        return $this->dbName;
    }

    public function getConn()
    {
        try {
            $conn = new PDO("mysql:host={$this->dbServer};dbname={$this->dbName}", $this->dbUser, $this->dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            return "Connection failed: " . $e->getMessage();
        }
    }
}