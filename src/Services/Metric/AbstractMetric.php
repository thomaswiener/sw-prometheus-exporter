<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

abstract class AbstractMetric implements MetricInterface
{
    private bool $enabled = true;
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    abstract public function getData(): array;

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}