<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

abstract class AbstractMetric implements MetricInterface
{
    private bool $enabled = true;

    private string $help = "";
    private string $type = "";
    private string $name = "";

    protected function getMetricHeader(): array
    {
        return [
            "# HELP {$this->getName()} {$this->getHelp()}",
            "# TYPE {$this->getName()} {$this->getType()}",
        ];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    abstract public function getData(): array;

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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
     */
    protected function setHelp(string $help): void
    {
        $this->help = $help;
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
     */
    protected function setType(string $type): void
    {
        $this->type = $type;
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
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }
}