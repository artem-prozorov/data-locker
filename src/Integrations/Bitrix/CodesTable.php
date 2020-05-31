<?php

namespace Prozorov\DataVerification\Integrations\Bitrix;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\ArrayField;

class CodesTable extends DataManager
{
    public static function getTableName()
	{
		return 'prozorov_data_verification_codes';
    }

    public static function getMap()
	{
		return [
			'ID' => [
				'data_type' => 'integer',
				'primary' => true,
            ],
			'VERIFICATION_CODE' => [
				'data_type' => 'string',
			],
			'ADDRESS' => [
				'data_type' => 'string',
            ],
			'PASS' => [
				'data_type' => 'string',
            ],
			'ATTEMPTS' => [
				'data_type' => 'integer',
            ],
			'VALIDATED' => [
				'data_type' => 'boolean',
				'values' => ['N','Y'],
			],
			'CREATED_AT' => [
				'data_type' => 'datetime',
			],
			(new ArrayField('DATA')),
        ];
	}
}
