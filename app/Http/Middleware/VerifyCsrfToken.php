<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
      'payment.notify.doku',
      'payment.redirect.doku',
      'payment.notify.doku.recuring',
      'payment.notify.doku.recuring.update',  
      'thankyou'  
    ];
}
