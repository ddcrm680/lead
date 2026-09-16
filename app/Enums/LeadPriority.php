<?php

namespace App\Enums;

enum LeadPriority: int
{
    case LOW = 10;
    case NORMAL = 20;
    case HIGH = 30;
    case URGENT = 40;
}