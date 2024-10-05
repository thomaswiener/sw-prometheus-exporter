<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

interface MetricInterface
{
    /**
     * A counter is a cumulative metric that represents a single monotonically increasing counter whose value can only
     * increase or be reset to zero on restart. For example, you can use a counter to represent the number of requests
     * served, tasks completed, or errors.
     */
    public const METRIC_TYPE_COUNTER = 'counter';

    /**
     * A gauge is a metric that represents a single numerical value that can arbitrarily go up and down.
     */
    public const METRIC_TYPE_GAUGE = 'gauge';

    /**
     * A histogram samples observations (usually things like request durations or response sizes) and counts them in
     * configurable buckets. It also provides a sum of all observed values.
     */
    public const METRIC_TYPE_HISTOGRAM = 'histogram';

    /**
     * Similar to a histogram, a summary samples observations (usually things like request durations and response
     * sizes). While it also provides a total count of observations and a sum of all observed values, it calculates
     * configurable quantiles over a sliding time window.
     */
    public const METRIC_TYPE_SUMMARY = 'summary';

    public function isEnabled(): bool;

    public function getData(): array;
}