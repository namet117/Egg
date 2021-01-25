<?php


namespace App\Traits;


trait ServiceTrait
{
    private string $msg = '';

    private int $code = 0;

    protected function setError(string $msg): void
    {
        $this->msg = $msg;
    }

    public function getError(): string
    {
        return $this->msg;
    }

    protected function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
