<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;

class OrdersAmountTotal extends AbstractMetric
{
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->setType(MetricInterface::METRIC_TYPE_SUMMARY);
        $this->setHelp("Orders amount Total");
        $this->setName("shopware_orders_amount_total");
    }

    public function getData(): array
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $data = $this->getOrdersAmountTotalBySalesChannel($salesChannels);

        return array_merge($this->getMetricHeader(), $data);
    }

    /**
     * @throws Exception
     */
    protected function getOrdersAmountTotalBySalesChannel(array $salesChannels): array
    {
        $result = $this->getQueryResult();

        $items = [];
        foreach ($result->fetchAllAssociative() as $item) {
            $labelValue = $salesChannels[$item['id']];
            $metricValue = $item['total'];

            $labels = ["sales_channel" => $labelValue];
            $items[] = $this->renderMetricLine($this->getName(), $metricValue, $labels);
        }

        return $items;
    }

    protected function getQueryResult(): Result
    {
        $query = <<<SQL
SELECT 
    sum(amount_total) AS `total`, 
    hex(sales_channel_id) AS `id` 
FROM 
    `order` 
GROUP BY 
    sales_channel_id
SQL;
        return $this->connection->executeQuery($query);
    }
}