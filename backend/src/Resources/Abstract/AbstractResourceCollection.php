<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Request\RequestInterface;
use Enm\JsonApi\Model\Resource\ResourceCollection;
use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

/**
 * @author Serge Boyko <s.boyko@gmail.com>
 */
abstract class AbstractResourceCollection extends ResourceCollection
{
    /**
     * @var string
     */
    protected $type = 'abstract';

    /**
     * @var integer
     */
    protected $total;

    /**
     * @var integer
     */
    protected $offset;

    /**
     * @var integer
     */
    protected $limit;

    /**
     * @var integer
     */
    protected $where;

    /**
     * Constructor
     */
    public function __construct( array $data = [] )
    {
        parent::__construct( $data );
        //print_r($data);
    }

    /**
     * @return ResourceCollectionInterface
     */
    public function load( $data ): ResourceCollectionInterface
    {
        if ( $data instanceof RequestInterface ) {
            if ( $data->type() !== $this->type ) {
	            throw new \InvalidArgumentException('Invalid data type given! C3.'.$data->type()." !== {$this->type}");
            }
            return $this->loadByRequest( $data );
        } elseif ($data instanceof ResourceCollectionInterface) {
            return $this->loadByResourceCollection( $data );
        } elseif (\is_string($data)) {
            return $this->loadFromDB( $data );
        } else {
            throw new \InvalidArgumentException('Invalid data given!  C4.'.get_class($data));
        }
    }

    /**
     * @return ResourceCollectionInterface
     */
    protected function loadByRequest( RequestInterface $request ): ResourceCollectionInterface
    {
        $this->whereExpr = $this->genWhereExpr( $request );
        // $this->sortExpr =

        $total = $this->total();

        if ( $request->hasPagination(App::$config['URI']['PageNoParam'])
        		|| $request->hasPagination(App::$config['URI']['PageSizeParam']) )
        {
	        $this->limit = (int)$request->paginationValue(App::$config['URI']['PageSizeParam']);
	        if ( $this->limit < 1 )
		        $this->limit = App::$config['View']['RowsOnPage'];

	        $page = (int)$request->paginationValue(App::$config['URI']['PageNoParam']);

	        if ( $page == 0 )
	        {
	            $page = 1;
	        }
	        elseif ( $page < 0 )
	        {
	            throw new \InvalidArgumentException('Invalid page argument given!');
	        }

	        $this->offset = ($page-1) * $this->limit;
	        if ( $this->offset > $this->total() )
	        {
	            throw new \InvalidArgumentException('Too big page argument given!');
	        }
        }

        // get filter/limitation from request and load from DB
        return $this->loadFromDB( );
    }

    /**
     * @return ResourceCollectionInterface
     */
    protected function loadByResourceCollection( ResourceCollectionInterface $resource ): ResourceCollectionInterface
    {
        /// TBD;

        return $this;
    }

    /**
     * @return ResourceCollectionInterface
     */
    protected function loadFromDB( $query = null ): ResourceCollectionInterface
    {
        // gather data from DB and generate the collection
        return $this;
    }

    /**
     * @return integer
     */
    public function total(): int
    {
        if ( isset($this->total) )
            return $this->total;

        $this->total = $this->countTotal();
        return $this->total;
    }

    /**
     * @return integer
     */
    public function countTotal(): int
    {
        // Use $this->whereExpr in SQL when count total
        return 0;
    }

    /**
     * @return string
     */
    public function genWhereExpr( RequestInterface $request )
    {
        return '';
    }

    /**
     * @return integer
     */
    public function offset(): int
    {
        return $this->offset;
    }

    /**
     * @return integer
     */
    public function limit(): int
    {
        return (int)$this->limit;
    }

    /**
     * @return integer
     */
    public function page(): int
    {
        if ( $this->limit ) {
            $page = ($this->offset / $this->limit) + 1;
            return $page;
        } else {
            return 0;
        }
    }

}