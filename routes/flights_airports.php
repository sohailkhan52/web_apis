<?php


// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';


switch ($methods) {
// --------------------
// READ FLIGHTS AIRPORTS
// --------------------

    case 'GET':
        $results = $db->select("flights_airports", "*");
        if (!$results) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                'MESSAGE' => "airports didnot found in the database"
            ]);
            exit;
        }

        $data = [];
        // i am using foreach loop to print all the results and save the data in $data array
        foreach ($results as $result) {
            $data[] = [
                //id airport city country code late long region type status
                'id' => $result['id'] ?? "",
                'airport' => ucwords(trim($result['airport'] ?? "")),
                'city' => ucwords(trim($result['city'] ?? "")),
                'country' => ucwords(trim($result['country'] ?? "")),
                'code' => strtoupper(trim($result['code'] ?? "")),
                'late' => trim($result['late'] ?? ""),
                'long' => trim($result['long'] ?? ""),
                'region' => strtoupper(trim($result['region'] ?? "/N")),
                'type' => strtolower(trim($result['type'] ?? "")),
                'status' => 1
            ];
        }

        if ($data) {
            echo json_encode([
                "status" => true,
                "data" => $data
            ]);

            exit;
        } else {

            http_response_code(401);
            echo json_encode(["error" => "Data getting error"]);
            exit;
        }
        break;

    // --------------------
    // CREATE AIRPORTS
    // --------------------
    case 'POST':
        // data coming through post method and properly arranged according to the requirement

        $airport = ucwords(trim($_POST['airport']));
        $city = ucwords(trim($_POST['city']));
        $country = ucwords(trim($_POST['country']));
        $code = strtoupper(trim($_POST['code']));
        $late = is_numeric(trim($_POST['late']));
        $long = is_numeric(trim($_POST['long']));
        $region = strtoupper(trim($_POST['region']));
        $type = strtolower(trim($_POST['type']));

        //---------------
        //INPUT VALIDATION
        //----------------

        if (!$airport || !$city || !$country || !$code || !$late || !$long || !$region || !$type) {
            http_response_code(404);
            echo json_encode([
                "status" => false,
                "error" => "All fields are required"
            ]);
            exit;
        }
        // checking airport existance
        $check_airport = $db->get("flights_airports", "*", ["airport" => $airport, "code" => $code]);

        if ($check_airport) {
            http_response_code(404);
            echo json_encode([
                "status" => false,
                'error' => "Airport already exist"
            ]);
            exit;
        }
        $result = $db->insert("flights_airports", [
            "airport" => $airport,
            "city" => $city,
            "country" => $country,
            "code" => $code,
            "late" => $late,
            "long" => $long,
            "type" => $type,
            "status" => 1
        ]);
        // if the addition of new airport fails then show the response

        if (!$result) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                'error' => "adding new airport error"
            ]);
            exit;
        }

        // if the addition of new airport adds successfully then show the reponse
        http_response_code(200);
        echo json_encode([
            "status" => true,
            'success' => "new Airport added successfully",
            "data" => $result
        ]);
        exit;
        break;

    //---------------------
    // update airport
    //---------------------
    case 'PUT':

        $input = json_decode(file_get_contents("php://input"), true);


        $id = $input['id'];

        // it checks the id  and find the existance of the airport
        $exist_airport = $db->get("flights_airports", "*", ['id' => $id]);
        if (!$exist_airport) {
            http_response_code(401);
            echo json_encode(['error' => "Airport does not exist"]);
            exit;
        }
        //   i ma using $airportdata array which stores the input fields  which can be used to updated 
        $airportdata = [];
        if (isset($input['airport'])) {
            $airportdata['airport'] = ucwords(trim($input['airport']));
        }
        if (isset($input['city'])) {
            $airportdata['city'] = ucwords(trim($input['city']));
        }
        if (isset($input['country'])) {
            $airportdata['country'] = ucwords(trim($input['country']));
        }

        if (isset($input['code'])) {
            $airportdata['code'] = trim($input['code']);
        }
        if (isset($input['late'])) {
            $airportdata['late'] = is_numeric(trim($input['late']));
        }
        if (isset($input['long'])) {
            $airportdata['long'] = is_numeric(trim($input['long']));
        }
        if (isset($input['region'])) {
            $airportdata['region'] = strtoupper(trim($input['region']));
        }
        if (isset($input['type'])) {
            $airportdata['type'] = strtolower(trim($input['type']));
        }

        //using medoo 

        $result = $db->update("flights_airports", $airportdata, ['id' => $id]);

        if (!$result) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                "message" => "airport updating error"
            ]);
            exit;
        } else {
            echo json_encode([
                "status" => true,
                "success" => "airport updated successfully",
                "data" => $result
            ]);
            exit;
        }

        break;
    case 'DELETE':
        $inputs = json_decode(file_get_contents("php://input"), true);
        $airport_id = $inputs['id'];

        if ($airport_id) {
            $airport_id = $db->get("flights_airports", "*", ['id' => $airport_id]);
            if (!$airport_id) {
                http_response_code(400);
                echo json_encode([
                    "status" => false,
                    "message" => "airport ID does not found"
                ]);
                exit;
            }
        }

        $result = $db->delete("flights_airports", ['id' => $airport_id]);
        if ($result->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                "status" => false,
                "message" => "flights airport deleting error. Please try again "
            ]);
            exit;
        } else {
            http_response_code(200);
            echo json_encode([
                "status" => true,
                "message" => "flights airport deleted successfully"
            ]);
            exit;
        }

        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Invalid request method"]);

        break;
}


?>