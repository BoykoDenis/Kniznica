<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

require_once (__DIR__."/Abstract/AbstractResourceCollection.php");
require_once (__DIR__."/AuthorResource.php");

/**
 * @author Serge Boyko s.boyko@gmail.com
 */
class AuthorResourceCollection extends AbstractResourceCollection
{
    /**
     * @var string
     */
	protected $type = 'authors';



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
            $req = 'SELECT * FROM authors';

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
           $rec = new AuthorResource();
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
		$req = 'SELECT COUNT(*) FROM authors';
		$query = $db::$dbh->prepare($req);
		$query->execute();
		$total = $query->fetch(\PDO::FETCH_ASSOC);
        return intval($total['COUNT(*)']);
    }

}
