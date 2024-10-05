<?php

namespace Wienerio\ShopwarePrometheusExporter\Services;

use Iterator;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\MetricInterface;

class MetricsCollector
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly iterator $metrics,
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
            $metricsData = array_merge($metricsData, $metric->getData());
        }

        return implode("\n", $metricsData);
    }
}