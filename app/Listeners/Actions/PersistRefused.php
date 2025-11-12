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
            'client_id' => $data['client_id'],
            'phone' => $data['phone'],
            'record_date' => $data['record_date'],
            'filial_id' => $data['filial_id'],
            'services' => $data['services'],
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
            'client_id' => $data['client_id'],
            'phone' => $data['phone'],
            'record_date' => $data['record_date'],
            'filial_id' => $data['filial_id'],
            'services' => $data['services'],
            'amount' => $data['amount'],
            'state' => 'DE',
        ];
        if ($rec) {
            $refusedModel->update($rec['id'],  $vl);
            return;
        }
        $refusedModel->insert($vl);
    }

    public static function handleNew(array $data): void
    {
        //TODO: удалить запись из отказников если клиент записался вновь
        $refusedModel = new RefusedClientModel();
        $rec=$refusedModel->where('record_id', $data['record_id'])->first();
        if ($rec) {
            $refusedModel->delete($rec['id']);
        }
    }
}