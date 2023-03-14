<?php
require_once('./config.php');

class Care
{
    private $conn;
    //constructor
    public function __construct() {
        $this->conn = mysqli_connect(DBHOST, DBUSER, DBPASS, DBDATABASE);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    public function __destruct() {
        mysqli_close($this->conn);
    }

//get all the listings from db
public function getListings() {
        $sql = "SELECT * FROM care";
        $result = mysqli_query($this->conn, $sql);
        $listings = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $listings[] = $row;
        }
        return $listings;
    }
// get listings with countrycode ALL
public function getAllListings() {
    $sql = "SELECT * FROM care WHERE countrycode='ALL'";
    $result = mysqli_query($this->conn, $sql);
    $all_country_listings = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $all_country_listings[] = $row;
    }
    return $all_country_listings;
}
//get listings with other countrycode than all
public function getNonAllListings() {
    $sql = "SELECT * FROM care WHERE countrycode!='ALL'";
    $result = mysqli_query($this->conn, $sql);
    $listings = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $listings[] = $row;
    }
    return $listings;
}
//insert data to db
public function insertData($table, $data) {
    $countrycode = mysqli_real_escape_string($this->conn, $data['countrycode']);
    $year = mysqli_real_escape_string($this->conn, $data['year']);
    $sql = "SELECT * FROM $table WHERE countrycode='$countrycode' AND year='$year'";
    $result = mysqli_query($this->conn, $sql);
    if (!$result) {
        // handle query error
        die("Query error: " . mysqli_error($this->conn));
    }
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $new_total = $data['total'];
        $new_women = $data['women'];
        $new_men = $data['men'];
        $update_fields = array();
        if (!is_null($new_total) && $new_total != $row['total']) {
            $update_fields[] = "total='$new_total'";
        }
        if (!is_null($new_women) && $new_women != $row['women']) {
            $update_fields[] = "women='$new_women'";
        }
        if (!is_null($new_men) && $new_men != $row['men']) {
            $update_fields[] = "men='$new_men'";
        }
        if (!empty($update_fields)) {
            $update_fields = implode(", ", $update_fields);
            $sql = "UPDATE $table SET $update_fields WHERE countrycode='$countrycode' AND year='$year'";
            $result = mysqli_query($this->conn, $sql);
            if (!$result) {
                // handle query error
                die("Query error: " . mysqli_error($this->conn));
            }
        } else {
            return true;
        }
    } else {
        // insert new data into the database
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function($value) {
            return ($value !== null && $value !== 0) ? "'" . mysqli_real_escape_string($this->conn, $value) . "'" : "DEFAULT";
        }, array_values($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            // handle query error
            die("Query error: " . mysqli_error($this->conn));
        }
    }
}
public function fetchDataAndInsertIntoDatabase() {
    // fetch data from API
    $url = "https://www.forsakringskassan.se/fk_apps/MEKAREST/public/v1/iv-planerad/IVplaneradvardland.json";
    $data = file_get_contents($url);

    //convert JSON data to PHP array
    $array = json_decode($data, true);

    //loop through array and insert data into database
    foreach ($array as $row) {

        $table = "care";
    $total = !empty($row['observations']['antal']['value']) && !empty($row['dimensions']['kon_kod']) && $row['dimensions']['kon_kod'] == 'ALL' ? $row['observations']['antal']['value'] : null;
$women = !empty($row['observations']['antal']['value']) && !empty($row['dimensions']['kon_kod']) && $row['dimensions']['kon_kod'] == 'K' ? $row['observations']['antal']['value'] : null;
$men = !empty($row['observations']['antal']['value']) && !empty($row['dimensions']['kon_kod']) && $row['dimensions']['kon_kod'] == 'M' ? $row['observations']['antal']['value'] : null;

        if ($women == 0 && $men == 0 && $total == 0) {
            continue;
        }
        $data = array(
            "countrycode" => $row['dimensions']['vardland_kod'],
            "year" => $row['dimensions']['ar'],
            "total" => $total,
            "women" => $women,
            "men" => $men,
        );

        $this->insertData($table, $data);
    }
}
}

$care = new Care();
//run fetchdataandinsertintodatabase function
    $care->fetchDataAndInsertIntoDatabase();

