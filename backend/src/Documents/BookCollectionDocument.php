<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Model\Document\Document;
use Enm\JsonApi\Model\Request\RequestInterface;
use Enm\JsonApi\Model\Resource\ResourceCollectionInterface;

require_once(__DIR__."/Abstract/AbstractCollectionDocument.php");

/**
 * @author Serge Boyko <s.boyko@gmail.com>
 */
class BookCollectionDocument extends AbstractCollectionDocument
{

    /**
     * @param ResourceCollectionInterface|ResourceInterface|ResourceInterface[]|null $data If data is not an array, "shouldBeHandledAsCollection" will return false
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($data, RequestInterface $request)
    {
        parent::__construct($data, $request);
    }
}
