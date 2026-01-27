<?php

namespace App\Controllers;

use App\Models\LiegeplatzBuchungModel;
use App\Models\BootBuchungModel;
use App\Models\LiegeplatzModel;
use App\Models\BootModel;

class BookingController extends BaseController
{
    public function select()
    {
        $von = (string) $this->request->getPost('von');
        $bis = (string) $this->request->getPost('bis');

        if (!$von || !$bis) {
            return redirect()->to('/#buchung');
        }

        $booking = session('booking') ?? [];

        $booking['von'] = $von;
        $booking['bis'] = $bis;

        // neue Struktur initialisieren, falls noch nicht da
        $booking['selected_lids'] = $booking['selected_lids'] ?? [];
        $booking['selected_boids'] = $booking['selected_boids'] ?? [];
        $booking['assignments'] = $booking['assignments'] ?? [];

        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

    public function reset()
    {
        session()->remove('booking');
        return redirect()->to('/#buchung');
    }

    public function toggleLiegeplatz()
    {
        $lid = (int) $this->request->getPost('lid');
        if ($lid <= 0) return redirect()->to('/#buchung');

        $booking = session('booking') ?? [];
        $selected = $booking['selected_lids'] ?? [];

        if (in_array($lid, $selected, true)) {
            $selected = array_values(array_diff($selected, [$lid]));
        } else {
            $selected[] = $lid;
        }

        $booking['selected_lids'] = $selected;
        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

    public function toggleBoot()
    {
        $boid = (int) $this->request->getPost('boid');
        if ($boid <= 0) return redirect()->to('/#buchung');

        $booking = session('booking') ?? [];
        $selected = $booking['selected_boids'] ?? [];

        if (in_array($boid, $selected, true)) {
            $selected = array_values(array_diff($selected, [$boid]));
        } else {
            $selected[] = $boid;
        }

        $booking['selected_boids'] = $selected;
        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

    /**
     * Drag&Drop: Boot -> Liegeplatz
     * POST: lid, boid, mode=attach|detach
     */
    public function assign()
    {
        $lid  = (int) $this->request->getPost('lid');
        $boid = (int) $this->request->getPost('boid');
        $mode = (string) $this->request->getPost('mode');

        if ($lid <= 0 || !in_array($mode, ['attach','detach'], true)) {
            return redirect()->to('/#buchung');
        }

        $booking = session('booking') ?? [];
        $booking['assignments'] = $booking['assignments'] ?? [];
        $booking['selected_lids'] = $booking['selected_lids'] ?? [];
        $booking['selected_boids'] = $booking['selected_boids'] ?? [];

        if ($mode === 'detach') {
            unset($booking['assignments'][(string)$lid]);
            session()->set('booking', $booking);
            return redirect()->to('/#buchung');
        }

        // attach benötigt boid
        if ($boid <= 0) return redirect()->to('/#buchung');

        // Boot darf nur einem Liegeplatz zugeordnet sein -> falls Boot schon woanders hängt: umhängen
        foreach ($booking['assignments'] as $kLid => $kBoid) {
            if ((int)$kBoid === $boid && (int)$kLid !== $lid) {
                unset($booking['assignments'][(string)$kLid]);
                break;
            }
        }

        // Liegeplatz bekommt genau 1 Boot -> überschreiben erlaubt
        $booking['assignments'][(string)$lid] = $boid;

        // sicherstellen, dass beide auch "ausgewählt" sind
        if (!in_array($lid, $booking['selected_lids'], true)) {
            $booking['selected_lids'][] = $lid;
        }
        if (!in_array($boid, $booking['selected_boids'], true)) {
            $booking['selected_boids'][] = $boid;
        }

        session()->set('booking', $booking);
        return redirect()->to('/#buchung');
    }

    public function summary()
    {
        $booking = session('booking') ?? [];

        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        if (!$von || !$bis) return redirect()->to('/#buchung');

        $selectedLids = array_map('intval', $booking['selected_lids'] ?? []);
        $selectedBoids = array_map('intval', $booking['selected_boids'] ?? []);
        $assignments = $booking['assignments'] ?? [];

        // Union mit assignments (falls mal inkonsistent)
        foreach ($assignments as $lidStr => $boidVal) {
            $lid = (int)$lidStr;
            $boid = (int)$boidVal;
            if ($lid > 0 && !in_array($lid, $selectedLids, true)) $selectedLids[] = $lid;
            if ($boid > 0 && !in_array($boid, $selectedBoids, true)) $selectedBoids[] = $boid;
        }

        $lpItems = [];
        $bootItems = [];

        if (!empty($selectedLids)) {
            $lpModel = new LiegeplatzModel();
            $lpItems = $lpModel->whereIn('lid', $selectedLids)
                ->orderBy('anleger', 'ASC')->orderBy('nummer', 'ASC')
                ->findAll();
        }

        if (!empty($selectedBoids)) {
            $bootModel = new BootModel();
            $bootItems = $bootModel->whereIn('boid', $selectedBoids)
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        return view('booking/summary', [
            'von' => $von,
            'bis' => $bis,
            'liegeplaetze' => $lpItems,
            'boote' => $bootItems,
            'assignments' => $assignments,
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function finish()
    {
        $booking = session('booking') ?? [];

        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        if (!$von || !$bis) return redirect()->to('/#buchung');

        $selectedLids = array_map('intval', $booking['selected_lids'] ?? []);
        $selectedBoids = array_map('intval', $booking['selected_boids'] ?? []);
        $assignments = $booking['assignments'] ?? [];

        foreach ($assignments as $lidStr => $boidVal) {
            $lid = (int)$lidStr;
            $boid = (int)$boidVal;
            if ($lid > 0 && !in_array($lid, $selectedLids, true)) $selectedLids[] = $lid;
            if ($boid > 0 && !in_array($boid, $selectedBoids, true)) $selectedBoids[] = $boid;
        }

        if (empty($selectedLids) && empty($selectedBoids)) {
            return redirect()->to('/#buchung');
        }

        $kid = (int) session('user_id');
        $now = date('Y-m-d H:i:s');

        // Kollisionen prüfen (aktiv blockt)
        $lpBuchungModel = new LiegeplatzBuchungModel();
        $bootBuchungModel = new BootBuchungModel();

        $bookedLids = $lpBuchungModel->findBookedLidsForRange($von, $bis);
        $bookedSetL = array_flip(array_map('intval', $bookedLids));
        foreach ($selectedLids as $lid) {
            if (isset($bookedSetL[(int)$lid])) {
                return redirect()->to('/buchung/zusammenfassung')
                    ->with('error', 'Mindestens ein ausgewählter Liegeplatz ist im Zeitraum nicht verfügbar.');
            }
        }

        $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
        $bookedSetB = array_flip(array_map('intval', $bookedBoids));
        foreach ($selectedBoids as $boid) {
            if (isset($bookedSetB[(int)$boid])) {
                return redirect()->to('/buchung/zusammenfassung')
                    ->with('error', 'Mindestens ein ausgewähltes Boot ist im Zeitraum nicht verfügbar.');
            }
        }

        // Status prüfen (verfuegbar)
        $lpModel = new LiegeplatzModel();
        if (!empty($selectedLids)) {
            $rows = $lpModel->select('lid,status')->whereIn('lid', $selectedLids)->findAll();
            foreach ($rows as $r) {
                if (($r['status'] ?? '') !== 'verfuegbar') {
                    return redirect()->to('/buchung/zusammenfassung')
                        ->with('error', 'Mindestens ein ausgewählter Liegeplatz ist gesperrt/vermietet/belegt.');
                }
            }
        }

        $bootModel = new BootModel();
        if (!empty($selectedBoids)) {
            $rows = $bootModel->select('boid,status')->whereIn('boid', $selectedBoids)->findAll();
            foreach ($rows as $r) {
                if (($r['status'] ?? '') !== 'verfuegbar') {
                    return redirect()->to('/buchung/zusammenfassung')
                        ->with('error', 'Mindestens ein ausgewähltes Boot ist nicht verfügbar (Status).');
                }
            }
        }

        // Speichern: group_token pro "Paket" (Single oder Paar)
        // Paare: assignments (lid=>boid) bekommen gemeinsamen group_token
        // Singles: jeder einzelne bekommt eigenen group_token
        $lpInserts = [];
        $bootInserts = [];

        $pairedLids = [];
        $pairedBoids = [];

        foreach ($assignments as $lidStr => $boidVal) {
            $lid = (int)$lidStr;
            $boid = (int)$boidVal;
            if ($lid <= 0 || $boid <= 0) continue;

            $token = $this->uuidv4();

            $lpInserts[] = [
                'lid' => $lid, 'kid' => $kid, 'von' => $von, 'bis' => $bis,
                'status' => 'aktiv', 'created_at' => $now, 'group_token' => $token,
            ];
            $bootInserts[] = [
                'boid' => $boid, 'kid' => $kid, 'von' => $von, 'bis' => $bis,
                'status' => 'aktiv', 'created_at' => $now, 'group_token' => $token,
            ];

            $pairedLids[$lid] = true;
            $pairedBoids[$boid] = true;
        }

        // Singles: Lids, die nicht gepaart sind
        foreach ($selectedLids as $lid) {
            $lid = (int)$lid;
            if (isset($pairedLids[$lid])) continue;

            $lpInserts[] = [
                'lid' => $lid, 'kid' => $kid, 'von' => $von, 'bis' => $bis,
                'status' => 'aktiv', 'created_at' => $now, 'group_token' => $this->uuidv4(),
            ];
        }

        // Singles: Boids, die nicht gepaart sind
        foreach ($selectedBoids as $boid) {
            $boid = (int)$boid;
            if (isset($pairedBoids[$boid])) continue;

            $bootInserts[] = [
                'boid' => $boid, 'kid' => $kid, 'von' => $von, 'bis' => $bis,
                'status' => 'aktiv', 'created_at' => $now, 'group_token' => $this->uuidv4(),
            ];
        }

        // Insert Batch / Insert
        foreach ($lpInserts as $row) $lpBuchungModel->insert($row);
        foreach ($bootInserts as $row) $bootBuchungModel->insert($row);

        session()->remove('booking');

        return redirect()->to('/meine-buchungen')->with('success', 'Buchung erfolgreich angelegt.');
    }

    private function uuidv4(): string
    {
        // einfache UUID v4 ohne externe Lib
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}