<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // This is for debug, on release this should be *.yiays.com

// API response framework
define('VALID_RESPONSE',    200);
define('VALIDATION_ERROR',  400);
define('UNAUTHORIZED',      401);
define('UNKNOWN_REQUEST',   404);
define('SERVER_ERROR',      500);
define('TO_BE_IMPLEMENTED', 501);
$CODE_DESC = [
    VALIDATION_ERROR  => "Part of the request failed validation, check the length, range and value of parameters in the request. No changes have been made.",
    UNAUTHORIZED      => "You must be logged in, provide a public key or a token to continue.",
    UNKNOWN_REQUEST   => "The request sent wasn't recognised by the server. No changes have been made.",
    SERVER_ERROR      => "There was a serverside error while handling the request, changes may have been made. This error has been recorded and reported to the developers.",
    TO_BE_IMPLEMENTED => "This feature hasn't been implemented yet, please check back later. No changes have been made."
];
function generic_error($error){
    global $CODE_DESC;
    http_response_code($error);
    die(json_encode(['desc' => $CODE_DESC[$error]]));
}
function specific_error($error, $desc){
    global $CODE_DESC;
    http_response_code($error);
    die(json_encode(['desc' => $desc]));
}

$method = $_SERVER['REQUEST_METHOD'];
$params = explode('/', substr($_SERVER['REQUEST_URI'], 5));

if(array_count_values($params)>0){
    switch($params[0]){
        case "test":
            http_response_code(VALID_RESPONSE);
            die(json_encode(['desc' => "$_SERVER[REQUEST_URI]"]));
        break;
        
        
    }
}

// If nothing else is called...
generic_error(UNKNOWN_REQUEST);
?>