<?php
namespace App\Listeners\Actions;

use App\Models\RefusedClientModel;
use App\Services\YougileService;

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
            $refusedModel->update($rec['record_id'],  $vl);
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
            $refusedModel->update($rec['record_id'],  $vl);
            return;
        }
        $refusedModel->insert($vl);
    }

    public static function handleNew(array $data): void
    {
        //TODO: удалить запись из отказников если клиент записался вновь
        $refusedModel = new RefusedClientModel();
        $rec = $refusedModel->where('phone', $data['phone'])->orderBy('record_date', 'DESC')->first();
        if (!$rec) return;
        if ($data['record_date'] >= $rec['record_date']) {
            if ($rec['done'] == 0) {
                $refusedModel->delete($rec['record_id']);
                return;
            }
            if (!empty($rec['yid'])&&$rec['done']==1) {
                $yougile = new YougileService();
                $task = $yougile->getTask($rec['yid']);
                if (isset($task['id'])) {
                    $task['completed'] = true;
                    $task['title'] .= ' (Найдена новая запись)';
                    $task['description'] .= self::createAddMessage($data);
                    $newtask=array_filter($task,function ($key){
                        return !in_array($key,['id','timestamp','createdBy','type']);
                    }, ARRAY_FILTER_USE_KEY);
                    $yougile->UpdateTask($rec['yid'], $newtask);
                    $refusedModel->update($rec['record_id'], ['done' => 2]);
                }
            };

        }
    }
    private static function createAddMessage($data):string
    {
        $msg='<hr>Новая дата записи: '.$data['record_date'].'<br>';
        $services=json_decode($data['services'],true);
        $msg.="Услуги:<br/>";
        foreach ($services as $service)
        {
            $msg.=$service['title']."  ".$service['cost']."<br>";
        }
        return $msg;
    }
}