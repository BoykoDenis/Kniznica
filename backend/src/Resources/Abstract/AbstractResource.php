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
use Enm\JsonApi\Model\Resource\Relationship\Relationship;

/**
 * @author Serge Boyko <s.boyko@gmail.com>
 */
abstract class AbstractResource implements ResourceInterface, RelatedMetaInformationInterface
{
    use RelatedMetaInformationTrait;

    /**
     * @var string
     */
    protected $type = 'abstract';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var KeyValueCollection
     */
    protected $attributeCollection;

    /**
     * @var RelationshipCollection
     */
    protected $relationshipCollection;

    /**
     * @var LinkCollection
     */
    protected $linkCollection;

    /**
     * @var KeyValueCollection
     */
    protected $metaCollection;

    /**
     * @var string
     */
    protected $idName = 'id';

    /**
     * @var array
     */
    protected $attributeMap;

    /**
     * @var array
     */
    protected $relationshipMap;

    /**
     * @param string $type
     * @param string $id
     * @param array $attributes
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->attributeCollection = new KeyValueCollection();
        $this->relationshipCollection = new RelationshipCollection();
        $this->linkCollection = new LinkCollection();
        $this->metaCollection = new KeyValueCollection();
    }


    /**
     * @return ResourceInterface
     */
    public function load( $data ): ResourceInterface
    {
        if ( $data instanceof RequestInterface )
        {
            if ( $data->type() !== $this->type )
            {
	            throw new \InvalidArgumentException('Invalid data type given! C1.');
            }
            return $this->loadByRequest( $data );
        }

        elseif ($data instanceof ResourceInterface)
        {
            if ( $data->type() !== $this->type )
            {
	            throw new \InvalidArgumentException('Invalid data type given! '.$data->type()." !== {$this->type}");
            }
            return $this->loadByResource( $data );

        }
        elseif ( \is_numeric($data) )
        {
            return $this->loadById( $data );
        }
        elseif (\is_array($data))
        {
            return $this->loadByArray( $data );
        }
        else
        {
            throw new \InvalidArgumentException('Invalid data given!  C2.'.get_class($data));
        }
    }

    public function add( Object $data )
    {
        if ($data)
        {
            return $this->addToDB( $data->attributes()->all());
        }
        else
        {
            throw new \InvalidArgumentException('Invalid data given!  C2.'.get_class($data));
        }
    }

    public function edit( Object $data )
    {
        if ($data)
        {
            return $this->editInDB( $data->id(), $data->attributes()->all() );
        }
    }

    public function remove( Object $data )
    {
        if ($data)
        {
            return $this->deleteFromDB( $data->id() );
        }
    }

    /**
     * @return Resource
     */
    protected function loadByRequest( RequestInterface $request ): ResourceInterface
    {
        echo $this->id();
        return $this->loadById($request->id());
    }

    /**
     * @return Resource
     */

    /*
     protected function loadById( string $id ): ResourceInterface
    {
        $this->id = $id;
        return $this;
    }
    */
    /**
     * @return Resource
     */
    protected function loadByResource( ResourceInterface $resource ): ResourceInterface
    {
        $this->id = $resource->id();

        return $this->loadAttributes( $resource->attributes()->all() );
    }

    /**
     * @return Resource
     */
    protected function loadByArray( array $data ): ResourceInterface
    {
        if ( $data[$this->idName] ) {
            $this->id = $data[$this->idName];
        } else {
	         throw new \InvalidArgumentException('Invalid data type given!');
        }

        return $this->loadAttributes( $data );
    }

    /**
     * @return Resource
     */
    protected function loadAttributes( array $data ): ResourceInterface
    {
        foreach ( $this->attributeMap as $fld )
        {
            if ( array_key_exists($fld, $data) )
                $this->attributeCollection->set($fld, $data[$fld]);
        }

        return $this;
    }

