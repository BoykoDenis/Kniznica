<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\JsonApiTrait;
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

/**
 * @author Serge Boyko s.boyko@gmail.com
 */
class GenreResource extends AbstractResource
{
    use \Enm\JsonApi\JsonApiTrait;
    /**
     * @var string
     */
    protected $type = 'genres';

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var array
     */
    protected $attributeMap = ['gname'];

    /**
     * @return Resource
     */
    protected function loadById( string $id ): ResourceInterface
    {
        // Here add loading by id from DB
        // and the call $this->loadByArray with gather from DB data
        $db = new App();
        $req = "SELECT * FROM genres WHERE id = :id";
        $query = $db::$dbh->prepare($req);
        $query->bindParam(":id", $id);
        $query->execute();
        $dbdata = $query->fetch(\PDO::FETCH_ASSOC);
        //echo $this->id();

        return $this->loadByArray( $dbdata );
    }

    protected function addToDB( $rawdata )
    {

        //find better solution
        $db = new App();
        $req = "INSERT INTO genres (gname) VALUE (?)";
        $query = $db::$dbh->prepare($req);

        try
        {
            $query->execute( $rawdata['gname'] );
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
        $req = 'UPDATE genres SET gname=? WHERE id=?';
        $query = $db::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['gname'], $id]);
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
        $req = 'DELETE FROM genres WHERE id=?';
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
}
