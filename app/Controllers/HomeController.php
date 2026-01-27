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
        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        $liegeplatzModel = new LiegeplatzModel();
        $lpBuchungModel  = new LiegeplatzBuchungModel();

        $bootModel       = new BootModel();
        $bootBuchungModel= new BootBuchungModel();

        // Liegeplätze IMMER komplett laden (wichtig fürs Karten-Overlay A/B-Nummern)
        $liegeplaetze = $liegeplatzModel->findAllOrdered();

        $bookedLids = [];
        if ($von && $bis) $bookedLids = $lpBuchungModel->findBookedLidsForRange($von, $bis);
        $bookedSetL = array_flip(array_map('intval', $bookedLids));

        foreach ($liegeplaetze as &$lp) {
            $lid = (int)$lp['lid'];
            $isBlockedByStatus = (($lp['status'] ?? '') !== 'verfuegbar');
            $isBookedInRange = isset($bookedSetL[$lid]);

            $lp['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
            $lp['is_booked_in_range'] = $isBookedInRange;
        }
        unset($lp);

        // Boote laden (optional: Suchfilter aus Session)
        $q = (string)($booking['filter']['q'] ?? '');
        $boote = $bootModel->findFiltered($q);

        $bookedBoids = [];
        if ($von && $bis) $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
        $bookedSetB = array_flip(array_map('intval', $bookedBoids));

        foreach ($boote as &$b) {
            $boid = (int)$b['boid'];
            $isBlockedByStatus = (($b['status'] ?? '') !== 'verfuegbar');
            $isBookedInRange = isset($bookedSetB[$boid]);

            $b['is_available_in_range'] = (!$isBlockedByStatus && !$isBookedInRange);
            $b['is_booked_in_range'] = $isBookedInRange;
        }
        unset($b);

        return view('home', [
            'liegeplaetze' => $liegeplaetze,
            'boote' => $boote,
        ]);
    }
}