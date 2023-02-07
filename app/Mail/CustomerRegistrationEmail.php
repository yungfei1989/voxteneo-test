<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API;

use Session;
use DB;

class CustomerRegistrationEmail extends Mailable
{
  use Queueable, SerializesModels;

  private $name;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($params){

    $this->name = $params['name'];
    $this->email = $params['email'];

  }

  /**
   * Build the message.
   *
   * @return $this
   */

  public function build(){

      $params = [
          'name' => $this->name,
          'link' => '/activate?email='.$this->email
      ];
      $subject = 'Thankyou for registration at our website';
      $email = $this->view('emails.customer-registration-collection', $params)
        ->subject($subject);

      return $email;

  }
}
