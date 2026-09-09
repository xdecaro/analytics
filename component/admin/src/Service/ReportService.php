<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class ReportService
{
    private $db;
    private $analytics;
    private $snapshots;

    public function __construct(DatabaseInterface $db, AnalyticsService $analytics, SnapshotService $snapshots)
    {
        $this->db = $db;
        $this->analytics = $analytics;
        $this->snapshots = $snapshots;
    }

    public function save(array $data, int $id = 0): int
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '' || strlen($title) > 255) { throw new InvalidArgumentException('Report title is required.'); }
        $provider = $this->token((string) ($data['provider_key'] ?? ''), 'provider_key');
        $sourceType = strtolower(trim((string) ($data['source_type'] ?? 'metric')));
        if (!in_array($sourceType, ['metric','dataset'], true)) { throw new InvalidArgumentException('Invalid report source type.'); }
        $sourceKey = $this->token((string) ($data['source_key'] ?? ''), 'source_key');
        $context = $this->normalizeContext($data['context'] ?? []);
        $contextJson = $context === [] ? null : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $scheduled = empty($data['scheduled']) ? 0 : 1;
        $state = isset($data['state']) ? ((int) $data['state'] === 1 ? 1 : 0) : 1;
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $now = Factory::getDate()->toSql();

        if ($id > 0) {
            $query = $this->db->getQuery(true)
                ->update($this->db->quoteName('#__xdecaroanalytics_reports'))
                ->set($this->db->quoteName('title') . ' = :title')
                ->set($this->db->quoteName('provider_key') . ' = :provider')
                ->set($this->db->quoteName('source_type') . ' = :source_type')
                ->set($this->db->quoteName('source_key') . ' = :source_key')
                ->set($this->db->quoteName('context_json') . ' = :context_json')
                ->set($this->db->quoteName('scheduled') . ' = :scheduled')
                ->set($this->db->quoteName('state') . ' = :state')
                ->set($this->db->quoteName('modified') . ' = :modified')
                ->set($this->db->quoteName('modified_by') . ' = :modified_by')
                ->where($this->db->quoteName('id') . ' = :id')
                ->bind(':title', $title)->bind(':provider', $provider)->bind(':source_type', $sourceType)
                ->bind(':source_key', $sourceKey)->bind(':context_json', $contextJson)
                ->bind(':scheduled', $scheduled, ParameterType::INTEGER)->bind(':state', $state, ParameterType::INTEGER)
                ->bind(':modified', $now)->bind(':modified_by', $userId, ParameterType::INTEGER)
                ->bind(':id', $id, ParameterType::INTEGER);
            $this->db->setQuery($query)->execute();
            return $id;
        }

        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__xdecaroanalytics_reports'))
            ->columns($this->db->quoteName(['title','provider_key','source_type','source_key','context_json','scheduled','state','created','created_by']))
            ->values(':title,:provider,:source_type,:source_key,:context_json,:scheduled,:state,:created,:created_by')
            ->bind(':title', $title)->bind(':provider', $provider)->bind(':source_type', $sourceType)
            ->bind(':source_key', $sourceKey)->bind(':context_json', $contextJson)
            ->bind(':scheduled', $scheduled, ParameterType::INTEGER)->bind(':state', $state, ParameterType::INTEGER)
            ->bind(':created', $now)->bind(':created_by', $userId, ParameterType::INTEGER);
        $this->db->setQuery($query)->execute();
        return (int) $this->db->insertid();
    }

    /** @return array<string,mixed> */
    public function get(int $id): array
    {
        if ($id < 1) { throw new InvalidArgumentException('Invalid report ID.'); }
        $query = $this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecaroanalytics_reports'))
            ->where($this->db->quoteName('id') . ' = :id')->bind(':id', $id, ParameterType::INTEGER);
        $row = $this->db->setQuery($query)->loadAssoc();
        if (!$row) { throw new RuntimeException('Analytics report not found.'); }
        $row['id'] = (int) $row['id']; $row['scheduled'] = (int) $row['scheduled']; $row['state'] = (int) $row['state'];
        $row['context'] = $this->decodeContext($row['context_json'] ?? null);
        return $row;
    }

    /** @return array<int,array<string,mixed>> */
    public function all(int $limit = 100): array
    {
        $limit = max(1, min(500, $limit));
        $query = $this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecaroanalytics_reports'))
            ->order($this->db->quoteName('title') . ' ASC');
        return (array) $this->db->setQuery($query, 0, $limit)->loadAssocList();
    }

    /** @return array<string,mixed> */
    public function run(int $id, int $actorId = 0): array
    {
        $report = $this->get($id);
        if ((int) $report['state'] !== 1) { throw new RuntimeException('Analytics report is disabled.'); }
        $started = Factory::getDate()->toSql();
        $status = 'running';
        $query = $this->db->getQuery(true)->insert($this->db->quoteName('#__xdecaroanalytics_report_runs'))
            ->columns($this->db->quoteName(['report_id','status','started_at','row_count','created_by']))
            ->values(':report_id,:status,:started_at,0,:created_by')
            ->bind(':report_id', $id, ParameterType::INTEGER)->bind(':status', $status)->bind(':started_at', $started)
            ->bind(':created_by', $actorId, ParameterType::INTEGER);
        $this->db->setQuery($query)->execute();
        $runId = (int) $this->db->insertid();

        try {
            if ($report['source_type'] === 'metric') {
                $result = $this->analytics->metric($report['provider_key'], $report['source_key'], $report['context']);
                $this->snapshots->storeResult($report['provider_key'], $report['source_key'], $report['context'], $result);
                $rowCount = 1;
            } else {
                $result = $this->analytics->dataset($report['provider_key'], $report['source_key'], $report['context']);
                $rowCount = count($result);
            }
            $json = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false || strlen($json) > 2097152) { throw new RuntimeException('Analytics report result exceeds the 2 MiB storage limit.'); }
            $completed = Factory::getDate()->toSql();
            $done = 'success';
            $query = $this->db->getQuery(true)->update($this->db->quoteName('#__xdecaroanalytics_report_runs'))
                ->set($this->db->quoteName('status') . ' = :status')->set($this->db->quoteName('completed_at') . ' = :completed')
                ->set($this->db->quoteName('row_count') . ' = :row_count')->set($this->db->quoteName('result_json') . ' = :result')
                ->where($this->db->quoteName('id') . ' = :id')
                ->bind(':status', $done)->bind(':completed', $completed)->bind(':row_count', $rowCount, ParameterType::INTEGER)
                ->bind(':result', $json)->bind(':id', $runId, ParameterType::INTEGER);
            $this->db->setQuery($query)->execute();
            return ['run_id' => $runId, 'report' => $report, 'result' => $result, 'row_count' => $rowCount];
        } catch (Throwable $exception) {
            $completed = Factory::getDate()->toSql(); $failed = 'failed'; $error = mb_substr($exception->getMessage(), 0, 65535);
            $query = $this->db->getQuery(true)->update($this->db->quoteName('#__xdecaroanalytics_report_runs'))
                ->set($this->db->quoteName('status') . ' = :status')->set($this->db->quoteName('completed_at') . ' = :completed')
                ->set($this->db->quoteName('error_text') . ' = :error')->where($this->db->quoteName('id') . ' = :id')
                ->bind(':status', $failed)->bind(':completed', $completed)->bind(':error', $error)->bind(':id', $runId, ParameterType::INTEGER);
            $this->db->setQuery($query)->execute();
            throw $exception;
        }
    }

    public function runScheduled(int $limit = 25): array
    {
        $limit = max(1, min(100, $limit)); $state = 1; $scheduled = 1;
        $query = $this->db->getQuery(true)->select($this->db->quoteName('id'))->from($this->db->quoteName('#__xdecaroanalytics_reports'))
            ->where($this->db->quoteName('state') . ' = :state')->where($this->db->quoteName('scheduled') . ' = :scheduled')
            ->order($this->db->quoteName('id') . ' ASC')->bind(':state', $state, ParameterType::INTEGER)
            ->bind(':scheduled', $scheduled, ParameterType::INTEGER);
        $ids = array_map('intval', (array) $this->db->setQuery($query, 0, $limit)->loadColumn());
        $stats = ['processed' => 0, 'success' => 0, 'failed' => 0];
        foreach ($ids as $id) {
            $stats['processed']++;
            try { $this->run($id, 0); $stats['success']++; } catch (Throwable $exception) { $stats['failed']++; }
        }
        return $stats;
    }

    /** @return array<string,mixed>|null */
    public function latestRun(int $reportId): ?array
    {
        $query = $this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecaroanalytics_report_runs'))
            ->where($this->db->quoteName('report_id') . ' = :report_id')->order($this->db->quoteName('id') . ' DESC')
            ->bind(':report_id', $reportId, ParameterType::INTEGER);
        $row = $this->db->setQuery($query, 0, 1)->loadAssoc();
        if (!$row) { return null; }
        $row['id'] = (int) $row['id']; $row['row_count'] = (int) $row['row_count'];
        $decoded = $row['result_json'] ? json_decode($row['result_json'], true) : null;
        $row['result'] = is_array($decoded) ? $decoded : null;
        return $row;
    }

    public function exportCsv(int $reportId): string
    {
        $run = $this->latestRun($reportId);
        if (!$run || $run['status'] !== 'success' || !is_array($run['result'])) { throw new RuntimeException('No successful report result is available for export.'); }
        $report = $this->get($reportId);
        $rows = $report['source_type'] === 'metric' ? [$run['result']] : $run['result'];
        if ($rows === []) { return "\xEF\xBB\xBF"; }
        $headers = [];
        foreach ($rows as $row) { if (is_array($row)) { $headers = array_values(array_unique(array_merge($headers, array_keys($row)))); } }
        $stream = fopen('php://temp', 'w+');
        if ($stream === false) { throw new RuntimeException('Unable to create CSV stream.'); }
        fwrite($stream, "\xEF\xBB\xBF"); fputcsv($stream, $headers);
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $header) { $value = $row[$header] ?? ''; $line[] = is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
            fputcsv($stream, $line);
        }
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $csv === false ? '' : $csv;
    }

    private function token(string $value, string $field): string
    {
        $value = strtolower(trim($value));
        if (!preg_match('/^[a-z][a-z0-9_.-]{1,99}$/', $value)) { throw new InvalidArgumentException('Invalid ' . $field . '.'); }
        return $value;
    }

    private function normalizeContext($context): array
    {
        if (is_string($context)) {
            $decoded = json_decode(trim($context) === '' ? '{}' : $context, true);
            if (!is_array($decoded)) { throw new InvalidArgumentException('Report context must be valid JSON.'); }
            $context = $decoded;
        }
        if (!is_array($context)) { throw new InvalidArgumentException('Report context must be an array.'); }
        $json = json_encode($context);
        if ($json === false || strlen($json) > 65535) { throw new InvalidArgumentException('Report context is too large.'); }
        return $context;
    }

    private function decodeContext($json): array
    {
        if ($json === null || trim((string) $json) === '') { return []; }
        $decoded = json_decode((string) $json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
