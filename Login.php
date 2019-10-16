<?php
    
    $conn = mysqli_connect("localhost", "id9864931_admin", "admin", "id9864931_heytaxi");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM user";
    $result = $conn->query($sql);

    
    $response = array();
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if($row["email"] == $email && $row["password"] == $password) {
            $response = $row;
            $response["success"] = true;
        } else
            $response["success"] = false; 
        
    }
    } else {
        $response["success"] = false;  
    }
    
    echo json_encode($response);
    
?>