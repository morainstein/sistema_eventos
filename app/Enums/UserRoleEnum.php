<?php

namespace App\Enums;

enum UserRoleEnum: string
{
  case ADMIN = 'ADMIN';
  case PROMOTER = 'PROMOTER';
  case CUSTOMER = 'CUSTOMER';
}