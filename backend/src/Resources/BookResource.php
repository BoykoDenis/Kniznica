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
        $req = "SELECT * FROM books WHERE id=?";
        $query = $db::$dbh->prepare( $req );
        $query->execute( [$id] );
        $dbdata = $query->fetch(\PDO::FETCH_ASSOC);

        return $this->loadByArray( $dbdata );
    }

    protected function addToDB( $rawdata )
    {

        $req = "INSERT INTO books (title, date_published, isbn) VALUE (?, ?, ?)";
        $query = App::$dbh->prepare($req);

        try
        {
            $query->execute( [$rawdata['title'], $rawdata['date_published'], $rawdata['isbn']] );
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
        $req = 'UPDATE books SET title=?, date_published=?, isbn=? WHERE id=?';
        $query = APP::$dbh->prepare($req);
        print_r([$rawdata['title'], $rawdata['date_published'], $rawdata['isbn'], $id]);
        try
        {
            $query->execute( array($rawdata['title'], $rawdata['date_published'], $rawdata['isbn'], $id) );
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
        $req = 'DELETE FROM books WHERE id=?';
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
        return ['authors'=>'authors',
                'genres'=>'genres'];
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
                        where ab.bid = '.intval($this->id);
        }
        elseif( $relname == 'genres' )
        {
            $col = new GenreResourceCollection();
            $req = 'select distinct g.*
                        from genres g
                            inner join bookgenre bg
                                on bg.gid = g.id
                        where bg.bid = '.intval($this->id);
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
                        from authorbook
                        where bid = '.intval($this->id);
        }
        elseif ( $relname == 'genres' )
        {
            $req = 'select gid as id
                        from bookgenre
                            where bid = '.intval($this->id);
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
        if ( $relname == 'authors' )
        {
            $req = 'INSERT INTO authorbook (aid, bid) VALUE ('
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
}
