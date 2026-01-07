<?php 


// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($methods) {

// --------------------
// READ STAYS
// --------------------
    case 'GET':
        $staysData = $db->select("stays", "*");

        if(!$staysData){
           
            http_response_code(200);
            echo json_encode([
                "status" => true,
                "message" => "No data found",
                "data" => []
            ]);
            exit;
        }

        $totalData = [];
         // i am using foreach loop to print all the data and save the data in $totaldata array
        foreach ($staysData as $data) {
            
            $amenity_ids = json_decode($data['amenity_ids'] ?? '[]', true) ?: [];
            $translations = json_decode($data['translations'] ?? '{}', true) ?: [];
            $img = json_decode($data['img'] ?? '[]', true) ?: [];
            
            $totalData[] = [
                "id" => $data['id'],
                "status" => $data['status'],
                "user_id" => $data['user_id'],
                "name" => $data['name'],
                "slug" => $data['slug'],
                "featured" => $data['featured'],
                "hotel_order" => $data['hotel_order'],
                "stars" => $data['stars'],
                "rating" => $data['rating'],
                "location" => $data['location'],
                "location_coords" => $data['location_coords'],
                "address" => $data['address'],
                "img" => $img, 
                "currency" => $data['currency'],
                "discount" => $data['discount'],
                "refundable" => $data['refundable'],
                "checkin_time" => $data['checkin_time'],
                "checkout_time" => $data['checkout_time'],
                "booking_age_requirement" => $data['booking_age_requirement'],
                "email" => $data['email'],
                "phone" => $data['phone'],
                "website" => $data['website'],
                "meta_title" => $data['meta_title'],
                "meta_keywords" => $data['meta_keywords'],
                "cancellation_policy" => $data['cancellation_policy'],
                "privacy_policy" => $data['privacy_policy'],
                "amenity_ids" => $amenity_ids, 
                "translations" => $translations, 
                "created_at" => $data['created_at'],
                "updated_at" => $data['updated_at'],
                "desc" => $data['desc'],
                "meta_desc" => $data['meta_desc'],
                "stay_type" => $data['stay_type']
            ];
        }
        //----------------------
        //RESPONSE SHOW HERE
        //----------------------
        http_response_code(200);
        echo json_encode([
            "status" => true,
            "message" => "Data fetched successfully",
            "data" => $totalData
        ]);
        break;
    // --------------------
    // CREATE STaYS
    // -------------------- 
    case 'POST':
        // data coming through post method and properly arranged according to the requirement
        $status = 1;
        $user_id =  trim($_POST['user_id']) ?? "";
        $name =ucwords(trim($_POST['name'])) ?? "";
        $slug = strtolower(trim($_POST['slug'])) ?? "";
        $featured =  (int)$_POST['featured'] ?? 1;
        $hotel_order =  $_POST['hotel_order'] ?? "";
        $stars = $_POST['stars'] ?? "";
        $rating =$_POST['rating'] ?? "";
        $location = ucwords(trim($_POST['location'])) ?? "";
        $location_coords = trim($_POST['location_coords']) ?? "";
        $address = $_POST['address'] ?? "";     
        $currency =  strtoupper(trim($_POST['currency'])) ??"USD";
        $discount =  $_POST['discount'] ?? 0;
        $refundable = (int)$_POST['refundable'] ?? 1;
        $checkin_time = date("H:i:s");
        $checkout_time = "14:30:45";
        $booking_age_requirement = (int)$_POST['booking_age_requirement'] ?? 18;
        $email =strtolower(trim($_POST['email'])) ?? "";
        $phone =trim($_POST['phone']) ?? "";
        $website = filter_var($_POST['website'], FILTER_SANITIZE_URL) ?? "";
        
        $meta_title =ucwords($_POST['meta_title']) ?? "";
        $meta_keywords =ucwords(trim($_POST['meta_keywords'])) ?? "";
        $cancellation_policy =$_POST['cancellation_policy'] ?? "";
        $privacy_policy =$_POST['privacy_policy'] ?? "";
        $translations =$_POST['translations'] ?? '{}';
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $desc =$_POST['desc'] ?? "";
        $meta_desc = $_POST['meta_desc'] ?? "";
        $stay_type = $_POST['stay_type'] ?? "hotel";

        $img =trim($_POST['img']) ?? "";
        if (!empty($img)) {
            $path = json_encode([
                [
                    'url' => '/uploads/hotels/gallery/hotel_' . time() . '_' . basename($img),
                    'default' => true
                ]
            ]);
        } else {
            $path = '[]';
        }
   
        $amenity_ids_input =$_POST['amenity_ids'] ?? "";
        if (is_array($amenity_ids_input)) {
            $amenity_ids = json_encode($amenity_ids_input);
        } elseif (!empty($amenity_ids_input)) {
       
            $amenity_ids = json_encode(explode(',', $amenity_ids_input));
        } else {
            $amenity_ids = '[]';
        }
        


        //---------------
        //INPUT VALIDATION
        //----------------        
       if(empty($user_id) || empty($name) || empty($slug) || empty($hotel_order) || empty($location) || empty($location_coords) || empty($address) || empty($img) || empty($currency) || empty($booking_age_requirement) || empty($email) || empty($amenity_ids) || empty($phone) || empty($website )){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"All fields are required"
            ]);
            exit;
           }


        //---------------
        //EMAIL VALIDATION
        //----------------           
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                "status" => false,
                "message" => "Invalid email format"
            ]);
            exit;
        }
 
        
        //---------------
        //PHONE VALIDATION
        //----------------   
        if(!empty($phone) && !preg_match('/^[0-9\s\-\+\(\)]{10,20}$/', $phone)) {
        http_response_code(400);
        echo json_encode([
        "status" => false,
        "message" => "Invalid phone number format"
        ]);
        exit;  } 
        //---------------
        //booking VALIDATION
        //----------------       
        if($booking_age_requirement < 18){
            http_response_code(400); 
            echo json_encode([
                "status" => false,
                "message" => "Booking age must be 18 or above"
            ]);
            exit;
        }

        //checking existing slug by using medoo

        $existing = $db->select("stays", "id", ["slug" => $slug]);
        if ($existing) {
            http_response_code(409); 
            echo json_encode([
                "status" => false,
                "message" => "Slug already exists"
            ]);
            exit;
        }
        // --------------------
        // INSERT DATA IN DATABASE
        // --------------------    
        $result = $db->insert("stays", [
            "status" => $status,
            "user_id" => $user_id,
            "name" => $name,
            "slug" => $slug,
            "featured" => $featured,
            "hotel_order" => $hotel_order,
            "stars" => $stars,
            "rating" => $rating,
            "location" => $location,
            "location_coords" => $location_coords,
            "address" => $address,
            "img" => $path, 
            "currency" => $currency,
            "discount" => $discount,
            "refundable" => $refundable,
            "checkin_time" => $checkin_time,
            "checkout_time" => $checkout_time,
            "booking_age_requirement" => $booking_age_requirement,
            "email" => $email,
            "phone" => $phone,
            "website" => $website,
            "meta_title" => $meta_title,
            "meta_keywords" => $meta_keywords,
            "cancellation_policy" => $cancellation_policy,
            "privacy_policy" => $privacy_policy,
            "amenity_ids" => $amenity_ids, 
            "translations" => $translations,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "desc" => $desc,
            "meta_desc" => $meta_desc,
            "stay_type" => $stay_type,
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
        // UPDATE STAYS
        // --------------------   
    case 'PUT':
        $input=json_decode(file_get_contents("php://input"),true);
        $stay_id=$input['id'];
        // -------------------------------
        // USER ID CHECKING (EMPTY OR NOT)
        // -------------------------------   
        if(empty($stay_id)){
        http_response_code(404); 
            echo json_encode([
                "status" => false,
                "message" => "stay id missing"            
            ]);
            exit;
        }
        // -----------------------------------------------
        // USER ID VALIDATION (ID PRESENT IN TABLE OR NOT)
        // ----------------------------------------------- 
        $exist_stay_id=$db->get("stays","*",['id'=>$stay_id]);
        if(!$exist_stay_id){
        http_response_code(404); 
            echo json_encode([
                "status" => false,
                "message" => "target stay did not found"
                
            
            ]);
            exit;
        }
        // STTAYS ARRAY IS INITIALIZED TO STORE INPUT DATA

        $stays=[];
        // -------------------------------------------------------------
        // using if(isset()) to get those input only coming throught post
        // ------------------------------------------------------------
        if(isset($input['user_id'])){
        $stays["user_id"] =  trim($input['user_id']) ?? "";}
        if(isset($input['name'])){
        $stays["name"] = ucwords(trim($input['name'])) ?? "";}
        if(isset($input['slug'])){
        $stays["slug"] =strtolower(trim($input['slug'])) ?? "";}
        if(isset($input['featured'])){
        $stays["featured"]=  (int)$input['featured'] ?? 1;}
        if(isset($input['hotel_order'])){
        $stays["hotel_order"]=$input['hotel_order'] ?? "";}
        if(isset($input['stars'])){
        $stays["stars"]=$input['stars'] ?? "";}
        if(isset($input['rating'])){
        $stays["rating"]=$input['rating'] ?? "";}
        if(isset($input['location'])){
        $stays["location"]=ucwords(trim($input['location'])) ?? "";}
        if(isset($input['location_coords'])){
        $stays["location_coords"]=trim($input['location_coords']) ?? "";}
        if(isset($input['address'])){
        $stays["address"]=$input['address'] ?? "";     }
        if(isset($input['currency'])){
        $stays["currency"]=strtoupper(trim($input['currency'])) ??"USD";}
        if(isset($input['discount'])){
        $stays["discount"]=$input['discount'] ?? 0;}
        if(isset($input['refundable'])){
        $stays["refundable"]=(int)$input['refundable'] ?? 1;}
        if(isset($input['booking_age_requirement'])){
        $stays["booking_age_requirement"]=(int)$input['booking_age_requirement'] ?? 18;}
        if(isset($input['email'])){
        $stays["email"]=strtolower(trim($input['email'])) ?? "";}
        if(isset($input['phone'])){
        $stays["phone"]=trim($input['phone']) ?? "";}
        if(isset($input['website'])){
        $stays["website"]=filter_var($input['website'], FILTER_SANITIZE_URL) ?? "";}
        if(isset($input['meta_title'])){
        $stays["meta_title"]=ucwords($input['meta_title']) ?? "";}
        if(isset($input['meta_keywords'])){
        $stays["meta_keywords"]=ucwords(trim($input['meta_keywords'])) ?? "";}
        if(isset($input['cancellation_policy'])){
        $stays["cancellation_policy"]=$input['cancellation_policy'] ?? "";}
        if(isset($input['privacy_policy'])){
        $stays["privacy_policy"]=$input['privacy_policy'] ?? "";}
        if(isset($input['translations'])){
        $stays["translations"]=$input['translations'] ?? '{}';}
        if(isset($input['desc'])){
        $stays["desc"]=$input['desc'] ?? "";}
        if(isset($input['meta_desc'])){
        $stays["meta_desc"]=$input['meta_desc'] ?? "";}
        
        
        $stays["updated_at"]=date("Y-m-d H:i:s");
        
        if(isset($input['stay_type'])){
        $stays["stay_type"]=$input['stay_type'] ?? "hotel";}
        if(isset($input['img'])){
        $stays["img"]=trim($input['img']) ?? "";
        if (!empty($img)) {
            $path = json_encode([
                [
                    'url' => '/uploads/hotels/gallery/hotel_' . time() . '_' . basename($img),
                    'default' => true
                ]
            ]);
        } else {
            $path = '[]';
        }
        }
        if(isset($input['amenity_ids'])){
        $stays["amenity_ids"]=$input['amenity_ids'] ?? "";
        if (is_array($amenity_ids_input)) {
            $amenity_ids = json_encode($amenity_ids_input);
        } elseif (!empty($amenity_ids_input)) {
       
            $amenity_ids = json_encode(explode(',', $amenity_ids_input));
        } else {
            $amenity_ids = '[]';
        }
        }

        // ----------------
        // EMAIL VALIDATION
        // ---------------- 
        if(isset($stays["email"])&& !filter_var($stays["email"],FILTER_VALIDATE_EMAIL)){

            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"invalid email field"
            ]);
            exit;
        }

        // -------------------------------
        // PHONE NUMBER VALIDATION
        // ------------------------------- 
         if(isset($phone)&& !empty($phone) && !preg_match('/^[0-9\s\-\+\(\)]{10,20}$/', $phone)) {
         http_response_code(400);
         echo json_encode([
        "status" => false,
        "message" => "Invalid phone number format"
         ]);
        exit;
         }

        // -------------------------------
        // BOOKING USER AGE VALIDATION
        // ------------------------------- 
        if(isset($stays["booking_age_requirement"]) && $stays["booking_age_requirement"] < 18){

            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"Age must be greater than 17 "
            ]);
            exit;
        }
        
        // -------------
        // DATA UPDATING
        // ------------- 
        $result=$db->update("stays",$stays,['id'=>$stay_id]);


        // -------------------------------
        // CHECKING THE UPDATED PROCESS
        // ------------------------------- 
        if(!$result){
            http_response_code(404);
            echo json_encode([
                "Status"=>false,
                "Message"=>"error occure while updating Stays"
            ]);
            exit;
        }
        else{
            http_response_code(200);
            echo json_encode([
                "Status"=>true,
                "Message"=>"stays successfully updated",
                "Data"=>$result
            ]);
            exit;
        }
        break;
        // --------------
        // STAYS DELETION
        // -------------- 
    case 'DELETE':
        $input=json_decode(file_get_contents("php://input"),true);
        $stay_id=$input['id'];
         
        // -------------------------------
        // STAY PROPER ID VALIDATION
        // ------------------------------- 
        if(empty($stay_id)){
        http_response_code(404); 
            echo json_encode([
                "status" => false,
                "message" => "stay id missing"            
            ]);
            exit;
        }

        $exist_stay_id=$db->get("stays","*",['id'=>$stay_id]);
        if(!$exist_stay_id){
        http_response_code(404); 
            echo json_encode([
                "status" => false,
                "message" => "target stay did not found"
                
            
            ]);
            exit;
        }

        $result=$db->delete("stays",['id'=>$stay_id]);

        if(!$result){
            http_response_code(404); 
            echo json_encode([
                "status" => false,
                "message" => "error occure while deleting stay"            
            ]);
            exit;
        }
        else{
            http_response_code(200); 
            echo json_encode([
                "status" => true,
                "message" => "stay id deleted successfully"            
            ]);
            exit;
        }
        break;
    
    default:
            http_response_code(404); 
            echo json_encode([
                "message" => "INVALID METHOD"            
            ]);
            exit;
            break;
}
?>