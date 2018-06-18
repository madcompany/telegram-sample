<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log; //Custom LOG
use Cache; // Cache
use Illuminate\Validation\ValidationException;

use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Laravel\Socialite\Facades\Socialite;
use \SocialiteProviders\Manager\Config;

/**
 * Class TelegramController
 * @package App\Http\Controllers
 */
class TelegramController extends Controller
{

    private $telegram;
    private $API_URL;
    private $config;

    public function __construct(Telegram $telegram)
    {
        $this->CHANNEL_NO = config('telegram.CHANNEL_NO'); //오피스톡 체널 ID

        $this->telegram = $telegram;
        $this->API_URL = config('telegram.API_URL');
        
        $this->config = new Config(
            config('telegram.KEY'),
            config('telegram.SECRET'),
            config('telegram.REDIRECT_URI'),
            []
        );
    }

    public function oauth(Request $request)
    {
        //Log::debug($request);
        
        return Socialite::driver('telegram')->setConfig($this->config)->redirect();
        
    }
    
    public function callback()
    
    {
        
        $user = Socialite::driver('telegram')->setConfig($this->config)->user();
        
        // OAuth Two Providers
        $token = $user->token;
        $refreshToken = $user->refreshToken; // not always provided
        $expiresIn = $user->expiresIn;
        
        // OAuth One Providers
        $token = $user->token;
        $tokenSecret = $user->tokenSecret;
        
        // All Providers
        $user->getId();
        $user->getNickname();
        $user->getName();
        $user->getEmail();
        $user->getAvatar();
        
        Log::debug($user);
    }
    
    public function users()
    {
        $user = Socialite::with('github')->userFromToken($token);
        
    }

    public function test()
    {
        /*
                $response = $this->telegram->getMe();
                pr($response);

                $keyboard = [
                    ['/setjoingroups']
                ];

                $reply_markup = $this->telegram->replyKeyboardMarkup([
                    'keyboard' => $keyboard,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);

                $response = $this->telegram->sendMessage([
                    'chat_id' => '517579507',
                    'text' => 'Hello World',
                    'reply_markup' => $reply_markup
                ]);

                $messageId = $response->getMessageId();

                echo $messageId;

                $response = $this->telegram->sendMessage([
                    'chat_id' => '517579507',
                    'text' => 'Hello World'
                ]);

                $messageId = $response->getMessageId();

                echo $messageId;
        */
/*
        try{
            $response = $this->telegram->setWebhook([
                'url' => 'https://telegram-test-secondhell.c9users.io/webhook',
                'certificate' => '' //공개키?
            ]);

            pr($response);
        }catch(TelegramSDKException $e){
            echo $e->getMessage();
        }
*/
        return redirect('test1');
        
    }
    
    public function test1(){
        return redirect('test2');
    }
    
    public function test2(){
        return redirect('https://telegram.me/cafe24testBot/start=abcdefg');
    }
    
    /**
     * 활성화
     */
    public function active(){
        //렌덤ID 생성 
        $uid = generateRandomString();
        
        //렌덤ID 케시저장
        Cache::put('telegram:'.$uid , '', 30) ;//임시로 케시에 저장 (30분)
        
        //텔레그램 로그인 팝업창으로 이동
        return redirect('http://t.me/cafe24testBot?start=' . $uid);
    }
    
