<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Controller\Storefront;


use Psr\Log\LoggerInterface;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wienerio\ShopwarePrometheusExporter\Services\MetricsHandler;

class MetricsController extends StorefrontController
{
    public function __construct(
        private readonly MetricsHandler  $metricsHandler,
        private readonly LoggerInterface $logger
    ) {}

    #[Route(
        path: '/store-api/metrics',
        name: 'wienerio.store-api.prometheus.metrics',
        defaults: ['_routeScope' => ['store-api'], 'auth_required' => false],
        methods: ['GET'])
    ]
    public function metrics(Request $request): Response
    {
        $data = $this->metricsHandler->collect();

        return $this->getResponse($data);
    }

    protected function getResponse(string $content): Response
    {
        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
