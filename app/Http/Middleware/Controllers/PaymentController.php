<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\TransactionLog;
use App\Models\StudentLog;
use Auth;
use Session;
use App\Http\Controllers\API;

class PaymentController extends Controller
{
  private $hash = 'xjskdjos8e-!@298wijdijahsjabuw';
  
    public function index()
    {
        $transaction = Session::get('transaction');
        
        if(!isset($transaction['code'])){
          $inv_prefix = 'INV-'.date('y').'-'.date('m').'-';
          $last_transaction = Transaction::where('code','like','%'.$inv_prefix.'%')->orderBy('id','desc')->limit(1)->select('code')->get();
          if(count($last_transaction)>0){
            $last_code_arr = explode('-',$last_transaction[0]->code);
            $last_code = (int) end($last_code_arr) + 1;
          }
          $new_code = $inv_prefix . str_pad($last_code,'4','0',STR_PAD_LEFT);
        
          
          $transaction_obj = new Transaction;
          $transaction_obj->code = $new_code;
          $transaction_obj->customer_id = $transaction['customerid'];
          $transaction_obj->payment_method = $transaction['payment_method'];
          $transaction_obj->total = $transaction['total'];
          $transaction_obj->instalment = $transaction['instalment'];
          $transaction_obj->transaction_date = date('Y-m-d');
          $transaction_obj->status = 0;
          $transaction_obj->save();
          
          $transaction['code'] = $new_code;
          Session::put('transaction',$transaction);
          
          
          foreach($transaction['items'] as $k => $item ){
            foreach($item as $item_detail){
              $transaction_line_obj = new TransactionLine;
              $transaction_line_obj->transaction_code = $new_code;
              $transaction_line_obj->item_code = $item_detail['code'];
              $transaction_line_obj->price = $item_detail['price'];
              $transaction_line_obj->qty = $item_detail['qty'];
              
              if($item_detail['item_type'] == 'donation'){
                $transaction_line_obj->description = 'Scholarship Donation ' . $item_detail['qty'] . $item_detail['qty']>1? ' years' : ' year' .' for '. $item_detail['student']['name'];
                $transaction_line_obj->item = json_encode($item_detail['student']);
              }else{
                $transaction_line_obj->description = $item_detail['name'];
              }                            
              $transaction_line_obj->save();
            }
          }
          
          
        }

        $html = '';
        $html .= "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>";
        $html .= "<script>$.fn.serializeObject=function(){var e={},i=this.serializeArray();return $.each(i,function(){e[this.name]?(e[this.name].push||(e[this.name]=[e[this.name]]),e[this.name].push(this.value||\"\")):e[this.name]=this.value||\"\"}),e};</script>";
    
        switch($transaction['payment_method']){
          case '1':
            $html .= $this->DokuController($transaction);
            break;
          default:
            $html .= "Payment not supported.";
            break;
        }
        
        if(Session::get('isdebug') > 0){
      $html .= '<script>$(function(){$("body").append("<pre>" + JSON.stringify($("form").serializeObject(), null, 2) + "</pre>");});</script>';
      $html .= '<button onclick=\'$("form").submit()\'>Submit</button>';
    }

      file_put_contents(storage_path('logs') . '/payment.log', "[" . date('Y-m-d H:i:s') . "]\n" . $html . "\n\n", FILE_APPEND);

      return $html;
    }
    
    function checkPayment(Request $request){
      $params = $request->all();
      $cart = Session::get('cart');
      
      if(count($cart)>0){
        $total = 0;
        foreach($cart as $k => $items){
          foreach($items as $items_detail){
            if($items_detail['item_type'] == 'donation'){
              $total += $items_detail['price'] * $items_detail['qty'] * 12;
            }else{
              $total += $items_detail['price'] * $items_detail['qty']; 
            }            
          }          
        }
      }else{
        return json_encode(array(
            'error' => 1,
            'message' => 'not valid transaction, please contact admin',
        ));
      }
      
      $transaction = [
          'payment_method' => 1,
          'instalment' => $params['payment_type'] === 'fp'? 0 : 1,
          'total' => $total,
          'items' => $cart,
          'customerid' => Session::get('user')['id'],          
      ];
      
      Session::put('transaction', $transaction);
      
      return json_encode(array(
          'error' => '0',
          'data' => $transaction,
          'message' => '',
      ));
      
    }
    
