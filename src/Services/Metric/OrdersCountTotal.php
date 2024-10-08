<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\Services\AbstractMetricCollector;
use Wienerio\ShopwarePrometheusExporter\Services\MetricInterface;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\Metric;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\MetricValue;

class OrdersCountTotal extends AbstractMetricCollector
{
    public function __construct(Connection $connection, SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        parent::__construct($connection, $systemConfigService, $logger);

        $this->setName("shopware_orders_count_total");
        $this->metric = new Metric(
            $this->getName(),
            MetricInterface::METRIC_TYPE_SUMMARY,
            "Orders count Total"
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

    /**
     * @throws Exception
     */
    protected function getOrdersCountTotalBySalesChannel(array $salesChannels): Metric
    {
        $result = $this->getQueryResult();

        $metric = new Metric($this->getName(), $this->getType(), $this->getHelp());
        foreach ($result->fetchAllAssociative() as $item) {
            $labelValue = $salesChannels[$item['id']];
            $metricValue = new MetricValue($item['count']);
            $metricValue->addLabel('sales_channel', $labelValue);
            $metric->addMetricValue($metricValue);
        }

        return $metric;
    }

    protected function getQueryResult(): Result
    {
        $query = <<<SQL
SELECT
    count(distinct `id`) as `count`, 
    hex(sales_channel_id) AS `id` 
FROM 
    `order` 
GROUP BY 
    sales_channel_id
SQL;

        return $this->connection->executeQuery($query);
    }
}