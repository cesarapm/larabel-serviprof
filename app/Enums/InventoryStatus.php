<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case DISPONIBLE = 'disponible';
    case RENTADO = 'rentado';
    case VENDIDO = 'vendido';
    case MANTENIMIENTO = 'mantenimiento';
}
