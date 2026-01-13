<?php

namespace App\Models;

use CodeIgniter\Model;

class BootBuchungModel extends Model
{
    protected $table      = 'boot_buchungen';
    protected $primaryKey = 'bbid';
    protected $returnType = 'array';
    protected $allowedFields = ['boid', 'kid', 'von', 'bis', 'status', 'created_at'];
    protected $useTimestamps = false;

    public function findBookedBoidsForRange(string $von, string $bis): array
    {
        $rows = $this->select('boid')
            ->where('status', 'aktiv')
            ->groupStart()
            ->where('bis >=', $von)
            ->where('von <=', $bis)
            ->groupEnd()
            ->findAll();

        $ids = [];
        foreach ($rows as $r) $ids[] = (int)$r['boid'];
        return array_values(array_unique($ids));
    }
}