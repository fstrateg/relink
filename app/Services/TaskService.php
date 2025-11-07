<?php

namespace App\Services;

use App\Models\RecordsModel;
use App\Models\WebhookModel;

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
}