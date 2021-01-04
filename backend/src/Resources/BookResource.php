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

class BookResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'books';

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var array
     */
    protected $attributeMap = ['title', 'date_published', 'isbn'];
    // isbn = international standart book number

    /**
     * @return Resource
     */


    protected function loadById( string $id ): ResourceInterface
    {
        // Here add loading by id from DB
        // and the call $this->loadByArray with gather from DB data
        $db = new App();
        $req = "SELECT * FROM books WHERE id = :id";
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
        $req = "INSERT INTO books (title, date_published, isbn) VALUE (?, ?, ?)";
        $query = $db::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['title'], $rawdata['date_published'], $rawdata['isbn']] );
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
        $req = 'UPDATE books SET title=?, date_published=?, isbn=? WHERE id=?';
        $query = $db::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['name'], $rawdata['date_of_birth'], $rawdata['date_of_death'], $id]);
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
        $req = 'DELETE FROM books WHERE id=?';
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
