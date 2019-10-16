<?php

    $conn = mysqli_connect("localhost", "id9864931_admin", "admin", "id9864931_heytaxi");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $is = 0;
    $p_location = $_POST["location"];
    $p_company = $_POST["company"];
    $p_vehicleType = $_POST["vehicleType"];
    $p_rating = $_POST["rating"];
    
    $sqlriz = "Select * FROM driver";
    $Rslt = mysqli_query($conn, $sqlriz);
    while($r=mysqli_fetch_object($Rslt)){
        $res[]=$r;
    }
    
    if($p_company == "all" && $p_vehicleType == "all") {
        for ($x = 0; $x < sizeof($res); $x++) {
            $obj = $res[$x];
            if(strtolower($obj->driverLocation) == strtolower($p_location) && $obj->rating <= $p_rating) {
                $response[] = $obj;
                $is = 1;
            }
        }
    } else if($p_company != "all") {
        for ($x = 0; $x < sizeof($res); $x++) {
            $obj = $res[$x];
            if(strtolower($obj->driverLocation) == strtolower($p_location) && $obj->rating <= $p_rating && $p_company == $obj->company) {
                $response[] = $obj;
                $is = 1;
            }
        }
    } else if($p_vehicleType != "all") {
        for ($x = 0; $x < sizeof($res); $x++) {
            $obj = $res[$x];
            if(strtolower($obj->driverLocation) == strtolower($p_location) && $obj->rating <= $p_rating && $p_vehicleType == $obj->vehicleType) {
                $response[] = $obj;
                $is = 1;
            }
        }
    }

    
    if($is == 1)
        echo json_encode($response);
    else {
        $response["success"] = "false";
        echo json_encode($response);
    }
        
?>