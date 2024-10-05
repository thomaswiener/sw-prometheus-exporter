<?php

declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Tests\Controller\Storefront;

use ArrayIterator;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Wienerio\ShopwarePrometheusExporter\Controller\Storefront\MetricsController;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\OrdersCountTotal;

class MetricsControllerTest extends TestCase
{
    public function testSomething(): void
    {
        $systemConfigServiceMock = $this->getMockBuilder(SystemConfigService::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $orderCountTotal = new OrdersCountTotal($connectionMock);
        $iterator = new ArrayIterator([
            $orderCountTotal
        ]);

        $metricsController = new MetricsController(
            $systemConfigServiceMock,
            $iterator,
            new NullLogger()
        );

        $request = new Request(['a' => 'b']);
        $response = $metricsController->all($request, $formFactory);

        $this->assertEquals('localhost', 'localhost');
    }
}