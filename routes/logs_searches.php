<?php
// --------------------
// AUTHENTICATION
// --------------------
require __DIR__."/../middleware/auth.php";
$auth_id=$decoded->id;
// --------------------------------
// HELPER FUNCTION TO SHOW RESPONSE 
// --------------------------------
function response($code,$status,$message,$data =null){
http_response_code($code);
$res=[];
$res[]=["status"=>$status,"Message"=>$message];
if($data!==null){
    $res["Data"]=$data;
}
echo json_encode([$res]);exit;
}

switch ($methods) {
// --------------------
// READ LOGS SEARCHES
// --------------------
    case 'GET':
        // MEDOO IS USED TO FETCH DATA
      $data = $db->select("logs_searches",
         [
             "[<]users" => ["user_id" => "user_id"]
         ],
         [   "logs_searches.id",
             "logs_searches.user_id",
             "logs_searches.module",
             "logs_searches.request",
             "logs_searches.created_at",
             "logs_searches.ip",
             "users.first_name",
             "users.last_name"]
     );
         if(empty($data))
        {response(404,false,"data fetching error occurer please try again");}

    // THIS ARRAY IS INITIALIZED TO STORE DATA FOR DISPLAY
    $displayData=[];
    // i am using foreach loop to print all the results and save the data in $data array
    foreach ($data as $datum) {
        $displayData[]=[
         "id"=>$datum["id"],
         "first_name"=>$datum["first_name"],
         "last_name"=>$datum["last_name"],
         "user_id"=>$datum["user_id"],
         "module"=>$datum["module"],
         "request"=>$datum["request"],
         "created_at"=>$datum["created_at"],
         "ip"=>$datum["ip"],
          ];
    }

    // GET RESPONSE WILL SHOW HERE
    if(empty($displayData))
        {response(404,false,"data Managing error");}
    else
        {response(404,false,"data fetched successfully",$displayData);}
        break;
    // -----------------------
    // CREATE LOGS SEARCHES
    // -----------------------    
    case 'POST':
        // data coming through post method and properly arranged and validated according to the requirement
       $user_id=$db->select("users","user_id",["id"=>$auth_id]);
       if(!$user_id)
        {$user_id=2;}       
       $module=strtolower(trim($_POST["module"]));
       if(empty($module))
          {response(400,false,"module cannot be empty");}
       if($module!=="stays"&& $module!=="flights"&& $module!=="tours"&& $module!=="cars"&&  $module!=="visa")
          {response(404,false,"module must be stays flights tours cars or visa");}
       $request=$_POST["request"];
       if(empty($request))
          {response(404,false,"request cannot be empty");}
       $request = json_decode($request, true);
       if (json_last_error() !== JSON_ERROR_NONE) {
           response(400, false, "Invalid JSON format");
       }
       $created_at=date("Y-m-d H:i:s");
       $ip=trim($_POST["ip"])??1;
       if(empty($ip))
          {response(400,false,"enter ip first");}
       // I HAVE CREATED DATA ARRAY TO SHOW THE DATA IN RESULT
       $totalinput=[];
       $totalinput=[
       "user_id"=>$user_id,
       "module"=>$module,
       "request"=>$request,
       "created_at"=>$created_at,
       "ip"=>$ip,
       ];
       //MEDOO IS USED TO INSERTED IN DATABASE
       $result=$db->insert("logs_searches",$totalinput);
        // if the addition of new LOGS SEARCH fails then show the response
       if(!$result)
          {response(404,false,"new Logs Searches creating error");} 
        // if the addition of new LOGS SEARCH adds successfully then show the reponse
       else {response(200,true,"new logs search created successfully",$totalinput);}    
        break;
    //---------------------
    // UPDATE LOGS SEARCH
    //---------------------    
    case 'PUT':
         $input=json_decode(file_get_contents("php://input"),true);
   
        $input_id=$input['id'];
        if(!$input_id)
        {response(401,false,"Target id missing");}
        // it checks the id  and find the existance of the airport
        $input_id_exist=$db->get("logs_searches","*",['id'=>$input_id]);
        if(!$input_id_exist)
        {response(401,false,"invalid id");}

        //i ma using $data array which stores the input fields  which can be used to updated 
        $data=[];
        $user_id=$db->select("users","user_id",["id"=>$auth_id]);
        $data["user_id"]=$user_id;
        if(!$user_id)
        {$user_id=2;
        $data["user_id"]=$user_id;} 
        if(isset($input['module'])){         
        $data["module"]=strtolower(trim($input["module"]));
        if($data["module"]!=="stays"&& $data["module"]!=="flights"&& $data["module"]!=="tours"&& $data["module"]!=="cars"&&  $data["module"]!=="visa")
          {response(404,false,"module must be stays flights tours cars or visa");}}

        if(isset($input['request'])){         
        $data["request"]=$input["request"];
        if(empty($request))
          {response(404,false,"request cannot be empty");}
        $request = json_decode($request, true);
        if (json_last_error() !== JSON_ERROR_NONE) 
        {response(400, false, "Invalid JSON format");}}

        $created_at=date("Y-m-d H:i:s");

        if(isset($input['ip'])){         
        $data["ip"]=trim($input["ip"])??1;
        if(empty($ip))
          {response(400,false,"enter ip first");}}
         //using medoo TO UPDATE THE FIELDS
        $result=$db->update("logs_searches",$data,['id'=>$input_id]);
        // RESPONSE WILL SHOW HERE
        if(!$result)
          {response(404,false,"Logs Searches updating error");} 
        else {response(200,true,"logs search updated successfully",$data);}   
        break;
    
    case 'DELETE':
        // DELETING LOGS SEARCH ID
         $input=json_decode(file_get_contents("php://input"),true);
        //GETTING ID THEN WITH PROPER VALIDATION CECKING THAT ID IN THE TABLE 
   
        $input_id=$input['id'];
        if(!$input_id)
        {response(401,false,"Target id missing");}
        $input_id_exist=$db->get("logs_searches","*",['id'=>$input_id]);
        if(!$input_id_exist)
        {response(401,false,"invalid id");}
        // THE TARGET DATA IS  DELETED THROUGHT MEDOO QUERRY 
        $result=$db->delete("logs_searches",['id'=>$input_id]);
        // CHECKING THE RESULT IN RESPONSE 
        if(!$result)
          {response(404,false,"Logs Searches deleting error");} 
        else {response(200,true,"logs search deleted successfully");}   
        break;
    
    default:
        # code...
        break;
}
?>