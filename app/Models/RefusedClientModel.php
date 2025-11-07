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
        'record_date',
        'record_id',
        'amount',
        'state',
    ];
}