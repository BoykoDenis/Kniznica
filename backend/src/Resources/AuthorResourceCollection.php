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

    protected function loadFromDB( string $where = ''): ResourceCollectionInterface
    {
        // gather data from DB and generate the collection
		$db = new App();

		if ( $this->limit() )
		{
			if (!$this->offset())
			{
				$this->offset = 0;
			}
			$query = $db::$dbh->prepare("SELECT * FROM authors");
		}
		else
		{
			//$query = $
			$query = $db::$dbh->prepare("SELECT * FROM authors");
			//echo get_type($query);
		}
		$query->execute();

			$tcnt = $cnt = 0;
		while($row = $query->fetch(\PDO::FETCH_ASSOC))
		{
			$tcnt++;
			if ( $this->offset && ($tcnt-1) < $this->offset )
				continue;
			$cnt++;
			$rec = new AuthorResource();
			$rec->load($row);
			$this->set($rec);
			if ( @$this->limit && $cnt >= $this->limit )
				break;
		}

        return $this;
    }


    /**
     * @return integer
     */
    public function countTotal( ): int
    {
        // gather data from DB and generate the collection
    		//$dbdata = include(__DIR__.'/../../temp/Authors.data.php');

        return 0;//\count($dbdata);
    }

}
