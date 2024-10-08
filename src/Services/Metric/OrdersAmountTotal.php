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

class OrdersAmountTotal extends AbstractMetricCollector
{
    public function __construct(Connection $connection, SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        parent::__construct($connection, $systemConfigService, $logger);

        $this->setName("shopware_orders_amount_total");
        $this->metric = new Metric(
            $this->getName(),
            MetricInterface::METRIC_TYPE_SUMMARY,
            "Orders amount Total"
        );
    }

    public function getMetric(): Metric
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $result = $this->getQueryResult();

        foreach ($result->fetchAllAssociative() as $item) {
            $metricValue = new MetricValue(floatval($item['total']));
            $metricValue->addLabel('sales_channel', $salesChannels[$item['id']]);
            $this->metric->addMetricValue($metricValue);
        }

        return $this->metric;
    }

    protected function getQueryResult(): Result
    {
        $query = <<<SQL
SELECT 
    sum(amount_total) * COUNT(DISTINCT `id`) / COUNT(*) AS `total`, 
    hex(sales_channel_id) AS `id` 
FROM 
    `order` 
GROUP BY 
    sales_channel_id
SQL;
        return $this->connection->executeQuery($query);
    }
}