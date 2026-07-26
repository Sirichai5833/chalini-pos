<?php

namespace App\Console\Commands;

use App\Services\DatabaseIntegrityRepair\RepairPreconditionChanged;
use App\Services\DatabaseIntegrityRepairService;
use Illuminate\Console\Command;

class RepairDatabaseIntegrity extends Command
{
    protected $signature = 'repair:database-integrity
        {--apply : Apply deterministic repairs inside a transaction}
        {--only= : Limit scope to shifts, roles, movements, or a comma-separated list}
        {--json : Emit machine-readable JSON}';

    protected $description =
        'Inspect critical integrity findings and safely apply deterministic repairs';

    public function handle(
        DatabaseIntegrityRepairService $repairs,
    ): int {
        $scopes = $this->scopes();

        if ($scopes === null) {
            return self::INVALID;
        }

        $report = $repairs->plan($scopes);

        if ($this->option('apply')) {
            try {
                $report = $repairs->apply($report);
            } catch (RepairPreconditionChanged $exception) {
                return $this->renderFailure($exception->getMessage());
            }
        }

        $this->renderReport($report);

        return self::SUCCESS;
    }

    private function scopes(): ?array
    {
        $only = trim((string) $this->option('only'));

        if ($only === '') {
            return DatabaseIntegrityRepairService::SCOPES;
        }

        $scopes = collect(explode(',', $only))
            ->map(fn ($scope) => trim($scope))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $invalid = array_diff(
            $scopes,
            DatabaseIntegrityRepairService::SCOPES,
        );

        if ($scopes === [] || $invalid !== []) {
            $message = 'The --only option accepts: shifts, roles, movements.';

            if ($this->option('json')) {
                $this->line(json_encode(
                    ['error' => $message],
                    JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR,
                ));
            } else {
                $this->error($message);
            }

            return null;
        }

        return $scopes;
    }

    private function renderReport(array $report): void
    {
        if ($this->option('json')) {
            $this->line($this->json($report));

            return;
        }

        $summary = $report['summary'];
        $this->info($summary['mode'] === 'dry-run'
            ? 'DRY RUN: no data was changed.'
            : 'APPLY completed for deterministic repairs.');
        $this->line(
            "Scopes: " . implode(', ', $summary['scopes']) .
            "; planned {$summary['planned']}; " .
            "manual-only {$summary['manual_only']}; " .
            "applied {$summary['applied']}."
        );
        $this->line(
            "Critical before: {$summary['critical_before']}; " .
            'critical after: ' .
            ($summary['critical_after'] ?? 'not applied') .
            '.'
        );
        $this->newLine();
        $this->table(
            ['Scope', 'Key', 'Status', 'Subject', 'Reason'],
            collect($report['items'])->map(fn ($item) => [
                $item['scope'],
                $item['key'],
                $item['status'],
                $item['subject_id'],
                $item['reason'],
            ]),
        );

        foreach ($report['items'] as $item) {
            $this->newLine();
            $this->line("<comment>{$item['key']}</comment>");
            $this->line('Before: ' . $this->json($item['before']));
            $this->line(
                'After: ' . ($item['after'] === null
                    ? 'manual decision required'
                    : $this->json($item['after']))
            );
            $this->line('Evidence: ' . $this->json($item['evidence']));
        }
    }

    private function renderFailure(string $message): int
    {
        if ($this->option('json')) {
            $this->line($this->json(['error' => $message]));
        } else {
            $this->error($message);
        }

        return self::FAILURE;
    }

    private function json(array $value): string
    {
        return json_encode(
            $value,
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR,
        );
    }
}
