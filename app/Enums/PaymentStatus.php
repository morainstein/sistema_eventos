<?php

namespace App\Enums;

enum PaymentStatus: string
{
  case PENDING = 'PENDING';
  case PAYED = 'PAYED';
  case CANCELLED = 'CANCELLED';
  case VOIDED = 'VOIDED';

  static public function statusCodeFromPixPaggue(int $statusCode)
  {
    return match($statusCode){
      0 => self::PENDING->value,
      1 => self::PAYED->value,
      3 => self::CANCELLED->value,
      4 => self::VOIDED->value,
      default => null
    };
  }

  static public function isPayed(int $statusCode) 
  {
    return match($statusCode){
      1 => true,
      default => false
    };
  }
}