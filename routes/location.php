<?php

// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($methods) {
// --------------------
// READ LOCATION
// --------------------
    case 'GET':
        $data=$db->select("locations","*");

     if(empty($data))
    {
    http_response_code(404);
    echo json_encode([
        "status"=>false,
        "Message"=>" Locations not found"
    ]);
    exit;

     }

     $locationdata=[];
     // i am using foreach loop to print all the data and save the data in $data array
       
     foreach($data as $data)
     {
        $locationdata[]=[
            "id"=>$data['id'],
            "country"=>$data['country'],
            "country_code"=>$data['country_code'],
            "city"=>$data['city'],
            "latitude"=>$data['latitude'],
            "longitude"=>$data['longitude'],
            "status"=>$data['status']
        ];
     }

     if(empty($locationdata)){
        http_response_code(404);
         echo json_encode([
        "status"=>false,
        "Message"=>" in complete Fetching data"
         ]);
    exit;
     }
     else{
        http_response_code(200);
         echo json_encode([
        "status"=>true,
        "Message"=>"total data",
        "data"=>$locationdata
         ]);
    exit;
     }


        break;
    // --------------------
    // CREATE LOCATION
    // --------------------    
    case"POST":
        // data coming through post method and properly arranged according to the requirement
            
        $country=ucwords(trim($_POST['country']));
        $country_code=strtoupper(trim($_POST['country_code']));
        $city=ucwords(trim($_POST['city']));
        $latitude=$_POST['latitude']?? null;
        $longitude=$_POST['longitude']?? null;

        //---------------
        //INPUT VALIDATION
        //----------------
        if($country===0 || $country_code ===0 || $city ===0 ||!$latitude || !$longitude){
        
        http_response_code(404);
         echo json_encode([
        "status"=>false,
        "Message"=>"(country country_code city latitude and longitude)All fields are required "
         ]);
        exit;
        }
        //checking that the latitude and longitude is numeric or not
        else if(!is_numeric($latitude )|| !is_numeric($longitude)){
        http_response_code(404);
         echo json_encode([
        "status"=>false,
        "Message"=>"(latitude and longitude must be numeric "
         ]);
        exit;   
        }
        $result=$db->insert("locations",[

            "country"=>$country,
            "country_code"=>$country_code,
            "city"=>$city,
            "latitude"=>$latitude,
            "longitude"=>$longitude,
            "status"=>1
        ]);
        if(!$result)
        {
            http_response_code(404);
            echo json_encode([
            "status"=>false,
            "Message"=>"new location did not created "
            ]);
            exit;   
            
        }
        http_response_code(201);
         echo json_encode([
        "status"=>true,
        "Message"=>"data inserted successfully",
        "data"=>$result
         ]);
        exit;   
        
        break;
    case"PUT":
        
        $input=json_decode(file_get_contents("php://input"),true);
        $location_id=$input['id'];
        if(!empty($location_id)){
            $exist_id=$db->get("locations","*",['id'=>$location_id]);
            if(empty($exist_id)){
            http_response_code(404);
            echo json_encode([
            "status"=>false,
            "Message"=>"id does not exist"
            ]);
            exit;   
        
            }}

            $locationdata=[];

            if(isset($input['country'])){
            $locationdata["country"]=ucwords(trim($input['country']));}
            if(isset($input['country_code'])){
            $locationdata["country_code"]=strtoupper(trim($input['country_code']));}
            if(isset($input['city'])){
            $locationdata["city"]=ucwords(trim($input['city']));}
            if(isset($input['latitude'])){
            $locationdata["latitude"]=is_numeric($input['latitude'])?? null;}
            if(isset($input['longitude'])){
            $locationdata["longitude"]=is_numeric($input['longitude'])?? null;}

            $result=$db->update("locations",$locationdata,['id'=>$location_id]);
            
            if(!$result)
         {
            http_response_code(404);
            echo json_encode([
            "status"=>false,
            "Message"=>" location did not updated plz try again  "
            ]);
            exit;   
            
         }
            http_response_code(201);
            echo json_encode([
            "status"=>true,
            "Message"=>"data updateded successfully",
            "data"=>$result
             ]);
            exit;   
        break;
    case "DELETE":
        $input=json_decode(file_get_contents("php://input"),true);
        
        $location_id=$input['id'];
        if(!empty($location_id)){
            $exist_id=$db->get("locations","*",['id'=>$location_id]);
            if(empty($exist_id)){
            http_response_code(404);
            echo json_encode([
            "status"=>false,
            "Message"=>"id does not exist"
            ]);
            exit;   
        
            }
        }
        $result=$db->delete("locations",['id'=>$location_id]);
         if(!$result)
         {
            http_response_code(404);
            echo json_encode([
            "status"=>false,
            "Message"=>" location deleting error plz try again later  "
            ]);
            exit;   
            
         }
            http_response_code(201);
            echo json_encode([
            "status"=>true,
            "Message"=>"locartion deleted successfully"
             ]);
            exit;   

        break;
    default:
        # code...
        break;
}


?>