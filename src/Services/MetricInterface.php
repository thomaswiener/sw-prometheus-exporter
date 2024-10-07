<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services;

use Wienerio\ShopwarePrometheusExporter\Services\Transport\Metric;

interface MetricInterface
{
    /**
     * https://prometheus.io/docs/concepts/metric_types/#counter
     */
    public const METRIC_TYPE_COUNTER = 'counter';

    /**
     * https://prometheus.io/docs/concepts/metric_types/#gauge
     */
    public const METRIC_TYPE_GAUGE = 'gauge';

    /**
     * https://prometheus.io/docs/concepts/metric_types/#histogram
     */
    public const METRIC_TYPE_HISTOGRAM = 'histogram';

    /**
     * https://prometheus.io/docs/concepts/metric_types/#summary
     */
    public const METRIC_TYPE_SUMMARY = 'summary';

    public function isEnabled(): bool;

    public function getName(): string;

    public function getMetric(): Metric;
}