<?php

namespace App\Controllers;

use App\Models\LiegeplatzModel;
use App\Models\LiegeplatzBuchungModel;
use App\Models\BootModel;
use App\Models\BootBuchungModel;

class HomeController extends BaseController
{
    public function index()
    {
        $booking = session('booking') ?? [];
        $typ = $booking['typ'] ?? null;

        // Models
        $liegeplatzModel = new LiegeplatzModel();
        $lpBuchungModel  = new LiegeplatzBuchungModel();
        $bootModel       = new BootModel();
        $bootBuchungModel= new BootBuchungModel();

        // Zeitraum (für beide Typen relevant)
        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        // === LIEGEPLÄTZE ===
        $liegeplaetze = [];
        $selectedLiegeplaetze = [];
        if ($typ === 'liegeplatz') {
            // Rechte Box (selected) unabhängig von Filter laden
            $selectedIds = $booking['liegeplaetze'] ?? [];
            if (!empty($selectedIds)) {
                $selectedLiegeplaetze = $liegeplatzModel
                    ->whereIn('lid', $selectedIds)
                    ->orderBy('anleger', 'ASC')
                    ->orderBy('nummer', 'ASC')
                    ->findAll();
            }

            // Linke Liste
            $onlyAvailable = (bool)($booking['filter']['only_available'] ?? false);
            $q = (string)($booking['filter']['q'] ?? '');

            $liegeplaetze = $liegeplatzModel->findFiltered(false, $q);

            $bookedLids = [];
            if ($von && $bis) {
                $bookedLids = $lpBuchungModel->findBookedLidsForRange($von, $bis);
            }
            $bookedSet = array_flip(array_map('intval', $bookedLids));

            foreach ($liegeplaetze as &$lp) {
                $lid = (int)$lp['lid'];

                $isBlockedByStatus = ($lp['status'] !== 'verfuegbar');
                $isBookedInRange   = isset($bookedSet[$lid]);

                $lp['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
                $lp['is_booked_in_range']    = $isBookedInRange;
            }
            unset($lp);

            if ($onlyAvailable) {
                $liegeplaetze = array_values(array_filter($liegeplaetze, fn($lp) => !empty($lp['is_available_in_range'])));
            }
        }

        // === BOOTE ===
        $boote = [];
        $selectedBoote = [];
        if ($typ === 'boot') {
            // Rechte Box: selected boote laden
            $selectedBoids = $booking['boote'] ?? [];
            if (!empty($selectedBoids)) {
                $selectedBoote = $bootModel
                    ->whereIn('boid', $selectedBoids)
                    ->orderBy('name', 'ASC')
                    ->findAll();
            }

            // Linke Liste: Filter (nur Suche)
            $q = (string)($booking['boot_filter']['q'] ?? '');
            $boote = $bootModel->findFiltered($q);

            // Zeitraum-Verfügbarkeit: aktive Bootbuchungen + Bootstatus
            $bookedBoids = [];
            if ($von && $bis) {
                $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
            }
            $bookedSet = array_flip(array_map('intval', $bookedBoids));

            foreach ($boote as &$b) {
                $boid = (int)$b['boid'];

                // Bootstatus blockt generell (nicht buchbar)
                $isBlockedByStatus = ($b['status'] !== 'verfuegbar');
                $isBookedInRange   = isset($bookedSet[$boid]);

                $b['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
                $b['is_booked_in_range']    = $isBookedInRange;
            }
            unset($b);
        }

        return view('home', [
            'liegeplaetze' => $liegeplaetze,
            'selectedLiegeplaetze' => $selectedLiegeplaetze,
            'boote' => $boote,
            'selectedBoote' => $selectedBoote,
        ]);
    }
}