    function DokuController($transaction){

    $payment_params = [
        "MALLID" => "5292",
        "CHAINMERCHANT" => "NA",
        "currency" => "IDR",
        "SHAREDKEY" => "4OIt1v0W6jTz",
        "redirectURL" => "https://staging.doku.com/Suite/Receive",
//        "redirectURL" => "https:\/\/pay.doku.com\/Suite\/Receive"
    ];

    if($transaction['instalment'] == '1'){
      $payment_params['PAYMENTCHANNEL'] = 17;
    }else{
      $payment_params['PAYMENTCHANNEL'] = 15;
    }
    $customer = Session::get('user');
    
    $transactionid = $transaction['code'];
    $customer_name = isset($customer['name']) && $customer['name'] !== ''? $customer['name']: $customer['email'];
    $customer_email = $customer['email'];
    $customer_id = $customer['id'];
    $products = $transaction['items'];
    $sessionid = Session::get('id');

    $mallid = $payment_params['MALLID'];
    $sharedkey = $payment_params['SHAREDKEY'];
    $payment_channel = $payment_params['PAYMENTCHANNEL'];
    $chainmerchant = $payment_params['CHAINMERCHANT'];
    $redirectURL = $payment_params['redirectURL'];
    $doku_currency = $payment_params['currency'];

    
    
    $basket = [];
    $total_recurement = 0;
    foreach($products as $k => $items) {
      foreach($items as $detail){
        if($detail['item_type'] == 'donation'){
          if($transaction['instalment'] == '1'){
            $basket[] = $detail['name'].",".number_format($detail['price'] , 2, '.', '').",1,".number_format($detail['price'] , 2, '.', '');
          }else{
            $basket[] = $detail['name'].",".number_format($detail['price'] * $detail['qty'] * 12, 2, '.', '').",1,".number_format($detail['price'] * $detail['qty'] * 12, 2, '.', '');
          }          
        }else{
          $basket[] = $detail['name'].",".number_format($detail['price'] , 2, '.', '').",".$detail['qty'].",".number_format($detail['price'] * $detail['qty'], 2, '.', '');
        }
        
        if($transaction['instalment'] == '1'){
          $recurement_month = $detail['qty'] * 12;
        }
        
        $total_recurement += $detail['price']; 
      }        
    }
    
    if($transaction['instalment'] == '1'){
      $total = $total_recurement;
    }else{
      $total = $transaction['total'];
    }
    
    
    $doku_currency_code = "";
    switch($doku_currency) {
      case 'ARS':
          $doku_currency_code = '032';
          break;
      case 'AUD':
          $doku_currency_code = '036';
          break;
      case 'BRL':
          $doku_currency_code = '076';
          break;
      case 'CLP':
          $doku_currency_code = '152';
          break;
      case 'CNY':
          $doku_currency_code = '156';
          break;
      case 'GBP':
          $doku_currency_code = '826';
          break;
      case 'HKD':
          $doku_currency_code = '344';
          break;
      case 'IDR':
          $doku_currency_code = '360';
          break;
      case 'INR':
          $doku_currency_code = '356';
          break;
      case 'JPY':
          $doku_currency_code = '392';
          break;
      case 'MXN':
          $doku_currency_code = '484';
          break;
      case 'MYR':
          $doku_currency_code = '458';
          break;
      case 'NZD':
          $doku_currency_code = '554';
          break;
      case 'PHP':
          $doku_currency_code = '608';
          break;
      case 'SGD':
          $doku_currency_code = '702';
          break;
      case 'THB':
          $doku_currency_code = '764';
          break;
      case 'TTD':
          $doku_currency_code = '780';
          break;
      case 'TWD':
          $doku_currency_code = '901';
          break;
      case 'USD':
          $doku_currency_code = '840';
          break;
      case 'ZAR':
          $doku_currency_code = '710';
          break;
    }

    $html = [];
    $html[] = "<form action=\"".$redirectURL."\" id=\"MerchatPaymentPage\" name=\"MerchatPaymentPage\" method=\"post\" >";
    $html[] = "<input type='hidden' name=\"BASKET\" id=\"BASKET\" value=\"".implode(";", $basket)."\"/>";
    $html[] = "<input type='hidden' name=\"MALLID\" id=\"MALLID\" value=\"".$mallid."\"/>";
    $html[] = "<input type='hidden' name=\"CHAINMERCHANT\" id=\"CHAINMERCHANT\" value=\"".$chainmerchant."\"/>";
    $html[] = "<input type='hidden' name=\"CURRENCY\" id=\"CURRENCY\" value=\"".$doku_currency_code."\"/>";
    $html[] = "<input type='hidden' name=\"PURCHASECURRENCY\" id=\"PURCHASECURRENCY\" value=\"".$doku_currency_code."\"/>";
    $html[] = "<input type='hidden' name=\"AMOUNT\" id=\"AMOUNT\" value=\"".number_format($total, 2, '.', '')."\"/>";
    $html[] = "<input type='hidden' name=\"PURCHASEAMOUNT\" id=\"PURCHASEAMOUNT\" value=\"".number_format($total, 2, '.', '')."\" size=\"12\"/>";
    $html[] = "<input type='hidden' name=\"TRANSIDMERCHANT\" id=\"TRANSIDMERCHANT\" value=\"".$transactionid.date("dHis")."\"/>";
    $html[] = "<input type='hidden' id=\"WORDS\" name=\"WORDS\" value=\"".sha1(number_format($total, 2, '.', '') . "" . $mallid . $sharedkey . $transactionid.date("dHis") )."\"/>";
    $html[] = "<input type='hidden' name=\"REQUESTDATETIME\" id=\"REQUESTDATETIME\" value=\"".date("YmdHis")."\"/>";
    $html[] = "<input type='hidden' id=\"SESSIONID\" name=\"SESSIONID\" value=\"".$sessionid."\"/>";
    $html[] = "<input type='hidden' id=\"PAYMENTCHANNEL\" name=\"PAYMENTCHANNEL\" value=\"".$payment_channel."\"/>";
    $html[] = "<input type='hidden' name=\"EMAIL\" id=\"EMAIL\" value=\"".$customer_email."\"/>";
    $html[] = "<input type='hidden' name=\"NAME\" id=\"NAME\" value=\"".$customer_name."\"/>";
    
    
    if($transaction['instalment'] == '1'){
      $html[] = "<input type='hidden' name=\"CUSTOMERID\" id=\"CUSTOMERID\" value=\"".$customer_id."\"/>";
      $html[] = "<input type='hidden' name=\"BILLNUMBER\" id=\"BILLNUMBER\" value=\"".$transactionid."\"/>";
      $html[] = "<input type='hidden' name=\"BILLDETAIL\" id=\"BILLDETAIL\" value=\"".implode(";", $basket)."\"/>";
      $html[] = "<input type='hidden' name=\"BILLTYPE\" id=\"BILLTYPE\" value=\"I\"/>";
      $html[] = "<input type='hidden' name=\"STARTDATE\" id=\"STARTDATE\" value=\"". date("Ymd") ."\"/>";
      $html[] = "<input type='hidden' name=\"ENDDATE\" id=\"ENDDATE\" value=\"". date("Ymd", strtotime(date("Y-m-d") . " + ".$recurement_month." month")) ."\"/>";
      $html[] = "<input type='hidden' name=\"EXECUTETYPE\" id=\"EXECUTETYPE\" value=\"DATE\"/>";
      $html[] = "<input type='hidden' name=\"EXECUTEDATE\" id=\"EXECUTEDATE\" value=\"Monday\"/>";
      $html[] = "<input type='hidden' name=\"EXECUTEMONTH\" id=\"EXECUTEMONTH\" value=\"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec\"/>";
      $html[] = "<input type='hidden' name=\"FLATSTATUS\" id=\"FLATSTATUS\" value=\"TRUE\"/>";
    }
    $html[] = "</form>";
    if(!Session::get('isdebug')) $html[] = "<script type=\"text/javascript\">document.MerchatPaymentPage.submit();</script>";

    $transaction['info'] = sha1(number_format($total, 2, '.', '') . $mallid . $sharedkey . $transactionid.date("dHis"));
    Session::put('transaction',$transaction);
    
//    file_put_contents(storage_path('logs') . '/payment.doku.log', "[" . date('Y-m-d H:i:s') . "]\n" . print_r(number_format($total, 2, '.', '') . $mallid . $sharedkey . $transactionid.date("dHis"), 1) . "\n", FILE_APPEND);
//    file_put_contents(storage_path('logs') . '/payment.doku.log', "[" . date('Y-m-d H:i:s') . "]\n" . print_r(implode("\n", $html), 1) . "\n", FILE_APPEND);

    return implode("\n", $html);
  }
  
