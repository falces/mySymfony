<?php

namespace App\Service;

class ParamCheck
{
    public function checkParams(
        array $requiredParams,
        array $requestedParams,
        bool  $allowEmpty = true,
    ): bool
    {
        if(!$allowEmpty && empty($requestedParams)){
            return false;
        }

        foreach ($requiredParams as $param) {
            if(!in_array($param, $requestedParams)){
                return false;
            }
        }
        return true;
    }
}