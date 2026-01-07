<?php


// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($methods) {
    // --------------------
    // READ MODULES
    // --------------------
    case 'GET':
        $data=$db->select("modules","*");

        if(empty($data)){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"error occure while fetching data"
            ]);
            exit;
        }

        $displayData=[];
         // i am using foreach loop to print all the data and save the data in $displaydata array
        foreach ($data as $data) {
            $displayData[]=[
                "id"=>$data['id'],
                "name"=>$data['name'],
                "active"=>$data['active'],
                "type"=>$data['type'],
                "status"=>$data['status'],
                "c1"=>$data['c1'],
                "c2"=>$data['c2'],
                "c3"=>$data['c3'],
                "c4"=>$data['c4'],
                "c5"=>$data['c5'],
                "c6"=>$data['c6'],
                "dev_mode"=>$data['dev_mode'],
                "payment_mode"=>$data['payment_mode'],
                "currency"=>$data['currency'],
                "module_color"=>$data['module_color'],
                "order"=>$data['order'],
                "prn_type"=>$data['prn_type'],
                "markup_type_b2c"=>$data['markup_type_b2c'],
                "markup_b2c"=>$data['markup_b2c'],
                "icon"=>$data['icon'],
                "markup_type_b2b"=>$data['markup_type_b2b'],
                "markup_b2b"=>$data['markup_b2b'],
                "check_balance"=>$data['check_balance'],
                "content_import"=>$data['content_import'],
                "host"=>$data['host'],
                "database"=>$data['database'],
                "username"=>$data['username'],
                "password"=>$data['password'],
            ];
        }

        $last10 = array_slice($displayData, -10);
        //----------------------
        //RESPONSE SHOW HERE
        //----------------------
            http_response_code(200);
            echo json_encode([
                "status"=>true,
                "Message"=>"data get successfully",
                "total" => count($displayData),
                "data" => $last10,
                "showing" => "last 10 of " . count($displayData)
            ]);
            exit;
        
        break;
    // --------------------
    // CREATE MODULES
    // -------------------- 
    case 'POST':
        // data coming through post method and properly arranged according to the requirement
        $name=strtolower(trim($_POST['name']))??"";
        $type=strtolower(trim($_POST['type']))??"";
       
        $active = (int)$_POST['active'] ?? 1;
        // --------------------
        // VALIDATING ACTIVE
        // --------------------
        if ($active != 0 && $active != 1) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"active must be 0 or 1"
        ]);
        exit;
        }
         $status = $_POST['status'] ?? '';
        // --------------------
        // VALIDATING STATUS
        // --------------------
        if($status !== '' && $status != 0 && $status != 1) {
        http_response_code(400);
         echo json_encode([
        "status"=>false,
        "Message"=>"status must be '', 0 or 1"
         ]);
        exit;
         }


        $c1=trim($_POST['c1'])??null;
        $c2=trim($_POST['c2'])??null;
        $c3=trim($_POST['c3'])??null;
        $c4=trim($_POST['c4'])??null;
        $c5=trim($_POST['c5'])??null;
        $c6=trim($_POST['c6'])??null;
        
         $dev_mode = (int)$_POST['dev_mode'] ?? 1;
        // --------------------
        // VALIDATING DEV_MODE
        // --------------------
        if ($dev_mode != 0 && $dev_mode != 1) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"dev_mode must be 0 or 1"
        ]);
        exit;
        }
        
        $payment_mode = (int)$_POST['payment_mode'] ?? "";
        // -----------------------
        // VALIDATING PAYMENT_MODE
        // -----------------------
        if ($payment_mode != 0 && $payment_mode != 1 ) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"payment mode must be  0 or 1"
        ]);
        exit;
        }
        $prn_type = (int)$_POST['prn_type'] ?? "";
        // --------------------
        // VALIDATING PRN TYPE
        // --------------------
        if ($prn_type != 0 && $prn_type != 1 ) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"prn type must be  0 or 1"
        ]);
        exit;

        }
        $currency=strtoupper(trim($_POST['currency']))??"";
        // --------------------
        // VALIDATING CURRENCY
        // --------------------
        if(empty($currency) || !preg_match('/^[A-Z]{3}$/', $currency)) {
        http_response_code(400);
         echo json_encode([
        "status"=>false,
        "Message"=>"Currency must be 3 uppercase letters (e.g., USD, EUR)"
         ]);
         exit;
         }
        $module_color=$_POST['module_color']??NULL;
        $order=(int)$_POST['order']??1;
        $markup_type_b2c=$_POST['markup_type_b2c']??"percentage";
        // ----------------------------
        // MARKUP B2C AND ITS VALDATION
        // ----------------------------
        $markup_b2c = $_POST['markup_b2c'] ?? null;
        if ($markup_b2c !== null) {
        // Check if it's a valid integer
        if (filter_var($markup_b2c, FILTER_VALIDATE_INT) !== false) {
        $markup_b2c = (int)$markup_b2c;
         } else {
        // Handle invalid input
        $markup_b2c = null; // or set to default value
        http_response_code(404);
        echo json_encode ([
            "status"=>false,
            "Message"=> "Please enter a valid integer"]);
        }
        }
        $icon=strtolower(trim($_POST['icon']));
        $markup_type_b2b=$_POST['markup_type_b2b']??"percentage";
        // ----------------------------
        // MARKUP B2B AND ITS VALDATION
        // ----------------------------
        $markup_b2b = $_POST['markup_b2b'] ?? null;
        if ($markup_b2b !== null) {
        // Check if it's a valid integer
        if (filter_var($markup_b2b, FILTER_VALIDATE_INT) !== false) {
        $markup_b2b = (int)$markup_b2b;
         } else {
        // Handle invalid input
        $markup_b2b = null; // or set to default value
        http_response_code(404);
        echo json_encode ([
            "status"=>false,
            "Message"=> "Please enter a valid integer"]);
        }
        }
        $check_balance=(int)$_POST['check_balance']??1;
        $content_import=(int)$_POST['content_import']??0;
        $host=$_POST['host'];
        $database=strtolower(trim($_POST['database']))??NULL;
        $username=strtolower(trim($_POST['username']))??NULL;
        $password=trim($_POST['password'])??NULL;

        //---------------
        //INPUT VALIDATION
        //----------------  
        if(empty($name)|| empty($type)||  empty($order)||empty($markup_type_b2c) || empty($markup_type_b2b)|| empty($icon)|| empty($active))
        {
            http_response_code(401);
            echo json_encode([
                "status"=>false,
                "Message"=>"All fields are required"
            ]);
            exit;
        }
        // --------------------
        // INSERT DATA IN DATABASE
        // --------------------          
        $result=$db->insert("modules",[
            "name"=>$name,
            "active"=>$active,
            "type"=>$type,
            "status"=>$status,
            "c1"=>$c1,
            "c2"=>$c2,
            "c3"=>$c3,
            "c4"=>$c4,
            "c5"=>$c5,
            "c6"=>$c6,
            "dev_mode"=>$dev_mode,
            "payment_mode"=>$payment_mode,
            "currency"=>$currency,
            "module_color"=>$module_color,
            "order"=>$order,
            "prn_type"=>$prn_type,
            "markup_type_b2c"=>$markup_type_b2c,
            "markup_b2c"=>$markup_b2c,
            "icon"=>$icon,
            "markup_type_b2b"=>$markup_type_b2b,
            "markup_b2b"=>$markup_b2b,
            "check_balance"=>$check_balance,
            "content_import"=>$content_import,
            "host"=>$host,
            "database"=>$database,
            "username"=>$username,
            "password"=>$password
        ]);
        // ------------------------------------------
        // CHECKING WHEATHER THE DATA INSERTED OR NOT
        // ------------------------------------------ 
                if(!$result){
            http_response_code(500); 
            echo json_encode([
                "status" => false,
                "message" => "Error occurred while creating data"
            ]);
            exit;
        } else {
            http_response_code(201); 
            echo json_encode([
                "status" => true,
                "message" => "Data created successfully",
                "data" => [
                    "id" =>$result
                ]
            ]);
            exit;
        }
        
        break;
        // --------------------
        // STAYS ROOM UPDATEING
        // --------------------
    case 'PUT':
        // ------------------------------------------
        // UPDATED DATA COMMING THROUGH INPUT METHOD
        // ------------------------------------------ 
        $input=json_decode(file_get_contents("php://input"),true);
        $module_id=$input['id'];

        $check_module_id=$db->get("modules","*",['id'=>$module_id]);
        if(!$check_module_id){
            http_response_code(500); 
            echo json_encode([
                "status" => false,
                "message" => "in valid id please enter valid id"
            ]);
            exit;
        }

        $moduledata=[];

        // -------------------------------------------------------------
        // using if(isset()) to get those input only coming throught post
        // ------------------------------------------------------------
        if(isset($input['name'])){
        $moduledata['name']=strtolower(trim($input['name']))??"";}
        if(isset($input['type'])){
        $moduledata['type']=strtolower(trim($input['type']))??"";}
        if(isset($input['active'])){
        $moduledata['active']= (int)$input['active'] ?? 1;
       // --------------------
       // VALIDATING ACTIVE
       // --------------------
        if ($active != 0 && $active != 1) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"active must be 0 or 1"
        ]);
        exit;
        }}

        if(isset($input['status'])){
        $moduledata['status']=$input['status'] ?? '';
        // --------------------
        // VALIDATING STATUS
        // --------------------
        if($status !== '' && $status != 0 && $status != 1) {
        http_response_code(400);
         echo json_encode([
        "status"=>false,
        "Message"=>"status must be '', 0 or 1"
         ]);
        exit;
         }}

        if(isset($input['c1'])){
        $moduledata['c1']=trim($input['c1'])??null;}
        if(isset($input['c2'])){
        $moduledata['c2']=trim($input['c2'])??null;}
        if(isset($input['c3'])){
        $moduledata['c3']=trim($input['c3'])??null;}
        if(isset($input['c4'])){
        $moduledata['c4']=trim($input['c4'])??null;}
        if(isset($input['c5'])){
        $moduledata['c5']=trim($input['c5'])??null;}
        if(isset($input['c6'])){
        $moduledata['c6']=trim($input['c6'])??null;}
        
        if(isset($input['dev_mode'])){
        $moduledata['dev_mode']=$input['dev_mode'] ?? 1;
        // --------------------
        // VALIDATING DEV_MODE
        // --------------------
        if ($dev_mode != 0 && $dev_mode != 1) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"dev_mode must be 0 or 1"
        ]);
        exit;
        }}
 
        if(isset($input['payment_mode'])){
        $moduledata['payment_mode']=$input['payment_mode'] ?? "";
        // -----------------------
        // VALIDATING PAYMENT_MODE
        // -----------------------
        if ($payment_mode != 0 && $payment_mode != 1 ) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"payment mode must be  0 or 1"
        ]);
        exit;
        }}

        if(isset($input['prn_type'])){
        $moduledata['prn_type']=$input['prn_type'] ?? "";
        // --------------------
        // VALIDATING PRN TYPE
        // --------------------
        if ($prn_type != 0 && $prn_type != 1 ) {
        http_response_code(404);
        echo json_encode([
            "status"=>false,
            "Message"=>"prn type must be  0 or 1"
        ]);
        exit;
        }
        }
        if(isset($input['currency'])){
        $moduledata['currency']=strtoupper(trim($input['currency']))??"";
        // --------------------
        // VALIDATING CURRENCY
        // --------------------
        if(empty($currency) || !preg_match('/^[A-Z]{3}$/', $currency)) {
        http_response_code(400);
         echo json_encode([
        "status"=>false,
        "Message"=>"Currency must be 3 uppercase letters (e.g., USD, EUR)"
         ]);
         exit;
         }}
        if(isset($input['module_color'])){
        $moduledata['module_color']=$input['module_color']??NULL;}
        if(isset($input['order'])){
        $moduledata['order']=(int)$input['order']??1;}
        if(isset($input['markup_type_b2c'])){
        $moduledata['markup_type_b2c']=$input['markup_type_b2c']??"percentage";}
        // ----------------------------
        // MARKUP B2C AND ITS VALDATION
        // ----------------------------
        if(isset($input['markup_b2c'])){
        $moduledata['markup_b2c']=$input['markup_b2c'] ?? null;
        if ($markup_b2c !== null) {
        // Check if it's a valid integer
        if (filter_var($markup_b2c, FILTER_VALIDATE_INT) !== false) {
        $markup_b2c = (int)$markup_b2c;
         } else {
        // Handle invalid input
        $markup_b2c = null; // or set to default value
        http_response_code(404);
        echo json_encode ([
            "status"=>false,
            "Message"=> "Please enter a valid integer"]);
        }}
        }
        if(isset($input['icon'])){
        $moduledata['icon']=strtolower(trim($input['icon']));}

        if(isset($input['markup_type_b2b'])){
        $moduledata['markup_type_b2b']=$input['markup_type_b2b']??"percentage";}
        // ----------------------------
        // MARKUP B2B AND ITS VALDATION
        // ----------------------------
        if(isset($input['markup_b2b'])){
        $moduledata['markup_b2b']=$input['markup_b2b'] ?? null;
        if ($markup_b2b !== null) {
        // Check if it's a valid integer
        if (filter_var($markup_b2b, FILTER_VALIDATE_INT) !== false) {
        $markup_b2b = (int)$markup_b2b;
         } else {
        // Handle invalid input
        $markup_b2b = null; // or set to default value
        http_response_code(404);
        echo json_encode ([
            "status"=>false,
            "Message"=> "Please enter a valid integer"]);
        }
        }}
        if(isset($input['check_balance'])){
        $moduledata['check_balance']=(int)$input['check_balance']??1; }
        if(isset($input['content_import'])){
        $moduledata['content_import']=(int)$input['content_import']??0;}
        if(isset($input['host'])){
        $moduledata['host']=$input['host'];}
        if(isset($input['database'])){
        $moduledata['database']=strtolower(trim($input['database']))??NULL;}
        if(isset($input['username'])){
        $moduledata['username']=strtolower(trim($input['username']))??NULL;}
        if(isset($input['password'])){
        $moduledata['password']=trim($input['password'])??NULL;}


        $result=$db->update("modules",$moduledata,['id'=>$module_id]);

        // ------------------------------------------
        // CHECKING WHEATHER THE DATA UPDATED OR NOT
        // ------------------------------------------ 
         if(!$result){
            http_response_code(500); 
            echo json_encode([
                "status" => false,
                "message" => "Error occurred while updating data"
            ]);
            exit;
        } else {
            http_response_code(201); 
            echo json_encode([
                "status" => true,
                "message" => "Data updated successfully",
                "data" => [
                    "id" =>$result
                ]
            ]);
            exit;
        }
        //-----------------------
        break;
    
    case 'DELETE':
        $input=json_decode(file_get_contents("php://input"),true);
        $module_id=$input['id'];
        // -------------------------------
        // MODULES ID PROPER VALIDATION
        // ------------------------------- 
        $check_module_id=$db->get("modules","*",['id'=>$module_id]);
        if(!$check_module_id){
            http_response_code(500); 
            echo json_encode([
                "status" => false,
                "message" => "in valid id please enter valid id"
            ]);
            exit;
        }
        $result=$db->delete("modules",['id'=>$module_id]);

        if(!$result){
            http_response_code(500); 
            echo json_encode([
                "status" => false,
                "message" => "Error occurred while Deleting data"
            ]);
            exit;
        } else {
            http_response_code(201); 
            echo json_encode([
                "status" => true,
                "message" => "Data Deleted successfully",
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