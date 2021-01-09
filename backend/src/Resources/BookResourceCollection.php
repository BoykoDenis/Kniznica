<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

require_once (__DIR__."/Abstract/AbstractResourceCollection.php");
require_once (__DIR__."/BookResource.php");


class BookResourceCollection extends AbstractResourceCollection
{
    /**
     * @var string
     */
    protected $type = 'books';

    /**
     * @return ResourceCollectionInterface
     */

	public function __construct(array $data = [])
	{
		parent::__construct( $data );
		//print_r($data);
	}


    protected function loadFromDB( $query = null ): ResourceCollectionInterface
    {
        if ( $query )
        {
            $req = $query;
        }
        else
        {
            $req = 'SELECT * FROM books';

            if ( $this->limit() )
            {
                if (!$this->offset())
                {
                    $this->offset = 0;
                }
                $req .= ' LIMIT '.intval($this->limit).' OFFSET '.intval($this->offset);
            }
        }

        $query = App::$dbh->prepare($req);
        $query->execute();

        while($row = $query->fetch(\PDO::FETCH_ASSOC))
        {
           $rec = new BookResource();
           $rec->load($row);
           $this->set($rec);
        }

        return $this;
    }


    /**
     * @return integer
     */
	public function countTotal( ): int
    {
		$db = new App();
		$req = 'SELECT COUNT(*) FROM books';
		$query = $db::$dbh->prepare($req);
		$query->execute();
		$total = $query->fetch(\PDO::FETCH_ASSOC);
        return intval($total['COUNT(*)']);
    }
}