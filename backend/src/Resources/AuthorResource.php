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
        $req = "SELECT * FROM authors WHERE id = ?";
        $query = App::$dbh->prepare( $req );
        $query->execute( [$id] );
        $dbdata = $query->fetch( \PDO::FETCH_ASSOC );

        return $this->loadByArray( $dbdata ) ?: null;
    }

    protected function addToDB( $rawdata )
    {

        $req = "INSERT INTO authors (name, date_of_birth, date_of_death) VALUE (?, ?, ?)";
        $query = App::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['name'], $rawdata['date_of_birth'], $rawdata['date_of_death']] );
            $this->id = App::$dbh->lastInsertId();
            $this->loadById( $this->id );
        }
        catch (Exception $e)
        {
            App::$dbh->rollback();
            throw new \Exception('Load Failed: '. $e->getMessage());
        }
    }

    protected function editInDB( string $id, array $rawdata )
    {
        $req = 'UPDATE authors SET name=?, date_of_birth=?, date_of_death=? WHERE id=?';
        $query = App::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['name'], $rawdata['date_of_birth'], $rawdata['date_of_death'], $id]);
            $this->loadById( $id );
        }
        catch (Exception $e)
        {
            App::$dbh->rollback();
            throw new \Exception('Patch Failed: '. $e->getMessage());
        }
    }

    protected function deleteFromDB( string $id )
    {
        $req = 'DELETE FROM authors WHERE id=?';
        $query = App::$dbh->prepare($req);
        try
        {
            $query->execute( [$id] );
        }
        catch (Exception $e)
        {
            App::$dbh->rollback();
            throw new \Exception('Deletion Failed: '. $e->getMessage());
        }
    }


    protected function getAllowedRelationshipsList()
    {
        return ['books'=>'books',
                'genres'=>'genres'];
    }

    protected function getRelationshipDataCollection( $relname )
    {
        if ( $relname == 'books' )
        {
            $col = new BookResourceCollection();
            $req = 'select distinct b.*
                        from books b
                          inner join authorbook ab
                            on ab.bid = b.id
                        where ab.aid = '.intval($this->id);
        }
        elseif( $relname == 'genres' )
        {
            $col = new GenreResourceCollection();
            $req = 'select distinct g.*
                        from genres g
                            inner join bookgenre bg
                                on bg.gid = g.id
                            inner join authorbook ab
                                on ab.bid = bg.bid
                        where ab.aid = '.intval($this->id);
        }
        else
            return parent::getRelationshipDataCollection( $relname );

        $col->load($req);
        return $col;
    }

    protected function getRelationshipIdList( String $relname )
    {
        if ( $relname == 'books' )
        {
            $req = 'select bid as id
                        from authorbook
                            where aid = '.intval($this->id);
        }
        elseif ( $relname == 'genres' )
        {
            $req = 'select gid as id
                        from bookgenre bg
                            inner join authorbook ab
                                on bg.bid = ab.bid
                            where aid = '.intval($this->id);
        }
        else
            return parent::getRelationshipIdList( $relname );

        $query = App::$dbh->prepare($req);
        $query->execute();

        $idlist = [];
        while($row = $query->fetch(\PDO::FETCH_ASSOC))
        {
           $idlist[] = $row['id'];
        }

        return $idlist;
    }

    protected function addRelFKToDB( String $relname, $id )
    {
        if ( $relname == 'books' )
        {
            $req = 'INSERT INTO authorbook (aid, bid) VALUE ('
                     .intval($this->id).','
                     .intval($id).')';
        }
        else
            return parent::addRelFKToDB( $relname, $id );

        $query = App::$dbh->prepare($req);

        try
        {
            $query->execute(  );
        }
        catch (Exception $e)
        {
            App::$dbh->rollback();
            throw new \Exception('Cannot add to DB: '.$req.';'. $e->getMessage());
        }

        return true;
    }

    protected function delRelFKFromDB( String $relname, $id )
    {
        if ( $relname == 'books' )
        {
            $req = 'DELETE FROM authorbook
                     where aid = '.intval($this->id).'
                       and bid = '.intval($id);
        }
        else
            return parent::delRelFKFromDB( $relname, $id );

        $query = App::$dbh->prepare($req);

        try
        {
            $query->execute(  );
        }
        catch (Exception $e)
        {
            App::$dbh->rollback();
            throw new \Exception('Cannot del from DB: '.$req.';'. $e->getMessage());
        }

        return true;
    }

}
