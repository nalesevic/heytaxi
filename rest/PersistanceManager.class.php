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
      Flight::json("User added");
    } else
        Flight::json("Email exists");
  }

  public function add_driver($driver){
    $query = "SELECT * FROM vehicle WHERE companyID = :id AND vehicleID = :vehicleID";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $driver["company"], "vehicleID" => $driver["vehicle"]]);
    $vehicle = $statement->fetch();
    if($vehicle != null) {
      $query = "INSERT INTO driver
              (firstName,
               lastName,
               vehicle,
               company,
               rating)
              VALUES (:firstName,
                      :lastName,
                      :vehicle,
                      :company,
                      50)";
      $statement = $this->pdo->prepare($query);
      $statement->execute($driver);
      $driver['id'] = $this->pdo->lastInsertId();
      return 1;
    } else {
      return 0;
    }
  }

  public function get_drivers($id){
    $query = "SELECT driverID, firstName, lastName, manufacturer, rating FROM driver JOIN vehicle ON vehicle = vehicleID WHERE company = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $drivers = $statement->fetchAll();
    return $drivers;
  }

  public function get_all_drivers() {
    return $this->pdo->query("SELECT firstName, lastName, manufacturer, companyName, rating FROM driver d JOIN company as c ON company = companyID JOIN vehicle as v ON vehicle = vehicleID ORDER BY rating DESC LIMIT 10")->fetchAll();
  }


  public function count_all_drivers(){

  }

  public function get_driver($id){
    $query = "SELECT driverID, firstName, lastName, vehicle FROM driver WHERE driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $driver = $statement->fetch();
    return $driver;
  }

  public function update_driver($id, $request){
    $firstName = $request["fname"];
    $lastName = $request["lname"];
    $vehicle = $request["vehicle"];
    $query = "UPDATE driver SET firstName = :firstName, lastName = :lastName, vehicle = :vehicle WHERE driverID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id, "firstName" => $firstName, "lastName" => $lastName, "vehicle" => $vehicle]);
  }

  public function delete_driver($id){
    $query = "DELETE FROM driver WHERE driverID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
  }



  public function add_vehicle($vehicle){
    $query = "INSERT INTO vehicle
            (manufacturer,
             model,
             year,
             companyID)
            VALUES (:manufacturer,
                    :model,
                    :year,
                    :company)";
    $statement = $this->pdo->prepare($query);
    $statement->execute($vehicle);
    $vehicle['id'] = $this->pdo->lastInsertId();
  }

  public function get_vehicles($id){
    $query = "SELECT vehicleID, manufacturer, model, year FROM vehicle WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $vehicles = $statement->fetchAll();
    return $vehicles;
  }

  public function get_vehicle($id){
    $query = "SELECT vehicleID, manufacturer, model, year FROM vehicle WHERE vehicleID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $vehicle = $statement->fetch();
    return $vehicle;
  }

  public function update_vehicle($id, $request){
    $manufacturer = $request["manufacturer"];
    $model = $request["model"];
    $year = $request["year"];
    $query = "UPDATE vehicle SET manufacturer = :manufacturer, model = :model, year = :year WHERE vehicleID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id, "manufacturer" => $manufacturer, "model" => $model, "year" => $year]);
  }

  public function delete_vehicle($id){
    $query = "DELETE FROM vehicle WHERE vehicleID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
  }

  public function get_company_byID($id) {
    $query = "SELECT * FROM company WHERE companyID = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $company = $statement->fetch();
    return $company;
  }

  public function get_company($user) {
    $stmt = $this->pdo->prepare("SELECT * FROM company WHERE companyEmail = :email;");
    $stmt->execute(array(
      "email" => $user["companyEmail"],
    ));
    $response = $stmt->fetch();
    if($response){
      if ($user["companyPassword"] == $response["companyPassword"]) {
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
    if(!isset($request["companyPassword"]) || $request["companyPassword"] == "") {
      $query = "UPDATE company SET companyName = :companyName, companyEmail = :companyEmail WHERE companyID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "companyName" => $companyName, "companyEmail" => $companyEmail]);
    } else {
      $companyPassword = $request["companyPassword"];
      $query = "UPDATE company SET companyName = :companyName, companyEmail = :companyEmail, companyPassword = :companyPassword WHERE companyID = :id";
      $statement = $this->pdo->prepare($query);
      $statement->execute(["id" => $id, "companyName" => $companyName, "companyEmail" => $companyEmail, "companyPassword" => $companyPassword]);
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
