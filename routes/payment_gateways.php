<?php
// --------------------
// AUTHENTICATION
// --------------------
require __DIR__."/../middleware/auth.php";

// --------------------------------
// HELPER FUNCTION TO SHOW RESPONSE 
// --------------------------------
function response($code,$status,$message,$data =null){
    http_response_code($code);
    $response=[];
    $response[]=["status"=>$status,"Message"=>$message];
    if($data!==null)
        $response["data"]=$data;
    echo json_encode([$response]);exit;
}
switch ($methods) {
// --------------------
// READ PAYMENT GATWAYS
// --------------------
    case 'GET':
        $data=$db->select("payment_gateways","*");
        if(!isset($data)){
          response(401,false,"data missing in table");
        }
        // THIS ARRAY IS INITIALIZED TO STORE DATA FOR DISPLAY
        $displayData=[];
         // i am using foreach loop to print all the results and save the data in $data array
        foreach($data as $datum){
            $displayData[]=[
                "id"=>$datum["id"],
                "name"=>$datum["name"],
                "c1"=>$datum["c1"],
                "c2"=>$datum["c2"],
                "c3"=>$datum["c3"],
                "c4"=>$datum["c4"],
                "c5"=>$datum["c5"],
                "dev_mode"=>$datum["dev_mode"],
                "currency"=>$datum["currency"],
                "status"=>$datum["status"],
                "order"=>$datum["order"],
                "default"=>$datum["default"],
                "note"=>$datum["note"],
            ];
        }
        // GET RESPONSE WILL SHOW HERE
            if(empty($displayData)){
                response(401,false,"data managing error table");
            }
            else{
                response(200,true,"data fetched Successfully",$displayData);
            }

        break;
    // --------------------
    // CREATE PAYMENT GATEWAYS
    // --------------------
    case 'POST':
        // data coming through post method and properly arranged and validated according to the requirement
        $name=ucwords($_POST["name"]);
        if(empty($name))
            {response(401,false,"Name cannot be empty");}
        $c1=ucwords($_POST["c1"])??"";
        $c2=ucwords($_POST["c2"])??"";
        $c3=ucwords($_POST["c3"])??"";
        $c4=ucwords($_POST["c4"])??"";
        $c5=ucwords($_POST["c5"])??"";
        $dev_mode=trim($_POST["dev_mode"]);
        if ($dev_mode === null || $dev_mode === '') 
            {response(401, false, "dev mode cannot be empty");}
        if ($dev_mode != 0 && $dev_mode != 1) 
            {response(404, false, "Dev mode can only be 0 or 1");}
        $currency=strtoupper(trim($_POST["currency"]))??"";
        if(empty($currency))
        {response(401,false,"Currency cannot be empty");}
        if (!preg_match('/^[A-Z]+$/i', $currency)) 
            {response(400, false, "Currency must contain only letters");}
        if(strlen($currency)!=3)
            {response(404,false,"Currency length must be 3 characters");}
        $status=$_POST["status"];
        if($status === null || $status === '')
        {response(401,false,"status cannot be empty");}
        if ($status != 0 && $status != 1) 
            {response(404, false, "status can only be 0 or 1");}
        $order=$_POST["order"];
        if($order === null || $order === '')
        {response(401,false,"order cannot be empty");}
        if(!is_numeric($order))
            {response(401,false,"order must be numeric");}
        $default=1;
        $note=ucfirst($_POST["note"])??"";

        $data=[];
        $data[]=[
           "name"=>$name,
           "c1"=>$c1,
           "c2"=>$c2,
           "c3"=>$c3,
           "c4"=>$c4,
           "c5"=>$c5,
           "dev_mode"=>$dev_mode,
           "currency"=>$currency,
           "status"=>$status,
           "order"=>$order,
           "default"=>$default,
           "note"=>$note,
        ];
        $result=$db->insert("payment_gateways",$data);
        // if the addition of new cms fails then show the response
         if(empty($result))
        {response(404,false,"error occure while creating new order");}
        // if the addition of new cms adds successfully then show the reponse
        else
        {response(404,false,"new order created successfully",$data);}
        break;

    //---------------------
    // UPDATE PAYMENT GATEWAYS
    //---------------------
    case 'PUT':
        $input=json_decode(file_get_contents("php://input"),true);
        $input_id=$input['id'];
        if(empty($input_id))
        {response(404,false,"payment id is empty");}
        // it checks the id  and find the existance of the airport
        $input_id_exist=$db->get("payment_gateways","*",["id"=>$input_id]);
        if(empty($input_id_exist))
        {response(404,false,"payment id not found");}

        //   i ma using $data array which stores the input fields  which can be used to updated 
        $data=[];
        if(isset($input['name'])){
            $data["name"]=ucwords($input["name"]);
        if(empty($name))
            {response(401,false,"Name cannot be empty");}}
        if(isset($input['c1'])){
            $data["c1"]=ucwords($input["c1"])??"";}

        if(isset($input['c2'])){
            $data["c2"]=ucwords($input["c2"])??"";}
            
        if(isset($input['c3'])){
            $data["c3"]=ucwords($input["c3"])??"";}

        if(isset($input['c4'])){
            $data["c4"]=ucwords($input["c4"])??"";}

        if(isset($input['c5'])){
            $data["c5"]=ucwords($input["c5"])??"";}

        if(isset($input['dev_mode'])){
            $data["dev_mode"]=$input["dev_mode"];
        if ($dev_mode === null || $dev_mode === '') 
            {response(401, false, "dev mode cannot be empty");}
        if ($dev_mode != 0 && $dev_mode != 1) 
            {response(404, false, "Dev mode can only be 0 or 1");}}
        
        if(isset($input['currency'])){
            $data["currency"]=strtoupper(trim($input["currency"]))??"";
        if(empty($currency))
        {response(401,false,"Currency cannot be empty");}
        if (!preg_match('/^[A-Z]+$/i', $currency)) 
            {response(400, false, "Currency must contain only letters");}
        if(strlen($currency)!=3)
            {response(404,false,"Currency length must be 3 characters");}}
        
        if(isset($input['status'])){
            $data["status"]=$input["status"];
        if($status === null || $status === '')
        {response(401,false,"status cannot be empty");}
        if ($status != 0 && $status != 1) 
            {response(404, false, "status can only be 0 or 1");}}
        
        if(isset($input['name'])){
            $data["name"]=$input["order"];
        if($order === null || $order === '')
        {response(401,false,"order cannot be empty");}
        if(!is_numeric($order))
            {response(401,false,"order must be numeric");}}
        
        if(isset($input['note'])){
            $data["note"]=ucfirst($input["note"])??"";}

        if(empty($data))
        {response(401,false,"there must be aleast one updating field");}    

             //using medoo TO UPDATE THE FIELDS
        $result=$db->update("payment_gateways",$data,['id'=>$input_id]);
        // RESPONSE WILL SHOW HERE
        if(empty($result))
        {response(404,false,"error occure while updating");}
        else
        {response(200,true,"payment_gateways updated successfully",$data);}        
        break;
    case 'DELETE':
        // DELETING PAYMENTS GATWAY ID
        $input=json_decode(file_get_contents("php://input"),true);
        //GETTING ID THEN WITH PROPER VALIDATION CECKING THAT ID IN THE TABLE 
        $input_id=$input['id'];
        if(empty($input_id))
        {response(404,false,"payment id is empty");}
        $input_id_exist=$db->get("payment_gateways","*",["id"=>$input_id]);
        if(empty($input_id_exist))
        {response(404,false,"payment id not found");}

        // THE TARGET DATA IS  DELETED THROUGHT MEDOO QUERRY 

        $result=$db->delete("payment_gateways",['id'=>$input_id]);
        // CHECKING THE RESULT IN RESPONSE 
        if(empty($result))
        {response(404,false,"error occure while deleting");}
        else
        {response(200,true,"payment_gateways deleted successfully");}        
        break;

        break;
    
    default:
        response(404,false,"invalid method");
        break;
}


?>