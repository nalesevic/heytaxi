<?php
require '../vendor/autoload.php';
require_once 'PersistanceManager.class.php';
require_once 'Config.class.php';
use \Firebase\JWT\JWT;

Flight::register('pm', 'PersistanceManager', [Config::DB]);

Flight::route('/', function(){
    echo 'Ovo je samo jedan slash';
});

Flight::route('POST /register', function(){
  $request = Flight::request();
  $company = [
    'companyName' => $request->data->companyName,
    'companyEmail' => $request->data->companyEmail,
    'companyPassword' => $request->data->companyPassword
  ];
  //Flight::json($company);
  Flight::pm()->register($company);
  header("Location: ../index.html");
 });

Flight::route('POST /login', function(){
  $data = Flight::request()->data->getData();
  $user = Flight::pm()->get_company($data);
  if($user["status"] == "success") {
    unset($user["companyPassword"]);
    unset($user["status"]);
    $token = array(
      "user" => $user,
      "iat" => time(),
      "exp" => time() + 2592000
    );
    $jwt = JWT::encode($token, CONFIG::JWT_SECRET);
    $user["token"] = $jwt;
    Flight::json($user);
  } else {
      return;
    }
});

Flight::route('GET /all_drivers', function() {
  $drivers = Flight::pm()->get_all_drivers();
  Flight::json($drivers);
});

Flight::route('GET /drivers', function(){
  $data = apache_request_headers();
  if(isset($data["Authorization"]) && $data["Authorization"] != "null") {
    $jwt = $data["Authorization"];
  } else {
    Flight::redirect('index.html');
    return;
  }
  $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256")); // decode jwt
  $decoded = json_decode(json_encode($decoded), true); // get json
  if(!isset($decoded['user']['companyID'])) {  // if there is no id of user (jwt is not valid) stop request
    echo "Invalid token";
    header("Location: ../index.html#welcome");
  } else {
    $drivers = Flight::pm()->get_drivers($decoded["user"]["companyName"]);
    Flight::json($drivers);
  }
},true);

Flight::route('POST /add_driver', function(){
  $request = Flight::request()->data->getData();
  $data = apache_request_headers();
  if($data['Authorization'] != 'null') {
    $jwt = $data["Authorization"];
    $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
    $decoded = json_decode(json_encode($decoded), true);
    $companyName = $decoded['user']['companyName'];
    $driver = [
      'firstName' => $request['fname'],
      'lastName' => $request['lname'],
      'vehicle' => $request['vehicle'],
      'company' => $companyName
    ];
    Flight::json($driver);
    Flight::pm()->add_driver($driver);
    header("Location: ../company.html#driver");
  }
});

Flight::route('DELETE /driver/@id', function($id){
     Flight::pm()->delete_driver($id);
});

Flight::route('/companies', function(){
  $companies = Flight::pm()->get_all_companies();
  Flight::json($companies);
});

Flight::start();
?>
