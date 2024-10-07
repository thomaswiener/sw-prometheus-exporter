<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\Metric;
use Wienerio\ShopwarePrometheusExporter\ShopwarePrometheusExporter;

abstract class AbstractMetricCollector implements MetricInterface
{
    private bool $enabled = true;

    private string $name;

    protected Metric $metric;

    public function __construct(
        protected readonly Connection $connection,
        protected readonly SystemConfigService $systemConfigService,
        protected readonly LoggerInterface $logger
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractMetricCollector
     */
    public function setName(string $name): AbstractMetricCollector
    {
        $this->name = $name;

        return $this;
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

    public function isEnabled(): bool
    {
        if ($this->getSystemConfig($this->getConfigKeyIsEnabled())) {
            return $this->enabled;
        }

        return false;
    }

    abstract public function getMetric(): Metric;

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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