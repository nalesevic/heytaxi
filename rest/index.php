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
      'bele' => 'levat',
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
  $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
  $decoded = json_decode(json_encode($decoded), true);
  if(!isset($decoded['user']['companyID'])) {
    echo "Invalid token";
    header("Location: ../index.html#welcome");
  } else {
    $drivers = Flight::pm()->get_drivers($decoded["user"]["companyID"]);
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
    $companyID = $decoded['user']['companyID'];
    $driver = [
      'firstName' => $request['fname'],
      'lastName' => $request['lname'],
      'vehicle' => $request['vehicle'],
      'company' => $companyID
    ];
    $status = Flight::pm()->add_driver($driver);
    Flight::json($status);
  }
});

Flight::route('GET /driver/@id', function($id){
  $driver = Flight::pm()->get_driver($id);
  Flight::json($driver);
});

// wont read request data with PUT
Flight::route('POST /driver/@id', function($id){
  $request = Flight::request()->data->getData();
  Flight::pm()->update_driver($id, $request);
});

Flight::route('DELETE /driver/@id', function($id){
     Flight::pm()->delete_driver($id);
});

Flight::route('GET /vehicles', function(){
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
    $vehicles = Flight::pm()->get_vehicles($decoded["user"]["companyID"]);
    Flight::json($vehicles);
  }
},true);

Flight::route('POST /add_vehicle', function(){
  $request = Flight::request()->data->getData();
  $data = apache_request_headers();
  if($data['Authorization'] != 'null') {
    $jwt = $data["Authorization"];
    $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
    $decoded = json_decode(json_encode($decoded), true);
    $companyID = $decoded['user']['companyID'];
    $vehicle = [
      'manufacturer' => $request['manufacturer'],
      'model' => $request['model'],
      'year' => $request['year'],
      'company' => $companyID
    ];
    Flight::pm()->add_vehicle($vehicle);
    header("Location: ../company.html#driver");
  }
});

Flight::route('GET /vehicle/@id', function($id){
  $vehicle = Flight::pm()->get_vehicle($id);
  Flight::json($vehicle);
});

Flight::route('POST /vehicle/@id', function($id){
  $request = Flight::request()->data->getData();
  Flight::pm()->update_vehicle($id, $request);
});

Flight::route('DELETE /vehicle/@id', function($id){
     Flight::pm()->delete_vehicle($id);
});

Flight::route('/companies', function(){
  $companies = Flight::pm()->get_all_companies();
  Flight::json($companies);
});

Flight::route('GET /company', function(){
  $data = apache_request_headers();
  if(isset($data["Authorization"]) && $data["Authorization"] != "null") {
    $jwt = $data["Authorization"];
  } else {
    Flight::redirect('company.html');
    return;
  }
  $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
  $decoded = json_decode(json_encode($decoded), true);
  if(!isset($decoded['user']['companyID'])) {
    echo "Invalid token";
    header("Location: ../index.html#welcome");
  } else {
    $company = Flight::pm()->get_company_byID($decoded["user"]["companyID"]);
    Flight::json($company);
  }
});

Flight::route('POST /companies/@id', function($id){
  $request = Flight::request()->data->getData();
  Flight::pm()->update_company($id, $request);
  //Flight::json($response);
});

Flight::route('GET /rides/@id', function($id){
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
    $rides = Flight::pm()->get_num_rides($decoded["user"]["companyID"], $id);
    Flight::json($rides);
  }
},true);

Flight::start();
?>
