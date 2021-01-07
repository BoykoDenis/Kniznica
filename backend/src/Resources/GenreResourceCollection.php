<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

require_once (__DIR__."/Abstract/AbstractResourceCollection.php");
require_once (__DIR__."/AuthorResource.php");

/**
 * @author Serge Boyko s.boyko@gmail.com
 */
class GenreResourceCollection extends AbstractResourceCollection
{
    /**
     * @var string
     */
	protected $type = 'genres';



    /**
     * @return ResourceCollectionInterface
     */

	public function __construct(array $data = [])
	{
		parent::__construct( $data );
		//print_r($data);
	}

    protected function loadFromDB(): ResourceCollectionInterface
    {
        // gather data from DB and generate the collection
		$db = new App();

		$req = 'SELECT * FROM genres';

		if ( $this->limit() )
		{
			if (!$this->offset())
			{
				$this->offset = 0;
			}
			$req .= ' LIMIT '.intval($this->limit).' OFFSET '.intval($this->offset);
		}

		$query = $db::$dbh->prepare($req);
		$query->execute();

		while($row = $query->fetch(\PDO::FETCH_ASSOC))
		{
			$rec = new GenreResource();
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
		$req = 'SELECT COUNT(*) FROM genres';
		$query = $db::$dbh->prepare($req);
		$query->execute();
		$total = $query->fetch(\PDO::FETCH_ASSOC);
        return intval($total['COUNT(*)']);
    }

}