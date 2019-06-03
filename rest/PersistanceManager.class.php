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
                    0)";
    $statement = $this->pdo->prepare($query);
    $statement->execute($driver);
    $driver['id'] = $this->pdo->lastInsertId();
  }

  public function get_drivers($companyName){
    $query = "SELECT * FROM driver WHERE company = :companyName";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['companyName' => $companyName]);
    $drivers = $statement->fetchAll();
    return $drivers;
  }

  public function get_all_drivers() {
    return $this->pdo->query("SELECT * FROM driver ORDER BY rating DESC LIMIT 10")->fetchAll();
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

  public function count_all_drivers(){

  }

  public function get_driver_by_id($id){

  }

  public function update_driver($id, $driver){

  }

  public function delete_driver($id){
    $query = "DELETE FROM driver WHERE driverID = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
  }
}

?>
