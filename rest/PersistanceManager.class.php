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
             rating)
            VALUES (:firstName,
                    :lastName,
                    :vehicle,
                    0)";
    $statement = $this->pdo->prepare($query);
    $statement->execute($driver);
    $driver['id'] = $this->pdo->lastInsertId();
  }

  public function get_all_drivers(){
    $query = "SELECT * FROM driver";
    return $this->pdo->query($query)->fetchAll();
  }

  public function get_company($email){
    $query = "SELECT companyPassword FROM company WHERE companyEmail=?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$email]);
    $userPass = $statement->fetch();
    return $userPass;
    //return $this->pdo->query($query)->fetch();
  }

  public function get_user($user) {
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
    $query = "SELECT count(*) as num_drivers FROM drivers";
    return reset($this->pdo->query($query)->fetchAll());
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
