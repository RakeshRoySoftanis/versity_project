<?php
namespace App\Models;

// require_once __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;
use Storage;
use Illuminate\Support\Facades\DB;

class Twillio
{

  public $from = '';
  public $sid = '';
  public $token = '';
  public $TwiML_Apps_SID = '';

  function __construct()
  {
    $setting = DB::table('twillio_setting')->where('status',1)->first();
    if ($setting) {
      $this->from  = $setting->number;
      $this->sid   = $setting->sid;
      $this->token = $setting->token;
      $this->TwiML_Apps_SID = $setting->TwiML_Apps_SID;
    }else{
      return 'Please provide twillio SID and Auth Token !';
    }
  }

  /**
   * Returns an authorized API client.
   * @return Client the authorized client object
  */
  public function send_sms($to,$msg) {
    $client = new Client($this->sid, $this->token);
    // Use the client to do fun stuff like send text messages!
    $res = $client->messages->create(
        // the number you'd like to send the message to
        $to,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => $this->from,
            // the body of the text message you'd like to send
            'body' => $msg
        )
    );
    return $res;
  }

  public function send_mms($from,$to,$msg,$url,$sid,$token) {
    $client = new Client($sid, $token);
    // Use the client to do fun stuff like send text messages!
    $res = $client->messages->create(
        // the number you'd like to send the message to
        $to,
        array(
          // A Twilio phone number you purchased at twilio.com/console
          'from' => $from,
          // the body of the text message you'd like to send
          'body' => $msg,
          'mediaUrl' => $url//'https://my.catalystconnect.com/image/favicon.png'
        )
    );
    return $res;
  }

  public function fetchMedia($sid,$token,$SmsSid,$mediaID)
  {
      // Find your Account Sid and Auth Token at twilio.com/console
      $twilio = new Client($sid, $token);

      $media = $twilio->messages($SmsSid)
                      ->media($mediaID)
                      ->fetch();

      return $media->contentType;

      /*{
        "account_sid": "ACXXXXXXXXXXXXXXXXXXXXX",
        "content_type": "image/jpeg",
        "date_created": "Sun, 16 Aug 2015 15:53:54 +0000",
        "date_updated": "Sun, 16 Aug 2015 15:53:55 +0000",
        "parent_sid": "MMXXXXXXXXXXXXXXXXXXXXXX",
        "sid": "MEXXXXXXXXXXXXXXXXXXXX",
        "uri": "/2010-04-01/Accounts/ACXXXXXXXXX/Messages/SMXXXXXXXXXXXXXX/Media/MEXXXXXXXXXX.json"
      }*/
  }


  public function sendNotification() 
  {
    $Twilio_res ='';
    $from = '+15204474858';
    $to = '+8801710254758, +8801683472360';

    $msg = 'Hi, This group message.';
    $twillio_sid = 'AC52XXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $twillio_token = '345cXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $serviceSid = "IS617XXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

    $client = new Client($twillio_sid, $twillio_token);
    $notification = $client->notify
                  ->services($serviceSid)->notifications
                  ->create
                  ([
                    "toBinding" => array('{"binding_type":"sms", "address":"+8801710254758"}', '{"binding_type":"sms", "address":"+8801683472360"}'),
                    'body' => 'Hi! This is your first Notify SMS'
                  ]);

    print($notification->sid);
    print($notification);
  }

  public function token($value='')
  {

      $ACC_SID           = $this->sid;
      $TWILIO_AUTH_TOKEN = $this->token;
      $TwiML_Apps_SID    = $this->TwiML_Apps_SID;
      // $TwiML_Apps_SID = 'AP27841bb0b08e4111b3369888ef358126';

      $capability = new ClientToken($ACC_SID, $TWILIO_AUTH_TOKEN);
      $capability->allowClientOutgoing($TwiML_Apps_SID);
      $capability->allowClientIncoming('twcatcall');
      // $capability->allowClientIncoming($identity);
      $token = $capability->generateToken();


      // return serialized token and the user's randomly generated ID
      header('Content-Type: application/json');
      return json_encode(array(
          // 'identity' => $identity,
          'token' => $token,
      ));
  }


  public function disconnect_all_call($value='')
  {
    $ACC_SID           = $this->sid;
    $TWILIO_AUTH_TOKEN = $this->token;

    $twilio = new Client($ACC_SID, $TWILIO_AUTH_TOKEN);
    $call_direction = $_POST["call_direction"];

    if($call_direction == 'outgoing'){
      $conferences = $twilio->conferences->read(array(
                      "friendlyName" => "myTW"
                    )
                  );

      $conferences_SID = $conferences[0]->sid;
      $conference = $twilio->conferences($conferences_SID)
                         ->update(array("status" => "completed"));
    }

    print_r($conferences);

    // echo "Complete";
  }

  public function call_reject($value='')
  {
    $response = new VoiceResponse();
    $response->reject();

    echo $response;
  }

  public function text_to_speach($to, $msg){
    $twilio   = new Client($this->sid, $this->token);

    $response = new VoiceResponse();
    $say      = $response->say('Hi, Your Solar Catalyst Verification Code is.', ['voice' => 'Polly.Joey']);
    $say->say_as($msg, ['interpret-as' => 'spell-out']);

    $path = public_path()."/voicexml/voice.xml";
    $myfile = fopen($path, "w");
    ftruncate($myfile,0);
    fwrite($myfile, $response);
    fclose($myfile);

    $voiceUrl = url('/').'/public/voicexml/voice.xml';

    $call = $twilio->calls->create(
        $to, $this->from,
        array("url" => $voiceUrl)
    );

    if( $call->accountSid == $this->sid ){
      return true;
    }
    return false;

  }
}
?>