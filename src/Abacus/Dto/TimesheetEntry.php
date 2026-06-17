<?php

namespace AbaConnect\Abacus\Dto;

final readonly class TimesheetEntry
{
    public function __construct(
        public string $employeeNumber,
        public \DateTimeInterface $periodDate,
        public string $periodNumber,
        public int $payrollType,
        public float $amount,
        public int $factor,
        public int $costCentre1,
        public ?string $textPayrollType = null,
    ) {
    }

    public function getEmployeeNumber(): string
    {
        return $this->employeeNumber;
    }

    public function getPeriodDate(): \DateTimeInterface
    {
        return $this->periodDate;
    }

    public function getPeriodNumber(): string
    {
        return $this->periodNumber;
    }

    public function getPayrollType(): int
    {
        return $this->payrollType;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getFactor(): int
    {
        return $this->factor;
    }

    public function getCostCentre1(): int
    {
        return $this->costCentre1;
    }

    public function getTextPayrollType(): ?string
    {
        return $this->textPayrollType;
    }
}
