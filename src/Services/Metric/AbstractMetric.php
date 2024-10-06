<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\ShopwarePrometheusExporter;

abstract class AbstractMetric implements MetricInterface
{
    private bool $enabled = true;
    private string $help = "";
    private string $type = "";
    private string $name = "";

    public function __construct(
        protected readonly Connection $connection,
        protected readonly SystemConfigService $systemConfigService,
        protected readonly LoggerInterface $logger
    ) {}

    protected function renderMetricLine(string $metricName, string|float|int $metricValue, array $labels = []): string
    {
        $label = $this->renderLabels($labels);

        return "{$metricName}$label {$metricValue}";
    }

    protected function renderLabels(array $labels): string
    {
        $lines = [];
        foreach ($labels as $key => $value) {
            $lines[] = "$key=\"$value\"";
        }

        if (!$lines) {
            return "";
        }

        return "{".implode(",", $lines)."}";
    }

    /**
     * @throws Exception
     */
    protected function getSalesChannelNamesById(): array
    {
        $query = <<<SQL
SELECT 
    hex(sct.sales_channel_id) AS `id`, 
    sct.`name` 
FROM 
    `sales_channel_translation` sct, 
    `language` l 
WHERE 
    sct.language_id = l.id AND 
    l.name = "Deutsch"
SQL;
        $result = $this->connection->executeQuery($query);

        $items = [];
        foreach ($result->fetchAllAssociative() as $item) {
            $items[$item['id']] = $item['name'];
        }

        return $items;
    }

    protected function getMetricHeader(): array
    {
        return [
            "# HELP {$this->getName()} {$this->getHelp()}",
            "# TYPE {$this->getName()} {$this->getType()}",
        ];
    }

    public function isEnabled(): bool
    {
        if ($this->getSystemConfig($this->getConfigKeyIsEnabled())) {
            return $this->enabled;
        }

        return false;
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

    protected function getSystemConfig(string $key)
    {
        $namespace = ShopwarePrometheusExporter::CONFIG_IDENTIFIER;

        return $this->systemConfigService->get("{$namespace}.config.{$key}");
    }

    protected function toCamelCase(string $value)
    {
        return str_replace(
            ' ',
            '',
            ucwords(
                str_replace(
                    ['-', '_'],
                    ' ',
                    $value
                )
            )
        );
    }

    protected function getConfigKeyIsEnabled()
    {
        return "{$this->getConfigKey()}IsEnabled";
    }
    protected function getConfigKey()
    {
        return $this->toCamelCase($this->getName());
    }
}