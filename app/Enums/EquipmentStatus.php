<?php

namespace App\Enums;

enum EquipmentStatus: string
{
    case NUEVO = 'nuevo';
    case USADO = 'usado';
    case RENTA = 'renta';
    case REPARACION = 'reparacion';
}
