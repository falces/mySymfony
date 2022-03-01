<?php

namespace App\Controller;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public const KEY_MESSAGE = "message";
    public const KEY_RESULT  = "result";
    public const KEY_DATA    = "data";

    public const RESPONSE_OK = "ok";
    public const RESPONSE_KO = "ko";

    /**
     * @param bool $result
     * @param string $message
     * @param array $data
     * @return array
     */
    #[ArrayShape([self::KEY_RESULT => "string", self::KEY_MESSAGE => "string", self::KEY_DATA => "array"])]
    protected function getResultData(
        bool $result,
        string $message,
        array $data): array
    {
        if ($result) {
            $result = self::RESPONSE_OK;
        } else {
            $result = self::RESPONSE_KO;
        }

        return [
            self::KEY_RESULT => $result,
            self::KEY_MESSAGE => $message,
            self::KEY_DATA => $data,
        ];
    }
}