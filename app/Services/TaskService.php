<?php

namespace App\Services;

use App\Models\RecordsModel;
use App\Models\RefusedClientModel;
use App\Models\WebhookModel;
use CodeIgniter\Database\Config;

class TaskService
{
    public function analizeNewRecords()
    {
        $model= new RecordsModel();
        $model->ProcessNewRecords();
    }

    public function decodeWebhookRecords(): void
    {
        $model = new WebhookModel();
        $model->ProcessRecords();
    }

    public function createRefusalTasks()
    {
        $refusedModel = new RefusedClientModel();
        $records = $refusedModel->where('done', 0)->findAll();
        $yougile = new YougileService();
        $columnId = config("Yougile")->ColumnId;
        $db=Config::connect();
        foreach ($records as $rec) {
            if ($this->NewRecordExists($rec['client_id'])) {
                // Если у клиента есть новые записи, пропускаем создание задачи
                $refusedModel->update($rec['record_id'], ['done' => 1]);
                continue;
            }
            // Логика создания задачи
            $fils=$db->table('filial')->where('id', $rec['filial_id'])->get()->getRowArray();
            $case=match ($rec['state']){
                'RE'=>"не пришел по записи на",
                'DE'=>"запись удалена на"
            };
            $msg="Клиент {$rec['client_name']} с телефоном {$rec['phone']}" . " $case {$rec['record_date']}<br>";
            $msg.="Филиал: {$fils['name']}<br><br>";
            $services=json_decode($rec['services'],true);
            $msg.="Услуги:<br/>";
            foreach ($services as $service)
            {
                $msg.=$service['title']."  ".$service['cost']."<br>";
            }
            //echo $msg . "\n\n";
            $case=match ($rec['state'])
            {
                'RE'=>'Клиент не пришел:',
                'DE'=>'Запись удалена:'
            };

            $rez=$yougile->createTask(
                $case.' ' . $rec['client_name'],
                $msg,
                $columnId,
                ['stickers'=>json_decode($fils['ysticker_id'])]
            );

            // Отметить запись как обработанную
            $refusedModel->update($rec['record_id'], ['done' => 1, 'yid' => $rez['id']]);
        }
    }

    public function NewRecordExists($clientID):bool
    {
        //Проверяем есть ли у нас новые активные записи по клиенту
        $db=Config::connect();
        $rez=$db->query("select count(*) cnt from records where
                          client_id=?
                          and record_date>now()
                          and oper<>'DE'
                          and attendance<>-1", [$clientID])->getResult();

        return $rez[0]->cnt>0;

    }
}