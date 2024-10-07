<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\Services\AbstractMetricCollector;
use Wienerio\ShopwarePrometheusExporter\Services\MetricInterface;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\Metric;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\MetricValue;

class CustomersCountTotal extends AbstractMetricCollector
{
    public function __construct(Connection $connection, SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        parent::__construct($connection, $systemConfigService, $logger);
        $this->setName("shopware_customers_count_total");
        $this->metric = new Metric(
            $this->getName(),
            MetricInterface::METRIC_TYPE_SUMMARY,
            "Customers count Total"
        );
    }

    public function getMetric(): Metric
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $result = $this->getQueryResult();

        foreach ($result->fetchAllAssociative() as $item) {
            $metricValue = new MetricValue(intval($item['count']));
            $metricValue->addLabel('sales_channel', $salesChannels[$item['id']]);
            $this->metric->addMetricValue($metricValue);
        }

        return $this->metric;
    }

    protected function getQueryResult(): Result
    {
        $query = <<<SQL
SELECT 
    count(*) as `count`, 
    hex(sales_channel_id) as `id` 
from 
    `customer` 
GROUP BY 
    sales_channel_id
SQL;

        return $this->connection->executeQuery($query);
    }
}