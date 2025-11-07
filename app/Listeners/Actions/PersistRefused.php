<?php
namespace App\Listeners\Actions;

use App\Models\RefusedClientModel;

final class PersistRefused
{
    public static function handle(array $data): void
    {
        $refusedModel = new RefusedClientModel();
        $rec=$refusedModel->where('record_id', $data['record_id'])->first();
        $vl=[
            'record_id' => $data['record_id'],
            'client_name' => $data['client_name'],
            'phone' => $data['phone'],
            'record_date' => $data['record_date'],
            'amount' => $data['amount'],
            'state' => 'RE',
        ];
        if ($rec) {
            $refusedModel->update($rec['id'],  $vl);
            return;
        }
        $refusedModel->insert($vl);
    }

    public static function handleDelete(array $data): void
    {
        $refusedModel = new RefusedClientModel();
        $rec=$refusedModel->where('record_id', $data['record_id'])->first();
        $vl=[
            'record_id' => $data['record_id'],
            'client_name' => $data['client_name'],
            'phone' => $data['phone'],
            'record_date' => $data['record_date'],
            'amount' => $data['amount'],
            'state' => 'DE',
        ];
        if ($rec) {
            $refusedModel->update($rec['id'],  $vl);
            return;
        }
        $refusedModel->insert($vl);
    }
}