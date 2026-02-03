<?php

namespace App\Controllers;

class MitarbeiterController extends BaseController
{
    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd.m.Y', 'd/m/Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $dt = \DateTimeImmutable::createFromFormat($format, $value);
            if ($dt && $dt->format($format) === $value) {
                return $dt;
            }
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function calculateDays(?string $von, ?string $bis): int
    {
        $start = $this->parseDate($von);
        $end = $this->parseDate($bis);
        if (!$start || !$end || $end < $start) {
            return 0;
        }

        return $start->diff($end)->days + 1;
    }

    private function requireLogin()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $onlyActive = $this->request->getGet('only_active') === '1';
        $db = \Config\Database::connect();

        $lpQuery = $db->table('liegeplatz_buchungen lb')
            ->select('lb.bid, lb.von, lb.bis, lb.status, lb.kosten, lp.anleger, lp.nummer, k.kid, k.vorname, k.nachname, k.email')
            ->join('liegeplaetze lp', 'lp.lid = lb.lid')
            ->join('kunden k', 'k.kid = lb.kid')
            ->orderBy('lb.von', 'DESC');

        if ($onlyActive) {
            $lpQuery->where('lb.status', 'aktiv');
        }
        $liegeplatzBuchungen = $lpQuery->get()->getResultArray();

        $bootQuery = $db->table('boot_buchungen bb')
            ->select('bb.bbid, bb.von, bb.bis, bb.status, bb.kosten, b.name, b.typ, b.plaetze, k.kid, k.vorname, k.nachname, k.email')
            ->join('boote b', 'b.boid = bb.boid')
            ->join('kunden k', 'k.kid = bb.kid')
            ->orderBy('bb.von', 'DESC');

        if ($onlyActive) {
            $bootQuery->where('bb.status', 'aktiv');
        }
        $bootBuchungen = $bootQuery->get()->getResultArray();

        $liegeplaetze = $db->table('liegeplaetze')
            ->select('lid, anleger, nummer, status')
            ->orderBy('anleger', 'ASC')
            ->orderBy('nummer', 'ASC')
            ->get()
            ->getResultArray();

