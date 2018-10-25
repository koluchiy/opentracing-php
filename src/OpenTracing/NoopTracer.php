<?php

namespace OpenTracing;

use OpenTracing\Propagators\Reader;
use OpenTracing\Propagators\Writer;

final class NoopTracer implements Tracer
{
    public static function create()
    {
        return new self();
    }

    public function startSpan(
        $operationName,
        Reference $parentReference = null,
        $startTimestamp = null,
        array $tags = []
    ) {
        return NoopSpan::create();
    }

    public function startSpanWithOptions($operationName, $options)
    {
        return NoopSpan::create();
    }

    public function inject(SpanContext $spanContext, $format, Writer $carrier)
    {
    }

    public function extract($format, Reader $carrier)
    {
        return new NoopSpanContext();
    }

    public function flush()
    {
    }
}
