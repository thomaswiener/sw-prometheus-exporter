<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Tests\Services;

use ArrayIterator;
use Exception;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\OrdersCountTotal;
use Wienerio\ShopwarePrometheusExporter\Services\MetricsHandler;

class MetricsCollectorTest extends TestCase
{
    public function testCollectException(): void
    {
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['error'])
            ->getMock();

        $loggerMock
            ->expects($this->once())
            ->method('error');

        $ordersCountTotalMock = $this->getMockBuilder(OrdersCountTotal::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEnabled', 'getData', 'getName'])
            ->getMock();

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('getData')
            ->willThrowException(new Exception('error processing'));

        $iterator = new ArrayIterator([
            $ordersCountTotalMock
        ]);

        $metricsCollector = new MetricsHandler(
            $iterator,
            $loggerMock
        );

        $data = $metricsCollector->collect();

        $this->assertEquals('', $data);
    }

    public function testCollect(): void
    {
        $ordersCountTotalMock = $this->getMockBuilder(OrdersCountTotal::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEnabled', 'getData', 'getName'])
            ->getMock();

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn([
                '# HELP some help test',
                '# TYPE orders_count_total summary',
                'orders_count_total 1'
            ]);

        $iterator = new ArrayIterator([
            $ordersCountTotalMock
        ]);

        $metricsCollector = new MetricsHandler(
            $iterator,
            new NullLogger()
        );

        $data = $metricsCollector->collect();

        $this->assertTrue(str_contains($data, 'TYPE orders_count_total summary'));
    }
}