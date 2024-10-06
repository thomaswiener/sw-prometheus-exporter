<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Tests\Services\Metric;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\CustomersCountTotal;

class CustomersCountTotalTest extends TestCase
{
    public function testGetData(): void
    {
        $result1 = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchAllAssociative'])
            ->getMock();

        $result1
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->willReturn([
                [
                    'id' => '1',
                    'name' => 'sales-channel-1'
                ], [
                    'id' => '2',
                    'name' => 'sales-channel-2'
                ]
            ]);

        $result2 = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchAllAssociative'])
            ->getMock();

        $result2
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->willReturn([
                [
                    'id' => '1',
                    'count' => 10
                ],
                [
                    'id' => '2',
                    'count' => 5
                ]
            ]);

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['executeQuery'])
            ->getMock();

        $connectionMock
            ->expects($this->any())
            ->method('executeQuery')
            ->willReturnOnConsecutiveCalls($result1, $result2);

        $systemConfigServiceMock = $this->getMockBuilder(SystemConfigService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $systemConfigServiceMock
            ->expects($this->any())
            ->method('get')
            ->willReturn(true);

        $metric = new CustomersCountTotal($connectionMock, $systemConfigServiceMock, new NullLogger());
        $data = $metric->getData();

        $this->assertEquals('# HELP shopware_customers_count_total Customers count Total', $data['0']);
        $this->assertEquals('# TYPE shopware_customers_count_total summary', $data['1']);
        $this->assertEquals('shopware_customers_count_total{sales_channel="sales-channel-1"} 10', $data['2']);
        $this->assertEquals('shopware_customers_count_total{sales_channel="sales-channel-2"} 5', $data['3']);
    }
}