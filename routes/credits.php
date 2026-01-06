<?php
// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($method) {
    // -------------
    // READ credits
    // -------------
    case 'GET':
       $data=$db->select("credits","*");
       // checking weather the data is fetched or not
       if(!$data){
        http_response_code(401);
        echo json_encode(["status"=>false,"Message"=>"Data getting failed"]);
        exit;
       }

       $totaldata=[];
      // i am using foreach loop to print all the data and save the data in $displaydata array
       foreach ($data as $data) {
        $totaldata[]=[
            "id"=>$data['id'],
            "user_id"=>$data['user_id'],
            "type"=>$data['type'],
            "credits"=>$data['credits'],
            "currency"=>$data['currency'],
            "description"=>$data['description'],
            "created_at"=>$data['created_at'],
        ];
       }
        //----------------------
        //RESPONSE SHOW HERE
        //----------------------
        if(!empty($totaldata)){
        http_response_code(200);
        echo json_encode(["status"=>true,"Message"=>"Data fetched successfully","Data"=>$totaldata]);
        exit;}
        else{
        http_response_code(401);
        echo json_encode(["status"=>false,"Message"=>"Data fetching error"]);
        exit;}
       
        break;
    // ---------------
    // CREATE CREDITS
    // ---------------    
    case 'POST':
    // this function is helping validing inputs
    function validateRequired($field, $value) {
    if (empty($value)) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "Message" => "$field is required"
        ]);
        exit;
    }
}
     $user_id=strtolower(trim($_POST['user_id']))??"";
     validateRequired("user_id", $_POST['user_id']);

     $type=strtolower(trim($_POST['type']))??"credit";
     validateRequired("type", $_POST['type']);
      if($type!=="credit" && $type!=="debit"){
        http_response_code(404);
        echo json_encode(["Status"=>false,"Message"=>"credits must be credit or debit"]);
        exit;
     }
     $credits=(int)$_POST['credits']??null;
     validateRequired("credits", $_POST['credits']);
     $currency=strtoupper(trim($_POST['currency']))??"USD";
     validateRequired("currency", $_POST['currency']);
     $description=$_POST['description']??"";
     validateRequired("description", $_POST['description']);

     $created_at = date('Y-m-d H:i:s');
     // --------------------
     // INSERT DATA IN DATABASE
     // -------------------- 
     $results=$db->insert("credits",[
            "user_id"=>$user_id,
            "type"=>$type,
            "credits"=>$credits,
            "currency"=>$currency,
            "description"=>$description,
            "created_at"=>$created_at,
     ]);
     // ------------------------------------------
     // CHECKING WHEATHER THE DATA INSERTED OR NOT
     // ------------------------------------------ 
     if(!$results){
        http_response_code(404);
        echo json_encode([
            "status" => false,
            "Message" => "error occure while creating new credit"
        ]);
        exit;
     }
        http_response_code(200);
        echo json_encode([
            "status" => true,
            "Message" => "new credit add successfully",
            "Data"=>$results
        ]);
        exit;
        break;
     // --------------------
     // CREDITS UPDATEING
     // --------------------   
    case 'PUT':
       // UPDATED DATA COMMING THROUGH INPUT METHOD

        $input=json_decode(file_get_contents("php://input"),true);
        $credit_id=$input['id'];
        if(empty($credit_id)){
            http_response_code(401);
            echo json_encode(["status"=>false,"Messege"=>"id missing"]);
            exit;
        }
         //CHECKING ROOM ID EXISTING IN TABLE  
        $credit_id_exist=$db->get("credits","*",['id'=>$credit_id]);
                // -------------------------------------------------------------
        // using if(isset()) to get those input only coming throught post
        // ------------------------------------------------------------          

         if(empty($credit_id_exist)){
            http_response_code(401);
            echo json_encode(["status"=>false,"Messege"=>"invalid id "]);
            exit;
        } 
        


        
    function validateRequired($field, $value) {
    if (empty($value)) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "Message" => "$field is required"
        ]);
        exit;
       }
     }
     $data=[];

     if($input['user_id']){
     $data['user_id']=strtolower(trim($input['user_id']))??"";
     validateRequired("user_id", $input['user_id']);}
     if($input['type']){
     $data['type']=strtolower(trim($input['type']))??"credit";
     validateRequired("type", $input['type']);
      if($type!=="credit" && $type!=="debit"){
        http_response_code(404);
        echo json_encode(["Status"=>false,"Message"=>"credits must be credit or debit"]);
        exit;
     }}
     if($input['credits']){
     $data['credits']=(int)$input['credits']??null;
     validateRequired("credits", $input['credits']);}
     if($input['currency']){
     $data['currency']=strtoupper(trim($input['currency']))??"USD";
     validateRequired("currency", $input['currency']);}
     if($input['description']){
     $data['description']=$input['description']??"";
     validateRequired("description", $input['description']);}

      //ADDING DATA IN DATABASE THROUGH MEDOO
     $result=$db->update("credits",$data,['id'=>$credit_id]);
         // ------------------------------------------
        // CHECKING WHEATHER THE DATA UPDATED OR NOT
        // ------------------------------------------
     if(!$results){
        http_response_code(404);
        echo json_encode([ "status" => false,"Message" => "error occure while updating credit"]);
        exit;
     }
        http_response_code(200);
        echo json_encode(["status" => true,"Message" => "credit updating successfully"]);
        exit;
        # code...
        break;
    

     // --------------------
     // CREDITS DELETE
     // --------------------
    case 'DELETE':  
        $input=json_decode(file_get_contents("php://input"),true);
        // -------------------------------
        // STAY ROOM ID PROPER VALIDATION
        // ------------------------------- 
        $credit_id=$input['id'];

        if(empty($credit_id)){
            http_response_code(401);
            echo json_encode(["Status"=>true,"Message"=>"credit id is missing"]);
            exit;
        }
        $credit_id_exist=$db->get("credits","*",['id'=>$credit_id]);
                
        if(empty($credit_id_exist)){
            http_response_code(401);
            echo json_encode(["Status"=>false,"Message"=>"credit id is is invalid"]);
            exit;
        }
        //DELETING DATA THROUGH MEDOO         
        $result=$db->delete("credits",['id'=>$credit_id]);
        if(empty($result)){
            http_response_code(401);
            echo json_encode(["Status"=>true,"Message"=>"credit deleting error occure"]);
            exit;
        }else{
            http_response_code(404);
            echo json_encode(["status"=>true,"Message"=>"Credit id deleted successfully"]);
            exit;
        }
        break;
    
    default:
            http_response_code(500);
            echo json_encode(["status"=>false,"Message"=>"Invalid method","data"=>$credit_user_id]);
            exit;
        break;
}

?>