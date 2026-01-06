<?php 
header("content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Include database  

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$password=$_POST['password'];
$cpassword=$_POST['cpassword'];
if($password!=$cpassword){
    echo json_encode(["error: "=>"confirm password does not match"]);
}
$hash= password_hash($password, PASSWORD_DEFAULT);
$token=$_GET['token'];
$user_id=$_GET['id'];
if(!$user_id){
    echo json_encode(["user_id is missing"]);
}
if(!$token){
    echo json_encode(["token is missing"]);
    exit;
}
    $update=$db->update("users",[
        "password"=>$hash,
        "reset_token"=>null,
        "reset_token_expires"=>null
    ],[
        "reset_token"=>$token,'id'=>$user_id]);
if($update->rowCount()>0){
    echo json_encode([
        "success"=>"password has been changed successfully"
    ]);
    exit;

}
if($update){
    echo json_encode([
        "error"=>"password has not been changed please try again"
    ]);
    exit;
}
