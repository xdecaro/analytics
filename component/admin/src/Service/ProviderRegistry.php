<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use InvalidArgumentException;
use xdecaro\Component\Analytics\Administrator\Contract\AnalyticsProviderInterface;

final class ProviderRegistry
{
    /** @var array<string,AnalyticsProviderInterface> */
    private $providers = [];

    public function register(AnalyticsProviderInterface $provider): void
    {
        $key = strtolower(trim($provider->getKey()));
        if (!preg_match('/^[a-z][a-z0-9_.-]{1,99}$/', $key)) {
            throw new InvalidArgumentException('Invalid Analytics provider key.');
        }
        $this->providers[$key] = $provider;
    }

    public function has(string $key): bool
    {
        return isset($this->providers[strtolower(trim($key))]);
    }

    public function get(string $key): AnalyticsProviderInterface
    {
        $key = strtolower(trim($key));
        if (!isset($this->providers[$key])) {
            throw new InvalidArgumentException('Analytics provider is unavailable: ' . $key);
        }
        return $this->providers[$key];
    }

    /** @return array<string,AnalyticsProviderInterface> */
    public function all(): array
    {
        ksort($this->providers);
        return $this->providers;
    }

    public function count(): int
    {
        return count($this->providers);
    }
}
