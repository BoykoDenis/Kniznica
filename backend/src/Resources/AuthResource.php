<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Request\RequestInterface;
use Enm\JsonApi\Model\Resource\ResourceInterface;
use Enm\JsonApi\Model\Common\KeyValueCollection;
use Enm\JsonApi\Model\Common\KeyValueCollectionInterface;
use Enm\JsonApi\Model\Resource\Extension\RelatedMetaInformationInterface;
use Enm\JsonApi\Model\Resource\Extension\RelatedMetaInformationTrait;
use Enm\JsonApi\Model\Resource\Link\LinkCollection;
use Enm\JsonApi\Model\Resource\Link\LinkCollectionInterface;
use Enm\JsonApi\Model\Resource\Relationship\RelationshipCollection;
use Enm\JsonApi\Model\Resource\Relationship\RelationshipCollectionInterface;
use Enm\JsonApi\Exception\UnauthorizedException;
use Enm\JsonApi\Exception\BadRequestException;

require_once (__DIR__."/UserResource.php");
require_once (__DIR__."/../Handlers/AuthHandler.php");

class AuthResource extends UserResource
{
    /**
     * @var string
     */
    protected $type = 'auth';

    /**
     * @var string
     */
    protected $idName = 'id';

    public function __construct()
    {
        $this->attributeMap[] = 'token';
        parent::__construct();
    }

    /**
     * @return Resource
     */
    public function loadByRequest( RequestInterface $request ): ResourceInterface
    {
        $uid = AuthHandler::getCurUserId();
        if ( $uid !== false && strlen($uid) )
        {
            if ( $uid === '0' )
            {
                $this->id = '0';
                $this->attributeCollection->set('uname', 'Root Admin');
                $this->attributeCollection->set('privileges', 99);
            }
            else
                $this->loadById($uid);
            $this->sanitize();
        }
        else
            throw new UnauthorizedException();

        return $this;
    }

    public function loadByResource( ResourceInterface $resource ): ResourceInterface
    {
        if ( !($uname = $resource->attributes()->getOptional('uemail'))
             || !($upass = $resource->attributes()->getOptional('upass')) )
            throw new BadRequestException('Login and password should be entered');

        if ( $uname === App::$config['Admin']['User']
	    && $upass === md5(App::$config['Admin']['Password']) )
	{ // root admin login
	    $this->id = '0';
            $this->attributeCollection->set('uname', 'Root Admin');
            $this->attributeCollection->set('privileges', 99);
	}
        else
        {
            $this->loadByCredentials( $uname, $upass );
        }
        AuthHandler::createSession( $this->id, 
                                    intval($this->attributeCollection->getOptional('privileges')) );

        $this->sanitize();
        return $this;
    }

    protected function loadByCredentials( string $login, string $pswd ): ResourceInterface
    {
        // Here add loading by id from DB
        // and the call $this->loadByArray with gather from DB data
        $db = new App();
        $req = "SELECT * FROM users WHERE uemail = :login and upass = :pswd";
        $query = $db::$dbh->prepare($req);
        $query->bindParam(":login", $login);
        $query->bindParam(":pswd", $pswd);
        $query->execute();
        if ( $dbdata = $query->fetch(\PDO::FETCH_ASSOC) )
            return $this->loadByArray( $dbdata );
        else
            throw new BadRequestException('Invalid username or password entered.');
    }

    public function sanitize(): ResourceInterface
    {
        $this->attributeCollection->set('uemail', '');
        $this->attributeCollection->set('upass', '');
        $this->attributeCollection->set('token', AuthHandler::getCurToken());

        return $this;
    }
}