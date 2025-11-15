<?php

namespace App\Models;

use CodeIgniter\Events\Events;
use CodeIgniter\Model;

class RecordsModel extends Model
{
    protected $table      = 'records';
    protected $primaryKey = 'record_id';

    protected $returnType     = 'array';
    protected $allowedFields = [
        'client_name',
        'phone',
        'client_id',
        'attendance',
        'record_date',
        'record_id',
        'amount',
        'services',
        'filial_id',
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

    public function ProcessNewRecords()
    {
        $recs=$this->where('done', 0)->findAll();
        foreach ($recs as $rec) {
            // Здесь должна быть логика обработки записи
            if ($rec['oper']=='DE') {
                Events::trigger('record.delete', $rec);
            }elseif ($rec['attendance']==-1) {
                Events::trigger('record.refuse', $rec);
            }
            // Для демонстрации просто отметим запись как обработанную
            $this->update($rec['record_id'], ['done' => 1]);
        }
    }
}