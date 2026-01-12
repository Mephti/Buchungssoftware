<?php

namespace App\Models;

use CodeIgniter\Model;

class MitarbeiterModel extends Model
{
    protected $table      = 'mitarbeiter';
    protected $primaryKey = 'mid';

    protected $returnType = 'array';

    protected $allowedFields = [
        'vorname',
        'nachname',
        'geschlecht',
        'passwort',
        'email',
    ];

    protected $useTimestamps = false;
}