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
     * @return	void
     */
    public function send(AbstractMessage $mesage): void;
}
