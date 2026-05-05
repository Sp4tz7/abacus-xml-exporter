<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Contract;

interface TimesheetEntryInterface
{
    public function getEmployeeIdentifier(): string;

    public function getDate(): \DateTimeInterface;

    /**
     * Duration in hours, for example 7.5.
     */
    public function getDuration(): float;

    public function getActivityCode(): ?string;

    public function getCostCenter(): ?string;

    public function getProjectCode(): ?string;

    public function getComment(): ?string;

    /**
     * Optional extra values for project-specific Abacus fields.
     *
     * @return array<string, scalar|null>
     */
    public function getExtraFields(): array;
}
