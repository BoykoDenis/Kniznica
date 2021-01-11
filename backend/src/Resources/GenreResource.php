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
        $req = "SELECT * FROM genres WHERE id = ?";
        $query = App::$dbh->prepare( $req );
        $query->execute( [$id] );
        $dbdata = $query->fetch(\PDO::FETCH_ASSOC);

        return $this->loadByArray( $dbdata );
    }

    protected function addToDB( $rawdata )
    {

        //find better solution
        $req = "INSERT INTO genres (gname) VALUE (?)";
        $query = App::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['gname']] );
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
        $req = 'UPDATE genres SET gname=? WHERE id=?';
        $query = App::$dbh->prepare($req);
        try
        {
            $query->execute( [$rawdata['gname'], $id]);
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
        $req = 'DELETE FROM genres WHERE id=?';
        $query = App::$dbh->prepare($req);
        echo $id;
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
                'authors'=>'authors'];
    }

    protected function getRelationshipDataCollection( $relname )
    {
        if ( $relname == 'authors' )
        {
            $col = new AuthorResourceCollection();
            $req = 'select distinct a.*
                        from authors a
                          inner join authorbook ab
                            on ab.aid = a.id
                          inner join bookgenre bg
                            on bg.bid = ab.bid
                        where bg.gid = '.intval($this->id);
        }
        elseif( $relname == 'books' )
        {
            $col = new BookResourceCollection();
            $req = 'select distinct b.*
                        from books b
                            inner join bookgenre bg
                                on bg.bid = b.id
                        where bg.gid = '.intval($this->id);
        }
        else
            return parent::getRelationshipDataCollection( $relname );

        $col->load($req);
        return $col;
    }

    protected function getRelationshipIdList( String $relname )
    {
        if ( $relname == 'authors' )
        {
            $req = 'select aid as id
                        from authorbook ab
                            inner join bookgenre bg
                                on bg.bid = ab.bid
                        where gid = '.intval($this->id);
        }
        elseif ( $relname == 'books' )
        {
            $req = 'select bid as id
                        from bookgenre bg
                            where gid = '.intval($this->id);
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
            $req = 'INSERT INTO bookgenre (bid, gid) VALUE ('
                     .intval($id).','
                     .intval($this->id).')';
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
            $req = 'DELETE FROM bookgenre
                     where bid = '.intval($id).'
                       and gid = '.intval($this->id);
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


