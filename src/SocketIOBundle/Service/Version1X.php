<?php

namespace SocketIOBundle\Service;

use ElephantIO\Engine\SocketIO\Version1X as Version1Xoriginal;

class Version1X extends Version1Xoriginal
{
    /** {@inheritDoc} */
    public function close()
    {
        parent::close();
        $this->session = null;
    }
}
