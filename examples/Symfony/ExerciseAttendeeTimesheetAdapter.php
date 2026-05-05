<?php

declare(strict_types=1);

namespace App\Adapter;

use App\Entity\ExerciseAttendee;
use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;

final readonly class ExerciseAttendeeTimesheetAdapter implements TimesheetEntryInterface
{
    public function __construct(private ExerciseAttendee $attendee)
    {
    }

    public function getEmployeeIdentifier(): string
    {
        return (string) $this->attendee->getUser()->getAbacusId();
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->attendee->getExercise()->getStart();
    }

    public function getDuration(): float
    {
        return round($this->attendee->getDuration() / 3600, 2);
    }

    public function getActivityCode(): ?string
    {
        return $this->attendee->getExercise()->getActivityCode();
    }

    public function getCostCenter(): ?string
    {
        return $this->attendee->getExercise()->getCostCenter();
    }

    public function getProjectCode(): ?string
    {
        return null;
    }

    public function getComment(): ?string
    {
        return $this->attendee->getExercise()->getTitle();
    }

    public function getExtraFields(): array
    {
        return [];
    }
}
