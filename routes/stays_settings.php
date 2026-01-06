<?php


// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';


// ====================
// VALIDATION HELPERS
// ====================
function validate($field, $value){
    if (!isset($value) || trim($value) === '') {
        http_response_code(400);
        echo json_encode(["status"=>false,"Message"=>"$field cannot be empty"]);
        exit;
    }
}

switch ($methods) {
    // ====================
    // READ STAYS SETTINGS
    // ====================
    case 'GET':
        //data getting through medoo
        $data=$db->select("stays_settings","*");
        // checking weather the data is fetched or not   
        if(!$data){
         http_response_code(200);
         echo json_encode(["status"=>false,"Message"=>"data does not exist"]);exit;
        }
        // i ma initializaing  array to store the fetched data in specific order
        $displayData=[];

        // using foreach loop for the iteration of data  and store data in DISPLAYDATA array
        foreach ($data as $datum) {
            $displayData[]=[
            "id"=>$datum['id'],
            "setting_type"=>$datum['setting_type'],
            "name"=>$datum['name'],
            "translations"=>$datum['translations'],
            "status"=>$datum['status'],
            "created_at"=>$datum['created_at'],
            "updated_at"=>$datum['updated_at'],
            ];
        }
        // RESPONSE SHOWS HERE OF GETTING HERE
        http_response_code(200);
        echo json_encode(["Status"=>true,"Message"=>"Data Fetched successfully","Data"=>$displayData]);exit;
        break;


    // ---------------------
    // CREATE STAYS SETTINGS
    //----------------------
    case 'POST':
            // ALL INPUTS COMMING FORM POST AND WITH SPECIFIC VALIDATION 
            $setting_type=strtolower(trim($_POST['setting_type']));
            validate("setting_type",$_POST['setting_type']);
            $name=ucwords($_POST['name']);
            validate("name",$_POST['name']);
            $translations=$_POST['translations'];
            validate("translations",$_POST['translations']);
            $status=1;
            $created_at=date("Y-m-d H:i:s");
            $updated_at=date("Y-m-d H:i:s");

            // ADDITIONAL VALIDATION FOR NAME
            $name_exist=$db->get("stays_settings","*",['name'=>$name]);
           if($name_exist){
              http_response_code(404);
              echo json_encode(["status"=>false,"Message"=>"Name already exist"]);exit;
            }
            else{
                // DATA INSERTING THROUGH MEDOO
                $result=$db->insert("stays_settings",[
                 "setting_type"=>$setting_type,
                 "name"=>$name,
                 "translations"=>$translations,
                 "status"=>$status,
                 "created_at"=>$created_at,
                 "updated_at"=>$updated_at,
                   ]);
                   //THE RESPONSE OF POST METHOD SHOWS HERE
                   if(!$result){
                       http_response_code(404);
                       echo json_encode(["status"=>false,"Message"=>"Stays setting creating error"]);exit;
                      }
                      else{ 
                       http_response_code(200);
                       echo json_encode(["status"=>true,"Message"=>"Stays setting updated successfully","data"=>$result]);exit;
                      
                      }
               }

            
        break;
    //-------------------
    //UPDATE STAYS SETTING
    //-------------------
    case 'PUT':
        // DATA COMMING THROUGH UPDATE METHOD  
        $input=json_decode(file_get_contents("php://input"),true);
        //TAKING  INPUT ID AND CHECKING THE EXISTANCE OF THAT INPUT ID
        $setting_id=$input['id'];
        if(empty($setting_id)){
            http_response_code(401);
            json_encode(["status"=>false,"Message"=>"target id is missing"]);exit;
        }
        $setting_id_exist=$db->get("stays_settings","*",['id'=>$setting_id]);
        if(empty($setting_id_exist)){
            http_response_code(404);
            json_encode(["status"=>false,"Message"=>"invalid id please try again with valid id"]);exit;
        }

         // I AM USING DATA ARRAY TO STORE ALL THE ENTERED INPUTS WITH PROPER VALIDATION 
        $dataarray=[];
         //IF CONDITION  IS USED TO UPDATE THE INPUTS DATA ONLY COMMMING THROUH PUT METHOD 
        if(isset($input['setting_type'])){
            $dataarray['setting_type']=strtolower(trim($input['setting_type']));
            validate("setting_type",$input['setting_type']);}
        if(isset($input['name'])){
            $dataarray['name']=ucwords($input['name']);
            validate("name",$input['name']);}
        if(isset($input['transalations'])){
            $dataarray['transalations']=$input['translations'];
            validate("translations",$input['translations']);}
            $dataarray['updated_at']=date("Y-m-d H:i:s");
        if(empty($setting_id_exist)){
            http_response_code(404);
            echo json_encode(["status"=>false,"Message"=>"enter updating field first"]);exit;
            }
            // DATA UPDATING THROUGH MEDOO IN BOOKINGS API
            $result=$db->update("stays_settings",$dataarray,['id'=>$setting_id]);
         //RESPONSE SHOWS HERE
        if(empty($result)){
            http_response_code(404);
            echo json_encode(["status"=>false,"Message"=>"error occure while deleting please try again"]);exit;
        }
        else{ 
            http_response_code(200);
            echo json_encode(["status"=>true,"Message"=>"Stays setting deleted successfully","data"=>$result]);exit;
        }
        break;
    // DELETING BOOKING ID
    case 'DELETE':
        $input=json_decode(file_get_contents("php://input"),true);
        //GETTING ID THEN WITH PROPER VALIDATION CECKING THAT ID IN THE TABLE 
        $setting_id=$input['id'];
        if(empty($setting_id)){
            http_response_code(401);
            json_encode(["status"=>false,"Message"=>"target id is missing"]);exit;
        }
        $setting_id_exist=$db->get("stays_settings","*",['id'=>$setting_id]);
        if(empty($setting_id_exist)){
            http_response_code(404);
            echo json_encode(["status"=>false,"Message"=>"invalid id please try again with valid id"]);exit;
        }
        // THE TARGET DATA IS DELETED THROUGHT MEDOO QUERRY 

        $delete=$db->delete("stays_settings",['id'=>$setting_id]);
        // CHECKING THE RESULT IN RESPONSE 
        if(empty($delete)){
            http_response_code(404);
            echo json_encode(["status"=>false,"Message"=>"error occure while deleting please try again"]);exit;
        }
        else{ 
            http_response_code(200);
            echo json_encode(["status"=>true,"Message"=>"Stays setting deleted successfully","data"=>$delete]);exit;
        }       
        break;
    
    default:
            http_response_code(404);
            echo json_encode(["status"=>false,"Message"=>"invalid method"]);exit;
        break;
}


?>