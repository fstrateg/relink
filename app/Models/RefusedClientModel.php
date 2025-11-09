<?php

namespace App\Models;

use CodeIgniter\Model;

class RefusedClientModel extends Model
{
    protected $table      = 'refused_clients';
    protected $primaryKey = 'record_id';

    protected $returnType     = 'array';
    protected $allowedFields = [
        'client_name',
        'phone',
        'client_id',
        'record_date',
        'record_id',
        'filial_id',
        'services',
        'amount',
        'state',
        'done'
    ];
}