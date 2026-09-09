<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use InvalidArgumentException;

final class AnalyticsService
{
    private $discovery;

    public function __construct(ProviderDiscoveryService $discovery)
    {
        $this->discovery = $discovery;
    }

    /** @return array<string,mixed> */
    public function metric(string $providerKey, string $metricKey, array $context = []): array
    {
        $provider = $this->discovery->discover()->get($providerKey);
        $metricKey = $this->validateSourceKey($metricKey);
        $result = $provider->getMetric($metricKey, $this->normalizeContext($context));

        if (!array_key_exists('value', $result)) {
            throw new InvalidArgumentException('Analytics metric result must contain a value key.');
        }

        return [
            'provider' => $provider->getKey(),
            'metric' => $metricKey,
            'label' => isset($result['label']) ? (string) $result['label'] : $metricKey,
            'value' => $result['value'],
            'unit' => isset($result['unit']) ? (string) $result['unit'] : '',
            'meta' => isset($result['meta']) && is_array($result['meta']) ? $result['meta'] : [],
        ];
    }

    /** @return array<int,array<string,mixed>> */
    public function dataset(string $providerKey, string $datasetKey, array $context = []): array
    {
        $provider = $this->discovery->discover()->get($providerKey);
        $datasetKey = $this->validateSourceKey($datasetKey);
        $context = $this->normalizeContext($context);
        $context['limit'] = max(1, min(5000, (int) ($context['limit'] ?? 500)));
        $rows = $provider->getDataset($datasetKey, $context);

        if (count($rows) > 5000) {
            throw new InvalidArgumentException('Analytics datasets may return at most 5000 rows per request.');
        }
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new InvalidArgumentException('Analytics dataset rows must be arrays.');
            }
        }

        return array_values($rows);
    }

    private function normalizeContext(array $context): array
    {
        $encoded = json_encode($context);
        if ($encoded === false || strlen($encoded) > 65535) {
            throw new InvalidArgumentException('Analytics context is invalid or too large.');
        }
        return $context;
    }

    private function validateSourceKey(string $key): string
    {
        $key = strtolower(trim($key));
        if (!preg_match('/^[a-z][a-z0-9_.-]{1,99}$/', $key)) {
            throw new InvalidArgumentException('Invalid Analytics source key.');
        }
        return $key;
    }
}
