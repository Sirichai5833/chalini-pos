<?php

namespace App\Services\DatabaseIntegrity;

abstract class IntegrityCheckProvider
{
    abstract public function definitions(): array;

    protected function check(
        string $key,
        string $name,
        string $severity,
        string $impact,
        \Closure $queryFactory,
        array $requirements,
    ): DatabaseIntegrityCheck {
        return DatabaseIntegrityCheck::query(
            $key,
            $name,
            $severity,
            $impact,
            $queryFactory,
            $requirements,
        );
    }

    protected function unsupported(
        string $key,
        string $name,
        string $severity,
        string $impact,
        string $reason,
    ): DatabaseIntegrityCheck {
        return DatabaseIntegrityCheck::unsupported(
            $key,
            $name,
            $severity,
            $impact,
            $reason,
        );
    }
}
