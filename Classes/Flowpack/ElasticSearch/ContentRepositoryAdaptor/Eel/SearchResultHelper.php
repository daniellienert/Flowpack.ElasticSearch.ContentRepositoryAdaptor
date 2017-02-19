<?php
namespace Flowpack\ElasticSearch\ContentRepositoryAdaptor\Eel;

/*
 * This file is part of the Flowpack.ElasticSearch.ContentRepositoryAdaptor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * Eel Helper to process the ElasticSearch Query Result
 */
class SearchResultHelper implements ProtectedContextAwareInterface
{

    /**
     * @param ElasticSearchQueryResult $searchResult
     * @param float $scoreThreshold The minimum required score to return the suggestion
     * @param string $suggestionName The suggestion name which
     * @return string
     */
    public function didYouMean(ElasticSearchQueryResult $searchResult, $scoreThreshold = 0.7, $suggestionName = 'suggestions')
    {
        $maxScore = 0;
        $suggestionParts = [];

        foreach ($searchResult->getSuggestions()[$suggestionName] as $suggestion) {
            if (array_key_exists('options', $suggestion) && !empty($suggestion['options'])) {
                $bestSuggestion = current($suggestion['options']);
                $maxScore = $bestSuggestion['score'] > $maxScore ? $bestSuggestion['score'] : $maxScore;
                $suggestionParts[] = $bestSuggestion['text'];
            } else {
                $suggestionParts[] = $suggestion['text'];
            }
        }
        if ($maxScore >= $scoreThreshold) {
            return implode(' ', $suggestionParts);
        }

        return '';
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
