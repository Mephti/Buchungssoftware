<?php

namespace App\Controllers;

class BookingController extends BaseController
{
    public function select()
    {
        $von = $this->request->getPost('von');
        $bis = $this->request->getPost('bis');
        $typ = $this->request->getPost('typ'); // liegeplatz | boot

        if (!$von || !$bis || !in_array($typ, ['liegeplatz', 'boot'], true)) {
            return redirect()->to('/#buchung');
        }

        session()->set('booking', [
            'von' => $von,
            'bis' => $bis,
            'typ' => $typ,
        ]);

        return redirect()->to('/#buchung');
    }

    public function next()
    {
        if (!session('booking')) {
            return redirect()->to('/#buchung');
        }

        return view('booking/step2', [
            'booking' => session('booking'),
        ]);
    }

    public function reset()
    {
        session()->remove('booking');
        return redirect()->to('/#buchung');
    }

    public function filter()
    {
        $onlyAvailable = $this->request->getPost('only_available') === '1';
        $q = trim((string) $this->request->getPost('q'));

        $booking = session('booking') ?? [];
        $booking['filter'] = [
            'only_available' => $onlyAvailable,
            'q' => $q,
        ];

        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

    public function toggleLiegeplatz()
    {
        $lid = (int) $this->request->getPost('lid');

        if ($lid <= 0) {
            return redirect()->to('/#buchung');
        }

        $booking = session('booking') ?? [];

        $selected = $booking['liegeplaetze'] ?? [];

        if (in_array($lid, $selected, true)) {
            // entfernen
            $selected = array_values(array_diff($selected, [$lid]));
        } else {
            // hinzufügen
            $selected[] = $lid;
        }

        $booking['liegeplaetze'] = $selected;
        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }
    public function summary()
    {
        $booking = session('booking') ?? [];

        // Nur Kunde darf buchen
        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        if (($booking['typ'] ?? null) !== 'liegeplatz') {
            return redirect()->to('/#buchung');
        }

        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;
        $selectedIds = $booking['liegeplaetze'] ?? [];

        if (!$von || !$bis || empty($selectedIds)) {
            return redirect()->to('/#buchung');
        }

        $lpModel = new \App\Models\LiegeplatzModel();
        $selectedLiegeplaetze = $lpModel->whereIn('lid', $selectedIds)
            ->orderBy('anleger', 'ASC')
            ->orderBy('nummer', 'ASC')
            ->findAll();

        return view('booking/summary', [
            'von' => $von,
            'bis' => $bis,
            'selectedLiegeplaetze' => $selectedLiegeplaetze,
        ]);
    }
    public function finish()
    {
        $booking = session('booking') ?? [];

        // Nur Kunde darf buchen
        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        if (($booking['typ'] ?? null) !== 'liegeplatz') {
            return redirect()->to('/#buchung');
        }

        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;
        $selectedIds = $booking['liegeplaetze'] ?? [];

        if (!$von || !$bis || empty($selectedIds)) {
            return redirect()->to('/#buchung');
        }

        $kid = (int) session('user_id');

        $buchungModel = new \App\Models\LiegeplatzBuchungModel();

        // 1) Kollisionen prüfen
        $bookedLids = $buchungModel->findBookedLidsForRange($von, $bis);
        $bookedSet = array_flip(array_map('intval', $bookedLids));

        foreach ($selectedIds as $lid) {
            $lid = (int)$lid;
            if (isset($bookedSet[$lid])) {
                return redirect()->to('/buchung/zusammenfassung')
                    ->with('error', 'Mindestens ein ausgewählter Liegeplatz ist im Zeitraum nicht mehr verfügbar.');
            }
        }

        // 2) Inserts (eine Buchung pro liegeplatz)
        $now = date('Y-m-d H:i:s');
        foreach ($selectedIds as $lid) {
            $buchungModel->insert([
                'lid' => (int)$lid,
                'kid' => $kid,
                'von' => $von,
                'bis' => $bis,
                'status' => 'aktiv',
                'created_at' => $now,
            ]);
        }

        // 3) Session-Buchung zurücksetzen
        session()->remove('booking');

        return redirect()->to('/meine-buchungen')
            ->with('success', 'Buchung erfolgreich angelegt.');
    }

}