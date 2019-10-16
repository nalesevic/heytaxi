<?php
  $conn = mysqli_connect("localhost", "id9864931_admin", "admin", "id9864931_heytaxi");
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
   // set parameters and execute

  $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

  $firstname = $_POST["firstName"];
  $lastname = $_POST["lastName"];
  $email = $_POST["email"];
  $password = $_POST["password"];

  $stmt->execute();
    $response = array();
    $response["success"] = true;

    echo json_encode($response);
?>
