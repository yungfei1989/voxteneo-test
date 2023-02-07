<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Models\Setting;
use Request;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\Mail;

class API {

  public static function currency_format($nominal) {
    return number_format($nominal, 0, '.', ',');
  }

  public static function getDefaultConfig() {
    $defaultConfig = Setting::whereIn('field',array('phone', 'facebook', 'address', 'email', 'whatsapp','instagram','image_size'))->get();
    $config = array();

    foreach ($defaultConfig as $defaultConfig_detail) {
      $config[$defaultConfig_detail->field] = $defaultConfig_detail->value;
    }

    return $config;
  }

  public static function getSetting($setting_name) {

    $setting = Front_model::getSetting($setting_name);
    if (is_array($setting)) {
      if (count($setting) == 1) {
        return $setting[0]->value;
      }
      if (count($setting) == 0){
        return '';
      }
      else {
        return $setting;
      }
    } else {
      return '';
    }
  }

  public static function sendEmail($obj) {
    $template = API::attrGet('template', $obj);
    $input = $obj;
    $sent = Mail::send($template, $obj, function($messageEmail) use ($input) {

              $from_email = API::attrGet('from_email', $input);
              $subject = API::attrGet('subject', $input);
              $swiftMessage = $messageEmail->getSwiftMessage();

              $headers = $swiftMessage->getHeaders();
              $headers->addTextHeader('x-mailgun-native-send', 'true');

              $type = $messageEmail->getHeaders()->get('Content-Type');
              $type->setParameter('charset', 'iso-8859-1');

              $messageEmail->from($from_email);
              $messageEmail->to($input['email'])->subject($subject);
            });

    if (!$sent) {
      return back()->withErrors('Failed send Email');
    }
  }

  public static function attrGet($key, $obj, $options = null) {

    $defaultvalue = isset($options['defaultvalue']) ? $options['defaultvalue'] : (isset($options['default_value']) ? isset($options['default_value']) : null);

    if (is_array($key)) {
      foreach ($key as $subkey) {
        if (isset($obj[$subkey])) {
          return attr_get($subkey, $obj, $options);
        }
      }
      return $defaultvalue;
    }

    $value = null;
    $value_exists = false;
    $datatype = isset($options['datatype']) ? $options['datatype'] : null;
    $return = isset($options['return']) && $options['return'] > 0 ? true : false;
    $db_unique = isset($options['db_unique']) ? $options['db_unique'] : false;
    $db_unique_except = isset($options['db_unique_except']) ? $options['db_unique_except'] : '';

    $invalid = [];
    if (!isset($obj[$key])) {
      if (isset($options['required']) && $options['required']) {
        $invalid[] = "Parameter $key required";
      } else
        $value = $defaultvalue;
    }
    else {
      $value = $obj[$key];
      $value_exists = true;
    }

    if ($value_exists) {

      if ($datatype) {

        switch ($datatype) {

          case 'int':
          case 'double':
          case 'number':
            if (!is_numeric($value))
              exc("Invalid $key datatype, number required.");
            $value = floatval($value);
            break;

          case 'email':
            if (!filter_var($value, FILTER_VALIDATE_EMAIL))
              exc('Invalid email format');
            break;

          case 'date':
            $d = DateTime::createFromFormat('Y-m-d', $value);
            $valid = $d && $d->format('Y-m-d') === $value;
            if (!$valid)
              exc("Invalid date format for parameter $key");

            $date_format = isset($options['format']) ? $options['format'] : 'Y-m-d';
            $value = date($date_format, strtotime($value));
            break;

          case 'enum':
            $enums = isset($options['enums']) && is_array($options['enums']) ? $options['enums'] : array();
            if (!in_array($value, $enums))
              exc("Invalid $key parameter");
            break;

          case 'string':

            $not_empty = isset($options['not_empty']) ? boolval($options['not_empty']) : false;
            if ($not_empty && strlen($value) == 0)
              exc("Invalid $key parameter, $key must be filled.");

            $min_length = isset($options['min_length']) ? intval($options['min_length']) : -1;
            if ($min_length != -1 && strlen($value) < $min_length)
              exc("Invalid $key parameter, required at least $min_length character.");

            if (isset($options['length']) && is_numeric($options['length'])) {
              $length = $options['length'];
              if (strlen($value) != $options['length'])
                exc("Invalid $key parameter, length must be $length.");
            }

            if (isset($options['max_length']) && is_numeric($options['max_length'])) {
              $max_length = $options['max_length'];
              if (strlen($value) > $max_length)
                exc("Invalid $key parameter, max length is $max_length.");
            }

            $regex = isset($options['regex']) ? $options['regex'] : null;
            if ($regex && !preg_match($regex, $value))
              exc("Invalid $key parameter, type mismatch.");

            $format = isset($options['format']) ? $options['format'] : '';
            switch ($format) {
              case 'uppercase': $value = strtoupper($value);
                break;
              case 'lowercase': $value = strtolower($value);
                break;
              case 'capitalize': $value = ucwords($value);
                break;
            }

            break;
        }
      }
    }

    if ($db_unique) {

      $db_params = explode(':', $db_unique);
      $schema = isset($db_params[0]) && !empty($db_params[0]) ? $db_params[0] : '';
      $table = isset($db_params[1]) && !empty($db_params[1]) ? $db_params[1] : '';
      $column = isset($db_params[2]) && !empty($db_params[2]) ? $db_params[2] : '';

      if ($db_unique_except) {
        $exists = db_select_col("select count(*) from {$schema}.{$table} where {$column} = ? and {$column} != ?", [$value, $db_unique_except]);
      } else {
        $exists = DB::table("{$schema}.{$table}")->where($column, '=', $value)->count();
      }
      if ($exists)
        exc("Parameter $key with value $value already exists");
    }

    if (count($invalid) > 0) {
      if ($return)
        return false;
      else
        exc(implode("\n", $invalid));
    }

    return $value;
  }

}
