<?php

namespace App\Controllers;

use App\Models\LiegeplatzModel;
use App\Models\LiegeplatzBuchungModel;
use App\Models\BootModel;
use App\Models\BootBuchungModel;

class HomeController extends BaseController
{
    private function calculateDays(?string $von, ?string $bis): int
    {
        $von = trim((string)$von);
        $bis = trim((string)$bis);
        if ($von === '' || $bis === '') {
            return 0;
        }

        $start = \DateTimeImmutable::createFromFormat('Y-m-d', $von);
        $end = \DateTimeImmutable::createFromFormat('Y-m-d', $bis);
        if ($start && $end && $start->format('Y-m-d') === $von && $end->format('Y-m-d') === $bis) {
            if ($end < $start) {
                return 0;
            }
            return $start->diff($end)->days + 1;
        }

        $startTs = strtotime($von);
        $endTs = strtotime($bis);
        if ($startTs === false || $endTs === false || $endTs < $startTs) {
            return 0;
        }
        return (int)floor(($endTs - $startTs) / 86400) + 1;
    }

    public function index()
    {
        $booking = session('booking') ?? [];

        // Models
        $liegeplatzModel   = new LiegeplatzModel();
        $lpBuchungModel    = new LiegeplatzBuchungModel();
        $bootModel         = new BootModel();
        $bootBuchungModel  = new BootBuchungModel();

        // Zeitraum
        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        // === SELECTED (rechte Box) ===
        $selectedLiegeplaetze = [];
        $selectedBoote = [];

        $selectedLids = $booking['liegeplaetze'] ?? [];
        if (!empty($selectedLids)) {
            $selectedLiegeplaetze = $liegeplatzModel
                ->whereIn('lid', $selectedLids)
                ->orderBy('anleger', 'ASC')
                ->orderBy('nummer', 'ASC')
                ->findAll();
        }

        $selectedBoids = $booking['boote'] ?? [];
        if (!empty($selectedBoids)) {
            $selectedBoote = $bootModel
                ->whereIn('boid', $selectedBoids)
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        $daysCount = $this->calculateDays($von, $bis);

        $kostenLiegeplatzTotal = 0;
        foreach ($selectedLiegeplaetze as &$lp) {
            $kostenPt = (int)($lp['kosten_pt'] ?? 0);
            $lp['kosten_pt'] = $kostenPt;
            $lp['kosten_total'] = $kostenPt * $daysCount;
            $kostenLiegeplatzTotal += $lp['kosten_total'];
        }
        unset($lp);

        $kostenBootTotal = 0;
        foreach ($selectedBoote as &$b) {
            $kostenPt = (int)($b['kosten_pt'] ?? 0);
            $b['kosten_pt'] = $kostenPt;
            $b['kosten_total'] = $kostenPt * $daysCount;
            $kostenBootTotal += $b['kosten_total'];
        }
        unset($b);

        // === LIEGEPLÄTZE (linke Seite: Map) ===
        $onlyAvailable = (bool)($booking['filter']['only_available'] ?? false);
        $qLp = (string)($booking['filter']['q'] ?? '');

        $liegeplaetze = $liegeplatzModel->findFiltered(false, $qLp);

        $bookedLids = [];
        if ($von && $bis) {
            $bookedLids = $lpBuchungModel->findBookedLidsForRange($von, $bis);
        }
        $bookedSetLp = array_flip(array_map('intval', $bookedLids));

        foreach ($liegeplaetze as &$lp) {
            $lid = (int)$lp['lid'];

            $isBlockedByStatus = (($lp['status'] ?? '') !== 'verfuegbar');
            $isBookedInRange   = isset($bookedSetLp[$lid]);

            $lp['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
            $lp['is_booked_in_range']    = $isBookedInRange;
        }
        unset($lp);

        if ($onlyAvailable) {
            $liegeplaetze = array_values(array_filter(
                $liegeplaetze,
                fn($lp) => !empty($lp['is_available_in_range'])
            ));
        }

        // === BOOTE (rechte Seite: draggable Liste) ===
        $qBoot = (string)($booking['boot_filter']['q'] ?? '');
        $boote = $bootModel->findFiltered($qBoot);

        $bookedBoids = [];
        if ($von && $bis) {
            $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
        }
        $bookedSetBoot = array_flip(array_map('intval', $bookedBoids));

        foreach ($boote as &$b) {
            $boid = (int)$b['boid'];

            $status = trim((string)($lp['status'] ?? ''));
            $isBlockedByStatus = ($status !== '' && $status !== 'verfuegbar');
            $isBookedInRange   = isset($bookedSetBoot[$boid]);

            $b['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
            $b['is_booked_in_range']    = $isBookedInRange;
        }
        unset($b);

        return view('home', [
            'liegeplaetze' => $liegeplaetze,
            'selectedLiegeplaetze' => $selectedLiegeplaetze,
            'boote' => $boote,
            'selectedBoote' => $selectedBoote,
            'daysCount' => $daysCount,
            'kostenLiegeplatzTotal' => $kostenLiegeplatzTotal,
            'kostenBootTotal' => $kostenBootTotal,
        ]);
    }
}
