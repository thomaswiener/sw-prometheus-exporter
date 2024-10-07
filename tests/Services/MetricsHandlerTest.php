<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Tests\Services;

use ArrayIterator;
use Exception;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\OrdersCountTotal;
use Wienerio\ShopwarePrometheusExporter\Services\MetricsHandler;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\Metric;
use Wienerio\ShopwarePrometheusExporter\Services\Transport\MetricValue;

class MetricsHandlerTest extends TestCase
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
            ->onlyMethods(['isEnabled', 'getMetric', 'getName'])
            ->getMock();

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('getMetric')
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
            ->onlyMethods(['isEnabled', 'getMetric', 'getName'])
            ->getMock();

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $metric = new Metric('orders_count_total', 'summary', 'some help test');
        $metricValue = new MetricValue(1);
        $metric->addMetricValue($metricValue);

        $ordersCountTotalMock
            ->expects($this->once())
            ->method('getMetric')
            ->willReturn($metric);

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