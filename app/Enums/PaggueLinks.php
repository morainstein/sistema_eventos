<?php

namespace App\Enums;

enum PaggueLinks: string
{
  case CREATE_PIX_STATIC = 'https://ms.paggue.io/cashin/api/billing_order';
  case WEBHOOK_MANAGE_URL = 'https://ms.paggue.io/client/integration/webhooks';
}