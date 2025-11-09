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
            if (!isset($client['id'])) continue;
            $services=$data['data']['services'] ?? [];
            $oper=match ($data['status']) {
                'create' => 'AP',
                'update' => 'ED',
                'delete' => 'DE',
                default => '--',
            };
            $model->upsert([
                'record_id' => $data['data']['id'],
                'filial_id' => $data['data']['company_id'] ?? 0,
                'client_name' => $client['name'] ?? '',
                'client_id' => $client['id'] ?? 0,
                'phone' => $client['phone'] ?? '',
                'record_date' => $data['data']['date'],
                'attendance' => $data['data']['attendance'],
                'services' => json_encode($services,JSON_UNESCAPED_UNICODE),
                'oper'=> $oper,
                'done'      => 0,
            ]);
        }
        MemoryModel::upsertval('last_rec', $last);
    }
}