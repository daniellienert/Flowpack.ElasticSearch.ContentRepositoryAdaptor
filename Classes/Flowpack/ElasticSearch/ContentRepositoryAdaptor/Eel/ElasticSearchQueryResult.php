<?php
namespace Flowpack\ElasticSearch\ContentRepositoryAdaptor\Eel;

/*                                                                                                  *
 * This script belongs to the TYPO3 Flow package "Flowpack.ElasticSearch.ContentRepositoryAdaptor". *
 *                                                                                                  *
 * It is free software; you can redistribute it and/or modify it under                              *
 * the terms of the GNU Lesser General Public License, either version 3                             *
 *  of the License, or (at your option) any later version.                                          *
 *                                                                                                  *
 * The TYPO3 project - inspiring people to share!                                                   *
 *                                                                                                  */

use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Persistence\QueryResultInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class ElasticSearchQueryResult implements QueryResultInterface, ProtectedContextAwareInterface {

	/**
	 * @var ElasticSearchQuery
	 */
	protected $elasticSearchQuery;

	/**
	 * @var array
	 */
	protected $result = NULL;

	/**
	 * @var array
	 */
	protected $nodes = NULL;

	/**
	 * @var integer
	 */
	protected $count = NULL;

	public function __construct(ElasticSearchQuery $elasticSearchQuery) {
		$this->elasticSearchQuery = $elasticSearchQuery;
	}

	/**
	 * Initialize the results by really executing the query
	 */
	protected function initialize() {
		if ($this->result === NULL) {
			$queryBuilder = $this->elasticSearchQuery->getQueryBuilder();
			$this->result = $queryBuilder->fetch();
			$this->nodes = $this->result['nodes'];
			$this->count = $queryBuilder->getTotalItems();
		}
	}

	/**
	 * @return \Flowpack\ElasticSearch\ContentRepositoryAdaptor\Eel\ElasticSearchQuery
	 */
	public function getQuery() {
		return clone $this->elasticSearchQuery;
	}

	/**
	 * {@inheritdoc}
	 */
	public function current() {
		$this->initialize();
		return current($this->nodes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function next() {
		$this->initialize();
		return next($this->nodes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function key() {
		$this->initialize();
		return key($this->nodes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function valid() {
		$this->initialize();
		return current($this->nodes) !== FALSE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rewind() {
		$this->initialize();
		reset($this->nodes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset) {
		$this->initialize();
		return isset($this->nodes[$offset]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset) {
		$this->initialize();
		return $this->nodes[$offset];
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value) {
		$this->initialize();
		$this->nodes[$offset] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset) {
		$this->initialize();
		unset($this->nodes[$offset]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFirst() {
		$this->initialize();
		if (count($this->nodes) > 0) {
			return array_slice($this->nodes, 0, 1);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray() {
		$this->initialize();
		return $this->nodes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function count() {
		if ($this->count === NULL) {
			$this->count = $this->elasticSearchQuery->getQueryBuilder()->count();
		}

		return $this->count;
	}

	/**
	 * @return integer the current number of results which can be iterated upon
	 * @api
	 */
	public function getAccessibleCount() {
		$this->initialize();
		return count($this->nodes);
	}

	/**
	 * @return array
	 */
	public function getAggregations() {
		$this->initialize();
		return $this->result['aggregations'];
	}

	/**
	 * @return array
	 */
	public function getSuggestions() {
		$this->initialize();
		if(count($this->result['suggest']) === 1) {
			$suggestArray = current($this->result['suggest']);

			if(count($suggestArray) === 1){
				return current($suggestArray);
			}
		}
		return $this->result['suggest'];
	}

	/**
	 * Returns the ElasticSearch "hit" (e.g. the raw content being transferred back from ElasticSearch)
	 * for the given node.
	 *
	 * Can be used for example to access highlighting information.
	 *
	 * @param NodeInterface $node
	 * @return array the ElasticSearch hit, or NULL if it does not exist.
	 * @api
	 */
	public function searchHitForNode(NodeInterface $node) {
		return $this->elasticSearchQuery->getQueryBuilder()->getFullElasticSearchHitForNode($node);
	}

	/**
	 * @param string $methodName
	 * @return boolean
	 */
	public function allowsCallOfMethod($methodName) {
		return TRUE;
	}
}