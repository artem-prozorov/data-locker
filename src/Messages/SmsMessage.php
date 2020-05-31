<?php

namespace Prozorov\DataVerification\Messages;

class SmsMessage extends AbstractMessage
{
    /**
     * @var string $template
     */
    protected $template = 'Здравствуйте, Ваш код подтверждения: #OTP#';

    public function __construct()
    {
        $this->via('sms');
    }
}
