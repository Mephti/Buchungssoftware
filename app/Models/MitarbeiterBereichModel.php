<?php

namespace App\Models;

class MitarbeiterBereichModel
{
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getKundenList(): array
    {
        return $this->db->table('kunden')
            ->select('kid, vorname, nachname, email')
            ->orderBy('nachname', 'ASC')
            ->orderBy('vorname', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getKundeById(int $kid): ?array
    {
        $row = $this->db->table('kunden')
            ->where('kid', $kid)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    public function getKundeByEmail(string $email): ?array
    {
        $row = $this->db->table('kunden')
            ->where('email', $email)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    public function createKunde(array $data): int
    {
        $this->db->table('kunden')->insert($data);
        return (int)$this->db->insertID();
    }

    public function getLiegeplatzBuchungen(bool $onlyActive): array
    {
        $query = $this->db->table('liegeplatz_buchungen lb')
            ->select('lb.bid, lb.von, lb.bis, lb.status, lb.kosten, lp.anleger, lp.nummer, k.kid, k.vorname, k.nachname, k.email')
            ->join('liegeplaetze lp', 'lp.lid = lb.lid')
            ->join('kunden k', 'k.kid = lb.kid')
            ->orderBy('lb.von', 'DESC');

        if ($onlyActive) {
            $query->where('lb.status', 'aktiv');
        }

        return $query->get()->getResultArray();
    }

    public function getBootBuchungen(bool $onlyActive): array
    {
        $query = $this->db->table('boot_buchungen bb')
            ->select('bb.bbid, bb.von, bb.bis, bb.status, bb.kosten, b.name, b.typ, b.plaetze, k.kid, k.vorname, k.nachname, k.email')
            ->join('boote b', 'b.boid = bb.boid')
            ->join('kunden k', 'k.kid = bb.kid')
            ->orderBy('bb.von', 'DESC');

        if ($onlyActive) {
            $query->where('bb.status', 'aktiv');
        }

        return $query->get()->getResultArray();
    }

    public function getLiegeplaetze(): array
    {
        return $this->db->table('liegeplaetze')
            ->select('lid, anleger, nummer, status')
            ->orderBy('anleger', 'ASC')
            ->orderBy('nummer', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getBoote(): array
    {
        return $this->db->table('boote')
            ->select('boid, name, typ, plaetze, status')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getLiegeplaetzeByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->db->table('liegeplaetze')
            ->select('lid, status, kosten_pt')
            ->whereIn('lid', $ids)
            ->get()
            ->getResultArray();
    }

    public function getBooteByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->db->table('boote')
            ->select('boid, status, kosten_pt')
            ->whereIn('boid', $ids)
            ->get()
            ->getResultArray();
    }

    public function cancelBooking(string $type, int $id): array
    {
        if ($type === 'liegeplatz') {
            $row = $this->db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->get()
                ->getRowArray();

            if (!$row) {
                return ['ok' => false, 'message' => 'Buchung nicht gefunden.'];
            }

            if (($row['status'] ?? '') === 'storniert') {
                return ['ok' => true, 'message' => 'Buchung war bereits storniert.'];
            }

            $this->db->table('liegeplatz_buchungen')
                ->where('bid', $id)
                ->update(['status' => 'storniert']);

            return ['ok' => true, 'message' => 'Liegeplatz-Buchung storniert.'];
        }

        if ($type === 'boot') {
            $row = $this->db->table('boot_buchungen')
                ->where('bbid', $id)
                ->get()
                ->getRowArray();

            if (!$row) {
                return ['ok' => false, 'message' => 'Buchung nicht gefunden.'];
            }

            if (($row['status'] ?? '') === 'storniert') {
                return ['ok' => true, 'message' => 'Buchung war bereits storniert.'];
            }

            $this->db->table('boot_buchungen')
                ->where('bbid', $id)
                ->update(['status' => 'storniert']);

            return ['ok' => true, 'message' => 'Boot-Buchung storniert.'];
        }

        return ['ok' => false, 'message' => 'Ungültige Anfrage.'];
    }

    public function updateStatus(string $type, int $id, string $status): array
    {
        if ($type === 'liegeplatz') {
            $this->db->table('liegeplaetze')
                ->where('lid', $id)
                ->update(['status' => $status]);
            return ['ok' => true, 'message' => 'Liegeplatz-Status aktualisiert.'];
        }

        if ($type === 'boot') {
            $this->db->table('boote')
                ->where('boid', $id)
                ->update(['status' => $status]);
            return ['ok' => true, 'message' => 'Boot-Status aktualisiert.'];
        }

        return ['ok' => false, 'message' => 'Ungültige Status-Änderung.'];
    }

    public function createBoat(array $data): void
    {
        $this->db->table('boote')->insert($data);
    }

    public function deleteOldCancelledBookings(int $days): array
    {
        $cutoff = (new \DateTimeImmutable('now'))->modify('-' . $days . ' days')->format('Y-m-d H:i:s');

        $lpCount = $this->db->table('liegeplatz_buchungen')
            ->where('status', 'storniert')
            ->where('created_at <', $cutoff)
            ->countAllResults();

        $bootCount = $this->db->table('boot_buchungen')
            ->where('status', 'storniert')
            ->where('created_at <', $cutoff)
            ->countAllResults();

        if ($lpCount > 0) {
            $this->db->table('liegeplatz_buchungen')
                ->where('status', 'storniert')
                ->where('created_at <', $cutoff)
                ->delete();
        }

        if ($bootCount > 0) {
            $this->db->table('boot_buchungen')
                ->where('status', 'storniert')
                ->where('created_at <', $cutoff)
                ->delete();
        }

        return [
            'liegeplatz' => $lpCount,
            'boot' => $bootCount,
        ];
    }
}
