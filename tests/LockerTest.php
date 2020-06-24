<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Prozorov\DataVerification\Configuration;
use Prozorov\DataVerification\Locker;
use Psr\Container\ContainerInterface;
use Prozorov\DataVerification\Types\Phone;
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Repositories\FakeCodeRepo;

class LockerTest extends MockeryTestCase
{
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

    /**
     * getContainer.
     *
     * @access	protected
     * @return	ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            protected $bindings;

            public function get($id)
            {
                return $this->bindings[$id];
            }

            public function has($id)
            {
                return empty($this->bindings[$id]);
            }

            public function setBindings(array $bindings)
            {
                $this->bindings = $bindings;
            }
        };
    }
}