        $boote = $db->table('boote')
            ->select('boid, name, typ, plaetze, status')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('mitarbeiter/index', [
            'liegeplatzBuchungen' => $liegeplatzBuchungen,
            'bootBuchungen' => $bootBuchungen,
            'liegeplaetze' => $liegeplaetze,
            'boote' => $boote,
            'onlyActive' => $onlyActive,
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function bookingForm()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $von = (string)($this->request->getGet('von') ?? '');
        $bis = (string)($this->request->getGet('bis') ?? '');
        $typ = (string)($this->request->getGet('typ') ?? 'liegeplatz');

        $db = \Config\Database::connect();
        $kunden = $db->table('kunden')
            ->select('kid, vorname, nachname, email')
            ->orderBy('nachname', 'ASC')
            ->orderBy('vorname', 'ASC')
            ->get()
            ->getResultArray();

        $liegeplaetze = [];
        $boote = [];
        $tage = $this->calculateDays($von, $bis);

        if ($von !== '' && $bis !== '') {
            if ($typ === 'liegeplatz') {
                $lpModel = new \App\Models\LiegeplatzModel();
                $lpBuchungModel = new \App\Models\LiegeplatzBuchungModel();

                $liegeplaetze = $lpModel->findAllOrdered();
                $bookedLids = $lpBuchungModel->findBookedLidsForRange($von, $bis);
                $bookedSet = array_flip(array_map('intval', $bookedLids));

                foreach ($liegeplaetze as &$lp) {
                    $lid = (int)$lp['lid'];
                    $status = (string)($lp['status'] ?? '');
                    $isBlocked = ($status !== 'verfuegbar');
                    $isBooked = isset($bookedSet[$lid]);
                    $lp['is_available_in_range'] = (!$isBlocked && !$isBooked);
                    $lp['is_booked_in_range'] = $isBooked;
                }
                unset($lp);
            }

            if ($typ === 'boot') {
                $bootModel = new \App\Models\BootModel();
                $bootBuchungModel = new \App\Models\BootBuchungModel();

                $boote = $bootModel->findFiltered('');
                $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
                $bookedSet = array_flip(array_map('intval', $bookedBoids));

                foreach ($boote as &$b) {
                    $boid = (int)$b['boid'];
                    $status = (string)($b['status'] ?? '');
                    $isBlocked = ($status !== 'verfuegbar');
                    $isBooked = isset($bookedSet[$boid]);
                    $b['is_available_in_range'] = (!$isBlocked && !$isBooked);
                    $b['is_booked_in_range'] = $isBooked;
                }
                unset($b);
            }
        }

        return view('mitarbeiter/booking', [
            'kunden' => $kunden,
            'von' => $von,
            'bis' => $bis,
            'typ' => $typ,
            'tage' => $tage,
            'liegeplaetze' => $liegeplaetze,
            'boote' => $boote,
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
            'old' => session()->getFlashdata('old') ?? [],
        ]);
    }

    public function createBooking()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $von = trim((string)$this->request->getPost('von'));
        $bis = trim((string)$this->request->getPost('bis'));
        $typ = (string)$this->request->getPost('typ');
        $tage = $this->calculateDays($von, $bis);

        if ($von === '' || $bis === '' || !in_array($typ, ['liegeplatz', 'boot'], true) || $tage <= 0) {
            return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte Zeitraum und Typ korrekt wählen.');
        }

        $newCustomer = $this->request->getPost('new_customer') === '1';
        $db = \Config\Database::connect();
        $kid = 0;

        if ($newCustomer) {
            $data = [
                'vorname' => trim((string)$this->request->getPost('vorname')),
                'nachname' => trim((string)$this->request->getPost('nachname')),
                'geburtsdatum' => (string)$this->request->getPost('geburtsdatum'),
                'geschlecht' => (string)$this->request->getPost('geschlecht'),
                'strasse' => trim((string)$this->request->getPost('strasse')),
                'hausnr' => trim((string)$this->request->getPost('hausnr')),
                'plz' => trim((string)$this->request->getPost('plz')),
                'ort' => trim((string)$this->request->getPost('ort')),
                'telefon' => trim((string)$this->request->getPost('telefon')),
                'email' => strtolower(trim((string)$this->request->getPost('email'))),
            ];
            $pass1 = (string)$this->request->getPost('passwort');
            $pass2 = (string)$this->request->getPost('passwort2');

            if (
                $data['vorname'] === '' || $data['nachname'] === '' || $data['email'] === ''
                || $data['geburtsdatum'] === '' || $data['strasse'] === '' || $data['hausnr'] === ''
                || $data['plz'] === '' || $data['ort'] === '' || $pass1 === ''
            ) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte alle Pflichtfelder des Kunden ausfüllen.')->with('old', $data);
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte eine gültige E-Mail eingeben.')->with('old', $data);
            }

            if (!in_array($data['geschlecht'], ['m', 'w', 'd'], true)) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte ein gültiges Geschlecht wählen.')->with('old', $data);
            }

