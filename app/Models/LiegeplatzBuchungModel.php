<?php

namespace App\Models;

use CodeIgniter\Model;

class LiegeplatzBuchungModel extends Model
{
    protected $table      = 'liegeplatz_buchungen';
    protected $primaryKey = 'bid';

    protected $returnType = 'array';

    protected $allowedFields = [
        'lid', 'kid', 'von', 'bis', 'status', 'created_at', 'kosten'
    ];

    protected $useTimestamps = false;

    /**
     * Liefert alle lid, die im Zeitraum [von..bis] eine aktive Überschneidung haben.
     */
    public function findBookedLidsForRange(string $von, string $bis): array
    {
        $rows = $this->select('lid')
            ->where('status', 'aktiv')
            // Überschneidung: NICHT (bis < von ODER von > bis)
            ->groupStart()
            ->where('bis >=', $von)
            ->where('von <=', $bis)
            ->groupEnd()
            ->findAll();

        // unique lids
        $lids = [];
        foreach ($rows as $r) {
            $lids[] = (int)$r['lid'];
        }
        return array_values(array_unique($lids));
    }
}
