<?php

namespace Prozorov\DataVerification\Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Prozorov\DataVerification\Configuration;
use Prozorov\DataVerification\Locker;
use Prozorov\DataVerification\Types\Phone;
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Repositories\FakeCodeRepo;
use Mockery;

class LockerTest extends MockeryTestCase
{
    use HasContainer;

    public function testLockingData()
    {
        $config = new Configuration(
            $this->getContainer(),
            ['code_repository' => new FakeCodeRepo()]
        );

        $locker = new Locker($config);

        $data = ['some data'];

        $address = new Phone('89181234567');

        $code = $locker->lockData($data, $address, new SmsMessage());

        $this->assertEquals($data, $code->getVerificationData());

        $verification = $code->getVerificationCode();
        $pass = $code->getOneTimePass();

        $repo = Mockery::mock(FakeCodeRepo::class);
        $repo->makePartial();
        $repo->shouldReceive('getOneUnvalidatedByCode')->andReturn($code);

        $config = new Configuration(
            $this->getContainer(),
            ['code_repository' => $repo]
        );

        $locker = new Locker($config);

        $unlocked = $locker->getUnlockedData($verification, $pass);

        $this->assertEquals($data, $unlocked);
    }

    public function testFakeCode()
    {
        $config = new Configuration(
            $this->getContainer(),
            ['code_repository' => new FakeCodeRepo()]
        );

        $locker = new Locker($config);

        $fakeCode = '1234';

        Locker::useFakePass($fakeCode);

        $code = $locker->lockData(['some data'], new Phone('89181234567'), new SmsMessage());

        $this->assertEquals($fakeCode, $code->getOneTimePass());
    }
}
