<?php

namespace App\Repositories\Contracts;

interface SessionRepositoryInterface
{
    public function findOrCreateSession($sessionId, array $defaults = []);
    public function createGetdataLog(array $data);
    public function getLatestSession();
    public function getSessionsInRange($startTime, $endTime);
    public function getSessionsWithLimit($limit);
}
