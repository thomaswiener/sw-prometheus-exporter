<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services;

use Exception;
use Psr\Log\LoggerInterface;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\MetricInterface;
use Wienerio\ShopwarePrometheusExporter\ShopwarePrometheusExporter;

class MetricsCollector
{
    public function __construct(
        private readonly iterable $metrics,
        private readonly LoggerInterface $logger
    ) {}

    public function collect(): string
    {
        $metricsData = [];
        foreach ($this->metrics as $metric) {
            /** @var MetricInterface $metric */
            if (!$metric->isEnabled()) {
                continue;
            }
            try {
                $metricsData = array_merge($metricsData, $metric->getData());
            } catch (Exception $e) {
                $context = [
                    'plugin' => ShopwarePrometheusExporter::UNIQUE_IDENTIFIER,
                    'metric' => $metric->getName(),
                    'message' => $e->getMessage()
                ];
                $this->logger->error('Error getting metric data for prometheus', $context);
            }
        }

        return implode("\n", $metricsData);
    }
}