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

        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        $typ = $booking['typ'] ?? null;
        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        if (!$typ || !$von || !$bis) {
            return redirect()->to('/#buchung');
        }

        if ($typ === 'liegeplatz') {
            $selectedIds = $booking['liegeplaetze'] ?? [];
            if (empty($selectedIds)) return redirect()->to('/#buchung');

            $lpModel = new \App\Models\LiegeplatzModel();
            $items = $lpModel->whereIn('lid', $selectedIds)
                ->orderBy('anleger', 'ASC')
                ->orderBy('nummer', 'ASC')
                ->findAll();

            $selectedBoote = [];
            $selectedBoids = $booking['boote'] ?? [];
            if (!empty($selectedBoids)) {
                $bootModel = new \App\Models\BootModel();
                $selectedBoote = $bootModel->whereIn('boid', $selectedBoids)
                    ->orderBy('name', 'ASC')
                    ->findAll();
            }

            return view('booking/summary', [
                'typ' => 'liegeplatz',
                'von' => $von,
                'bis' => $bis,
                'items' => $items,
                'selectedBoote' => $selectedBoote,
                'error' => session()->getFlashdata('error'),
            ]);
        }

        if ($typ === 'boot') {
            $selectedIds = $booking['boote'] ?? [];
            if (empty($selectedIds)) return redirect()->to('/#buchung');

            $bootModel = new \App\Models\BootModel();
            $items = $bootModel->whereIn('boid', $selectedIds)
                ->orderBy('name', 'ASC')
                ->findAll();

            return view('booking/summary', [
                'typ' => 'boot',
                'von' => $von,
                'bis' => $bis,
                'items' => $items,
                'error' => session()->getFlashdata('error'),
            ]);
        }

        return redirect()->to('/#buchung');
    }
    public function finish()
    {
        $booking = session('booking') ?? [];

        if (!session('isLoggedIn') || session('role') !== 'kunde') {
            return redirect()->to('/login');
        }

        $typ = $booking['typ'] ?? null;
        $von = $booking['von'] ?? null;
        $bis = $booking['bis'] ?? null;

        if (!$typ || !$von || !$bis) {
            return redirect()->to('/#buchung');
        }

        $kid = (int) session('user_id');
        $now = date('Y-m-d H:i:s');

        // === Liegeplatz buchen ===
        if ($typ === 'liegeplatz') {
            $selectedIds = $booking['liegeplaetze'] ?? [];
            if (empty($selectedIds)) return redirect()->to('/#buchung');

            $buchungModel = new \App\Models\LiegeplatzBuchungModel();
            $bootBuchungModel = new \App\Models\BootBuchungModel();

            // Kollisionen prüfen
            $bookedLids = $buchungModel->findBookedLidsForRange($von, $bis);
            $bookedSet = array_flip(array_map('intval', $bookedLids));

            foreach ($selectedIds as $lid) {
                $lid = (int)$lid;
                if (isset($bookedSet[$lid])) {
                    return redirect()->to('/buchung/zusammenfassung')
                        ->with('error', 'Mindestens ein ausgewählter Liegeplatz ist im Zeitraum nicht mehr verfügbar.');
                }
            }

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

            $selectedBoids = $booking['boote'] ?? [];
            if (!empty($selectedBoids)) {
                $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
                $bookedSetBoote = array_flip(array_map('intval', $bookedBoids));

                foreach ($selectedBoids as $boid) {
                    $boid = (int)$boid;
                    if (isset($bookedSetBoote[$boid])) {
                        return redirect()->to('/buchung/zusammenfassung')
                            ->with('error', 'Mindestens ein ausgewähltes Boot ist im Zeitraum nicht mehr verfügbar.');
                    }
                }

                foreach ($selectedBoids as $boid) {
                    $bootBuchungModel->insert([
                        'boid' => (int)$boid,
                        'kid' => $kid,
                        'von' => $von,
                        'bis' => $bis,
                        'status' => 'aktiv',
                        'created_at' => $now,
                    ]);
                }
            }

            session()->remove('booking');

            return redirect()->to('/meine-buchungen')
                ->with('success', 'Liegeplatz-Buchung erfolgreich angelegt.');
        }

        // === Boot buchen ===
        if ($typ === 'boot') {
            $selectedIds = $booking['boote'] ?? [];
            if (empty($selectedIds)) return redirect()->to('/#buchung');

            $bootBuchungModel = new \App\Models\BootBuchungModel();

            // Kollisionen prüfen
            $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
            $bookedSet = array_flip(array_map('intval', $bookedBoids));

            foreach ($selectedIds as $boid) {
                $boid = (int)$boid;
                if (isset($bookedSet[$boid])) {
                    return redirect()->to('/buchung/zusammenfassung')
                        ->with('error', 'Mindestens ein ausgewähltes Boot ist im Zeitraum nicht mehr verfügbar.');
                }
            }

            foreach ($selectedIds as $boid) {
                $bootBuchungModel->insert([
                    'boid' => (int)$boid,
                    'kid' => $kid,
                    'von' => $von,
                    'bis' => $bis,
                    'status' => 'aktiv',
                    'created_at' => $now,
                ]);
            }

            session()->remove('booking');

            return redirect()->to('/meine-buchungen')
                ->with('success', 'Boot-Buchung erfolgreich angelegt.');
        }

        return redirect()->to('/#buchung');
    }

    public function toggleBoot()
    {
        $boid = (int) $this->request->getPost('boid');
        if ($boid <= 0) {
            return redirect()->to('/#buchung');
        }

        $booking = session('booking') ?? [];
        $selected = $booking['boote'] ?? [];

        if (in_array($boid, $selected, true)) {
            $selected = array_values(array_diff($selected, [$boid]));
        } else {
            $selected[] = $boid;
        }

        $booking['boote'] = $selected;
        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

    public function bootFilter()
    {
        $q = trim((string) $this->request->getPost('q'));

        $booking = session('booking') ?? [];
        $booking['boot_filter'] = [
            'q' => $q,
        ];

        session()->set('booking', $booking);

        return redirect()->to('/#buchung');
    }

}
