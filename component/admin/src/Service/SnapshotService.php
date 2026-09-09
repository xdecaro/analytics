<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use InvalidArgumentException;

final class SnapshotService
{
    private $db;
    private $analytics;

    public function __construct(DatabaseInterface $db, AnalyticsService $analytics)
    {
        $this->db = $db;
        $this->analytics = $analytics;
    }

    public function capture(string $providerKey, string $metricKey, array $context = []): int
    {
        return $this->storeResult($providerKey, $metricKey, $context, $this->analytics->metric($providerKey, $metricKey, $context));
    }

    public function storeResult(string $providerKey, string $metricKey, array $context, array $metric): int
    {
        if (!array_key_exists('value', $metric)) {
            throw new InvalidArgumentException('Metric snapshot requires a value.');
        }
        $contextHash = hash('sha256', $this->canonicalJson($context));
        $value = $metric['value'];
        $numeric = is_int($value) || is_float($value) || (is_string($value) && is_numeric($value)) ? (string) $value : null;
        $text = $numeric === null ? mb_substr((string) $value, 0, 255) : null;
        $payload = json_encode($metric, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($payload === false || strlen($payload) > 1048576) {
            throw new InvalidArgumentException('Metric snapshot payload is invalid or too large.');
        }
        $measured = Factory::getDate()->toSql();
        $created = $measured;
        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__xdecaroanalytics_snapshots'))
            ->columns($this->db->quoteName(['provider_key','metric_key','context_hash','value_numeric','value_text','payload_json','measured_at','created']))
            ->values(':provider,:metric,:hash,:numeric,:text,:payload,:measured,:created')
            ->bind(':provider', $providerKey)
            ->bind(':metric', $metricKey)
            ->bind(':hash', $contextHash)
            ->bind(':numeric', $numeric)
            ->bind(':text', $text)
            ->bind(':payload', $payload)
            ->bind(':measured', $measured)
            ->bind(':created', $created);
        $this->db->setQuery($query)->execute();
        return (int) $this->db->insertid();
    }

    /** @return array<int,array<string,mixed>> */
    public function history(string $providerKey, string $metricKey, array $context = [], int $limit = 30): array
    {
        $hash = hash('sha256', $this->canonicalJson($context));
        $limit = max(1, min(365, $limit));
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(['id','value_numeric','value_text','measured_at']))
            ->from($this->db->quoteName('#__xdecaroanalytics_snapshots'))
            ->where($this->db->quoteName('provider_key') . ' = :provider')
            ->where($this->db->quoteName('metric_key') . ' = :metric')
            ->where($this->db->quoteName('context_hash') . ' = :hash')
            ->order($this->db->quoteName('measured_at') . ' DESC')
            ->bind(':provider', $providerKey)
            ->bind(':metric', $metricKey)
            ->bind(':hash', $hash);
        return (array) $this->db->setQuery($query, 0, $limit)->loadAssocList();
    }

    private function canonicalJson(array $context): string
    {
        $this->sortRecursive($context);
        $json = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new InvalidArgumentException('Invalid snapshot context.');
        }
        return $json;
    }

    private function sortRecursive(array &$value): void
    {
        foreach ($value as &$item) {
            if (is_array($item)) { $this->sortRecursive($item); }
        }
        unset($item);
        if ($value !== [] && array_keys($value) !== range(0, count($value) - 1)) { ksort($value); }
    }
}
