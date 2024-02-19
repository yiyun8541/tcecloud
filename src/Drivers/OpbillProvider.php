<?php

namespace Cloud\TceCloud\Drivers;

use Cloud\TceCloud\AbstractProvider;

class OpbillProvider extends AbstractProvider
{
    protected function setClient()
    {

        $this->clientInit();
        $this->client = new $this->clientClass($this->credential, "", $this->clientProfile);
    }

}
