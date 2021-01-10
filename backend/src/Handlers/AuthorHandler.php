<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

use Enm\JsonApi\Server;

use Enm\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Enm\JsonApi\Server\RequestHandler\NoResourceFetchTrait;
use Enm\JsonApi\Server\RequestHandler\NoRelationshipFetchTrait;
use Enm\JsonApi\Server\RequestHandler\NoResourceModificationTrait;
use Enm\JsonApi\Server\RequestHandler\NoResourceDeletionTrait;
use Enm\JsonApi\Server\RequestHandler\NoRelationshipModificationTrait;
use Enm\JsonApi;

use Enm\JsonApi\Model\Response\ResponseInterface;
use Enm\JsonApi\Model\Request\RequestInterface;

use Enm\JsonApi\Model\Document\Document;
use Enm\JsonApi\Model\Document\OffsetBasedPaginatedDocument;
use Enm\JsonApi\Model\Resource\JsonResource;


require_once (__DIR__.'/../Resources/AuthorResource.php');
require_once (__DIR__.'/../Documents/AuthorDocument.php');

require_once (__DIR__.'/../Resources/AuthorResourceCollection.php');
require_once (__DIR__.'/../Documents/AuthorCollectionDocument.php');

class AuthorHandler implements RequestHandlerInterface
{
//	use NoResourceFetchTrait;
    //use NoRelationshipFetchTrait;
	use NoResourceModificationTrait;
	use NoResourceDeletionTrait;
	use NoRelationshipModificationTrait;

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
	public function fetchResource(RequestInterface $request): ResponseInterface
	{
        $resource = new AuthorResource();
        $resource->load($request);

		$document = new AuthorDocument($resource, $request);

		return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
	}

		/**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
	public function fetchResources(RequestInterface $request): ResponseInterface
	{
		$resource = new AuthorResourceCollection();
		$resource->load($request);
		$document = new AuthorCollectionDocument($resource, $request);

		return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
	}


    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
    public function createResource(RequestInterface $request): ResponseInterface
    {
        $resource = new AuthorResource();
        //$request = Server\JsonApiServer::createResponseBody($request);
        $resource->add($request->requestBody()->data()->all()[0]);
        $document = new AuthorDocument($resource, $request);
//        $resource->add();



        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotAllowedException
     */
    public function patchResource(RequestInterface $request): ResponseInterface
    {
        $resource = new AuthorResource();
//        $resource->edit($request->requestBody()->data()->all()[0]);
        $resource->saveByRequest($request);

        $document = new AuthorDocument($resource, $request);

        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }

    public function deleteResource(RequestInterface $request): ResponseInterface
    {
        $resource = new AuthorResource();
        $resource->remove($request->requestBody()->data()->all()[0]);

        $document = new AuthorDocument($resource, $request);

        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }

    public function fetchRelationship( RequestInterface $request ): ResponseInterface
    {
        $resource = new AuthorResource();
/*
        //echo gettype($request->requestBody()->data());
        //try to add related resources into the document included
        $resource->load_relationship( $request );
*/
        $resource->loadRelationships($request);

        $document = new AuthorDocument($resource, $request);
        return new \Enm\JsonApi\Model\Response\DocumentResponse($document);
    }
}

