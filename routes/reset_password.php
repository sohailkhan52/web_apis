<?php 
// Include database  



use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$token=$_GET['token'];
$user_id=$_GET['id'];
if(!$user_id){
    echo json_encode(["error"=>"user_id is missing"]);
    exit;
}
if(!$token){
    echo json_encode(["error"=>"token is missing"]);
    exit;
}else {
 
    $access_token=$db->get("users","*",['reset_token'=>$token,'id'=>$user_id]);
    if(!$access_token){
        echo json_encode(["error"=>"access token is empty"]);
    exit;
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
   }

   body {
    height: 100vh;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
   }

   .card {
    background: #fff;
    width: 100%;
    max-width: 400px;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
   }

   .card h2 {
    text-align: center;
    margin-bottom: 10px;
    color: #333;
  }

  .card p {
    text-align: center;
    color: #666;
    font-size: 14px;
    margin-bottom: 25px;
  }

  .form-group {
    margin-bottom: 15px;
  }

  .form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
  }

  .form-group input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 14px;
  }

  .form-group input:focus {
    outline: none;
    border-color: #6366f1;
  }

  button {
    width: 100%;
    padding: 12px;
    background: #4f46e5;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
  }

  button:hover {
    background: #4338ca;
  }

  .message {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
  }

  .message.success {
    color: green;
  }

  .message.error {
    color: red;
}
</style>
</head>

<body>

<div class="card">
    <h2>Reset Password</h2>
    <p>Enter your new password below</p>

    <form method=post action="update_password.php?token=<?php echo $token?>&id=<?php echo $user_id?>">
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name=password id="password" required>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name=cpassword id="confirm" required>
        </div>

        <button type="submit">Reset Password</button>
    </form>

    <div class="message" id="message"></div>
</div>
</body>
</html>
