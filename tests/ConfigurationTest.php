<?php

namespace Prozorov\DataVerification\Tests;

use PHPUnit\Framework\TestCase;
use Prozorov\DataVerification\Configuration;
use Prozorov\DataVerification\Repositories\FakeCodeRepo;

class ConfigurationTest extends TestCase
{
    public function testVerificationDataSetterAndGetter()
    {
        $data = [
            'pass_length' => 8,
            'creation_code_threshold' => 120,
            'limit_per_hour' => 25,
            'attempts' => 5,
            'password_validation_period' => 7200,
        ];

        $parameters = [
            'code_repository' => FakeCodeRepo::class,
            'passwords' => [
                'default' => [
                    'pass_length' => $data['pass_length'],
                    'creation_code_threshold' => $data['creation_code_threshold'],
                    'limit_per_hour' => $data['limit_per_hour'],
                    'attempts' => $data['attempts'],
                    'password_validation_period' => $data['password_validation_period'],
                ],
            ],
        ];

        $config = new Configuration(null, $parameters);

        $this->assertEquals($config->getPassLength(), $data['pass_length']);
    }
}
