<?php
namespace xdecaro\Component\Analytics\Administrator\Contract;

defined('_JEXEC') or die;

interface AnalyticsProviderInterface
{
    public function getKey(): string;

    public function getLabel(): string;

    /** @return array<int,array<string,mixed>> */
    public function getMetrics(): array;

    /** @return array<int,array<string,mixed>> */
    public function getDatasets(): array;

    /** @return array<string,mixed> */
    public function getMetric(string $metricKey, array $context = []): array;

    /** @return array<int,array<string,mixed>> */
    public function getDataset(string $datasetKey, array $context = []): array;
}
