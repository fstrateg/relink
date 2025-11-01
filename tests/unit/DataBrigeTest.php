<?php

namespace unit;

use App\Services\DataBridgeService;
use CodeIgniter\Test\CIUnitTestCase;

class DataBrigeTest extends CIUnitTestCase
{
    public function testClientRecords(): void
    {
        $model=new DataBridgeService();
        $arr=$model->getClientRecords(160362190);
        self::assertIsArray($arr);

        self::assertEquals(9, $arr['wax']['vl']);

        $arr=$model->getClientRecords(95545253);
        self::assertIsArray($arr);

        $this->assertEquals(12, $arr['el']['vl']);
        $this->assertEquals(4, $arr['lz']['vl']);
    }

}