    public function load_relationship( RequestInterface $request )
    {
        $relationsIDnames = array
        (
            'books'=>'bid',
            'authors'=>'aid',
            'genres'=>'gid',
            'users'=>'uid'
        );
        if ( $request->relationship() == 'relationships' )
        {
            //$req = 'SELECT * FROM '.$name.' WHERE aid=?';
            echo 'easter egg';
        }
        elseif ( $request->relationship() == 'authorbook' )
        {

            if ( $this->type() == 'authors' ) $relname = 'books';
            elseif ( $this->type() == 'books' ) $relname = 'authors';
            else echo 'wrong relationship name';
            $this->loadById( $request->id() );
            $related = $this->getRelatedIDs( $request->relationship(), // relationship name
                                             $request->id(), // main ID
                                             $relationsIDnames[ $this->type() ], // main object IDname
                                             $relationsIDnames[ $relname ]); // related object IDname

            $related = $this->createCollection( $relname, $related );

            $rel = new Relationship( $relname, $related );
            $this->relationshipCollection->set( $rel );

        }
        elseif ( $request->relationship() == 'bookgenre')
        {
            if ( $this->type() == 'books' ) $relname = 'genres';
            elseif ( $this->type() == 'genres' ) $relname = 'books';
            else echo 'wrong relationship name';
            $this->loadById( $request->id() );
            $related = $this->getRelatedIDs( $request->relationship(), // relationship name
                                             $request->id(), // main ID
                                             $relationsIDnames[ $this->type() ], // main object IDname
                                             $relationsIDnames[ $relname ]); // related object IDname

            $related = $this->createCollection( $relname, $related );

            $rel = new Relationship( $relname, $related );
            $this->relationshipCollection->set( $rel );
        }
        elseif ( $request->relationship() == 'authorgenre')
        {
            //non steght forward db connection (author-genre)
            //should be remade
            if ( $this->type() == 'authors' ) $relname = 'genres';
            elseif ( $this->type() == 'genres' ) $relname = 'authors';
            else echo 'wrong relationship name';
            $this->loadById( $request->id() );
            $related = $this->getRelatedIDs( $request->relationship(), // relationship name
                                             $request->id(), // main ID
                                             $relationsIDnames[ $this->type() ], // main object IDname
                                             $relationsIDnames[ $relname ]); // related object IDname

            $related = $this->createCollection( $relname, $related );

            $rel = new Relationship( $relname, $related );
            $this->relationshipCollection->set( $rel );
        }
        elseif ( $request->relationship() == 'userbook')
        {
            if ( $this->type() == 'users' ) $relname = 'books';
            elseif ( $this->type() == 'books' ) $relname = 'users';
            else echo 'wrong relationship name';
            $this->loadById( $request->id() );
            $related = $this->getRelatedIDs( $request->relationship(), // relationship name
                                             $request->id(), // main ID
                                             $relationsIDnames[ $this->type() ], // main object IDname
                                             $relationsIDnames[ $relname ]); // related object IDname

            $related = $this->createCollection( $relname, $related );

            $rel = new Relationship( $relname, $related );
            $this->relationshipCollection->set( $rel );
        }
    }

    protected function getRelatedIDs( string $relationshipname, string $mainID, string $mainIDname, string $relatedIDname )
    {
        $db = new App();
        $req = " SELECT ".$relatedIDname." FROM ".$relationshipname." WHERE ".$mainIDname."=".$mainID;
        $query = $db::$dbh->prepare( $req );
        $query->execute();
        $rels = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC))
        {
            $rels[] = $row[$relatedIDname];
        }
        return $rels;

    }

    public function createCollection(string $type, array $ids = [])
	{
        $db = new App();

        if ( $type == 'books' )
        {
            foreach($ids as $id)
            {
                $res = new BookResource();
                $res->loadById( $id );
                $rels[] = $res;
            }
            return new BookResourceCollection( $rels ?: [] );
        }
        elseif ( $type == 'authors' )
        {
            foreach($ids as $id)
            {
                $res = new AuthorResource();
                $res->loadById( $id );
                $rels[] = $res;
            }
            return new AuthorResourceCollection( $rels ?: [] );
        }
        elseif ( $type == 'genres' )
        {
            foreach($ids as $id)
            {
                $res = new GenreResource();
                $res->loadById( $id );
                $rels[] = $res;
            }
            return new GenreResourceCollection( $rels ?: [] );
        }
        elseif ( $type == 'users' )
        {
            foreach($ids as $id)
            {
                $res = new UserResource();
                $res->loadById( $id );
                $rels[] = $res;
            }
            return new UserResourceCollection( $rels ?: [] );
        }
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return (string)$this->id;
    }

    /**
     * @return KeyValueCollectionInterface
     */
    public function attributes(): KeyValueCollectionInterface
    {
        return $this->attributeCollection;
    }

    /**
     * @return RelationshipCollectionInterface
     */
    public function relationships(): RelationshipCollectionInterface
    {
        return $this->relationshipCollection;
    }

    /**
     * @return LinkCollectionInterface
     */
    public function links(): LinkCollectionInterface
    {
        return $this->linkCollection;
    }

    /**
     * @return KeyValueCollectionInterface
     */
    public function metaInformation(): KeyValueCollectionInterface
    {
        return $this->metaCollection;
    }

    /**
     * Creates a new resource containing all data from the current one.
     * If set, the new request will have the given id.
     *
     * @param string $id
     * @return ResourceInterface
     * @throws \InvalidArgumentException
     */
    public function duplicate(string $id = null): ResourceInterface
    {
        $resource = new self($this->type(), $id ?? $this->id(), $this->attributes()->all());

        $resource->metaInformation()->mergeCollection($this->metaInformation());

        foreach ($this->relationships()->all() as $relationship) {
            $resource->relationships()->set($relationship->duplicate());
        }

        foreach ($this->links()->all() as $link) {
            $resource->links()->set($link->duplicate());
        }

        return $resource;
    }
}
