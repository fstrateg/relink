<?php

namespace App\Services;

use App\Models\WebhookModel;

class TaskService
{
    public function createTasks()
    {
        // Implementation for creating tasks
    }

    public function decodeWebhookRecords(): void
    {
        $model = new WebhookModel();
        $model->ProcessRecords();
    }
}