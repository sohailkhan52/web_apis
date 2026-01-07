<?php

require __DIR__ . '/../middleware/auth.php';
// ====================
// VALIDATION HELPERS
// ====================

function Validation($field, $value) {
    if (!isset($value) || trim($value) === '') {
        http_response_code(400);
        echo json_encode(["status"=>false,"message"=>"$field cannot be empty"]);
        exit;
    }
}

function ValidationInt($field, $value) {
    if (!filter_var($value, FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(["status"=>false,"message"=>"$field must be an integer"]);
        exit;
    }
}

function ValidationPrice($field, $value) {
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
        http_response_code(400);
        echo json_encode(["status"=>false,"message"=>"$field must be a valid number"]);
        exit;
    }
}

switch ($methods) {
    // -------------
    // READ bookings
    // -------------
    case 'GET':

        //data getting through medoo
        $data=$db->select("bookings","*");

        // checking weather the data is fetched or not       
        if(empty($data)){
            http_response_code(401);
            echo json_encode(['Status'=>false,"Message"=>"data fetching error"]);
            exit;
        }
        // i ma initializaing  array to store the fetched data in specific order
        $totaldata=[];

        // using foreach loop for the iteration of data  and store data in totaldata array
        foreach ($data as $datum) {
            $totaldata[]=[
                
              "id"=>$datum['id'],
              "invoice_id"=>$datum['invoice_id'],
              "booking_date"=>$datum['booking_date'],
              "booking_status"=>$datum['booking_status'],
              "price_original"=>$datum['price_original'],
              "price_markup"=>$datum['price_markup'],
              "agent_earning"=>$datum['agent_earning'],
              "vat"=>$datum['vat'],
              "tax"=>$datum['tax'],
              "gst"=>$datum['gst'],
              "first_name"=>$datum['first_name'],
              "last_name"=>$datum['last_name'],
              "email"=>$datum['email'],
              "address"=>$datum['address'],
              "phone_country_code"=>$datum['phone_country_code'],
              "phone"=>$datum['phone'],
              "country"=>$datum['country'],
              "stars"=>$datum['stars'],
              "adults"=>$datum['adults'],
              "infants"=>$datum['infants'],
              "childs"=>$datum['childs'],
              "child_ages"=>$datum['child_ages'],
              "currency_original"=>$datum['currency_original'],
              "currency_markup"=>$datum['currency_markup'],
              "payment_date"=>$datum['payment_date'],
              "cancellation_request"=>$datum['cancellation_request'],
              "cancellation_status"=>$datum['cancellation_status'],
              "booking_data"=>$datum['booking_data'],
              "payment_status"=>$datum['payment_status'],
              "supplier"=>$datum['supplier'],
              "transaction_id"=>$datum['transaction_id'],
              "user_id"=>$datum['user_id'],
              "user_data"=>$datum['user_data'],
              "guest"=>$datum['guest'],
              "nationality"=>$datum['nationality'],
              "payment_gateway"=>$datum['payment_gateway'],
              "module_type"=>$datum['module_type'],
              "pnr"=>$datum['pnr'],
              "booking_response"=>$datum['booking_response'],
              "error_response"=>$datum['error_response'],
              "cost"=>$datum['cost'],
              "final_price"=>$datum['final_price'],
              "earning"=>$datum['earning'],
              "final_earning"=>$datum['final_earning'],
              "module"=>$datum['module'],
            ];
        }

        // RESPONSE SHOWS HERE OF GETTING HERE
        if(empty($totaldata)){
            http_response_code(401);
            echo json_encode(["status"=>false,"Message"=>"data managing error"]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode(["Status"=>true,"Message data fetched successfuly","data"=>$totaldata]);
            exit;
        }
        break;
    // --------------
    // CREATE BOOKING
    //---------------
    case 'POST':
    
        // ALL INPUTS COMMING FORM POST AND WITH SPECIFIC VALIDATION 
        $invoice_id=trim($_POST['invoice_id'])??"";
        Validation("invoice_id",$_POST['invoice_id']);
        ValidationInt("invoice_id",$_POST['invoice_id']);
        $booking_date=date("Y-m-d H:i:s");
        $booking_status=strtolower(trim($_POST['booking_status']));
        Validation("booking_status",$_POST['booking_status']);
        $price_original=trim($_POST['price_original'])??0;
        ValidationPrice("price_original",$_POST['price_original']);
        $price_markup=trim($_POST['price_markup'])??NULL;
        ValidationPrice("price_markup",$_POST['price_markup']);
        $agent_earning=trim($_POST['agent_earning'])??NULL;
        ValidationPrice("agent_earning",$_POST['agent_earning']);
        $vat=(int)trim($_POST['vat']);
        ValidationInt("vat",$_POST['vat']);
        $tax=(int)trim($_POST['tax'])??"";
        ValidationInt("tax",$_POST['tax']);
        $gst=(int)trim($_POST['gst'])??0;
        ValidationInt("gst",$_POST['gst']);
        $first_name=ucwords(trim($_POST['first_name']))??"";
        Validation("first_name",$_POST['first_name']);
        $last_name=ucwords(trim($_POST['last_name']))??"";
        Validation("last_name",$_POST['last_name']);
        $email=strtolower(trim($_POST['email']));
        Validation("email",$_POST['email']);
        $address=$_POST['address']??"";
        $phone_country_code=trim($_POST['phone_country_code']);
        Validation("phone_country_code",$_POST['phone_country_code']);
        $phone=trim($_POST['phone']);
        Validation("phone",$_POST['phone']);
        $country=ucwords(trim($_POST['country']));
        Validation("country",$_POST['country']);
        ValidationInt("country",$_POST['country']);
        $stars=(int)trim($_POST['stars']);
        Validation("stars",$_POST['stars']);
        ValidationInt("stars",$_POST['stars']);
        $adults=(int)trim($_POST['adults']);
        Validation("adults",$_POST['adults']);
        ValidationInt("adults",$_POST['adults']);
        $infants=(int)trim($_POST['infants'])??0;
        ValidationInt("infants",$_POST['infants']);
        $childs=(int)trim($_POST['childs'])??0;
        ValidationInt("childs",$_POST['childs']);
        $child_ages=trim($_POST['child_ages'])??"";
        $currency_original=strtoupper(trim($_POST['currency_original']))??"USD";
        $currency_markup=strtoupper(trim($_POST['currency_markup']))??"USD";
        $payment_date=date("Y-m-d H:i:s");
        $updated_at=date("Y-m-d H:i:s");
        $cancellation_request=trim($_POST['cancellation_request'])??0;
        ValidationInt("cancellation_request",$_POST['cancellation_request']);
        $cancellation_status=trim($_POST['cancellation_status'])??0;
        ValidationInt("cancellation_status",$_POST['cancellation_status']);
        $booking_data=trim($_POST['booking_data']);
        Validation("booking_data",$_POST['booking_data']);
        $payment_status=strtolower(trim($_POST['payment_status']));
        Validation("payment_status",$_POST['payment_status']);
        $supplier=ucwords(trim($_POST['supplier']))??"";
        $transaction_id=strtoupper(trim($_POST['transaction_id']));
        Validation("transaction_id",$_POST['transaction_id']);
        $user_id=trim($_POST['user_id']);
        Validation("user_id",$_POST['user_id']);
        ValidationInt("user_id",$_POST['user_id']);
        $user_data=$_POST['user_data']??"";
        $guest=$_POST['guest']??'';
        $nationality=strtoupper(trim($_POST['nationality']));
        Validation("nationality",$_POST['nationality']);
        $payment_gateway=strtolower(trim($_POST['payment_gateway']));
        Validation("payment_gateway",$_POST['payment_gateway']);
        $module_type=strtolower(trim($_POST['module_type']));
        Validation("module_types",$_POST['module_types']);
        $pnr=strtoupper(trim($_POST['pnr']));
        Validation("pnr",$_POST['pnr']);
        $booking_response=trim($_POST['booking_response']);
        Validation("booking_response",$_POST['booking_response']);
        $error_response=trim($_POST['error_response']);
        Validation("error_response",$_POST['error_response']);
        $cost=trim($_POST['cost']);
        ValidationPrice("cost",$_POST['cost']);
        $final_price=trim($_POST['final_price'])??0;
        ValidationPrice("final_price",$_POST['final_price']);
        $earning=trim($_POST['earning']);
        Validation("earning",$_POST['earning']);
        ValidationPrice("earning",$_POST['earning']);
        $final_earning=trim($_POST['final_earning']);
        Validation("final_earning",$_POST['final_earning']);
        ValidationPrice("final_earning",$_POST['final_earning']);
        $module=$_POST['module']??"";
        
        // ADDITIONAL VALIDATION FOR BOOKING STATUS
        if($booking_status!="pending" &&$booking_status!="cancelled" && $booking_status!="confirmed"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"booking status must be pending cancelled or confirmed"]);
            exit;
        }
        
        // ADDITIONAL VALIDATION FOR  STATS THAT CAN BE 1 TO 5
        if($stars<-1 &&$stars>5){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Stars can only be upto 5"]);
            exit;
        }
        
        // ADDITIONAL VALIDATION FOR  EMAIL 
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Email must be valid email"]);
            exit;
        }
        $eemail=$db->get("bookings","*",["email"=>$email]);
        if($eemail){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Email already existing"]);
            exit;
        }

        // ADDITIONAL VALIDATION FOR ALDUTS THAT THERE SHOULD BE ATLEAST ONE ADULTS
        if($adults<1){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Adults must be greater than 0"]);
            exit;
        }
        
        // ADDITIONAL VALIDATION FOR COST THAT POSITIVE INTEGER
        if($cost<0 ){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"cost must be greater than 0"]);
            exit;
        }

        // ADDITIONAL VALIDATION FOR PAYMENT STATUS 
        if($payment_status!="paid" && $payment_status!="unpaid"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"payment status must be paid or unpaid only"]);
            exit;
        }

        // ADDITIONAL VALIDATION FOR MODULES TYPE ACCORDING TO THE REQUIREMENT
        if($module_type!="hotels"&& $module_type!="flights"&&$module_type!="tours"&&$module_type!="visas"&&$module_type!="cars"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"modules type must be hotels flights tours visas or cars only"]);
            exit;
        }

        // ADDITIONAL VALIDATION FOR BOOKING STATUS
        $result=$db->insert("bookings",[
               "invoice_id"=>$invoice_id,
               "booking_date"=>$booking_date,
               "booking_status"=>$booking_status,
               "price_original"=>$price_original,
               "price_markup"=>$price_markup,
               "agent_earning"=>$agent_earning,
               "vat"=>$vat,
               "tax"=>$tax,
               "gst"=>$gst,
               "first_name"=>$first_name,
               "last_name"=>$last_name,
               "email"=>$email,
               "address"=>$address,
               "phone_country_code"=>$phone_country_code,
               "phone"=>$phone,
               "country"=>$country,
               "stars"=>$stars,
               "adults"=>$adults,
               "updated_at"=>$updated_at,
               "infants"=>$infants,
               "childs"=>$childs,
               "child_ages"=>$child_ages,
               "currency_original"=>$currency_original,
               "currency_markup"=>$currency_markup,
               "payment_date"=>$payment_date,
               "cancellation_request"=>$cancellation_request,
               "cancellation_status"=>$cancellation_status,
               "booking_data"=>$booking_data,
               "payment_status"=>$payment_status,
               "supplier"=>$supplier,
               "transaction_id"=>$transaction_id,
               "user_id"=>$user_id,
               "user_data"=>$user_data,
               "guest"=>$guest,
               "nationality"=>$nationality,
               "payment_gateway"=>$payment_gateway,
               "module_type"=>$module_type,
               "pnr"=>$pnr,
               "booking_response"=>$booking_response,
               "error_response"=>$error_response,
               "cost"=>$cost,
               "final_price"=>$final_price,
               "earning"=>$earning,
               "final_earning"=>$final_earning,
               "module"=>$module,
        ]);

        //THE RESPONSE OF POST METHOD SHOWS HERE
        if(!$result){
            http_response_code(404);
            echo json_encode(['status'=>false,"Message"=>"error occure while creating new booking"]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode(['status'=>true,"Message"=>"New booking successfully created ","data"=>$result]);
            
            exit;
        }
        break;
    //-------------------
    //UPDATE BOOKINGS
    //-------------------    
    case 'PUT':


        // DATA COMMING THROUGH UPDATE METHOD  
        $input=json_decode(file_get_contents("php://input"),true);

        //TAKING  INPUT ID AND CHECKING THE EXISTANCE OF THAT INPUT ID
        $input_id=$input['id'];
        if (!$input_id) {
            http_response_code(401);
            echo json_encode(["status"=>false,"Message"=>"id missing"]);exit;
        }
        $input_id_exist=$db->get("bookings","*",["id"=>$input_id]);
        if (!$input_id_exist) {
            http_response_code(401);
            echo json_encode(["status"=>false,"Message"=>"invalid id"]);exit;
        }

        // I AM USING DATA ARRAY TO STORE ALL THE ENTERED INPUTS WITH PROPER VALIDATION 
        $data=[];

        if(isset($input["invoice_id"])){
        $data["invoice_id"]=trim($input['invoice_id'])??"";
        Validation("invoice_id",$input['invoice_id']);
        ValidationInt("invoice_id",$input['invoice_id']);}

        if(isset($input["booking_status"])){
        $data["booking_status"]=strtolower(trim($input['booking_status']));
        Validation("booking_status",$input['booking_status']);
        if($booking_status!="pending" &&$booking_status!="cancelled" && $booking_status!="confirmed"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"booking status must be pending cancelled or confirmed"]);
            exit;
        }}
        if(isset($input["price_original"])){
        $data["price_original"]=trim($input['price_original'])??0;
        ValidationPrice("price_original",$input['price_original']);}

        if(isset($input["price_markup"])){
        $data["price_markup"]=trim($input['price_markup'])??NULL;
        ValidationPrice("price_markup",$input['price_markup']);}

        if(isset($input["agent_earning"])){
        $data["agent_earning"]=trim($input['agent_earning'])??NULL;
        ValidationPrice("agent_earning",$input['agent_earning']);}

        if(isset($input["vat"])){
        $data["vat"]=trim($input['vat']);
        ValidationInt("vat",$input['vat']);}

        if(isset($input["tax"])){
        $data["tax"]=trim($input['tax'])??"";
        ValidationInt("tax",$input['tax']);}

        if(isset($input["gst"])){
        $data["gst"]=trim($input['gst'])??0;
        ValidationInt("gst",$input['gst']);}

        if(isset($input["first_name"])){
        $data["first_name"]=ucwords(trim($input['first_name']))??"";
        Validation("first_name",$input['first_name']);}

        if(isset($input["last_name"])){
        $data["last_name"]=ucwords(trim($input['last_name']))??"";
        Validation("last_name",$input['last_name']);}

        $data["updated_at"] = date("Y-m-d H:i:s");


        if(isset($input["email"])){
        $data["email"]=strtolower(trim($input['email']));
        Validation("email",$input['email']);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Email must be valid email"]);
            exit;
        }
        $eemail=$db->get("bookings","*",["email"=>$email]);
        if($eemail){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Email already existing"]);
            exit;
        }}

        if(isset($input["address"])){
        $data["address"]=$input['address']??"";}

        if(isset($input["phone_country_code"])){
        $data["phone_country_code"]=trim($input['phone_country_code']);
        Validation("phone_country_code",$input['phone_country_code']);}

        if(isset($input["phone"])){
        $data["phone"]=trim($input['phone']);
        Validation("phone",$input['phone']);}

        if(isset($input["country"])){
        $data["country"]=ucwords(trim($input['country']));
        Validation("country",$input['country']);
        ValidationInt("country",$input['country']);}

        if(isset($input["stars"])){
        $data["stars"]=trim($input['stars']);
        Validation("stars",$input['stars']);
        ValidationInt("stars",$input['stars']);
        if($stars<-1 &&$stars>5){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Stars can only be upto 5"]);
            exit;
        }}

        if(isset($input["adults"])){
        $data["adults"]=trim($input['adults']);
        Validation("adults",$input['adults']);
        ValidationInt("adults",$input['adults']);
        if($adults<1){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"Adults must be greater than 0"]);
            exit;
        }}

        if(isset($input["infants"])){
        $data["infants"]=trim($input['infants'])??0;
        ValidationInt("infants",$input['infants']);}

        if(isset($input["childs"])){
        $data["childs"]=trim($input['childs'])??0;
        ValidationInt("childs",$input['childs']);}

        if(isset($input["child_ages"])){
        $data["child_ages"]=trim($input['child_ages'])??"";}

        if(isset($input["currency_original"])){
        $data["currency_original"]=strtoupper(trim($input['currency_original']))??"USD";
        Validation("currency_original",$input['currency_original']);}


        if(isset($input["currency_markup"])){
        $data["currency_markup"]=strtoupper(trim($input['currency_markup']))??"USD";
        Validation("currency_markup",$input['currency_markup']);}

        if(isset($input["cancellation_request"])){
        $data["cancellation_request"]=trim($input['cancellation_request'])??0;
        ValidationInt("cancellation_request",$input['cancellation_request']);}

        if(isset($input["cancellation_status"])){
        $data["cancellation_status"]=trim($input['cancellation_status'])??0;
        ValidationInt("cancellation_status",$input['cancellation_status']);}

        if(isset($input["booking_data"])){
        $data["booking_data"]=trim($input['booking_data']);
        Validation("booking_data",$input['booking_data']);}

        if(isset($input["payment_status"])){
        $data["payment_status"]=strtolower(trim($input['payment_status']));
        Validation("payment_status",$input['payment_status']);
        if($payment_status!="paid" && $payment_status!="unpaid"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"payment status must be paid or unpaid only"]);
            exit;
        }}

        if(isset($input["supplier"])){
        $data["supplier"]=ucwords(trim($input['supplier']))??"";}

        if(isset($input["transaction_id"])){
        $data["transaction_id"]=strtoupper(trim($input['transaction_id']));
        Validation("transaction_id",$input['transaction_id']);}

        if(isset($input["user_id"])){
        $data["user_id"]=trim($input['user_id']);
        Validation("user_id",$input['user_id']);
        ValidationInt("user_id",$input['user_id']);}

        if(isset($input["user_data"])){
        $data["user_data"]=$input['user_data']??"";}

        if(isset($input["guest"])){
        $data["guest"]=$input['guest']??'';}

        if(isset($input["nationality"])){
        $data["nationality"]=strtoupper(trim($input['nationality']));
        Validation("nationality",$input['nationality']);}

        if(isset($input["payment_gateway"])){
        $data["payment_gateway"]=strtolower(trim($input['payment_gateway']));
        Validation("payment_gateway",$input['payment_gateway']);}

        if(isset($input["module_type"])){
        $data["module_type"]=strtolower(trim($input['module_type']));
        Validation("module_types",$input['module_types']);
        if($module_type!="hotels"&& $module_type!="flights"&&$module_type!="tours"&&$module_type!="visas"&&$module_type!="cars"){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"modules type must be hotels flights tours visas or cars only"]);
            exit;
        }}

        if(isset($input["pnr"])){
        $data["pnr"]=strtoupper(trim($input['pnr']));
        Validation("pnr",$input['pnr']);}

        if(isset($input["booking_response"])){
        $data["booking_response"]=trim($input['booking_response']);
        Validation("booking_response",$input['booking_response']);}

        if(isset($input["error_response"])){
        $data["error_response"]=trim($input['error_response']);
        Validation("error_response",$input['error_response']);}

        if(isset($input["cost"])){
        $data["cost"]=trim($input['cost']);
        ValidationPrice("cost",$input['cost']);
        if($cost<0 ){
            http_response_code(404);
            echo json_encode(["Status"=>false,"Message"=>"cost must be greater than 0"]);
            exit;
        }}

        if(isset($input["final_price"])){
        $data["final_price"]=trim($input['final_price'])??0;
        ValidationPrice("final_price",$input['final_price']);}

        if(isset($input["earning"])){
        $data["earning"]=trim($input['earning']);
        Validation("earning",$input['earning']);
        ValidationPrice("earning",$input['earning']);}

        if(isset($input["final_earning"])){
        $data["final_earning"]=trim($input['final_earning']);
        Validation("final_earning",$input['final_earning']);
        ValidationPrice("final_earning",$input['final_earning']);}

        if(isset($input["module"])){
        $data["module"]=$input['module']??"";}
        
        // checking weather the data is empty or not 
        if(!$data){
            http_response_code(401);
            echo json_encode(["status"=>false,"message"=>"updated data getting error"]);exit;
        }

        // DATA UPDATING THROUGH MEDOO IN BOOKINGS API
        $result=$db->update("bookings",$data,['id'=>$input_id]);

        //RESPONSE SHOWS HERE
        if(!$result){
            http_response_code(404);
            echo json_encode(['status'=>false,"Message"=>"error occure while updating booking"]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode(['status'=>true,"Message"=>"booking successfully updated ","data"=>$result]);
            
            exit;
        }
        break;
    
    case 'DELETE':
        // DELETING BOOKING ID
        $input=json_decode(file_get_contents("php://input"),true);
        //GETTING ID THEN WITH PROPER VALIDATION CECKING THAT ID IN THE TABLE 
        $input_id=$input['id'];
        if (!$input_id) {
            http_response_code(401);
            echo json_encode(["status"=>false,"Message"=>"id missing"]);exit;
        }
        $input_id_exist=$db->get("bookings","*",["id"=>$input_id]);
        if (!$input_id_exist) {
            http_response_code(401);
            echo json_encode(["status"=>false,"Message"=>"invalid id"]);exit;
        }
        
        // THE TARGET DATA IS DELETED THROUGHT MEDOO QUERRY 
        $result=$db->delete("bookings",["id"=>$input_id]);

        // CHECKING THE RESULT IN RESPONSE 
        if(!$result){
            http_response_code(404);
            echo json_encode(['status'=>false,"Message"=>"error occure while deleting booking"]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode(['status'=>true,"Message"=>"booking successfully deleted "]);
            
            exit;
        }     
        break;
    
    default:
        http_response_code(404);
            echo json_encode(['status'=>FALSE,"Message"=>"INVALID METHOD PLEASE TRY AGAIN WITH VALID METHOD A"]);
            
            exit;
        break;
}

?>