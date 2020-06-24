<?php

namespace Prozorov\DataVerification\Contracts;

use Prozorov\DataVerification\Messages\AbstractMessage;

interface TransportInterface
{
    /**
     * send.
     *
     * @access	public
     * @param	Address	$address
     * @param	string 	$text
     * @throws  \Prozorov\DataVerification\Exceptions\TransportException
     * @return	void
     */
    public function send(AbstractMessage $mesage): void;
}
