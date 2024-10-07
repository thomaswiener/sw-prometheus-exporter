<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Transport;


class MetricValue
{
    private float|int $value;

    private array $labels = [];

    public function __construct(float|int $value)
    {
        $this->value = $value;
    }

    public function addLabel(string $key, string $value): MetricValue
    {
        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getValue(): float|int
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    public function renderLabels(): string
    {
        $labels = [];
        foreach ($this->labels as $key => $value) {
            $labels[] = "$key=\"$value\"";
        }

        return "{".implode(",", $labels)."}";
    }
}