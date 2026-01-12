<?php

namespace App\Models;

use CodeIgniter\Model;

class KundeModel extends Model
{
    protected $table      = 'kunden';
    protected $primaryKey = 'kid';

    protected $returnType = 'array';

    protected $allowedFields = [
        'nachname',
        'vorname',
        'geburtsdatum',
        'geschlecht',
        'passwort',
        'strasse',
        'hausnr',
        'plz',
        'ort',
        'telefon',
        'email',
    ];

    protected $useTimestamps = false;

    public function findNewest(int $limit = 5): array
    {
        return $this->orderBy($this->primaryKey, 'DESC')
            ->findAll($limit);
    }

}
