<?php


// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';
//----------------------------
//VALIDATION HELPER FUNCTION 
//----------------------------
function  validate($field,$value){
    if(!isset($value) || trim($value) === ''){
        http_response_code(422);
        echo json_encode(['Status'=>false,"Message"=>"$field cannnot be empty"]);exit;
    }
}

switch ($methods) {
    // -----------------
    // READ transactions
    // -----------------
    case 'GET':
        //data getting through medoo
        $data=$db->select("transactions","*");
        // checking wether the data is fetched or not   
        if(!$data){
            http_response_code(401);
            echo json(["Status"=>true,"Message"=>"empty data in the table"]);exit;
            }
        // i ma initializaing  array to store the fetched data in specific order

        $displayData=[];
        // using foreach loop for the iteration of data  and store data in totaldata array

        foreach ($data as $datum) {
               $displayData[]=[
                "id"=>$datum["id"],
                "user_id"=>$datum["user_id"],
                "trx_id"=>$datum["trx_id"],
                "type"=>$datum["type"],
                "date"=>$datum["date"],
                "payment_gateway"=>$datum["payment_gateway"],
                "amount"=>$datum["amount"],
                "currency"=>$datum["currency"],
                "description"=>$datum["description"],
                "attachment"=>$datum["attachment"],
                "status"=>$datum["status"],
                "created_by"=>$datum["created_by"],
                "created_at"=>$datum["created_at"],
               ];
            }
            // RESPONSE SHOWS HERE OF GETTING HERE

            if(!$displayData){
                http_response_code(400);
                echo json_encode(["status"=>false,"Message"=>"data managing error"]);exit;
            }
            else{
                http_response_code(200);
                echo json_encode(['Status'=>true,"Message"=>"Data fetched Successfully","Data"=>$displayData]);exit;
            }
        
        break;
    // -------------------
    // CREATE TRANSACTION
    //--------------------
    case 'POST':
        // ALL INPUTS COMMING FORM POST AND WITH SPECIFIC VALIDATION 
        $user_id=trim($_POST["user_id"]);
        validate("user_id",$_POST["user_id"]);
        if(!is_numeric($user_id)){
            http_response_code(404);
            echo json_encode(['status'=>false,"message"=>"User id should be numeric"]);exit;
        }
        
        $trx_id="TRX".date("YmdHis").rand(1000, 9999);
        $type=strtolower(trim($_POST["type"]));
        validate("type",$_POST["type"]);
        if($type!="credit"&&$type!="debit"){
            http_response_code(404);{
                echo json_encode(["Status"=>false,"Message"=>"Type can only be credit or debit"]);exit;
            }
        }
        $date=date("Y-m-d H:i:s");
        $payment_gateway=ucwords($_POST["payment_gateway"]);
        validate("payment_gateway",$_POST["payment_gateway"]);
        $amount=$_POST["amount"];
        validate("amount",$_POST["amount"]);
        if (!is_numeric($amount) || $amount <= 0) {
            http_response_code(422);
            echo json_encode(["status"=>false, "Message"=>"Amount must be a positive number"]);exit;
         }
        $currency=strtoupper(trim($_POST["currency"]));
        validate("currency",$_POST["currency"]);
        if (strlen($currency) !== 3) {
            http_response_code(422);
            echo json_encode(["status"=>false, "Message"=>"Currency must be a 3-letter code"]);exit;            
         }
        $description=ucfirst($_POST["description"]);
        validate("description",$_POST["description"]);
        if (strlen($description) > 255) {
            http_response_code(422);
            echo json_encode(["status"=>false, "Message"=>"Description too long"]);exit; 
           }
        $attachment=$_POST["attachment"];
        $attachment = $_POST["attachment"] ?? null;
        $status=$_POST["status"]??"pending";
        $created_by=ucwords($_POST["created_by"]);
        validate("created_by",$_POST["created_by"]);
        $created_at=date("Y-m-d H:i:s");
    
        $result=$db->insert("transactions",[
             "user_id"=>$user_id,
             "trx_id"=>$trx_id,
             "type"=>$type,
             "date"=>$date,
             "payment_gateway"=>$payment_gateway,
             "amount"=>$amount,
             "currency"=>$currency,
             "description"=>$description,
             "attachment"=>$attachment,
             "status"=>$status,
             "created_by"=>$created_by,
             "created_at"=>$created_at,
        ]);
          if(empty($result)){
                 http_response_code(401);
                 echo json_encode(['Status'=>false,"Message"=>"error occure while updating",["trx_id" => $trx_id] ]);exit;              
            }
             else{
                 http_response_code(200);
                 echo json_encode(['Status'=>true,"Message"=>"status updated successfully","status"=>$status]);exit;       
             }        
        break;
    //-------------------
    //UPDATE TRANSACTIONS
    //------------------- 
    case 'PUT':
        
        // DATA COMMING THROUGH UPDATE METHOD  
        $input=json_decode(file_get_contents("php://input"),true);
        //TAKING  INPUT ID AND CHECKING THE EXISTANCE OF THAT INPUT ID
        $input_id=$input['id'];
        validate("id",$input['id']);
        
        $input_id_exist=$db->get("transactions","*",['id'=>$input_id]);
        if(empty($input_id_exist)){
            http_response_code(401);
            echo json_encode(['Status'=>false,"Message"=>"invalid id"]);exit;
        }
        //I WE WILL ONLY VALIDATE STATUS IN TRANSACTIONS
        $status=strtolower(trim($input['status']));

        
        validate("status",$status);
        if($status!="success"&&$status!="failed"&& $status!="cancelled"&& $status!="pending"){
            http_response_code(401);
            echo json_encode(['Status'=>false,"Message"=>"status must be pending success failed or cancelled"]);exit;           
        }
        $result=$db->update("transactions",['status'=>$status],['id'=>$input_id]);
        if(empty($result)){
             http_response_code(401);
            echo json_encode(['Status'=>false,"Message"=>"error occure while updating"]);exit;              
        }
        else{
            http_response_code(200);
            echo json_encode(['Status'=>true,"Message"=>"status updated successfully","status"=>$status]);exit;       
        }
        break;
    
    case 'DELETE':
        // DELETING BOOKING ID
        $input=json_decode(file_get_contents("php://input"),true);
        //GETTING ID THEN WITH PROPER VALIDATION CECKING THAT ID IN THE TABLE 

        $input_id=$input['id'];
        validate("id",$input_id);
        $input_id_exist=$db->get("transactions","*",['id'=>$input_id]);
        if (empty($input_id_exist) || $input_id_exist['status'] === "cancelled") {
            http_response_code(400);
            echo json_encode(['status'=>false,"Message"=>"invalid id or cancelled status "]);exit;
        }
        // THE TARGET DATA IS SOFTLY DELETED THROUGHT MEDOO QUERRY 

        $result=$db->update("transactions",["status"=>"cancelled"],["id"=>$input_id]);
        // CHECKING THE RESULT IN RESPONSE 
        if(empty($result)){
            http_response_code(404);
            echo json_encode(['status'=>false,"Message"=>"Error occure while transaction deleting "]);exit;
        }        
        else{
            http_response_code(200);
            echo json_encode(['status'=>true,"Message"=>"transaction deleted successfully"]);exit;
        }
        break;
    
    default:
        # code...
        break;
}

?>