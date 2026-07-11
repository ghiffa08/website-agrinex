<?php

namespace App\Repositories\Contracts;

interface IrrigationRepositoryInterface
{
    public function createIrrigationLog(array $data);
    public function createIrrigateLog(array $data);
    public function createValveLog(array $data);
    public function getLatestCompleted();
    public function getHistory($filters, $limit);
}
