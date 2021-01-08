<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\JsonApiTrait;
use Enm\JsonApi\Model\Resource\ResourceInterface;
use Enm\JsonApi\Model\Resource\ResourceCollection;
use Enm\JsonApi\Model\Resource\JsonResource;
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
class AuthorResource extends AbstractResource
{
    use \Enm\JsonApi\JsonApiTrait;
    /**
     * @var string
     */
    protected $type = 'authors';

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var array
     */
    protected $attributeMap = ['name', 'date_of_birth', 'date_of_death'];

    /**
     * @return Resource
     */
    protected function loadById( string $id ): ResourceInterface
    {
        // Here add loading by id from DB
        // and the call $this->loadByArray with gather from DB data
        $db = new App();
        $req = "SELECT * FROM authors WHERE id = ?";
        $query = $db::$dbh->prepare( $req );
        $query->execute( [$id] );
        $dbdata = $query->fetch( \PDO::FETCH_ASSOC );

        return $this->loadByArray( $dbdata ) ?: null;
    }

    protected function addToDB( $rawdata )
    {

        $db = new App();
        $req = "INSERT INTO authors (name, date_of_birth, date_of_death) VALUE (?, ?, ?)";
        $query = $db::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['name'], $rawdata['date_of_birth'], $rawdata['date_of_death']] );
            $this->id = $db::$dbh->lastInsertId();
            $this->loadById( $this->id );
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
        $req = 'UPDATE authors SET name=?, date_of_birth=?, date_of_death=? WHERE id=?';
        $query = $db::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['name'], $rawdata['date_of_birth'], $rawdata['date_of_death'], $id]);
            $this->loadById( $id );
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
        $req = 'DELETE FROM authors WHERE id=?';
        $query = $db::$dbh->prepare($req);
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


    protected function getAllowedRelationshipsList()
    {
        return ['books'=>'books'];
    }

    protected function getRelationshipDataCollection( $relname )
    {
        if ( $relname == 'books' )
        {
            $col = new BookResourceCollection();
            $query = 'select distinct b.*
                        from books b
                          inner join authorbook ab 
                            on ab.bid = b.id
                        where ab.aid = '.intval($this->id);
        }
        else
            parent::getRelationshipDataCollection( $relname );

        $col->load($query);

        return $col;
    }

}
