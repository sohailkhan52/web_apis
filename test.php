// Success
200 OK - Successful request (GET, PUT, DELETE)
201 Created - Resource created successfully (POST)
202 Accepted - Request accepted for processing
204 No Content - Successful but no content to return


git status
git add .
git commit -m "Updated routes files"
 git push
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


id
name
c1
c2
c3
c4
c5
dev_mode
currency
status
order
default
note
