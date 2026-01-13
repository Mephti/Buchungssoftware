<?php

namespace App\Models;

use CodeIgniter\Model;

class BootModel extends Model
{
    protected $table      = 'boote';
    protected $primaryKey = 'boid';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'typ', 'plaetze', 'status'];
    protected $useTimestamps = false;

    public function findFiltered(string $q): array
    {
        $b = $this->builder();

        $q = trim($q);
        if ($q !== '') {
            $b->groupStart()
                ->like('name', $q)
                ->orLike('typ', $q)
                ->groupEnd();
        }

        return $b->orderBy('name', 'ASC')->get()->getResultArray();
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'verfuegbar' => 'verfügbar',
            'gesperrt'   => 'gesperrt',
            'wartung'    => 'Wartung',
            'unterwegs'  => 'unterwegs',
            default      => $status,
        };
    }
}