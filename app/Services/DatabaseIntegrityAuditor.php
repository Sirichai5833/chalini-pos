<?php

namespace App\Services;

use App\Services\DatabaseIntegrity\AccessIntegrityChecks;
use App\Services\DatabaseIntegrity\CommerceIntegrityChecks;
use App\Services\DatabaseIntegrity\CreditIntegrityChecks;
use App\Services\DatabaseIntegrity\DatabaseIntegrityCheck;
use App\Services\DatabaseIntegrity\StockIntegrityChecks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseIntegrityAuditor
{
    private array $schemaColumns = [];

    public function __construct(
        private readonly StockIntegrityChecks $stockChecks,
        private readonly CommerceIntegrityChecks $commerceChecks,
        private readonly CreditIntegrityChecks $creditChecks,
        private readonly AccessIntegrityChecks $accessChecks,
    ) {
    }

    public function audit(int $limit): array
    {
        $results = collect($this->definitions())
            ->map(fn (DatabaseIntegrityCheck $check) =>
                $this->runCheck($check, $limit))
            ->values();

        $criticalIssues = $results
            ->where('status', 'checked')
            ->where('severity', 'critical')
            ->sum('count');
        $warningIssues = $results
            ->where('status', 'checked')
            ->where('severity', 'warning')
            ->sum('count');
        $infoIssues = $results
            ->where('status', 'checked')
            ->where('severity', 'info')
            ->sum('count');

        return [
            'summary' => [
                'checked' => $results->where('status', 'checked')->count(),
                'unsupported' =>
                    $results->where('status', 'unsupported')->count(),
                'critical_issues' => $criticalIssues,
                'warning_issues' => $warningIssues,
                'info_issues' => $infoIssues,
                'exit_code' => $criticalIssues > 0 ? 1 : 0,
            ],
            'checks' => $results->all(),
        ];
    }

    private function definitions(): array
    {
        return [
            ...$this->stockChecks->definitions(),
            ...$this->commerceChecks->definitions(),
            ...$this->creditChecks->definitions(),
            ...$this->accessChecks->definitions(),
        ];
    }

    private function runCheck(
        DatabaseIntegrityCheck $check,
        int $limit,
    ): array {
        $unsupportedReason = $check->unsupportedReason
            ?? $this->missingSchemaReason($check->requirements);

        if ($unsupportedReason !== null) {
            return [
                'key' => $check->key,
                'name' => $check->name,
                'severity' => $check->severity,
                'status' => 'unsupported',
                'count' => null,
                'sample_ids' => [],
                'impact' => $check->impact,
                'reason' => $unsupportedReason,
            ];
        }

        $query = ($check->queryFactory)();
        $count = DB::query()
            ->fromSub(clone $query, 'integrity_issues')
            ->count();
        $sampleIds = DB::query()
            ->fromSub(clone $query, 'integrity_issues')
            ->orderBy('record_id')
            ->limit($limit)
            ->pluck('record_id')
            ->map(fn ($id) => is_numeric($id) ? (int) $id : (string) $id)
            ->all();

        return [
            'key' => $check->key,
            'name' => $check->name,
            'severity' => $check->severity,
            'status' => 'checked',
            'count' => (int) $count,
            'sample_ids' => $sampleIds,
            'impact' => $check->impact,
            'reason' => null,
        ];
    }

    private function missingSchemaReason(array $requirements): ?string
    {
        foreach ($requirements as $table => $columns) {
            $available = $this->columnsFor($table);

            if ($available === null) {
                return "Missing table [{$table}].";
            }

            $missing = collect($columns)
                ->reject(fn ($column) => in_array(
                    strtolower($column),
                    $available,
                    true,
                ))
                ->implode(', ');

            if ($missing !== '') {
                return "Missing columns on [{$table}]: {$missing}.";
            }
        }

        return null;
    }

    private function columnsFor(string $table): ?array
    {
        if (array_key_exists($table, $this->schemaColumns)) {
            return $this->schemaColumns[$table];
        }

        if (!Schema::hasTable($table)) {
            return $this->schemaColumns[$table] = null;
        }

        return $this->schemaColumns[$table] = collect(
            Schema::getColumnListing($table)
        )
            ->map(fn ($column) => strtolower($column))
            ->all();
    }
}
