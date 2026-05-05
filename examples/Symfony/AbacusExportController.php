<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Adapter\ExerciseAttendeeTimesheetAdapter;
use App\Repository\ExerciseAttendeeRepository;
use Sp4tz\AbacusXmlExporter\Exporter\AbacusTimesheetExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AbacusExportController extends AbstractController
{
    #[Route('/admin/abacus/export', name: 'admin_abacus_export', methods: ['GET'])]
    public function __invoke(
        ExerciseAttendeeRepository $repository,
        AbacusTimesheetExporter $exporter,
    ): Response {
        $attendees = $repository->findValidatedForExport();

        $entries = array_map(
            static fn ($attendee): ExerciseAttendeeTimesheetAdapter => new ExerciseAttendeeTimesheetAdapter($attendee),
            $attendees,
        );

        $result = $exporter->export($entries);

        if (!$result->isSuccess()) {
            return $this->json([
                'success' => false,
                'errors' => $result->getValidationResult()->toArray(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response(
            $result->getXml(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/xml; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="abacus-export.xml"',
            ],
        );
    }
}
