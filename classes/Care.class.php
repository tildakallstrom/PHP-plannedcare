<?php
require_once('./config.php');

class Care
{
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect(DBHOST, DBUSER, DBPASS, DBDATABASE);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function __destruct() {
        mysqli_close($this->conn);
    }



public function getListings() {
        $sql = "SELECT * FROM care";
        $result = mysqli_query($this->conn, $sql);
        $listings = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $listings[] = $row;
        }
        return $listings;
    }
   public function fetchDataAndInsertIntoDatabase() {
    // Fetch data from API
    $url = "https://www.forsakringskassan.se/fk_apps/MEKAREST/public/v1/iv-planerad/IVplaneradvardland.json";
    $data = file_get_contents($url);

    // Convert JSON data to PHP array
    $array = json_decode($data, true);

    // Loop through array and insert data into database
    foreach ($array as $row) {
    $table = "care";
    $data = array(
        "countrycode" => $row['dimensions']['vardland_kod'],
        "year" => $row['dimensions']['ar'],
        "total" => $row['observations']['antal']['value'],
        "women" => isset($row['dimensions']['kon_kod']) && $row['dimensions']['kon_kod'] === 'K' ? $row['observations']['antal']['value'] : null,
        "men" => isset($row['dimensions']['kon_kod']) && $row['dimensions']['kon_kod'] === 'M' ? $row['observations']['antal']['value'] : null,
    );
    $this->insertData($table, $data);
}

}


public function insertData($table, $data) {
    $columns = implode(", ", array_keys($data));
    $values = implode(", ", array_map(function($value) {
        return $value !== null ? "'" . mysqli_real_escape_string($this->conn, $value) . "'" : "DEFAULT";
    }, array_values($data)));
    $sql = "INSERT INTO $table ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE total = VALUES(total) + VALUES(men), women = VALUES(women)";
    if (mysqli_query($this->conn, $sql)) {
        return true;
    } else {
        echo "Error adding record: " . mysqli_error($this->conn);
        return false;
    }
}


/*
public function insertData($table, $data) {
    $columns = implode(", ", array_keys($data));
    $values = implode(", ", array_map(function($value) {
        return $value !== null ? "'" . mysqli_real_escape_string($this->conn, $value) . "'" : "DEFAULT";
    }, array_values($data)));
    $sql = "INSERT INTO $table ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE total = VALUES(total) + VALUES(women) + VALUES(men)";
    if (mysqli_query($this->conn, $sql)) {
        return true;
    } else {
        echo "Error adding record: " . mysqli_error($this->conn);
        return false;
    }
}


    
   public function fetchDataAndInsertIntoDatabase() {
    // Fetch data from API
    $url = "https://www.forsakringskassan.se/fk_apps/MEKAREST/public/v1/iv-planerad/IVplaneradvardland.json";
    $data = file_get_contents($url);

    // Convert JSON data to PHP array
    $array = json_decode($data, true);

    // Loop through array and insert data into database
    foreach ($array as $row) {
        $table = "care";
        $data = array(
            "countrycode" => $row['dimensions']['vardland_kod'],
            "year" => $row['dimensions']['ar'],
            "total" => $row['observations']['antal']['value'],
          "women" => !empty($row['kvinna']) ? $row['kvinna'] : null,
        "men" => !empty($row['man']) ? $row['man'] : null
        );
       // var_dump($data);
        $this->insertData($table, $data);
    }
} */

}

$care = new Care();
$care->fetchDataAndInsertIntoDatabase();
