<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Validator;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;
use Sp4tz\AbacusXmlExporter\Model\ValidationError;

final readonly class TimesheetValidator
{
    public function __construct(private ExportConfig $config = new ExportConfig())
    {
    }

    /**
     * @param iterable<TimesheetEntryInterface> $entries
     */
    public function validate(iterable $entries): ValidationResult
    {
        $errors = [];

        foreach ($entries as $index => $entry) {
            if (!$entry instanceof TimesheetEntryInterface) {
                $errors[] = new ValidationError((int) $index, 'entry', 'The entry must implement TimesheetEntryInterface.');
                continue;
            }

            if (in_array('employee_id', $this->config->requiredFields, true) && trim($entry->getEmployeeIdentifier()) === '') {
                $errors[] = new ValidationError((int) $index, 'employee_id', 'Employee identifier is required.');
            }

            if (in_array('duration', $this->config->requiredFields, true) && $entry->getDuration() <= 0) {
                $errors[] = new ValidationError((int) $index, 'duration', 'Duration must be greater than 0.');
            }

            if ($entry->getDuration() > 24) {
                $errors[] = new ValidationError((int) $index, 'duration', 'Duration cannot be greater than 24 hours for one entry.');
            }

            if (in_array('activity_code', $this->config->requiredFields, true) && !$entry->getActivityCode()) {
                $errors[] = new ValidationError((int) $index, 'activity_code', 'Activity code is required.');
            }

            if (in_array('cost_center', $this->config->requiredFields, true) && !$entry->getCostCenter()) {
                $errors[] = new ValidationError((int) $index, 'cost_center', 'Cost center is required.');
            }

            if (in_array('project_code', $this->config->requiredFields, true) && !$entry->getProjectCode()) {
                $errors[] = new ValidationError((int) $index, 'project_code', 'Project code is required.');
            }
        }

        return new ValidationResult($errors);
    }
}
