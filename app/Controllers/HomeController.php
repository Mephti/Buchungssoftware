<?php

namespace App\Controllers;

use App\Models\LiegeplatzModel;
use App\Models\LiegeplatzBuchungModel;

class HomeController extends BaseController
{
    public function index()
    {
        $booking = session('booking') ?? [];

        $liegeplatzModel = new LiegeplatzModel();
        $buchungModel    = new LiegeplatzBuchungModel();

        $liegeplaetze = [];
        $selectedLiegeplaetze = [];

        $typ = $booking['typ'] ?? null;

        // Ausgewählte Plätze rechts (immer unabhängig von Filter laden)
        $selectedIds = $booking['liegeplaetze'] ?? [];
        if (!empty($selectedIds)) {
            $selectedLiegeplaetze = $liegeplatzModel
                ->whereIn('lid', $selectedIds)
                ->orderBy('anleger', 'ASC')
                ->orderBy('nummer', 'ASC')
                ->findAll();
        }

        // Linke Liste nur für Liegeplatz-Modus
        if ($typ === 'liegeplatz') {
            $onlyAvailable = (bool)($booking['filter']['only_available'] ?? false);
            $q = (string)($booking['filter']['q'] ?? '');

            $von = $booking['von'] ?? null;
            $bis = $booking['bis'] ?? null;

            // Basisliste (nach Anleger/Nummer/Filter)
            $liegeplaetze = $liegeplatzModel->findFiltered(false, $q); // nur Suche, NICHT status-filter

            // Wenn Zeitraum gesetzt ist: booked lids bestimmen und Flag setzen
            $bookedLids = [];
            if ($von && $bis) {
                $bookedLids = $buchungModel->findBookedLidsForRange($von, $bis);
            }

            // Verfügbarkeit je Platz berechnen
            $bookedSet = array_flip(array_map('intval', $bookedLids));
            foreach ($liegeplaetze as &$lp) {
                $lid = (int)$lp['lid'];

                $isBlockedByStatus = ($lp['status'] !== 'verfuegbar'); // gesperrt/vermietet/belegt => generell nicht wählbar
                $isBookedInRange   = isset($bookedSet[$lid]);          // im Zeitraum gebucht

                $lp['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
                $lp['is_booked_in_range']    = $isBookedInRange;
            }
            unset($lp);

            // Wenn "nur verfügbare" aktiv: nach Zeitraum-Verfügbarkeit filtern
            if ($onlyAvailable) {
                $liegeplaetze = array_values(array_filter($liegeplaetze, fn($lp) => !empty($lp['is_available_in_range'])));
            }
        }

        return view('home', [
            'liegeplaetze' => $liegeplaetze,
            'selectedLiegeplaetze' => $selectedLiegeplaetze,
        ]);
    }
}
