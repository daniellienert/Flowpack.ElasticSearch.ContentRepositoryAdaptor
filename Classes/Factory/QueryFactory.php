<?php

declare(strict_types=1);

namespace Flowpack\ElasticSearch\ContentRepositoryAdaptor\Factory;

/*
 * This file is part of the Flowpack.ElasticSearch.ContentRepositoryAdaptor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\ElasticSearch\ContentRepositoryAdaptor\Driver\QueryInterface;
use Flowpack\ElasticSearch\ContentRepositoryAdaptor\Exception\ConfigurationException;
use Neos\Flow\Annotations as Flow;

/**
 * A factory for creating the ElasticSearch Query
 *
 * @Flow\Scope("singleton")
 */
class QueryFactory extends AbstractDriverSpecificObjectFactory
{
    /**
     * @return QueryInterface
     * @throws ConfigurationException
     */
    public function createQuery(): QueryInterface
    {
        return $this->resolve('query');
    }
}
