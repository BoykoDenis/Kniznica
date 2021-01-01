<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Document\Document;
use Enm\JsonApi\Model\Request\RequestInterface;
use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

/**
 * @author Serge Boyko <s.boyko@gmail.com>
 */
class BookListDocument extends Document
{

    /**
     * @param ResourceCollectionInterface|ResourceInterface|ResourceInterface[]|null $data If data is not an array, "shouldBeHandledAsCollection" will return false
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($data, RequestInterface $request)
    {
        parent::__construct($data);

    		if ( $this->data()->limit() )
    		{
	    		$this->metaInformation()->set('page', $this->data()->page() );
  	  		$this->metaInformation()->set('resources_per_page',
    			                               $this->data()->limit());
    			$this->metaInformation()->set('total_resources', $this->data()->total());
    		}
    }
}
?>