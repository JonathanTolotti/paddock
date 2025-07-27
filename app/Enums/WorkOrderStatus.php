<?php

namespace App\Enums;

enum WorkOrderStatus: string
{
    case BUDGET = 'budget';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::BUDGET => 'Orçamento',
            self::APPROVED => 'Aprovado',
            self::IN_PROGRESS => 'Em Andamento',
            self::FINISHED => 'Finalizado',
            self::CANCELED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BUDGET => 'secondary',
            self::APPROVED => 'default',
            self::IN_PROGRESS => 'outline',
            self::FINISHED => 'success',
            self::CANCELED => 'destructive',
        };
    }
}
