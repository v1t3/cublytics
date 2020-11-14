<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Class CodeGenerator
 *
 * @package App\Service
 */
class CodeGenerator
{
    /**
     *
     */
    public const RANDOM_STRING = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @return string
     * @throws \Exception
     */
    public function getConfirmationCode(): string
    {
        $stringLength = strlen(self::RANDOM_STRING);
        $code = '';

        for ($i = 0; $i < $stringLength; $i++) {
            $code .= self::RANDOM_STRING[random_int(0, $stringLength - 1)];
        }

        return $code;
    }
}