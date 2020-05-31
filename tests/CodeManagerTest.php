<?php

use PHPUnit\Framework\TestCase;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\App\CodeManager;;
use Prozorov\DataVerification\Types\Phone;
use Prozorov\DataVerification\App\Configuration;
use Prozorov\DataVerification\Exceptions\{LimitException, VerificationException};
use Prozorov\DataVerification\Integrations\Bitrix\Repositories\CodeRepo;

class CodeManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configuration::getInstance()->loadConfig([
            'code_repository' => new Prozorov\DataVerification\Repositories\FakeCodeRepo(),
        ]);
    }

    public function testCodeLength()
    {
        $phone = new Phone('89181234567');

        $manager = CodeManager::getInstance();
        $code = $manager->generate($phone);

        $this->assertEquals(Configuration::getInstance()->getPassLength(), mb_strlen($code->getOneTimePass()));
    }

    public function testCodeCreationLimit()
    {
        $this->expectException(LimitException::class);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getLastCodeForAddress')->willReturn(new Code());

        Configuration::getInstance()->setCodeRepo($codeRepo);

        $phone = new Phone('89181234567');

        $manager = CodeManager::getInstance();
        $code = $manager->generate($phone);
    }

    public function testCodeThresholdExceed()
    {
        $this->expectException(LimitException::class);

        $overLimit = (Configuration::getInstance()->getLimitPerHour() + 1);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getLastCodeForAddress')->willReturn(null);
        $codeRepo->method('getCodesCountForAddress')->willReturn($overLimit);

        Configuration::getInstance()->setCodeRepo($codeRepo);

        $phone = new Phone('89181234567');

        $manager = CodeManager::getInstance();
        $code = $manager->generate($phone);
    }

    public function testVerificationData()
    {
        $testData = ['test' => 'test_data'];

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('save')->willReturn(new Code());
        Configuration::getInstance()->setCodeRepo($codeRepo);

        $phone = new Phone('89181234567');

        $manager = CodeManager::getInstance();
        $code = $manager->generate($phone, $testData);

        $this->assertEquals($testData, $code->getVerificationData());
    }

    public function testPassNotFound()
    {
        $this->expectException(\OutOfBoundsException::class);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getOneUnvalidatedByCode')->willReturn(null);
        Configuration::getInstance()->setCodeRepo($codeRepo);

        $manager = CodeManager::getInstance();
        $manager->verify('test', 'test');
    }

    public function testIncorrectPass()
    {
        $this->expectException(VerificationException::class);

        $code = new Code();
        $code->setOneTimePass(1234);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getOneUnvalidatedByCode')->willReturn($code);
        $codeRepo->method('save')->willReturn($code);
        Configuration::getInstance()->setCodeRepo($codeRepo);

        $manager = CodeManager::getInstance();
        $manager->verify('test', 'test');

        $this->assertEquals(1, $code->getAttempts());
    }

    public function testVerificationLimit()
    {
        $this->expectException(LimitException::class);

        $code = new Code();
        $code->setOneTimePass(1234)->setAttempts(3);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getOneUnvalidatedByCode')->willReturn($code);
        $codeRepo->method('save')->willReturn($code);
        Configuration::getInstance()->setCodeRepo($codeRepo);

        $manager = CodeManager::getInstance();
        $manager->verify('test', 1234);
    }

    public function testCorrectVerification()
    {
        $testData = ['test_verification_data'];

        $code = new Code();
        $code->setOneTimePass(1234)->setVerificationData($testData);

        $codeRepo = $this->createMock(CodeRepo::class);
        $codeRepo->method('getOneUnvalidatedByCode')->willReturn($code);
        $codeRepo->method('save')->willReturn($code);
        Configuration::getInstance()->setCodeRepo($codeRepo);

        $manager = CodeManager::getInstance();
        $resultCode = $manager->verify('test', 1234);

        $this->assertEquals($testData, $resultCode->getVerificationData());
        $this->assertTrue($code->isValidated());
    }
}
