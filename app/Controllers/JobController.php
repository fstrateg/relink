<?php

namespace App\Controllers;

use App\Services\TaskService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class JobController extends Controller
{
    public function firstTouch():ResponseInterface
    {
        $task = new TaskService();
        $task->createTasks();
        return $this->response->setJSON(['status' => 'success']);
    }

    public function decodeWebhook():ResponseInterface
    {
        $task = new TaskService();
        $task->decodeWebhookRecords();
        return $this->response->setJSON(['status' => 'success']);
    }

}