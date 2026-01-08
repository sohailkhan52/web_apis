<?php


function response($code,$status,$message,$data =null){
    http_response_code($code);
    $response=[];
    $response[]=["status"=>$status,"Message"=>$message];
    if($data!==null)
        $response["data"]=$data;
    json_encode([$response]);exit;
}
switch ($methods) {
    case 'GET':
        $data=$db->select("payment_gateways","*");
        if(!isset($data)){
          response(401,false,"data missing in table");
        }

        $displayData=[];
        break;
    case 'POST':
        # code...
        break;
    case 'PUT':
        # code...
        break;
    case 'DELETE':
        # code...
        break;
    
    default:
        # code...
        break;
}


?>