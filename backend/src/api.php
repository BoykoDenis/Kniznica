<?php
namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Server\JsonApiServer;
use Enm\JsonApi\Serializer\Deserializer;
use Enm\JsonApi\Serializer\Serializer;
use Enm\JsonApi\Model\Request\Request;
use Enm\JsonApi\Exception\UnauthorizedException;

if ( $_SERVER["REQUEST_METHOD"] === "OPTIONS" )
{
    outCORSHeaders();

    echo "\n";
    exit();
}

require_once(__DIR__."/../vendor/autoload.php");
require_once(__DIR__."/Handlers/AuthHandler.php");
require_once(__DIR__."/Handlers/AuthorHandler.php");
require_once(__DIR__."/Handlers/BookHandler.php");
require_once(__DIR__."/Handlers/GenreHandler.php");
require_once(__DIR__."/Handlers/UserHandler.php");

// create the server
$jsonApi = new JsonApiServer(new Deserializer(), new Serializer());

// init application
// get a json api response json api request
try {
    if ( AuthHandler::connectSession() === false )
        throw new UnauthorizedException();

    require_once(__DIR__."/App/App.php");

    // add your request handlers to the registry of the json api server

    $jsonApi->addHandler('auth', new AuthHandler());

    if ( AuthHandler::isUserRole() )
    {
        $jsonApi->addHandler('authors', new AuthorHandler());
        $jsonApi->addHandler('books', new BookHandler());
        $jsonApi->addHandler('genres', new GenreHandler());
    }
    if ( AuthHandler::isAdminRole() )
        $jsonApi->addHandler('users', new UserHandler());

    // create the json api request
    $request = new Request( $_SERVER['REQUEST_METHOD'],
        new \GuzzleHttp\Psr7\Uri($_SERVER['REQUEST_URI']),
        $jsonApi->createRequestBody(file_get_contents('php://input')),
        '/api.php' );
    // get a json api response json api request
    $response = $jsonApi->handleRequest($request);

} catch(\Exception $e) {
    $response = $jsonApi->handleException($e);
}

// send the response back to requesting HTTP client...
header('HTTP/1.1 '.$response->status());

outCORSHeaders();

foreach ($response->headers()->all() as $header => $value){
    header($header.': '.$value);
}

// auth required answer
if ( $response->status() == 401 ) {
    // The WWW-Authenticate header must be present in 401 answer by RFC 7235
    // See discussion at https://stackoverflow.com/questions/48408530/what-www-authenticate-header-should-a-http-server-return-in-a-401-response-when
    // Decided to use custom auth type according to test cases published at
    // http://test.greenbytes.de/tech/tc/httpauth/#unknown
    header('WWW-Authenticate: CustomAuth realm="Kniznica App"');
}

// Debug header to check session
header('X-Debug-CurAuth: '.AuthHandler::getCurUserId().';'.AuthHandler::getCurUserRole());

echo $jsonApi->createResponseBody($response);

function outCORSHeaders()
{
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: ".$_SERVER["HTTP_ORIGIN"]);
    if ( $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"] )
        header("Access-Control-Allow-Headers: ".$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]);
    header("Access-Control-Allow-Methods: PUT, POST, PATCH, GET, DELETE, OPTIONS");
    header("Access-Control-Max-Age: 1728000");
}