    public function authorization(){
        //인증처리
        
        //그룹 혹은 채널 선택 버튼을 생성하여 전송한다. 
        $msg = "Please press the button below to connect your Telegram account to IFTTT.
                IFTTT connected successfully! You can now use Telegram Applets on IFTTT or create your own.
                To add channels or groups to your export and import list for IFTTT Applets, use the /connect_group and /connect_channel commands.
                Enjoy!
                
                Hi. I'm the IFTTT bot. I can help connect your Telegram account to more than 360 different services.
                You will need an IFTTT account to use me. Please follow this link to get started: https://ifttt.com/telegram
                Available commands:
                
                /connect_group - connect IFTTT to a group chat
                /connect_channel - connect IFTTT to a Telegram channel
                
                Please press the button below to connect your Telegram account to IFTTT.";
                
        //이미 인증된 사용자이면 안내문구만 그룹 연결에 대한 안내문구만 나온다. 채널연결 링크
        $msg = "Hi. I'm the IFTTT bot. I can help connect your Telegram account to more than 360 different services.
                You will need an IFTTT account to use me. Please follow this link to get started: https://ifttt.com/telegram
                Available commands:

                /connect_group - connect IFTTT to a group chat
                /connect_channel - connect IFTTT to a Telegram channel";
                
                
        //인증완료 시 webhook 호출
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $msg,
        ]);
        
    }


    public function webhook(Request $request){

        //텔레그램 웹훅 데이터 수신 
        $updates = $this->telegram->getWebhookUpdates();
        
        Log::debug($updates);
        
        if($updates->has('callback_query')){
            $query = $updates->getCallbackQuery();
            
            Log::debug($query);
            
            $data  = $query->getData();
            $chid = $query->getFrom()->getId();
            
            if($data == "click"){ //클릭했을 경우 링크 띄우기 
                
            }
            
        }
        

        //메세지 있을 경우
        if($updates->has('message') ){
            Log::debug(222);
            
            //메세지 파싱
            $message = $updates->getMessage();    
            
            $chat_id = $message['from']['id'];
            //$first_name = $message['from']['first_name'];
            //$last_name = $message['from']['last_name'];
            //$user_name = $message['from']['user_name'];
            
            $text = $message['text'];
            
            if(preg_match('/^\/start/',  $text)){ //시작을 눌렀을 경우 
            
                if(strpos($text, " ") !== false){
                    list($command, $payload) = @explode(' ', $text);
                    
                    //인증을 위한 버튼을 생성하여 전송한다.  (새창 링크에 redirect url 을 입력 - chat_id 를 전송한다)
                    $msg = "Please press the button below to connect your Telegram account to IFTTT.";
                    
                    //인증을 위한 code / state 
                    //인증코드
                    $payload;
                    
                    
                    $keyboard = [
                        [['text'=> 'Authorize RECIPE' , 'url' => config('telegram.AUTHORIZE_URL')]]
                    ];
                    
                    //$reply_markup = $this->telegram->replyKeyboardMarkup($params);
                    $reply_markup['inline_keyboard'] = $keyboard;
                    
                    $this->telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => $msg,
                        'reply_markup' => json_encode($reply_markup)
                    ]);
                    
                    
                }else{
                    //등록해야 한다는 소개 메세지
                    $msg = "Hi. I'm the IFTTT bot. I can help connect your Telegram account to more than 360 different services.

                            You will need an IFTTT account to use me. Please follow this link to get started: https://ifttt.com/telegram
                            
                            Available commands:
                            
                            /connect_group - connect IFTTT to a group chat
                            /connect_channel - connect IFTTT to a Telegram channel";
                    
                    $this->telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => $msg
                    ]);
                    
                    return response(null, 200);
                }
                
                //완료 되면 채널이나 그룹에 연결하라는 메세지가 나온다. 
                $msg = "IFTTT connected successfully! You can now use Telegram Applets on IFTTT or create your own.
                        To add channels or groups to your export and import list for IFTTT Applets, use the /connect_group and /connect_channel commands.
                        Enjoy!";
                
                
                //인증을 위한 버튼을 생성하여 전송한다.  (새창 링크에 redirect url 을 입력 - chat_id 를 전송한다)
                $msg = "Please press the button below to connect your Telegram account to IFTTT.";
                
                //인증을 위한 code / state 
                $keyboard = [
                    [['text'=> 'Authorize RECIPE' , 'url' => config('telegram.AUTHORIZE_URL')]]
                ];
                /*
                $params ['inline_keyboard'] = $keyboard;
                $params ['resize_keyboard'] = true;
                $params ['one_time_keyboard'] = true;
                $params ['selective'] = true;
                */
                
                //$reply_markup = $this->telegram->replyKeyboardMarkup($params);
                $reply_markup['inline_keyboard'] = $keyboard;
                
                $this->telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $msg,
                    'reply_markup' => json_encode($reply_markup)
                ]);
            }
            
            if($text == '/authorize'){ //인증버튼을 클릭했을 경우
                //그룹 혹은 채널 선택 버튼을 생성하여 전송한다. 
                $msg = "Please press the button below to connect your Telegram account to IFTTT.
                        IFTTT connected successfully! You can now use Telegram Applets on IFTTT or create your own.
                        To add channels or groups to your export and import list for IFTTT Applets, use the /connect_group and /connect_channel commands.
                        Enjoy!
                        
                        Hi. I'm the IFTTT bot. I can help connect your Telegram account to more than 360 different services.
                        You will need an IFTTT account to use me. Please follow this link to get started: https://ifttt.com/telegram
                        Available commands:
                        
                        /connect_group - connect IFTTT to a group chat
                        /connect_channel - connect IFTTT to a Telegram channel
                        
                        Please press the button below to connect your Telegram account to IFTTT.";
                        
            }
            
            if($text == '/connect_group'){
                //그룹 생성하는 버튼을 만들어서 전송한다. 
                $msg = "You can connect IFTTT to any of your group chats. Once this is done, you will be able to import data to the group via IFTTT or use messages in the group as triggers for outside events.
                        
                        Tap the button below to connect a group.
                    
                        You can connect IFTTT to any public Telegram channels, where you are an administrator. If you do this, you will be able to import data to that channel via IFTTT or use posts in the channel as triggers for outside events.
                        
                        To connect a channel:
                        - Add this bot to the channel as an administrator.
                        - Then send me the channel username (e.g. telegram.me/telegram or @telegram) or simply forward any message from the target channel to this chat.";
                        
                        //선택완료 시 그룹 ID 를 전송한다. 
            }
            
            if($text == '/connect_channel'){
                //채널에 관리자로 연결하라는 안내멘트를 보내준다. 
                $msg = "You can connect IFTTT to any public Telegram channels, where you are an administrator. If you do this, you will be able to import data to that channel via IFTTT or use posts in the channel as triggers for outside events.
        
                        To connect a channel:
                        - Add this bot to the channel as an administrator.
                        - Then send me the channel username (e.g. telegram.me/telegram or @telegram) or simply forward any message from the target channel to this chat.";
            }
            
        }

        return response(null, 200);
    }
}
