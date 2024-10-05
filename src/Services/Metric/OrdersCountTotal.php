<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Services\Metric;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class OrdersCountTotal extends AbstractMetric
{
    private string $help = "Orders count Total";
    private string $type = MetricInterface::METRIC_TYPE_COUNTER;
    private string $name = "shopware_orders_count_total";

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function getData(): array
    {
        $query1 = 'select sct.sales_channel_id, sct.`name` from sales_channel_translation sct, `language` l where sct.language_id = l.id and l.name = "Deutsch"';
        $result = $this->connection->executeQuery($query1);
        $query2 = 'select count(*), sales_channel_id from `order` group by sales_channel_id';
        $result = $this->connection->executeQuery($query2);

        $metric = 1;
        $salesChannel = new SalesChannelEntity();
        $salesChannel->setId('1');

        return [
            "# HELP {$this->name} {$this->help}",
            "# TYPE {$this->name} {$this->type}",
            "{$this->name}{sales_channel=\"{$salesChannel->getId()}\"} {$metric}"
        ];
    }
}