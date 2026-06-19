<?php

namespace AbaConnect\Abacus\Dto;

final readonly class TimesheetEntry
{
    public function __construct(
        public int $employeeNumber, // mandatory yes
        public \DateTimeInterface $periodDate, // mandatory yes
        public int $periodNumber, // mandatory yes
        public int $payrollType, // mandatory yes
        public ?float $amount = null, // max 10 characters, decimal 6, a . (dot) is required as decimal separator, mandatory no
        public ?float $factor = null, // max 10 characters, decimal 6, a . (dot) is required as decimal separator, mandatory no
        public ?int $costCentre1 = null, // max 12 characters, mandatory no
        public ?string $textPayrollType = null, // max 100 characters, mandatory no
    ) {
    }

    public function getEmployeeNumber(): int
    {
        return $this->employeeNumber;
    }

    public function getPeriodDate(): \DateTimeInterface
    {
        return $this->periodDate;
    }

    public function getPeriodNumber(): int
    {
        return $this->periodNumber;
    }

    public function getPayrollType(): int
    {
        return $this->payrollType;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getFactor(): ?float
    {
        return $this->factor;
    }

    public function getCostCentre1(): ?int
    {
        return $this->costCentre1;
    }

    public function getTextPayrollType(): ?string
    {
        return $this->textPayrollType;
    }
}
