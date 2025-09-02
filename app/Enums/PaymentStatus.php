<?php

namespace App\Enums;

enum PaymentStatus: string
{
  case PENDING = 'PENDING';
  case COMPLETED = 'COMPLETED';
  case FAILED = 'FAILED';
}