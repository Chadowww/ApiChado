<?php

namespace App\Services\EntityServices;

use App\Entity\{Apply , Candidate, Company, Contract, JobOffer, Resume, SocialMedia, User};

class EntityBuilder
{
    /**
     * Method to build an entity with the given data
     * @param Apply|Candidate|Company|Contract|JobOffer|Resume|SocialMedia|User $object
     * @param array $data
     * @return Apply|Candidate|Company|Contract|JobOffer|Resume|SocialMedia|User
     */
    public function buildEntity(
        Apply | Candidate | Company | Contract | JobOffer | Resume | SocialMedia | User  $object,
        array $data
    ): Apply | Candidate | Company | Contract | JobOffer | Resume | SocialMedia | User
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucwords($key);
            if (method_exists($object, $method)) {
                $object->$method($value);
            }
        }
        return $object;
    }
}