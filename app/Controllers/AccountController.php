<?php

namespace App\Controllers;

class AccountController extends BaseController
{
    private function calculateDays(?string $von, ?string $bis): int
    {
        $von = trim((string)$von);
        $bis = trim((string)$bis);
        if ($von === '' || $bis === '') {
            return 0;
        }
        try {
            $start = new \DateTimeImmutable($von);
            $end = new \DateTimeImmutable($bis);
            if ($end < $start) {
                return 0;
            }
            return $start->diff($end)->days + 1;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function pdfText(string $text): string
    {
        $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        return $converted === false ? $text : $converted;
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
            ->select('lb.bid, lb.von, lb.bis, lb.status, lb.kosten, lp.anleger, lp.nummer')
            ->join('liegeplaetze lp', 'lp.lid = lb.lid')
            ->where('lb.kid', $kid)
            ->orderBy('lb.von', 'DESC')
            ->get()
            ->getResultArray();

        $bootBuchungen = $db->table('boot_buchungen bb')
            ->select('bb.bbid, bb.von, bb.bis, bb.status, bb.kosten, b.name, b.typ, b.plaetze')
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

    public function invoice(string $type, int $id)
    {
        if ($redir = $this->requireLogin()) return $redir;

        if (session('role') !== 'kunde') {
            return redirect()->to('/');
        }

        if (!in_array($type, ['liegeplatz', 'boot'], true) || $id <= 0) {
            return redirect()->to('/meine-buchungen');
        }

        $kid = (int) session('user_id');
        $db = \Config\Database::connect();

        if ($type === 'liegeplatz') {
            $row = $db->table('liegeplatz_buchungen lb')
                ->select('lb.bid, lb.von, lb.bis, lb.status, lb.kosten, lb.created_at, lp.anleger, lp.nummer, k.vorname, k.nachname, k.strasse, k.hausnr, k.plz, k.ort, k.email')
                ->join('liegeplaetze lp', 'lp.lid = lb.lid')
                ->join('kunden k', 'k.kid = lb.kid')
                ->where('lb.bid', $id)
                ->where('lb.kid', $kid)
                ->get()
                ->getRowArray();

            if (!$row || ($row['status'] ?? '') !== 'aktiv') {
                return redirect()->to('/meine-buchungen');
            }

            $itemLabel = 'Liegeplatz ' . $row['anleger'] . ' - ' . $row['nummer'];
            $invoiceNo = 'LP-' . $row['bid'] . '-' . date('Ymd');
        } else {
            $row = $db->table('boot_buchungen bb')
                ->select('bb.bbid, bb.von, bb.bis, bb.status, bb.kosten, bb.created_at, b.name, b.typ, k.vorname, k.nachname, k.strasse, k.hausnr, k.plz, k.ort, k.email')
                ->join('boote b', 'b.boid = bb.boid')
                ->join('kunden k', 'k.kid = bb.kid')
                ->where('bb.bbid', $id)
                ->where('bb.kid', $kid)
                ->get()
                ->getRowArray();

            if (!$row || ($row['status'] ?? '') !== 'aktiv') {
                return redirect()->to('/meine-buchungen');
            }

            $itemLabel = 'Boot ' . $row['name'] . ($row['typ'] ? ' (' . $row['typ'] . ')' : '');
            $invoiceNo = 'BO-' . $row['bbid'] . '-' . date('Ymd');
        }

        require_once APPPATH . 'ThirdParty/fpdf/fpdf.php';
        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $logoPath = FCPATH . 'img/logo.png';
        if (is_file($logoPath)) {
            $pdf->Image($logoPath, 10, 10, 30);
        }

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(50, 12);
        $pdf->Cell(0, 8, $this->pdfText('Rechnung'), 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(50, 20);
        $pdf->Cell(0, 5, $this->pdfText('Rechnungsnummer: ' . $invoiceNo), 0, 1);
        $pdf->SetX(50);
        $pdf->Cell(0, 5, $this->pdfText('Datum: ' . date('d.m.Y')), 0, 1);

        $pdf->SetXY(10, 40);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $this->pdfText('Rechnung an:'), 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $this->pdfText(trim($row['vorname'] . ' ' . $row['nachname'])), 0, 1);
        $pdf->Cell(0, 6, $this->pdfText(trim($row['strasse'] . ' ' . $row['hausnr'])), 0, 1);
        $pdf->Cell(0, 6, $this->pdfText(trim($row['plz'] . ' ' . $row['ort'])), 0, 1);
        $pdf->Cell(0, 6, $this->pdfText((string)$row['email']), 0, 1);

        $pdf->SetXY(10, 80);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $this->pdfText('Leistung'), 0, 1);

        $tage = $this->calculateDays($row['von'] ?? null, $row['bis'] ?? null);
        $kosten = (int)($row['kosten'] ?? 0);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $this->pdfText($itemLabel), 0, 1);
        $pdf->Cell(0, 6, $this->pdfText('Zeitraum: ' . $row['von'] . ' bis ' . $row['bis'] . ' (' . $tage . ' Tage)'), 0, 1);

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $this->pdfText('Gesamtbetrag: ' . $kosten . ' EUR'), 0, 1);

        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, $this->pdfText('Bitte ueberweisen Sie den Betrag an:'), 0, 1);
        $pdf->Cell(0, 5, $this->pdfText('Zahlungsempfaenger: Hafendorf'), 0, 1);
        $pdf->Cell(0, 5, $this->pdfText('IBAN: DE00 0000 0000 0000 0000 00'), 0, 1);

        $filename = 'rechnung-' . $invoiceNo . '.pdf';
        $content = $pdf->Output('S');

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

}
