<?php    


header("content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$email=$_POST['email'];


if(!$email){echo json_encode([
    "error:"=>"email is required"
]);}


    $user=$db->get("users","*",['email'=>$email]);

    if(!$user){
        echo json_encode(["error: "=>"email does not exist"]);
    }else{
    $payload = [
    "id"    => $user['id'],
    "email" => $user['email'],
    "fname"  => $user['first_name'],
    "iat"   => time(),
    "exp"   => time() + 900 // 15 minutes
];

// Create JWT
$reset_token = JWT::encode($payload, $jwt_secret, 'HS256');


        $expiry=date("Y-m-d H:i:s",strtotime("+1 day"));

        $update=$db->update("users",[
            "reset_token"=>"$reset_token",
            "reset_token_expires"=>"$expiry"
        ],["email"=>$email]);
        if(!$update){
            echo json_encode([
                "error="=>"the reset token and expiry error occure"
            ]);
        }else{
            $reset_link="http://localhost/project_medoo/reset_password.php?token=".$reset_token."&id=".$user["id"];
          
               $mail = new PHPMailer(true);
           
               $mail->isSMTP();
               $mail->Host       = "smtp.gmail.com";
               $mail->SMTPAuth   = true;
               $mail->Username   = "sohailkhan52117@gmail.com";
               $mail->Password   = "xazd pgtj wuym bzza";
               $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
               $mail->Port       = 587;
           
               $mail->setFrom("your_email@gmail.com", "Your App");
               $mail->addAddress($email, $user["first_name"]);
           
               $mail->isHTML(true);
               $mail->Subject = "Reset Your Password";
               $mail->Body = "
                   <h3>Hello {$user['first_name']}</h3>
                   <p>Click below to reset your password:</p>
                   <a href='{$reset_link}'>Reset Password</a>
                   <p>This link expires in 1 hour.</p>
               ";
           
               $mail->send();
           

            echo json_encode([
                "message"=>" the reset_link sent successfully",
                "reset-link"=>$reset_link
            ]);
            exit;
        }
        }
    
?>