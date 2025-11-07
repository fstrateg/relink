<?php

namespace App\Controllers;

use App\Services\TaskService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class JobController extends Controller
{
    public function processRecords():ResponseInterface
    {
        $task = new TaskService();
        $task->analizeNewRecords();
        return $this->response->setJSON(['status' => 'success']);
    }

    public function decodeWebhook():ResponseInterface
    {
        $task = new TaskService();
        $task->decodeWebhookRecords();
        return $this->response->setJSON(['status' => 'success']);
    }

}