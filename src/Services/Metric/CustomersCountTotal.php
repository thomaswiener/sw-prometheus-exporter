<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;

class CustomersCountTotal extends AbstractMetric
{
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->setType(MetricInterface::METRIC_TYPE_SUMMARY);
        $this->setHelp("Customers count Total");
        $this->setName("shopware_customers_count_total");
    }

    public function getData(): array
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $data = $this->getCustomersCountTotalBySalesChannel($salesChannels);

        return array_merge($this->getMetricHeader(), $data);
    }

    protected function getCustomersCountTotalBySalesChannel(array $salesChannels): array
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