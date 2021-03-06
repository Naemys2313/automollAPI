<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'objects/car.php';
include_once 'objects/user.php';
include_once 'objects/car_model.php';
include_once 'objects/car_mark.php';

$database = new Database();

$car = new Car($database);
$user = new User($database);
$car_model = new CarModel($database);
$car_mark = new CarMark($database);

$user_id = isset($_GET['id']) ? $_GET['id'] : die();
$data = json_decode(file_get_contents("php://input"));

if(
    // !empty($data->vin) &&
    // !empty($data->label) &&
    !empty($data->mark_id) &&
    !empty($data->model_id) &&
    // !empty($data->year) &&
    !empty($data->gosnum)
    // !empty($data->mileage) 
) {
    $car->vin = $data->vin;

    $car->readByVIN();
    if ($car->id != null) {
        echo json_encode(getResponse("error", 10, "vin already exists"));
    } else {
        $user->id = $user_id;
        $user->readOne();

        if($user->name != null) {
            
            $car_model->id = $data->model_id;
            $car_model->readOne();
    
            $car_mark->id = $data->mark_id;
            $car_mark->readOne();

            $car->user_id = $user_id;
            $car->vin = $data->vin;
            $car->label = $data->label;
            $car->mark_id = $car_mark->id;
            $car->model_id = $car_model->id;
            $car->year = $data->year;
            $car->gosnum = $data->gosnum;
            $car->mileage = $data->mileage;
            
            if($car->create()) {
                echo json_encode(getResponse("ok", 0, "success"));
            } else {
                echo json_encode(getResponse("error", 1, "car not create"));
            }
        } else {
            echo json_encode(getResponse("error", 1, "user with id#".$user_id." not exists."));
        }  
    }   
} else {
    echo json_encode(getResponse("error", 1, "fields not filled"));
}



function getResponse($status, $errorcode, $message) {
    $response = array();
    $response['status'] = $status;
    $response['errorcode'] = $errorcode;
    $response['message'] = $message;
    
    return $response;
}
?>