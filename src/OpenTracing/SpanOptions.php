<?php

namespace OpenTracing;

use OpenTracing\Exceptions\InvalidReferencesSet;
use OpenTracing\Exceptions\InvalidSpanOption;

final class SpanOptions
{
    /**
     * @var Reference[]
     */
    private $references = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var int|float|\DateTime
     */
    private $startTime;

    /**
     * @param array|mixed[] $options
     * @throws InvalidSpanOption when one of the options is invalid
     * @throws InvalidReferencesSet when there are inconsistencies about the references
     * @return SpanOptions
     */
    public static function create(array $options)
    {
        $spanOptions = new self();
        
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'references':
                    if ($value instanceof Reference) {
                        $spanOptions->references = [$value];
                    } elseif (is_array($value)) {
                        $spanOptions->references = self::buildReferences($value);
                    } else {
                        throw InvalidSpanOption::forInvalidReferenceSet($value);
                    }

                    break;

                case 'tags':
                    if (!is_array($value)) {
                        throw InvalidSpanOption::forInvalidTags($value);
                    }

                    foreach ($value as $tag => $tagValue) {
                        if ($tag !== (string) $tag) {
                            throw InvalidSpanOption::forInvalidTag($tag);
                        }

                        $spanOptions->tags[$tag] = $tagValue;
                    }
                    break;

                case 'start_time':
                    if (is_scalar($value) && !is_numeric($value)) {
                        throw InvalidSpanOption::forInvalidStartTime();
                    }

                    $spanOptions->startTime = $value;
                    break;

                default:
                    throw InvalidSpanOption::forUnknownOption($key);
                    break;
            }
        }

        return $spanOptions;
    }

    private static function buildReferences(array $referencesArray)
    {
        $references = [];

        foreach ($referencesArray as $reference) {
            if (!($reference instanceof Reference)) {
                throw InvalidSpanOption::forInvalidReference($reference);
            }

            $references[] = $reference;
        }

        return $references;
    }

    /**
     * @return Reference[]
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return int|float|\DateTime if returning float or int it should represent
     * the timestamp (including as many decimal places as you need)
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
}
