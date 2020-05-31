<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Messages;

use Prozorov\DataVerification\App\Configuration;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;

abstract class AbstractMessage
{
    /**
     * @var string $template
     */
    protected $template;

    /**
     * @var string $transportCode
     */
    protected $transportCode;

    /**
     * @var Address $address
     */
    protected $address;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * via.
     *
     * @access	public
     * @param	string	$code	
     * @return	AbstractMessage
     */
    public function via(string $code): AbstractMessage
    {
        if (! Configuration::getInstance()->getTransportFactory()->entityExists($code)) {
            throw new \InvalidArgumentException('Такой метод доставки сообщения '.$code.' недоступен');
        }

        $this->transportCode = $code;

        return $this;
    }

    /**
     * setCode.
     *
     * @access	public
     * @param	Code	$code	
     * @return	AbstractMessage
     */
    public function setCode(Code $code): AbstractMessage
    {
        $this->code = $code;

        return $this;
    }

    /**
     * getCode.
     *
     * @access	public
     * @return	Code
     */
    public function getCode(): Code
    {
        if (empty($this->code)) {
            throw new \InvalidArgumentException('Не установлен одноразовый пароль');
        }

        return $this->code;
    }

    /**
     * setTemplate.
     *
     * @access	public
     * @param	string	$text	
     * @return	AbstractMessage
     */
    public function setTemplate(string $text): AbstractMessage
    {
        $this->text = $text;

        return $this;
    }

    /**
     * getTemplate.
     *
     * @access	public
     * @return	string
     */
    public function getTemplate(): string
    {
        if (empty($this->template)) {
            throw new \InvalidArgumentException('Не установлен шаблон сообщения');
        }

        return $this->template;
    }

    /**
     * setAddress.
     *
     * @access	public
     * @param	Address	$address	
     * @return	AbstractMessage
     */
    public function setAddress(Address $address): AbstractMessage
    {
        $this->address = $address;

        return $this;
    }

    /**
     * getAddress.
     *
     * @access	public
     * @return	Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * send.
     *
     * @access	public
     * @return	void
     */
    public function send(): void
    {
        $text = $this->render();

        $transport = Configuration::getInstance()->getTransportFactory()->make($this->transportCode);

        $transport->send($this->getAddress(), $text);
    }

    /**
     * render.
     *
     * @access	public
     * @return	string
     */
    public function render(): string
    {
        return str_replace('#OTP#', $this->getCode()->getOneTimePass(), $this->getTemplate());
    }
}
