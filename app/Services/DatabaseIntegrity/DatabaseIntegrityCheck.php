<?php

namespace App\Services\DatabaseIntegrity;

use Closure;

class DatabaseIntegrityCheck
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $severity,
        public readonly string $impact,
        public readonly ?Closure $queryFactory,
        public readonly array $requirements = [],
        public readonly ?string $unsupportedReason = null,
    ) {
    }

    public static function query(
        string $key,
        string $name,
        string $severity,
        string $impact,
        Closure $queryFactory,
        array $requirements,
    ): self {
        return new self(
            $key,
            $name,
            $severity,
            $impact,
            $queryFactory,
            $requirements,
        );
    }

    public static function unsupported(
        string $key,
        string $name,
        string $severity,
        string $impact,
        string $reason,
    ): self {
        return new self(
            $key,
            $name,
            $severity,
            $impact,
            null,
            [],
            $reason,
        );
    }
}
