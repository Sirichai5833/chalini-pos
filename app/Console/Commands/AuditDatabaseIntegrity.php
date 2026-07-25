<?php

namespace App\Console\Commands;

use App\Services\DatabaseIntegrityAuditor;
use Illuminate\Console\Command;

class AuditDatabaseIntegrity extends Command
{
    protected $signature = 'audit:database-integrity
        {--json : Emit machine-readable JSON}
        {--limit=20 : Maximum sample IDs per check}';

    protected $description =
        'Read-only audit of POS database integrity invariants';

    public function handle(DatabaseIntegrityAuditor $auditor): int
    {
        $limit = filter_var(
            $this->option('limit'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 1000]],
        );

        if ($limit === false) {
            $this->error('The --limit option must be an integer from 1 to 1000.');

            return self::INVALID;
        }

        $report = $auditor->audit($limit);

        if ($this->option('json')) {
            $this->line(json_encode(
                $report,
                JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR,
            ));
        } else {
            $this->renderTable($report);
        }

        return $report['summary']['exit_code'] === 0
            ? self::SUCCESS
            : self::FAILURE;
    }

    private function renderTable(array $report): void
    {
        $rows = collect($report['checks'])->map(fn (array $check) => [
            $check['severity'],
            $check['name'],
            $check['status'] === 'checked' ? $check['count'] : 'N/A',
            $check['status'] === 'checked'
                ? (implode(', ', $check['sample_ids']) ?: '-')
                : 'unsupported',
            $check['status'] === 'checked'
                ? $check['impact']
                : $check['reason'] . ' Impact: ' . $check['impact'],
        ]);

        $this->table(
            ['Severity', 'Check', 'Count', 'Sample IDs', 'Impact / reason'],
            $rows,
        );

        $summary = $report['summary'];
        $this->newLine();
        $this->line(
            "Checked {$summary['checked']}; " .
            "unsupported {$summary['unsupported']}; " .
            "critical {$summary['critical_issues']}; " .
            "warning {$summary['warning_issues']}; " .
            "info {$summary['info_issues']}."
        );
    }
}
