<?php
class PersistanceManager{
  private $pdo;

  public function __construct($params){
    $this->pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['scheme'], $params['username'], $params['password']);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }

  public function register($company){
    $checkEmail = $company['companyEmail'];
    $query = "SELECT companyEmail FROM company WHERE companyEmail=?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$checkEmail]);
    $queryEmail = $statement->fetch();
    $existEmail = $queryEmail['companyEmail'];
    if($existEmail != $checkEmail) {
      $query = "INSERT INTO company
              (companyName,
               companyEmail,
               companyPassword)
              VALUES (:companyName,
                      :companyEmail,
                      :companyPassword)";
      $statement = $this->pdo->prepare($query);
      $statement->execute($company);
      $company['id'] = $this->pdo->lastInsertId();
      return true;
    } else
        return false;
  }

  public function add_driver($driver){
    $query = "SELECT * FROM vehicle WHERE companyID = :id AND vehicleID = :vehicleID";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $driver["companyID"], "vehicleID" => $driver["vehicleID"]]);
    $vehicle = $statement->fetch();
    if($vehicle != null && $vehicle["available"] == 1) {
      $query = "INSERT INTO driver
              (firstName,
               lastName,
               rating,
               email,
               password,
               vehicleID,
               companyID)
              VALUES (:firstName,
                      :lastName,
                      50,
                      :email,
                      :password,
                      :vehicleID,
                      :companyID)";
      $statement = $this->pdo->prepare($query);
      $statement->execute($driver);
      $driver['id'] = $this->pdo->lastInsertId();

      $query = "UPDATE vehicle SET available = :available WHERE vehicleID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["available" => 0, "id" => $driver["vehicleID"]]);

      return 1;
    } else {
      return 0;
    }

  }

  public function get_drivers($id){
    $query = "SELECT  driverID, firstName, lastName, rating, manufacturer, model FROM driver d JOIN vehicle v ON d.vehicleID = v.vehicleID WHERE d.companyID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
    $drivers = $statement->fetchAll();
    return $drivers;
  }

  public function get_all_drivers() {
    return $this->pdo->query("SELECT firstName, lastName, manufacturer, companyName, rating FROM driver d JOIN company as c ON d.companyID = c.companyID JOIN vehicle as v ON d.vehicleID = v.vehicleID ORDER BY rating DESC LIMIT 10")->fetchAll();
  }

  public function get_all_companies() {
    return $this->pdo->query("SELECT companyID, companyName, companyEmail, description FROM company")->fetchAll();
  }

  public function count_all_drivers(){
    return $this->pdo->query("SELECT COUNT(*) as driverNum FROM driver")->fetch();
  }

  public function count_all_rides(){
    return $this->pdo->query("SELECT COUNT(*) as rideNum FROM ride")->fetch();
  }

  public function vehicleType_count($id){
    $query = "SELECT vehicleType, COUNT(*) as vehicleTypeNum FROM vehicle WHERE companyID = :id GROUP BY vehicleType";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $num = $statement->fetchAll();
    return $num;
  }

  public function vehicle_count($id){
    $query = "SELECT COUNT(*) as vehicleNum FROM vehicle WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $num = $statement->fetch();
    return $num;
  }

  public function driver_count($id){
    $query = "SELECT COUNT(*) as driverNum FROM driver WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $num = $statement->fetch();
    return $num;
  }

  public function get_driver($id){
    $query = "SELECT driverID, firstName, lastName, vehicleID, email FROM driver WHERE driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $driver = $statement->fetch();
    return $driver;
  }

  public function update_driver($id, $request){
    $firstName = $request["fname"];
    $lastName = $request["lname"];
    $vehicleID = $request["vehicleID"];
    $email = $request["email"];
    $password = $request["password"];

    $query = "Select vehicleID from driver where driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id]);
    $vehicle = $statement->fetch();

    $query = "UPDATE vehicle SET available =  1 WHERE vehicleID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$vehicle["vehicleID"]]);

    if ($password == "") {
      $query = "UPDATE driver SET firstName = :firstName, lastName = :lastName, email = :email, vehicleID = :vehicleID WHERE driverID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "firstName" => $firstName, "lastName" => $lastName, "vehicleID" => $vehicleID, "email" => $email]);
    } else {
      $query = "UPDATE driver SET firstName = :firstName, lastName = :lastName, email = :email, password = :password, vehicleID = :vehicleID WHERE driverID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "firstName" => $firstName, "lastName" => $lastName, "vehicleID" => $vehicleID, "email" => $email, "password" => $password]);
    }
    $query = "Select vehicleID from driver where driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id]);
    $vehicle = $statement->fetch();

    $query = "UPDATE vehicle SET available = 0 WHERE vehicleID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$vehicle["vehicleID"]]);
  }

  public function delete_driver($id){
    $query = "Select vehicleID from driver where driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id]);
    $vehicle = $statement->fetch();;
    $query = "UPDATE vehicle SET available = 1 WHERE vehicleID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$vehicle["vehicleID"]]);

    $query = "DELETE FROM driver WHERE driverID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
  }

  public function delete_company($id){
    $query = "DELETE FROM company WHERE companyID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
  }

  public function add_vehicle($vehicle){
    $query = "INSERT INTO vehicle
            (manufacturer,
             model,
             year,
             vehicleType,
             available,
             companyID)
            VALUES (:manufacturer,
                    :model,
                    :year,
                    :vehicleType,
                    1,
                    :company)";
    $statement = $this->pdo->prepare($query);
    $statement->execute($vehicle);
    $vehicle['id'] = $this->pdo->lastInsertId();
  }

  public function get_vehicles($id){
    $query = "SELECT vehicleID, manufacturer, model, year, vehicleType FROM vehicle WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $vehicles = $statement->fetchAll();
    return $vehicles;
  }

  public function get_available_vehicles($id){
    $query = "SELECT * FROM vehicle WHERE companyID = :id AND available = 1";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $vehicles = $statement->fetchAll();
    return $vehicles;
  }

  public function get_vehicle($id){
    $query = "SELECT vehicleID, manufacturer, model, year, vehicleType FROM vehicle WHERE vehicleID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $vehicle = $statement->fetch();
    return $vehicle;
  }

  public function update_vehicle($id, $request){
    $manufacturer = $request["manufacturer"];
    $model = $request["model"];
    $year = $request["year"];
    $vehicleType = $request["vehicleType"];
    $query = "UPDATE vehicle SET manufacturer = :manufacturer, model = :model, year = :year, vehicleType = :vehicleType WHERE vehicleID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id, "manufacturer" => $manufacturer, "model" => $model, "year" => $year, "vehicleType" => $vehicleType]);
  }

  public function delete_vehicle($id){
    $query = "SELECT * FROM driver WHERE vehicleID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
    if ($statement->fetch() == null) {
      $query = "DELETE FROM vehicle WHERE vehicleID = ?";
      $statement = $this->pdo->prepare($query);
      $statement->execute([$id]);
      return '0';
    } else {
      return '1';
    }
  }

  public function get_company_byID($id) {
    $query = "SELECT * FROM company WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $company = $statement->fetch();
    return $company;
  }

  public function get_admin($user) {
    $stmt = $this->pdo->prepare("SELECT * FROM admin WHERE email = :email;");
    $stmt->execute(array(
      "email" => $user["companyEmail"],
    ));
    $response = $stmt->fetch();
    if($response){
      if ($user["companyPassword"] == $response["password"]) {
        $response["status"] = "success";
        return $response;
      } else {
        $response["status"] = "fail";
        return $response;
      }
    }
  }

  public function get_company($user) {
    $stmt = $this->pdo->prepare("SELECT * FROM company WHERE companyEmail = :email;");
    $stmt->execute(array(
      "email" => $user["companyEmail"],
    ));
    $response = $stmt->fetch();
    if($response){
      if (password_verify($user["companyPassword"], $response["companyPassword"])) {
        $response["status"] = "success";
        return $response;
      } else {
        $response["status"] = "fail";
        return $response;
      }
    }
  }

  public function update_company($id, $request){
    $companyName = $request["companyName"];
    $companyEmail = $request["companyEmail"];
    $description = $request["description"];
    if(!isset($request["companyPassword"]) || $request["companyPassword"] == "") {
      $query = "UPDATE company SET companyName = :companyName, companyEmail = :companyEmail, description = :description WHERE companyID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "companyName" => $companyName, "companyEmail" => $companyEmail, "description" => $description]);
    } else {
      $companyPassword = $request["companyPassword"];
      $query = "UPDATE company SET companyName = :companyName, companyEmail = :companyEmail,description = :description, companyPassword = :companyPassword WHERE companyID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "companyName" => $companyName, "companyEmail" => $companyEmail, "companyPassword" => $companyPassword, "description" => $description]);
    }
  }

  public function get_num_rides($companyID, $driverID){
    $query = "SELECT COUNT(*) as sum FROM ride WHERE companyID = :companyID AND driverID = :driverID";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['companyID' => $companyID, 'driverID' => $driverID]);
    $rides = $statement->fetch();
    return $rides;
  }
}

?>
