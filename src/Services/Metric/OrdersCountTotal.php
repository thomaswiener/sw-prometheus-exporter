<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;

class OrdersCountTotal extends AbstractMetric
{
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->setType(MetricInterface::METRIC_TYPE_SUMMARY);
        $this->setHelp("Orders count Total");
        $this->setName("shopware_orders_count_total");
    }

    public function getData(): array
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $data = $this->getOrdersCountTotalBySalesChannel($salesChannels);

        return array_merge($this->getMetricHeader(), $data);
    }

    /**
     * @throws Exception
     */
    protected function getOrdersCountTotalBySalesChannel(array $salesChannels): array
    {
        $result = $this->getQueryResult();

        $items = [];
        foreach ($result->fetchAllAssociative() as $item) {
            $labelValue = $salesChannels[$item['id']];
            $metricValue = $item['count'];

            $labels = ["sales_channel" => $labelValue];
            $items[] = $this->renderMetricLine($this->getName(), $metricValue, $labels);
        }

        return $items;
    }

    protected function getQueryResult(): Result
    {
        $query = <<<SQL
SELECT
    count(*) AS `count`, 
    hex(sales_channel_id) AS `id` 
FROM 
    `order` 
GROUP BY 
    sales_channel_id
SQL;

        return $this->connection->executeQuery($query);
    }
}