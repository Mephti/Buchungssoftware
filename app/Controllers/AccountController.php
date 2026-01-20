<?php

namespace App\Controllers;

class AccountController extends BaseController
{
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

        return view('account/index');
    }

    public function bookings()
    {
        if ($redir = $this->requireLogin()) return $redir;

        // Optional: wenn du sicherstellen willst, dass nur Kunden rein dürfen
        if (session('role') !== 'kunde') {
            return redirect()->to('/');
        }

        $kid = (int) session('user_id');

        $db = \Config\Database::connect();

        $liegeplatzBuchungen = $db->table('liegeplatz_buchungen lb')
            ->select('lb.bid, lb.von, lb.bis, lb.status, lp.anleger, lp.nummer')
            ->join('liegeplaetze lp', 'lp.lid = lb.lid')
            ->where('lb.kid', $kid)
            ->orderBy('lb.von', 'DESC')
            ->get()
            ->getResultArray();

        $bootBuchungen = $db->table('boot_buchungen bb')
            ->select('bb.bbid, bb.von, bb.bis, bb.status, b.name, b.typ, b.plaetze')
            ->join('boote b', 'b.boid = bb.boid')
            ->where('bb.kid', $kid)
            ->orderBy('bb.von', 'DESC')
            ->get()
            ->getResultArray();

        return view('account/bookings', [
            'liegeplatzBuchungen' => $liegeplatzBuchungen,
            'bootBuchungen'       => $bootBuchungen,
        ]);
    }
    public function cancelBooking()
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'kunde') {
            return redirect()->to('/');
        }

        $kid = (int) session('user_id');

        $type = $this->request->getPost('type'); // 'liegeplatz' | 'boot'
        $id   = (int) $this->request->getPost('id'); // bid | bbid

        if (!in_array($type, ['liegeplatz', 'boot'], true) || $id <= 0) {
            return redirect()->to('/meine-buchungen')->with('success', 'Ungültige Anfrage.');
        }

        $db = \Config\Database::connect();

        if ($type === 'liegeplatz') {
            // nur eigene Buchung stornieren
            $row = $db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->where('kid', $kid)
                ->get()
                ->getRowArray();

            if (!$row) {
                return redirect()->to('/meine-buchungen')->with('success', 'Buchung nicht gefunden.');
            }

            if (($row['status'] ?? '') === 'storniert') {
                return redirect()->to('/meine-buchungen')->with('success', 'Buchung war bereits storniert.');
            }

            $db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->where('kid', $kid)
                ->update(['status' => 'storniert']);

            return redirect()->to('/meine-buchungen')->with('success', 'Liegeplatz-Buchung storniert.');
        }

        // boot
        $row = $db->table('boot_buchungen')
            ->where('bbid', $id)
            ->where('kid', $kid)
            ->get()
            ->getRowArray();

        if (!$row) {
            return redirect()->to('/meine-buchungen')->with('success', 'Buchung nicht gefunden.');
        }

        if (($row['status'] ?? '') === 'storniert') {
            return redirect()->to('/meine-buchungen')->with('success', 'Buchung war bereits storniert.');
        }

        $db->table('boot_buchungen')
            ->where('bbid', $id)
            ->where('kid', $kid)
            ->update(['status' => 'storniert']);

        return redirect()->to('/meine-buchungen')->with('success', 'Boot-Buchung storniert.');
    }

}
