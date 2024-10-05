<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;

class OrdersAmountTotal extends AbstractMetric
{
    public function __construct(
        private readonly Connection $connection
    ) {
        $this->setType(MetricInterface::METRIC_TYPE_COUNTER);
        $this->setHelp("Orders amount Total");
        $this->setName("shopware_orders_amount_total");
    }

    public function getData(): array
    {
        $salesChannels = $this->getSalesChannelNamesById();
        $data = $this->getOrdersAmountTotalBySalesChannel($salesChannels);

        return array_merge($this->getMetricHeader(), $data);
    }

    protected function getOrdersAmountTotalBySalesChannel(array $salesChannels): array
    {
        $query2 = 'select sum(amount_total) as `total`, hex(sales_channel_id) as id from `order` group by sales_channel_id';
        $result = $this->connection->executeQuery($query2);

        $items = [];
        foreach ($result->fetchAll() as $item) {
            $label = $salesChannels[$item['id']];
            $metric = $item['total'];

            $items[] = "{$this->getName()}{sales_channel=\"{$label}\"} {$metric}";
        }

        return $items;
    }

    protected function getSalesChannelNamesById(): array
    {
        $query1 = 'select hex(sct.sales_channel_id) as id, sct.`name` from sales_channel_translation sct, `language` l where sct.language_id = l.id and l.name = "Deutsch"';
        $result = $this->connection->executeQuery($query1);

        $items = [];
        foreach ($result->fetchAll() as $item) {
            $items[$item['id']] = $item['name'];
        }

        return $items;
    }
}