            if ($pass1 !== $pass2) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Die Passwörter stimmen nicht überein.')->with('old', $data);
            }

            if (strlen($pass1) < 8) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Passwort muss mindestens 8 Zeichen haben.')->with('old', $data);
            }

            $exists = $db->table('kunden')->where('email', $data['email'])->get()->getRowArray();
            if ($exists) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Diese E-Mail ist bereits registriert.')->with('old', $data);
            }

            $data['passwort'] = password_hash($pass1, PASSWORD_DEFAULT);
            $db->table('kunden')->insert($data);
            $kid = (int)$db->insertID();
        } else {
            $kid = (int)$this->request->getPost('kid');
            if ($kid <= 0) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte einen Kunden auswählen.');
            }
            $exists = $db->table('kunden')->where('kid', $kid)->get()->getRowArray();
            if (!$exists) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Kunde nicht gefunden.');
            }
        }

        $now = date('Y-m-d H:i:s');

        if ($typ === 'liegeplatz') {
            $selected = $this->request->getPost('liegeplaetze') ?? [];
            if (empty($selected) || !is_array($selected)) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte mindestens einen Liegeplatz auswählen.');
            }
            $selectedIds = array_map('intval', $selected);
            $buchungModel = new \App\Models\LiegeplatzBuchungModel();
            $bookedLids = $buchungModel->findBookedLidsForRange($von, $bis);
            $bookedSet = array_flip(array_map('intval', $bookedLids));

            $lpModel = new \App\Models\LiegeplatzModel();
            $lpRows = $lpModel->select('lid, status, kosten_pt')->whereIn('lid', $selectedIds)->findAll();
            $lpById = [];
            foreach ($lpRows as $row) {
                $lpById[(int)$row['lid']] = $row;
            }

            foreach ($selectedIds as $lid) {
                $row = $lpById[(int)$lid] ?? null;
                if (!$row) {
                    return redirect()->to('/mitarbeiter/buchung')->with('error', 'Ungültiger Liegeplatz ausgewählt.');
                }
                if ((string)($row['status'] ?? '') !== 'verfuegbar' || isset($bookedSet[(int)$lid])) {
                    return redirect()->to('/mitarbeiter/buchung')->with('error', 'Mindestens ein Liegeplatz ist im Zeitraum nicht verfügbar.');
                }
            }

            foreach ($selectedIds as $lid) {
                $row = $lpById[(int)$lid];
                $kostenPt = (int)($row['kosten_pt'] ?? 0);
                $buchungModel->insert([
                    'lid' => (int)$lid,
                    'kid' => $kid,
                    'von' => $von,
                    'bis' => $bis,
                    'status' => 'aktiv',
                    'created_at' => $now,
                    'kosten' => $kostenPt * $tage,
                ]);
            }

            return redirect()->to('/mitarbeiter')->with('success', 'Liegeplatz-Buchung angelegt.');
        }

        if ($typ === 'boot') {
            $selected = $this->request->getPost('boote') ?? [];
            if (empty($selected) || !is_array($selected)) {
                return redirect()->to('/mitarbeiter/buchung')->with('error', 'Bitte mindestens ein Boot auswählen.');
            }
            $selectedIds = array_map('intval', $selected);
            $bootBuchungModel = new \App\Models\BootBuchungModel();
            $bookedBoids = $bootBuchungModel->findBookedBoidsForRange($von, $bis);
            $bookedSet = array_flip(array_map('intval', $bookedBoids));

            $bootModel = new \App\Models\BootModel();
            $bootRows = $bootModel->select('boid, status, kosten_pt')->whereIn('boid', $selectedIds)->findAll();
            $bootById = [];
            foreach ($bootRows as $row) {
                $bootById[(int)$row['boid']] = $row;
            }

            foreach ($selectedIds as $boid) {
                $row = $bootById[(int)$boid] ?? null;
                if (!$row) {
                    return redirect()->to('/mitarbeiter/buchung')->with('error', 'Ungültiges Boot ausgewählt.');
                }
                if ((string)($row['status'] ?? '') !== 'verfuegbar' || isset($bookedSet[(int)$boid])) {
                    return redirect()->to('/mitarbeiter/buchung')->with('error', 'Mindestens ein Boot ist im Zeitraum nicht verfügbar.');
                }
            }

            foreach ($selectedIds as $boid) {
                $row = $bootById[(int)$boid];
                $kostenPt = (int)($row['kosten_pt'] ?? 0);
                $bootBuchungModel->insert([
                    'boid' => (int)$boid,
                    'kid' => $kid,
                    'von' => $von,
                    'bis' => $bis,
                    'status' => 'aktiv',
                    'created_at' => $now,
                    'kosten' => $kostenPt * $tage,
                ]);
            }

            return redirect()->to('/mitarbeiter')->with('success', 'Boot-Buchung angelegt.');
        }

        return redirect()->to('/mitarbeiter/buchung')->with('error', 'Unbekannter Buchungstyp.');
    }

    public function cancelBooking()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $type = $this->request->getPost('type'); // 'liegeplatz' | 'boot'
        $id   = (int) $this->request->getPost('id'); // bid | bbid

        if (!in_array($type, ['liegeplatz', 'boot'], true) || $id <= 0) {
            return redirect()->to('/mitarbeiter')->with('error', 'Ungültige Anfrage.');
        }

        $db = \Config\Database::connect();

        if ($type === 'liegeplatz') {
            $row = $db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->get()
                ->getRowArray();

            if (!$row) {
                return redirect()->to('/mitarbeiter')->with('error', 'Buchung nicht gefunden.');
            }

            if (($row['status'] ?? '') === 'storniert') {
                return redirect()->to('/mitarbeiter')->with('success', 'Buchung war bereits storniert.');
            }

            $db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->update(['status' => 'storniert']);

            return redirect()->to('/mitarbeiter')->with('success', 'Liegeplatz-Buchung storniert.');
        }

        $row = $db->table('boot_buchungen')
            ->where('bbid', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return redirect()->to('/mitarbeiter')->with('error', 'Buchung nicht gefunden.');
        }

        if (($row['status'] ?? '') === 'storniert') {
            return redirect()->to('/mitarbeiter')->with('success', 'Buchung war bereits storniert.');
        }

        $db->table('boot_buchungen')
            ->where('bbid', $id)
            ->update(['status' => 'storniert']);

        return redirect()->to('/mitarbeiter')->with('success', 'Boot-Buchung storniert.');
    }

    public function updateStatus()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $type = $this->request->getPost('type'); // 'liegeplatz' | 'boot'
        $id = (int)$this->request->getPost('id'); // lid | boid
        $status = (string)$this->request->getPost('status');

        $allowed = [
            'liegeplatz' => ['verfuegbar', 'gesperrt', 'vermietet', 'belegt'],
            'boot' => ['verfuegbar', 'gesperrt', 'wartung', 'unterwegs'],
        ];

        if (!isset($allowed[$type]) || $id <= 0 || !in_array($status, $allowed[$type], true)) {
            return redirect()->to('/mitarbeiter')->with('error', 'Ungültige Status-Änderung.');
        }

        $db = \Config\Database::connect();

        if ($type === 'liegeplatz') {
            $db->table('liegeplaetze')
                ->where('lid', $id)
                ->update(['status' => $status]);
            return redirect()->to('/mitarbeiter')->with('success', 'Liegeplatz-Status aktualisiert.');
        }

        $db->table('boote')
            ->where('boid', $id)
            ->update(['status' => $status]);

        return redirect()->to('/mitarbeiter')->with('success', 'Boot-Status aktualisiert.');
    }

    public function createBoat()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'mitarbeiter') {
            return redirect()->to('/');
        }

        $name = trim((string)$this->request->getPost('name'));
        $typ = trim((string)$this->request->getPost('typ'));
        $plaetze = (int)$this->request->getPost('plaetze');
        $status = (string)$this->request->getPost('status');
        $kostenPt = $this->request->getPost('kosten_pt');

        $allowedStatus = ['verfuegbar', 'gesperrt', 'wartung', 'unterwegs'];

        if ($name === '' || $plaetze <= 0 || !in_array($status, $allowedStatus, true)) {
            return redirect()->to('/mitarbeiter')->with('error', 'Bitte gültige Boot-Daten angeben.');
        }

        $kostenValue = null;
        if ($kostenPt !== null && $kostenPt !== '') {
            $kostenValue = (int)$kostenPt;
            if ($kostenValue < 0) {
                return redirect()->to('/mitarbeiter')->with('error', 'Kosten/Tag müssen positiv sein.');
            }
        }

        $db = \Config\Database::connect();
        $db->table('boote')->insert([
            'name' => $name,
            'typ' => $typ !== '' ? $typ : null,
            'plaetze' => $plaetze,
            'status' => $status,
            'kosten_pt' => $kostenValue,
        ]);

        return redirect()->to('/mitarbeiter')->with('success', 'Boot angelegt.');
    }
}
