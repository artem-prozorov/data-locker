<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Prozorov\DataVerification\CodeManager;
use Prozorov\DataVerification\Configuration;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Phone;
use Prozorov\DataVerification\Exceptions\{LimitException, VerificationException};
use Prozorov\DataVerification\Repositories\FakeCodeRepo;
use Psr\Container\ContainerInterface;

class CodeManagerTest extends MockeryTestCase
{
    public function testCodeLength()
    {
        $phone = new Phone('89181234567');

        $config = new Configuration(
            $this->getContainer(),
            ['code_repository' => new FakeCodeRepo()]
        );

        $manager = new CodeManager($config);

        $code = $manager->generate($phone);

        $this->assertEquals($config->getPassLength(), mb_strlen($code->getOneTimePass()));
    }

    public function testCodeCreationLimit()
    {
        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getLastCodeForAddress')->andReturn(new Code());

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $phone = new Phone('89181234567');

        $this->expectException(LimitException::class);

        $code = $manager->generate($phone);
    }

    public function testCodeThresholdExceed()
    {
        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getLastCodeForAddress')->andReturn(null);
        $codeRepo->shouldReceive('getCodesCountForAddress')->andReturn(4);

        $config = [
            'code_repository' => $codeRepo,
            'limit_per_hour' => 3,
        ];

        $phone = new Phone('89181234567');

        $manager = $this->getManager($config);

        $this->expectException(LimitException::class);

        $code = $manager->generate($phone);
    }

    public function testVerificationData()
    {
        $testData = ['test' => 'test_data'];

        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getLastCodeForAddress')->andReturn(null);
        $codeRepo->shouldReceive('getCodesCountForAddress')->andReturn(1);
        $codeRepo->shouldReceive('save')->andReturn(new Code());

        $phone = new Phone('89181234567');

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $code = $manager->generate($phone, $testData);

        $this->assertEquals($testData, $code->getVerificationData());
    }

    public function testPassNotFound()
    {
        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getOneUnvalidatedByCode')->andReturn(null);

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $this->expectException(\OutOfBoundsException::class);

        $manager->verify('test', 'test');
    }

    public function testIncorrectPass()
    {
        $this->expectException(VerificationException::class);

        $code = new Code();
        $code->setOneTimePass(1234);

        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getOneUnvalidatedByCode')->andReturn($code);
        $codeRepo->shouldReceive('save')->andReturn($code);

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $manager->verify('test', 'test');

        $this->assertEquals(1, $code->getAttempts());
    }

    public function testVerificationLimit()
    {
        $code = new Code();
        $code->setOneTimePass(1234)->setAttempts(3);

        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getOneUnvalidatedByCode')->andReturn($code);
        $codeRepo->shouldReceive('save')->andReturn($code);

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $this->expectException(LimitException::class);

        $manager->verify('test', 1234);
    }

    public function testCorrectVerification()
    {
        $testData = ['test_verification_data'];

        $code = new Code();
        $code->setOneTimePass(1234)->setVerificationData($testData);

        $codeRepo = Mockery::mock(FakeCodeRepo::class);
        $codeRepo->shouldReceive('getOneUnvalidatedByCode')->andReturn($code);
        $codeRepo->shouldReceive('save')->andReturn($code);

        $manager = $this->getManager(['code_repository' => $codeRepo]);

        $resultCode = $manager->verify('test', 1234);

        $this->assertEquals($testData, $resultCode->getVerificationData());
        $this->assertTrue($code->isValidated());
    }

    /**
     * getManager.
     *
     * @access	protected
     * @param	array	$config	
     * @return	CodeManager
     */
    protected function getManager(array $config): CodeManager
    {
        $configuration = new Configuration($this->getContainer(), $config);

        return new CodeManager($configuration);
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
