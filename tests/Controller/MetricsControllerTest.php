<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Tests\Controller\Storefront;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Wienerio\ShopwarePrometheusExporter\Controller\Storefront\MetricsController;
use Wienerio\ShopwarePrometheusExporter\Services\MetricsCollector;

class MetricsControllerTest extends TestCase
{
    public function testMetrics(): void
    {
        $metricsCollectorMock = $this->getMockBuilder(MetricsCollector::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['collect'])
            ->getMock();

        $metricsCollectorMock
            ->expects($this->once())
            ->method('collect')
            ->willReturn('some-data');

        $metricsController = new MetricsController(
            $metricsCollectorMock,
            new NullLogger()
        );

        $request = new Request(['a' => 'b']);
        $response = $metricsController->metrics($request);

        $this->assertEquals('some-data', $response->getContent());
    }
}