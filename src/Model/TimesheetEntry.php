<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Model;

use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;

final readonly class TimesheetEntry implements TimesheetEntryInterface
{
    /**
     * @param array<string, scalar|null> $extraFields
     */
    public function __construct(
        private string $employeeIdentifier,
        private \DateTimeInterface $date,
        private float $duration,
        private ?string $activityCode = null,
        private ?string $costCenter = null,
        private ?string $projectCode = null,
        private ?string $comment = null,
        private array $extraFields = [],
    ) {
    }

    public function getEmployeeIdentifier(): string
    {
        return $this->employeeIdentifier;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function getActivityCode(): ?string
    {
        return $this->activityCode;
    }

    public function getCostCenter(): ?string
    {
        return $this->costCenter;
    }

    public function getProjectCode(): ?string
    {
        return $this->projectCode;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getExtraFields(): array
    {
        return $this->extraFields;
    }
}
