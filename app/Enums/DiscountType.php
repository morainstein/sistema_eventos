<?php

namespace App\Enums;

enum DiscountType: string
{
  case FIXED = 'FIXED';
  case PERCENTAGE = 'PERCENTAGE';
}