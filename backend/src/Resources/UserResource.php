<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Resource\ResourceInterface;
use Enm\JsonApi\Model\Common\KeyValueCollection;
use Enm\JsonApi\Model\Common\KeyValueCollectionInterface;
use Enm\JsonApi\Model\Resource\Extension\RelatedMetaInformationInterface;
use Enm\JsonApi\Model\Resource\Extension\RelatedMetaInformationTrait;
use Enm\JsonApi\Model\Resource\Link\LinkCollection;
use Enm\JsonApi\Model\Resource\Link\LinkCollectionInterface;
use Enm\JsonApi\Model\Resource\Relationship\RelationshipCollection;
use Enm\JsonApi\Model\Resource\Relationship\RelationshipCollectionInterface;

require_once (__DIR__."/Abstract/AbstractResource.php");

class UserResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'users';

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var array
     */
    protected $attributeMap = ['uname', 'uemail', 'upass', 'privileges'];
    // isbn = international standart book number

    /**
     * @return Resource
     */


    protected function loadById( string $id ): ResourceInterface
    {
        // Here add loading by id from DB
        // and the call $this->loadByArray with gather from DB data
        $db = new App();
        $req = "SELECT * FROM users WHERE id = :id";
        $query = $db::$dbh->prepare($req);
        $query->bindParam(":id", $id);
        $query->execute();
        $dbdata = $query->fetch(\PDO::FETCH_ASSOC);

        return $this->loadByArray( $dbdata );
    }

    protected function addToDB( $rawdata )
    {

        //find better solution
        $db = new App();
        $req = "INSERT INTO users (uname, uemail, upass, privileges) VALUE (?, ?, ?, ?)";
        $query = $db::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['uname'], $rawdata['uemail'], $rawdata['upass'], $rawdata['privileges']] );
        }
        catch (Exception $e)
        {
            $db::$dbh->rollback();
            throw new \Exception('Load Failed: '. $e->getMessage());
        }
    }

    protected function editInDB( string $id, array $rawdata )
    {
        $db = new App();
        $req = 'UPDATE users SET uname=?, uemail=?, upass=?, privileges=? WHERE id=?';
        $query = $db::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['uname'], $rawdata['uemail'], $rawdata['upass'], $rawdata['privileges'], $id]);
        }
        catch (Exception $e)
        {
            $db::$dbh->rollback();
            throw new \Exception('Patch Failed: '. $e->getMessage());
        }
    }

    protected function deleteFromDB( string $id )
    {
        $db = new App();
        $req = 'DELETE FROM users WHERE id=?';
        $query = $db::$dbh->prepare($req);
        echo $id;
        try
        {
            $query->execute( [$id] );
        }
        catch (Exception $e)
        {
            $db::$dbh->rollback();
            throw new \Exception('Deletion Failed: '. $e->getMessage());
        }
    }
/*
    protected function createByArray( array $ids )
    {
        foreach($ids as $id)
		{
			$res = new BookResource();
			$res->loadById( $id );
			$rels[] = $res;
		}
        return $rels;
    }
    */
}