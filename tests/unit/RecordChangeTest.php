<?php

namespace unit;

use App\Models\TaskModel;
use App\Models\TasktypeModel;
use App\Models\TestCasesModel;
use App\Services\TaskService;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;
use PHPUnit\Framework\Assert;

class RecordChangeTest extends CIUnitTestCase
{
    // Новая запись должна сохранять список оказанных услуг.
    public function testNewRecord()
    {
        $testCases = (new TestCasesModel())->findAll();
        $db=Database::connect();
        // Очистить таблицу перед тестом
        $db->query("DELETE FROM tst_services");
        $db->query("DELETE FROM tst_record_services");


        foreach ($testCases as $case) {
            $rec = json_decode($case['data'], true);
            if ($rec['status'] != 'create') continue;


            // Выполнить процедуру
            $service = new TaskService();
            $service->NewRecord($rec);

        }
        // Проверка: появились записи
        $inserted = $db->table('tst_services')->countAll();
        $this->assertNotEmpty($inserted, 'Нет записей после вызова RecordChange.');

        // Проверка: количество вставок равно количеству tasktype
        $inserted = $db->table('tst_record_services')->where(['record_id'=>$rec['resource_id']])->countAll();
        $this->assertEquals(3, $inserted);

    }

    // Клиент пришел, создаем задачи по оповещению.
    public function testClientArrived()
    {
        // Подготовка к тестам
        $db=Database::connect();
        $db->query("DELETE FROM tst_task");
        $test = (new TestCasesModel())->find(2);
        $rec = json_decode($test['data'], true);

        // Начало тестов
        $service = new TaskService();
        $service->ClientArrived($rec);

        $cnt=$db->table('tst_task')->countAll();
        $this->assertGreaterThan(0, $cnt);
    }

    // Клиент записался повторно, удаляем все задачи по оповещению
    public function testClientRecordedAgain()
    {
        $this->testClientArrived(); // клиент пришел, по нему назначены задачи
        $this->testNewRecord(); // клиент записался заново

        $test = (new TestCasesModel())->find(2);
        $rec = json_decode($test['data'], true);

        $db=Database::connect();
        $cnt=$db->table('tst_task')->where(['client_id'=>$rec['data']['client']['id']])->countAll();
        $this->assertEquals(0, $cnt);
    }

    public function testBitrixMessage()
    {
        $model = new TasktypeModel();
        $tt=$model->find(3);

        $model = new TaskModel();
        $task=$model->find(1);

        $cls=new TaskService();
        $msg=$cls->createTaskText($task, $tt);
        fwrite(STDERR,$msg);

        $this->assertGreaterThan(0, strlen($msg));
    }

    public function testDebugView()
    {
        $params=[
            'client_name'     => 'Иван Иванов',
            'record_date'     => '2025-05-10',
            'staff'           => 'Петров А.А.',
            'addText'         => 'Обратите внимание на новые условия.',
            'url'             => [
                'url'       => 'https://wa.me/',
                'phone'     => '79991234567',
                'text'      => urlencode('Здравствуйте! Напоминаем про запись.'),
                'link_name' => 'WhatsApp',
                'text0'     => 'Здравствуйте! Напоминаем про запись.'
            ],
            'visits'  => [
                'fails'=>['vl'=>0,'typ'=>'фл'],
                'el'=>['vl'=>2,'typ'=>'ээ'],
                'lz'=>['vl'=>0,'typ'=>'лз'],
                'wax'=>['vl'=>0,'typ'=>'шг'],
            ],
            'services'        => [
                ['title' => 'Стрижка'],
                ['title' => 'Маникюр'],
            ],
        ];



        $msg=view('bitrix/task_body',$params);
        fwrite(STDERR,$msg);

        $this->assertGreaterThan(0, strlen($msg));
    }

    public function TestGetTasksForVisit()
    {
        $service = new TaskService();
        $rec = ['data' => ['client' => ['success_visits_count' => 3]]];

        $result = $service->getTasktypes($rec, 'лз');

        $this->assertCount(3, $result);
        $this->assertEquals([1, 2, 3], array_column($result, 'id'));
    }
}