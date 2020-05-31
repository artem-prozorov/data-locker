<?php

namespace Prozorov\DataVerification\Integrations\Bitrix\Repositories;

use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Integrations;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;
use Bitrix\Main\Type\DateTime as BxDatetime;
use Datetime;

class CodeRepo implements CodeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function save(Code $code): Code
    {
        if ($code->isNew()) {
            return $this->create($code);
        }

        return $this->update($code);
    }

    /**
     * @inheritDoc
     */
    public function delete(Code $code)
    {
        if ($code->isNew()) {
            throw new \Bitrix\Main\SystemException('Не удалось удалить данные');
        }

        $result = Integrations\Bitrix\CodesTable::delete($code->getId());

        if (! $result->isSuccess()) {
            throw new \Bitrix\Main\SystemException('Не удалось удалить данные');
        }
    }

    /**
     * @inheritDoc
     */
    public function create(Code $code): Code
    {
        $result = Integrations\Bitrix\CodesTable::add([
            'VERIFICATION_CODE' => $code->getVerificationCode(),
            'ADDRESS' => $code->getAddress()->toString(),
            'PASS' => $code->getOneTimePass(),
            'ATTEMPTS' => $code->getAttempts(),
            'VALIDATED' => $code->isValidated() ? 'Y' : 'N',
            'CREATED_AT' => BxDatetime::createFromPhp($code->getCreatedAt()),
            'DATA' => $code->getVerificationData(),
        ]);

        if (!$result->isSuccess()) {
            throw new \Bitrix\Main\SystemException('Не удалось сохранить код');
        }

        $code->setId($result->getId());

        return $code;
    }

    /**
     * @inheritDoc
     */
    public function update(Code $code): Code
    {
        if ($code->isNew()) {
            throw new \Bitrix\Main\SystemException('Не удалось обновить данные: указанный код не найден в БД');
        }

        $data = [
            'VERIFICATION_CODE' => $code->getVerificationCode(),
            'ADDRESS' => $code->getAddress()->toString(),
            'PASS' => $code->getOneTimePass(),
            'ATTEMPTS' => $code->getAttempts(),
            'VALIDATED' => $code->isValidated() ? 'Y' : 'N',
            'CREATED_AT' => BxDatetime::createFromPhp($code->getCreatedAt()),
            'DATA' => $code->getVerificationData(),
        ];

        $result = Integrations\Bitrix\CodesTable::update($code->getId(), $data);

        if (!$result->isSuccess()) {
            throw new \Bitrix\Main\SystemException(
                'Не удалось обновить данные: ' . implode(', ', $result->getErrorMessages())
            );
        }

        return $code;
    }

    /**
     * @inheritDoc
     */
    public function getOneUnvalidatedByCode(string $code, Datetime $createdAfter = null)
    {
        $params = [
            'filter' => [
                'VERIFICATION_CODE' => $code,
                'VALIDATED' => 'N',
            ],
        ];

        return $this->getData($params, $createdAfter);
    }

    /**
     * @inheritDoc
     */
    public function getLastCodeForAddress(Address $address, Datetime $createdAfter = null)
    {
        $params = [
            'filter' => [
                'ADDRESS' => $address->toString(),
                'VALIDATED' => 'N',
                'CREATED_AT' => DateTime::createFromPhp($createdAfter),
            ],
        ];

        return $this->getData($params, $createdAfter);
    }

    /**
     * @inheritDoc
     */
    public function getValidatedCode(string $verificationCode)
    {
        $params = ['filter' => [
            'VERIFICATION_CODE' => $verificationCode,
            'VALIDATED' => 'Y',
        ]];

        return $this->getData($params);
    }

    /**
     * @inheritDoc
     */
    public function getCodesCountForAddress(Address $address, Datetime $createdAfter = null): ?int
    {
        $params = [
            'runtime' => [
                new \Bitrix\Main\ORM\Fields\ExpressionField('CNT', 'COUNT(*)')
            ],
            'filter' => [
                'ADDRESS' => $address->toString(),
                'VALIDATED' => 'N',
            ],
        ];

        if (!empty($createdAfter)) {
            $params['filter']['>CREATED_AT'] = $createdAfter->format('Y-m-d H:i:s');
        }

        $row = Integrations\Bitrix\CodesTable::getList($params)->fetch();
        if (empty($row)) {
            return 0;
        }

        return (int) $row['CNT'];
    }

    /**
     * getData.
     *
     * @access	protected
     * @param	array   	$params      	
     * @param	DateTime	$createdAfter	Default: null
     * @return	Code|null
     */
    protected function getData(array $params, Datetime $createdAfter = null): ?Code
    {
        if (!empty($createdAfter)) {
            $params['filter']['>CREATED_AT'] = $createdAfter->format('Y-m-d H:i:s');
        }

        $row = Integrations\Bitrix\CodesTable::getList($params)->fetch();

        if (empty($row)) {
            return null;
        }

        $row['CREATED_AT'] = (new Datetime())->setTimestamp($row['CREATED_AT']->getTimestamp());

        $code = new Code($row);

        return $code;
    }
}
