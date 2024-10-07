<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services;

use Exception;
use Psr\Log\LoggerInterface;
use Wienerio\ShopwarePrometheusExporter\ShopwarePrometheusExporter;

class MetricsHandler
{
    public function __construct(
        private readonly iterable        $metricCollectors,
        private readonly LoggerInterface $logger
    ) {}

    public function collect(): string
    {
        $metricsData = [];
        foreach ($this->metricCollectors as $metricCollector) {
            /** @var MetricInterface $metricCollector */
            if (!$metricCollector->isEnabled()) {
                continue;
            }
            try {
                $metricsData = array_merge($metricsData, $metricCollector->getMetric()->renderMetrics());
            } catch (Exception $e) {
                $context = [
                    'plugin' => ShopwarePrometheusExporter::UNIQUE_IDENTIFIER,
                    'metric' => $metricCollector->getName(),
                    'message' => $e->getMessage()
                ];
                $this->logger->error('Error getting metric data for prometheus', $context);
            }
        }

        return implode("\n", $metricsData);
    }
}