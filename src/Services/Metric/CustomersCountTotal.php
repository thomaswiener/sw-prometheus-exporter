<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;

class CustomersCountTotal extends AbstractMetric
{
    public function __construct(
        private readonly Connection $connection
    ) {
        $this->setType(MetricInterface::METRIC_TYPE_COUNTER);
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
        $query2 = 'select count(*) as `count`, hex(sales_channel_id) as id from `customer` group by sales_channel_id';
        $result = $this->connection->executeQuery($query2);

        $items = [];
        foreach ($result->fetchAll() as $item) {
            $label = $salesChannels[$item['id']];
            $metric = $item['count'];

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