<?php

namespace App\Models;

use Config\Database;
class WebhookModel
{
    public function ProcessRecords():void
    {
        $last=MemoryModel::nval('last_rec');
        $db= Database::connect("alt");
        $results = $db->table('webhook')
            ->where('id >', $last)
            ->orderBy('id', 'ASC')
            ->limit(20)
            ->get()
            ->getResultArray();
        if (count($results)==0) return;
        $db=Database::connect(); // Switch back to default connection
        $model=new RecordsModel();
        foreach ($results as $row) {
            // Process each record (for demonstration, we'll just print it)
            echo "Processing record ID: " . $row['id'] . "\n";
            $last=$row['id'];
            $data= json_decode($row['content'], true);
            if ($data['resource']!='record') continue;
            $client=$data['data']['client'];
            if (empty($client)) continue;
            $services=$data['data']['services'] ?? [];
            $services_list=[];
            $amount=0;
            foreach ($services as $service) {
                $services_list[]=$service['id'];
                $amount += $service['cost'] ?? 0;
            }
            $oper=match ($data['status']) {
                'create' => 'AP',
                'update' => 'ED',
                'delete' => 'DE',
                default => '--',
            };
            $model->upsert([
                'record_id' => $data['data']['id'],
                'client_name' => $client['name'] ?? '',
                'phone' => $client['phone'] ?? '',
                'record_date' => $data['data']['date'],
                'attendance' => $data['data']['attendance'],
                'services' => implode(',', $services_list),
                'amount' => $amount,
                'oper'=> $oper,
                'done'      => 0,
            ]);
        }
        MemoryModel::upsertval('last_rec', $last);
    }
}