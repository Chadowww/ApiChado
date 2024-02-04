<?php

namespace App\Services\EntityServices;

use App\Entity\Apply;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

/**
 *  Service for creating and updating Apply entity
 */
class ApplyService
{

    /**
     * @throws \JsonException
     */
    public function buildApply(Apply $apply, array $data): Apply
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucwords($key);
            if (method_exists($apply, $method)) {
                $apply->$method($value);
            }
        }
        return $apply;
    }
}