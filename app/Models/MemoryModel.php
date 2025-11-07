<?php

namespace App\Models;


use CodeIgniter\Model;

class MemoryModel extends Model
{
    protected $table      = 'memory';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = ['nval'];

    public function getnval($id): int
    {
        return $this->where('id', $id)->first()['nval'] ?? 0;
    }

    public static function nval($id): int
    {
        $model = new MemoryModel();
        return $model->getnval($id);
    }

    public static function upsertval($id, $nval): void
    {
        $model = new MemoryModel();
        $existing = $model->where('id', $id)->first();
        if ($existing) {
            $model->update($existing['id'], ['nval' => $nval]);
        } else {
            $model->insert(['id' => $id, 'nval' => $nval]);
        }
    }
}