<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Server;

use Enm\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Enm\JsonApi\Server\RequestHandler\NoResourceFetchTrait;
use Enm\JsonApi\Server\RequestHandler\NoRelationshipFetchTrait;
use Enm\JsonApi\Server\RequestHandler\NoResourceModificationTrait;
use Enm\JsonApi\Server\RequestHandler\NoResourceDeletionTrait;
use Enm\JsonApi\Server\RequestHandler\NoRelationshipModificationTrait;

use Enm\JsonApi\Model\Response\ResponseInterface;
use Enm\JsonApi\Model\Request\RequestInterface;

use Enm\JsonApi\Model\Document\Document;
use Enm\JsonApi\Model\Document\OffsetBasedPaginatedDocument;
use Enm\JsonApi\Model\Resource\JsonResource;

use Enm\JsonApi\Exception\NotAllowedException;

require_once (__DIR__.'/../Resources/AuthResource.php');
require_once (__DIR__.'/../Documents/AuthDocument.php');

require_once (__DIR__.'/../Resources/UserResourceCollection.php');
require_once (__DIR__.'/../Documents/UserCollectionDocument.php');

require_once (__DIR__.'/../Exception/UnauthorizedException.php');

class AuthHandler implements RequestHandlerInterface
{
    use NoRelationshipFetchTrait;
//    use NoResourceModificationTrait;
//    use NoResourceDeletionTrait;
    use NoRelationshipModificationTrait;

    public function fetchResource(RequestInterface $request): ResponseInterface
    {
        $resource = new AuthResource();
        $resource->load($request);
        $document = new AuthDocument($resource, $request);

        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
    public function fetchResources(RequestInterface $request): ResponseInterface
    {
        return $this->fetchResource($request);
    }


    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
    public function createResource(RequestInterface $request): ResponseInterface
    {
        return $this->patchResource($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
    public function patchResource(RequestInterface $request): ResponseInterface
    {
        $resource = new AuthResource();
        $resource->loadByResource($request->requestBody()->data()->all()[0]);
        $document = new AuthDocument($resource, $request);

        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }

    public function deleteResource(RequestInterface $request): ResponseInterface
    {
        static::closeSession();
        $resource = new JsonResource( 'auth' );
        $document = new AuthDocument($resource, $request);

        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }


    static function isAuthorized()
    {
        if ( isset($_SESSION['auth']['uid'])
             && isset($_SESSION['auth']['role'])
             && strlen($_SESSION['auth']['uid']) 
             && intval($_SESSION['auth']['uid']) >= 0
             && intval($_SESSION['auth']['role']) > 0 )
            return true;

        return false;
    }

    static function getCurUserId()
    {
        if ( isset($_SESSION['auth']['uid'])
             && strlen($_SESSION['auth']['uid']) 
             && intval($_SESSION['auth']['uid']) >= 0 )
            return ''.intval($_SESSION['auth']['uid']);

        return false;
    }

    static function getCurUserRole()
    {
        if ( isset($_SESSION['auth']['role'])
             && intval($_SESSION['auth']['role']) )
            return intval($_SESSION['auth']['role']);

        return false;
    }

    static function isUserRole()
    {
        if ( isset($_SESSION['auth']['role'])
             && intval($_SESSION['auth']['role']) )
            return intval($_SESSION['auth']['role']) > 0;

        return false;
    }

    static function isAdminRole()
    {
        if ( isset($_SESSION['auth']['role'])
             && intval($_SESSION['auth']['role']) )
            return intval($_SESSION['auth']['role']) >= 10;

        return false;
    }

    static function getCurToken()
    {
        return session_id();
    }

    static function createSession( string $uid, int $role )
    {
        session_regenerate_id();
        session_start();

        $_SESSION['auth']['uid'] = $uid;
        $_SESSION['auth']['role'] = $role;

        return static::getCurToken();
    }

    static function closeSession()
    {
        $_SESSION['auth']['uid'] = '';
        $_SESSION['auth']['role'] = '';

        session_destroy();

        return true;
    }

    static function connectSession()
    {
       // Check if it's auth request - then accept any of it
        if ( !isset($_SERVER['PATH_INFO']) )
            return false;

        $sessionStatus = false;
        if ( substr($_SERVER['PATH_INFO'],0,6) === '/auth/' )
            $sessionStatus = null;

        // Check if Authorization header has value like
        // token XXXXX_auth_token_XXXXXXXXX

        // Check if Authorization header received
        if ( !isset($_SERVER['HTTP_X_AUTH_TOKEN']) )
            return $sessionStatus;
        if ( !strlen($_SERVER['HTTP_X_AUTH_TOKEN']) )
            return $sessionStatus;

        // Check if Authorization header contains 'token ' prefix
        if ( substr($_SERVER['HTTP_X_AUTH_TOKEN'], 0, 6) !== 'token ' )
            return $sessionStatus;

        // get session id from token
        $sesid = substr($_SERVER['HTTP_X_AUTH_TOKEN'], 6);

        // Check if token in Authorization header match to current session token
        if ( strlen($sesid) < 24 )
            return $sessionStatus;

        // init PHP session by session id
        session_id($sesid);
        session_start();

        // check PHP session 
        if ( static::getCurUserId() === false )
            return $sessionStatus;

        if ( static::getCurUserRole() === false )
            return $sessionStatus;

        return true;
    }
}