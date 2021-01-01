<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Document\Document;
use Enm\JsonApi\Model\Request\RequestInterface;
use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;
use Psr\Http\Message\UriInterface;

/**
 * @author Serge Boyko <s.boyko@gmail.com>
 */
class AbstractCollectionDocument extends Document
{
    protected const SELF_LINK = 'self';
    protected const FIRST_LINK = 'first';
    protected const PREVIOUS_LINK = 'previous';
    protected const NEXT_LINK = 'next';
    protected const LAST_LINK = 'last';

    /**
     * @param ResourceCollectionInterface|ResourceInterface|ResourceInterface[]|null $data If data is not an array, "shouldBeHandledAsCollection" will return false
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($data, RequestInterface $request)
    {
        parent::__construct($data);

        $this->links()->createLink(self::SELF_LINK, (string)$request->uri());

    		if ( $this->data()->limit() )
    		{
	    		  $this->metaInformation()->set('page', $this->data()->page() );
  	  		  $this->metaInformation()->set('resources_per_page',
    			                               $this->data()->limit());
    			  $this->metaInformation()->set('total_resources', $this->data()->total());

            if ( $this->data()->page() > 1 ) {
  	            $this->links()->createLink(
                    self::FIRST_LINK,
                    $this->createPaginatedUri($request->uri(), 1)
                );

                $this->links()->createLink(
                    self::PREVIOUS_LINK,
                    $this->createPaginatedUri($request->uri(), $this->data()->page()-1)
                );
            }

            $last = intval($this->data()->total() / $this->data()->limit()) + 1;
            $next = $this->data()->page() + 1;

            if ($next <= $last) {
                $this->links()->createLink(
                    self::NEXT_LINK,
                    $this->createPaginatedUri($request->uri(), $next)
                );
            }

            if ( $last > $this->data()->page() ) {
                $this->links()->createLink(
                    self::LAST_LINK,
                    $this->createPaginatedUri($request->uri(), $last)
                );
            }

        }
    }

    /**
     * @param UriInterface $uri
     * @param int $offset
     * @param int $limit
     * @return string
     */
    protected function createPaginatedUri(UriInterface $uri, int $offset, int $limit = null): string
    {
        parse_str($uri->getQuery(), $query);

        $query['page'][App::$config['URI']['PageNoParam']] = $offset;
        $query['page'][App::$config['URI']['PageSizeParam']] = $limit;

        return (string)$uri->withQuery(http_build_query($query));
    }
}