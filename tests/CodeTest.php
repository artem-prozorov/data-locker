<?php

namespace Prozorov\DataVerification\Tests;

use PHPUnit\Framework\TestCase;
use Prozorov\DataVerification\Models\{Code, VerificationData};

class CodeTest extends TestCase
{
    public function testVerificationDataSetterAndGetter()
    {
        $data = ['test'];

        $code = new Code();

        $code->setVerificationData($data);

        $this->assertEquals($data, $code->getVerificationData());
    }

    public function testConstructor()
    {
        $row = [
            'ID' => 1,
            'VERIFICATION_CODE' => 12345678,
            'ADDRESS' => 89181234567,
            'PASS' => 1234,
            'ATTEMPTS' => 1,
            'VALIDATED' => 'N',
            'DATA' => ['test'],
        ];

        $code = new Code($row);

        $this->assertEquals($row['ID'], $code->getId());
        $this->assertEquals($row['VERIFICATION_CODE'], $code->getVerificationCode());
        $this->assertEquals($row['ADDRESS'], $code->getAddress()->__toString());
        $this->assertEquals($row['PASS'], $code->getOneTimePass());
        $this->assertEquals($row['ATTEMPTS'], $code->getAttempts());
        $this->assertEquals(false, $code->isValidated());
        $this->assertEquals($row['DATA'], $code->getVerificationData());
        $this->assertFalse($code->isNew());
    }
}
