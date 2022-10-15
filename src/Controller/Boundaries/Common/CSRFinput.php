<?php

declare(strict_types=1);

namespace App\Controller\Boundaries\Common;

trait CSRFinput
{
    private string $_token;

    public function get_token(): string
    {
        return $this->_token;
    }

    public function set_token(string $_token): void
    {
        $this->name = $_token;
    }
}