  function DokuNotifyController()
  {
      $data = array();
      $data['post'] = $_POST;
      file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r($data, 1) . "\n", FILE_APPEND);

      $transactionid = isset($data['post']['TRANSIDMERCHANT'])?substr($data['post']['TRANSIDMERCHANT'], 0, -8):$data['post']['BILLNUMBER'];
      
      if($data['post']['STATUSTYPE'] == 'P'){
      //normal payment  
        if(isset($data['post']['RESPONSECODE']) && $data['post']['RESPONSECODE'] === '0000' && $data['post']['RESULTMSG'] === 'SUCCESS'){

          try{
            $data = Transaction::where('code', $transactionid)->firstOrFail();
            $data->status = 1;
            $data->save();

            //insert transaction log
            $log = new TransactionLog;
            $log->transaction_code = $transactionid;        
            $log->log = json_encode($data);        
            $log->save();    

            $transaction_line = $data->transaction_lines;

file_put_contents(storage_path('logs') . '/test.log', print_r($transaction_line, 1) . "\n", FILE_APPEND);

            foreach($transaction_line as $transaction_line_detail){
              if($transaction_line_detail->item_code == 'SCH'){
                $student = json_decode($transaction_line_detail->item);
                file_put_contents(storage_path('logs') . '/test.log', print_r($student, 1) . "\n", FILE_APPEND);
                $student_log = new StudentLog;
                $student_log->student_id = $student->id;        
                $student_log->invoice_id = $transactionid;        
                $student_log->start_date = date('Y-m-d');        
                $student_log->end_date = date('Y-m-d', strtotime('+2 years'));;        
                $student_log->save();
                
                file_put_contents(storage_path('logs') . '/test.log', print_r($student_log->toSql(), 1) . "\n", FILE_APPEND);
                file_put_contents(storage_path('logs') . '/test.log', print_r($student_log, 1) . "\n", FILE_APPEND);
              }
            }
          } catch (Exception $ex) {
            file_put_contents(storage_path('logs') . '/error.log', print_r($ex, 1) . "\n", FILE_APPEND);
          }
          
          
              
          
  //        EmailTemplate::SendEmailUsingTemplate(1, array('to'=>$customer_email, 'cc'=>'florists@floweradvisor.com'), array('transaction'=>$transaction));

            file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('CONTINUE', 1) . "\n", FILE_APPEND);
            echo "CONTINUE";
        }
        elseif(isset($data['post']['RESPONSECODE']) && $data['post']['RESPONSECODE'] === '5511' && $data['post']['RESULTMSG'] === 'SUCCESS'){
          
          try{
            $data = Transaction::where('code', $transactionid)->firstOrFail();
            $data->status = 1;
            $data->save();

            //insert transaction log
            $log = new TransactionLog;
            $log->transaction_code = $transactionid;        
            $log->log = json_encode($data);        
            $log->save();    

            $transaction_line = $data->transaction_lines;

            foreach($transaction_line as $transaction_line_detail){
              if($transaction_line_detail->item_code == 'SCH'){
                $student = json_decode($transaction_line_detail->item);
                $student_log = new StudentLog;
                $student_log->student_id = $student->id;        
                $student_log->invoice_id = $transactionid;        
                $student_log->start_date = date('Y-m-d');        
                $student_log->end_date = date('Y-m-d', strtotime('+'.$transaction_line_detail->year.' years'));;        
                $student_log->save();
              }
            }
          } catch (Exception $ex) {
            file_put_contents(storage_path('logs') . '/error.log', print_r($ex, 1) . "\n", FILE_APPEND);
          }
          
          file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('CONTINUE', 1) . "\n", FILE_APPEND);
          echo "CONTINUE";
        }
        else{
          $data = Transaction::where('code', $transactionid)->firstOrFail();
          $data->status = 0;
          $data->save();
          
          $log = new TransactionLog;
          $log->transaction_code = $transactionid;        
          $log->log = json_encode($data);      
          $log->save();
          
          file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('STOP', 1) . "\n", FILE_APPEND);
          echo "STOP";
        }
      }else{
        //recurement
        if(isset($data['post']['ERRORCODE']) && $data['post']['ERRORCODE'] == '' && $data['post']['STATUS'] === 'SUCCESS'){

          $data = Transaction::where('code', $transactionid)->firstOrFail();
          $data->status = 1;
          $data->save();

          $log = new TransactionLog;
          $log->transaction_code = $transactionid;        
          $log->log = json_encode($data);      
          $log->save();
          
  //        EmailTemplate::SendEmailUsingTemplate(1, array('to'=>$customer_email, 'cc'=>'florists@floweradvisor.com'), array('transaction'=>$transaction));
          file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('CONTINUE', 1) . "\n", FILE_APPEND);
          echo "CONTINUE";
        }else{
          $data = Transaction::where('code', $transactionid)->firstOrFail();
          $data->status = 0;
          $data->save();
          
          $log = new TransactionLog;
          $log->transaction_code = $transactionid;        
          $log->log = json_encode($data);      
          $log->save();
          
          file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('STOP', 1) . "\n", FILE_APPEND);
          echo "STOP";
        }
      }
      
  }
  
  function generateUpdateDokuHtml($transactionid){
    $payment_params = [
        "MALLID" => "5292",
        "CHAINMERCHANT" => "NA",
        "currency" => "IDR",
        "SHAREDKEY" => "4OIt1v0W6jTz",
        "PAYMENTCHANNEL" => "17",
        "redirectURL" => "https://staging.doku.com/Suite/RecurUpdateCard",
//        "redirectURL" => "https:\/\/pay.doku.com\/Suite\/Receive"
    ];
    
    $data = Transaction::where('code', $transactionid)->firstOrFail();
    if($data){
    $customer = $data->customer;
    $customer_name = isset($customer['name']) && $customer['name'] !== ''? $customer['name']: $customer['email'];
    $customer_email = $customer['email'];
    $customer_id = $customer['id'];
    
    $mallid = $payment_params['MALLID'];
    $sharedkey = $payment_params['SHAREDKEY'];
    $payment_channel = $payment_params['PAYMENTCHANNEL'];
    $chainmerchant = $payment_params['CHAINMERCHANT'];
    $redirectURL = $payment_params['redirectURL'];
    $doku_currency = $payment_params['currency'];
    $sessionid = Session::get('id');
    $doku_currency_code = "";
    switch($doku_currency) {
      case 'ARS':
          $doku_currency_code = '032';
          break;
      case 'AUD':
          $doku_currency_code = '036';
          break;
      case 'BRL':
          $doku_currency_code = '076';
          break;
      case 'CLP':
          $doku_currency_code = '152';
          break;
      case 'CNY':
          $doku_currency_code = '156';
          break;
      case 'GBP':
          $doku_currency_code = '826';
          break;
      case 'HKD':
          $doku_currency_code = '344';
          break;
      case 'IDR':
          $doku_currency_code = '360';
          break;
      case 'INR':
          $doku_currency_code = '356';
          break;
      case 'JPY':
          $doku_currency_code = '392';
          break;
      case 'MXN':
          $doku_currency_code = '484';
          break;
      case 'MYR':
          $doku_currency_code = '458';
          break;
      case 'NZD':
          $doku_currency_code = '554';
          break;
      case 'PHP':
          $doku_currency_code = '608';
          break;
      case 'SGD':
          $doku_currency_code = '702';
          break;
      case 'THB':
          $doku_currency_code = '764';
          break;
      case 'TTD':
          $doku_currency_code = '780';
          break;
      case 'TWD':
          $doku_currency_code = '901';
          break;
      case 'USD':
          $doku_currency_code = '840';
          break;
      case 'ZAR':
          $doku_currency_code = '710';
          break;
    }

      $html = [];
      $html[] = "<form action=\"".$redirectURL."\" id=\"MerchatPaymentPage\" name=\"MerchatPaymentPage\" method=\"post\" >";
      $html[] = "<input type='hidden' name=\"MALLID\" id=\"MALLID\" value=\"".$mallid."\"/>";
      $html[] = "<input type='hidden' name=\"CHAINMERCHANT\" id=\"CHAINMERCHANT\" value=\"".$chainmerchant."\"/>";
      $html[] = "<input type='hidden' name=\"TRANSIDMERCHANT\" id=\"TRANSIDMERCHANT\" value=\"".$transactionid.date("dHis")."\"/>";
      $html[] = "<input type='hidden' id=\"WORDS\" name=\"WORDS\" value=\"".sha1($mallid . $chainmerchant .$transactionid . $customer_id .$sharedkey )."\"/>";
      $html[] = "<input type='hidden' name=\"REQUESTDATETIME\" id=\"REQUESTDATETIME\" value=\"".date("YmdHis")."\"/>";
      $html[] = "<input type='hidden' id=\"SESSIONID\" name=\"SESSIONID\" value=\"".$sessionid."\"/>";
      $html[] = "<input type='hidden' id=\"CUSTOMERID\" name=\"CUSTOMERID\" value=\"".$customer_id."\"/>";
      $html[] = "<input type='hidden' name=\"PAYMENTCHANNEL\" id=\"PAYMENTCHANNEL\" value=\"".$payment_channel."\"/>";
      $html[] = "<input type='hidden' name=\"BILLNUMBER\" id=\"BILLNUMBER\" value=\"".$transactionid."\"/>";

      $html[] = "</form>";
      if(!Session::get('isdebug')) $html[] = "<script type=\"text/javascript\">document.MerchatPaymentPage.submit();</script>";

      file_put_contents(storage_path('logs') . '/incuring.verify.doku.log', implode("\n", $html) , FILE_APPEND);
      
      return implode("\n", $html);
    
    }else{
      file_put_contents(storage_path('logs') . '/incuring.verify.doku.log', "[" . date('Y-m-d H:i:s') . "] \n generate html : ". print_r($payment_params,1) ."\n", FILE_APPEND);
      return; 
    }
  }
  
  function UpdateDokuController($transaction_code){
    $transaction_code_deccode = explode('*',base64_decode($transaction_code));
    
    if(isset($transaction_code_deccode[1])){
      $transactionId = $transaction_code_deccode[1];
      $html = '';
      $html .= "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>";
      $html .= "<script>$.fn.serializeObject=function(){var e={},i=this.serializeArray();return $.each(i,function(){e[this.name]?(e[this.name].push||(e[this.name]=[e[this.name]]),e[this.name].push(this.value||\"\")):e[this.name]=this.value||\"\"}),e};</script>";
      $html .= $this->generateUpdateDokuHtml($transactionId);
      $html .= '<script>$(function(){$("body").append("<pre>" + JSON.stringify($("form").serializeObject(), null, 2) + "</pre>");});</script>';
      $html .= '<button onclick=\'$("form").submit()\'>Submit</button>';
    }else{
      file_put_contents(storage_path('logs') . '/incuring.verify.doku.log', "[" . date('Y-m-d H:i:s') . "] \n transaction code: ". $transaction_code ."\n", FILE_APPEND);
      return redirect()->route('front.home')->with('status', 'Please contact your administrator!');
    } 
  }
  
  function UpdateProcessNotify(){
    $data = array();
    $data['post'] = $_POST;
    file_put_contents(storage_path('logs') . '/update.recuring.notify.doku.log', date("Y-m-d H:i:s"). '\n' . print_r($data, 1) . "\n", FILE_APPEND);

    if(isset($data['post']['STATUS']) && $data['post']['STATUS'] == 'SUCCESS'){
      return "<script>window.location = 'thankyou';</script>";
    }else{
      return "<script>window.location = 'failed';</script>";
    }
            
  }
  
  function DokuRecuringNotifyController()
  {
      $data = array();
      $data['post'] = $_POST;
      file_put_contents(storage_path('logs') . '/payment.recuring.notify.doku.log', print_r($data, 1) . "\n", FILE_APPEND);

      $transactionid = isset($data['post']['TRANSIDMERCHANT'])?substr($data['post']['TRANSIDMERCHANT'], 0, -8):$data['post']['BILLNUMBER'];
      
      $log = new TransactionLog;
      $log->transaction_code = $transactionid;        
      $log->log = json_encode($data);       
      $log->save();

      if($data['post']['RESPONSECODE'] == '0054'){
        //credit card expired do send email

        $data = Transaction::where('code', $transactionid)->firstOrFail();
        if($data){
          $customer = $data->customer;
          $arr['template'] = 'emails.doku_recuring_update';
          $arr['from_email'] = 'optimus@funedge.co.id';
          $arr['email'] = $customer->email;
          $arr['name'] = $customer->name;
          $arr['subject'] = 'Credit card have been expired';
          $arr['invoice_number'] = $transactionid;
          $arr['link'] = route('front.updateDoku',['payment_code'=>base64_encode($this->hash.'*'.$transactionid.'*'.$this->hash)]);
          
          API::sendEmail($arr);
        }
      }
      
      file_put_contents(storage_path('logs') . '/payment.notify.doku.log', print_r('CONTINUE', 1) . "\n", FILE_APPEND);
      echo "CONTINUE";
      
      
      
    }
    
  function test(){
    $transaction = 'INV-18-11-0002';
    $encode = base64_encode($this->hash.'*'.$transaction.'*'.$this->hash);
    echo $encode.'<br>';
    echo base64_decode($encode);
    $data = Transaction::where('code', $transaction)->firstOrFail();
    if($data){
      $customer = $data->customer;
      echo $customer->email;
      dd($customer);
    }
    
    
    
  }  
  
  function DokuRedirectController()
  {

      file_put_contents(storage_path('logs') . '/payment.redirect.doku.log', print_r($_REQUEST, 1) . "\n", FILE_APPEND);
      $transaction = Session::get('transaction');

      if(count($transaction) == 0){
        return "<script>window.location = 'failed';</script>";
      }
      
      $transactionid = $transaction['code'];
      // 1. Process
      // - Save payment code if exists
      if(isset($_REQUEST['PAYMENTCODE'])){
        Session::put('DOKU_PAYMENTCODE', isset($_REQUEST['PAYMENTCODE']) ? $_REQUEST['PAYMENTCODE'] : ''); // if exists, save to session
      }
      if (isset($_REQUEST['TRANSIDMERCHANT'])) {
        Session::put('DOKU_TRANSIDMERCHANT', isset($_REQUEST['TRANSIDMERCHANT']) ? $_REQUEST['TRANSIDMERCHANT'] : '');
      }
      $transactionid = isset($_REQUEST['TRANSIDMERCHANT'])?substr($_REQUEST['TRANSIDMERCHANT'], 0, -8):'';
      
      if($transactionid !== ''){
        $transaction = Transaction::where('code', $transactionid)->firstOrFail();
        if($transaction->status == 1){
          return "<script>window.location = 'thankyou';</script>";
        }else{
          return "<script>window.location = 'failed';</script>";
        }
        
      }else{
        return "<script>window.location = 'failed';</script>";
      }

  }
    
}
