<?php

namespace App\Schemas;

use App\Supplier;
use Neomerx\JsonApi\Schema\SchemaProvider;

class SupplierSchema extends SchemaProvider
{

    protected $resourceType = 'suppliers';

    /**
     * Get resource identity.
     *
     * @param Supplier $resource
     *
     * @return string
     */
    public function getId($resource)
    {
        return $resource->getKey();
    }

    /**
     * Get resource attributes.
     *
     * @param Supplier $resource
     *
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            Supplier::NAME => $resource->getAttribute(Supplier::NAME),
            Supplier::TRADE_NAME => $resource->getAttribute(Supplier::TRADE_NAME),
            Supplier::LEGAL_DOCUMENT_CODE => $resource->getAttribute(Supplier::LEGAL_DOCUMENT_CODE),
        ];
    }

    /**
     * @param Supplier $resource
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            Supplier::RELATIONSHIP_CONTACTS => [
                self::DATA => function () use ($resource) {
                    return $resource->contacts()->getEager();
                }
            ],
        ];
    }

    public function getIncludePaths()
    {
        return [Supplier::RELATIONSHIP_CONTACTS];
    }

}
