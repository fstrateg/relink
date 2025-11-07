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
        'attendance',
        'record_date',
        'record_id',
        'amount',
        'services',
        'oper',
        'done'];

    public function upsert(array $data): bool
    {
        if (empty($data['record_id'])) {
            return false; // ключ обязателен
        }

        $existing = $this->where('record_id', $data['record_id'])->first();

        if ($existing) {
            return $this->update($data['record_id'], $data);
        }

        return $this->insert($data) !== false;
    }
}