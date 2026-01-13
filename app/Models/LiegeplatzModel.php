<?php

namespace App\Models;

use CodeIgniter\Model;

class LiegeplatzModel extends Model
{
    protected $table      = 'liegeplaetze';
    protected $primaryKey = 'lid';

    protected $returnType = 'array';

    protected $allowedFields = ['anleger', 'nummer', 'status'];

    protected $useTimestamps = false;

    public function findAllOrdered(): array
    {
        return $this->orderBy('anleger', 'ASC')
            ->orderBy('nummer', 'ASC')
            ->findAll();
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'verfuegbar' => 'verfügbar',
            'gesperrt'   => 'gesperrt',
            'vermietet'  => 'vermietet',
            'belegt'     => 'belegt',
            default      => $status,
        };
    }

    public function findFiltered(bool $onlyAvailable, string $q): array
    {
        $builder = $this->builder();

        if ($onlyAvailable) {
            $builder->where('status', 'verfuegbar');
        }

        if ($q !== '') {
            // Suche: "A-3", "A 3", "A", "3"
            $qNorm = strtoupper(trim($q));

            // Wenn Format wie A-3 oder A 3
            if (preg_match('/^([A-Z])\s*[- ]\s*(\d+)$/', $qNorm, $m)) {
                $builder->where('anleger', $m[1]);
                $builder->where('nummer', (int)$m[2]);
            } else {
                // nur Zahl -> nummer
                if (ctype_digit($qNorm)) {
                    $builder->where('nummer', (int)$qNorm);
                } else {
                    // sonst Anleger enthält
                    $builder->like('anleger', $qNorm);
                }
            }
        }

        return $builder->orderBy('anleger', 'ASC')
            ->orderBy('nummer', 'ASC')
            ->get()
            ->getResultArray();
    }

}