<?php declare(strict_types=1);

namespace Wienerio\ShopwarePrometheusExporter\Command;

use Iterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wienerio\ShopwarePrometheusExporter\Services\Metric\MetricInterface;

#[AsCommand(
    name: 'wio:prometheus:metrics:test',
    description: 'Prometheus Metrics'
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly iterable $metrics
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('WienerioPrometheusMetrics');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->metrics as $metric) {
            /** @var MetricInterface $metric */
            $data = $metric->getData();
            $output->writeln(implode("\n", $data));
        }
        return Command::SUCCESS;
    }
}