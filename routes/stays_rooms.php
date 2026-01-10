<?php

// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($methods) {
    // --------------------
    // READ STAY ROOMS
    // --------------------
    case 'GET':
        $totaldata = $db->select("stays_rooms",
         [
             "[<]stays" => ["stay_id" => "id"]
         ],
         [  "stays_rooms.id",
            "stays_rooms.stay_id",
            "stays_rooms.room_type_id",
            "stays_rooms.room_images",
            "stays_rooms.amenities",
            "stays_rooms.status",
            "stays_rooms.room_options",
            "stays_rooms.created_at",
            "stays_rooms.updated_at",
          ]
        );
        if(!$totaldata){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"data is empty"
            ]);
            exit;
        }

        $dataarray=[];
      // i am using foreach loop to print all the data and save the data in $displaydata array
        foreach ($totaldata as $data) {
            $dataarray[]=[
                
            "id"=>$data['id'],
            "stay_id"=>$data['stay_id'],
            "room_type_id"=>$data['room_type_id'],
            "room_images"=>$data['room_images'],
            "amenities"=>$data['amenities'],
            "status"=>$data['status'],
            "room_options"=>$data['room_options'],
            "created_at"=>$data['created_at'],
            "updated_at"=>$data['updated_at']
            ];
        }

        if($dataarray){

        //----------------------
        //RESPONSE SHOW HERE
        //----------------------
            http_response_code(200);
            echo json_encode([
                "Status"=>true,
                "Message"=>"data fetched successfully",
                "Data"=>$dataarray
            ]);
        }
        else{
        http_response_code(401);
        echo json_encode(["status"=>false,"Message"=>"Data fetching error"]);
        exit;}
        break;

    // --------------------
    // CREATE STAY ROOMS
    // --------------------
    case 'POST':
          // data coming through post method and properly arranged according to the requirement
        $stay_id=(int)$_POST['stay_id'];
       $stay_id=$db->get("stays","id",["id"=>$stay_id]);
       if(!$stay_id)
        {
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"invalid stay id"
            ]);
            exit;
        }  
            $room_type_id=(int)$_POST['room_type_id'];
            
            $room_images = $_POST['room_images'] ?? '';
            
            if (!empty($room_images)) {               
                $decoded = json_decode($room_images, true);  
                if (is_array($decoded)) {
                    $path = json_encode($decoded);
                } else {                
                    $path = json_encode([
                        [
                            'url' => "/uploads/hotels/rooms/room_$room_images",
                            'default' => true
                        ]
                    ]);
                }
            } else {
                $path = '[]';
            }

            $amenities = $_POST['amenities'] ?? '[]';
            $amenities= json_decode($amenities, true) ?: [];
            $amenities= json_encode(array_values(array_filter($amenities, 'is_numeric')));

            $status=(int)$_POST['status'];
            $room_options=$_POST['room_options'];
            
            $room_options = $_POST['room_options'] ;

            if (!empty($room_options) && strpos(trim($room_options), '{') === 0) {
            $room_options = '[' . $room_options . ']';
             }
           
             $decoded = json_decode($room_options, true);
             $room_options =  json_encode($decoded) ?? '[]';

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');


            if ($stay_id <= 0 || $room_type_id <= 0) {
            echo json_encode(["error" => "id must be greater than 0"]);
            exit;
            }

            if (empty($room_options)) {
            echo json_encode(["error" => "Room options required"]);
            exit; 
            }
        // --------------------
        // INSERT DATA IN DATABASE
        // --------------------   
            $result=$db->insert("stays_rooms",[
                "stay_id"=>$stay_id,
                "room_type_id"=>$room_type_id,
                "room_images"=>$path,
                "amenities"=>$amenities,
                "status"=>$status,
                "room_options"=>$room_options,
                "created_at"=>$created_at,
                "updated_at"=>$updated_at,
            ]);
        // ------------------------------------------
        // CHECKING WHEATHER THE DATA INSERTED OR NOT
        // ------------------------------------------ 
            if(!$result){
                http_response_code(404);
                echo json_encode([
                    "status"=>false,
                    "Message"=>"Data did not created"
                ]);
                exit;
            }
            else{
                http_response_code(200);
                echo json_encode([
                    "status"=>true,
                    "Message"=>"Data  created successfully",
                    "Data"=>$result
                ]);
                exit;
            }
        break;
        // --------------------
        // STAYS ROOM UPDATEING
        // --------------------
    case 'PUT':

        // UPDATED DATA COMMING THROUGH INPUT METHOD

        $input=json_decode(file_get_contents("php://input"),true);
        $room_id=(int)$input['id'];
        if(!$room_id){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "message"=>"room id missing"
            ]);
            exit;
        
        }
         //CHECKING ROOM ID EXISTING IN TABLE        
         $room_id_check=$db->get("stays_rooms","*",['id'=>$room_id]);
         if(!$room_id_check){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "message"=>"room id not found"
            ]);
            exit;
        }

        $roomData=[];

       // -------------------------------------------------------------
        // using if(isset()) to get those input only coming throught post
        // ------------------------------------------------------------          

        if(isset($input['room_type_id'])&& $input['room_type_id'] > 0 ){
            $roomData["room_type_id"]=(int)$input['room_type_id'];
               
             }else{
                http_response_code(404);
            echo json_encode([
                "status"=>false,
                "error" => "room type id must be greater than 0"
            ]);
            exit;
             }

        if(isset($input['room_images'])){
            $roomData["room_images"]=$input['room_images'] ?? '';
            if (!empty($room_images)) {               
                $decoded = json_decode($room_images, true);  
                if (is_array($decoded)) {
                    $path = json_encode($decoded);
                } else {                
                    $path = json_encode([
                        [
                            'url' => "/uploads/hotels/rooms/room_$room_images",
                            'default' => true
                        ]
                    ]);
                }
            } else {
                $path = '[]';
            }}
        if(isset($input['amenities'])){
            $roomData["amenities"]=$input['amenities'] ?? '[]';
            $amenities= json_decode($amenities, true) ?: [];
            $amenities= json_encode(array_values(array_filter($amenities, 'is_numeric')));}

        if(isset($input['status'])){
            $roomData["status"]=(int)$input['status'];}

        if(isset($input['room_options'])){
            $roomData["room_options"]=$input['room_options'];
            
            $room_options = $input['room_options'] ;

            if (!empty($room_options) && strpos(trim($room_options), '{') === 0) {
            $room_options = '[' . $room_options . ']';
             }           
             $decoded = json_decode($room_options, true);
             $room_options =  json_encode($decoded) ?? '[]';}

        if(isset($input['updated_at'])){
            $roomData["updated_at"]=date('Y-m-d H:i:s');}


        if(isset($room_id) && $room_id <= 0) {
            http_response_code(404);
            echo json_encode([
                "Status"=>false,
                "error" => "room id must be greater than 0"
            ]);
            exit;
             }
         //ADDING DATA IN DATABASE THROUGH MEDOO
        $result=$db->update("stays_rooms",$roomData,['id'=>$room_id]);
        // ------------------------------------------
        // CHECKING WHEATHER THE DATA UPDATED OR NOT
        // ------------------------------------------
        if(!$result) {
            http_response_code(404);
            echo json_encode([
                "Status"=>false,
                "Message" => "updating error occure plz try again"
            ]);
            exit;
             }
               else {
            http_response_code(200);
            echo json_encode([
                "Status"=>true,
                "Message" => "data updated succesfully",
                "data"=>$result
            ]);
            exit;
             }

        break;
    case 'DELETE':
        $input=json_decode(file_get_contents("php://input"),true);
        // -------------------------------
        // STAY ROOM ID PROPER VALIDATION
        // ------------------------------- 
        $room_id=(int)$input['id'];
        if(!$room_id){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "message"=>"room id missing"
            ]);
            exit;
        
        }
        
         $room_id_check=$db->get("stays_rooms","*",['id'=>$room_id]);
          
        if(!$room_id_check){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "message"=>"room id not found"
            ]);
            exit;
        }
        //DELETING DATA THROUGH MEDOO 

        $result=$db->delete("stays_rooms",['id'=>$room_id]);
        if(!$result){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "message"=>"stays room deleting error plz try again"
            ]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode([
                "Status"=>true,
                "message"=>"stay room succesfully deleted"
            ]);
            exit;
            }
        break;
    
    default:
             http_response_code(404); 
            echo json_encode([
                "status" => FALSE,
                "message" => "INVALID METHOD",
            ]);
            exit;
        break;
}
?>