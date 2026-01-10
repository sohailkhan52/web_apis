// Success
200 OK - Successful request (GET, PUT, DELETE)
201 Created - Resource created successfully (POST)
202 Accepted - Request accepted for processing
204 No Content - Successful but no content to return
//password 2wtd4BW0sI1L




git status
git add .
git commit -m "Updated routes files"
git push -u origin main


// Client Errors
400 Bad Request - Invalid request syntax/parameters ✅ Use for: invalid input
401 Unauthorized - Authentication required ✅ Use for: missing/invalid token
403 Forbidden - No permission ✅ Use for: insufficient permissions
404 Not Found - Resource doesn't exist ✅ Use for: ID not found
422 Unprocessable Entity - Validation errors

// Server Errors
500 Internal Server Error - General server error ✅ Use for: database/unknown errors
501 Not Implemented
502 Bad Gateway
503 Service Unavailable
504 Gateway Timeout


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");






function response($statusCode, $status, $message, $data = null) {
    http_response_code($statusCode);
    $res = [
        "status" => $status,
        "message" => $message
    ];
    if ($data !== null) {
        $res["data"] = $data;
    }
    echo json_encode($res);
    exit;
}

response(200, true, "Data fetched successfully", $displayData);
$data = $db->select("logs_searchs",
    [
        "[<]users" => ["logs_searchs.user_id" => "users.id"]
    ],
    [
        "logs_searchs.user_id",
        "users.first_name",
        "users.last_name"
    ]
);



      $data = $db->select("stay_rooms",
         [
             "[<]stay" => ["stay_id" => "id"]
         ],
         [  "stay_rooms.id",
            "stay_rooms.stay_id",
            "stay_rooms.room_type_id",
            "stay_rooms.room_images",
            "stay_rooms.amenities",
            "stay_rooms.status",
            "stay_rooms.room_options",
            "stay_rooms.created_at",
            "stay_rooms.updated_at",
          ]
        );

Full texts
id
stay_id
room_type_id
room_images
amenities
status
room_options
created_at
updated_at
      
,

       
       "id"=>$_POST["id"];
       "first_name"=>$_POST["first_name"];
       "last_name"=>$_POST["last_name"];
       "user_id"=>$_POST["user_id"];
       "module"=>$_POST["module"];
       "request"=>$_POST["request"];
       "created_at"=>$_POST["created_at"];
       "ip"=>$_POST["ip"];