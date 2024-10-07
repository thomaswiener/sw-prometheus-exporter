<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Transport;


class Metric
{
    private string $name;

    private string $type;

    private string $help;

    private array $metricValues = [];

    public function __construct(string $name, string $type, string $help)
    {
        $this->name = $name;
        $this->type = $type;
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Metric
     */
    public function setName(string $name): Metric
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Metric
     */
    public function setType(string $type): Metric
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @param string $help
     * @return Metric
     */
    public function setHelp(string $help): Metric
    {
        $this->help = $help;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetricValues(): array
    {
        return $this->metricValues;
    }

    /**
     * @param array $metricValues
     * @return Metric
     */
    public function setMetricValues(array $metricValues): Metric
    {
        $this->metricValues = $metricValues;

        return $this;
    }

    /**
     * @param MetricValue $metricValue
     * @return Metric
     */
    public function addMetricValue(MetricValue $metricValue): Metric
    {
        $this->metricValues[] = $metricValue;

        return $this;
    }

    protected function getMetricHeader(): array
    {
        return [
            "# HELP {$this->getName()} {$this->getHelp()}",
            "# TYPE {$this->getName()} {$this->getType()}",
        ];
    }

    public function renderMetrics(): array
    {
        $metrics = $this->getMetricHeader();

        /** @var MetricValue $metricValue */
        foreach ($this->metricValues as $metricValue) {
            $name = $this->getName();
            $labels = $metricValue->renderLabels();
            $value = $metricValue->getValue();

            $metrics[] = "{$name}$labels {$value}";
        }

        return $metrics;
    }



}