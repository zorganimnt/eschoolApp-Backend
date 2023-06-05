<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Validator;

class ChatController extends BaseController
{


    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required',
            'receiver_id' => 'required',
            'msg_content' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Bad Request', $validator->errors());
        }

        $msgInput['sender_id'] = $request['sender_id'];
        $msgInput['receiver_id'] = $request['receiver_id'];
        $msgInput['msg_content'] = $request['msg_content'];
        $mytime = \Carbon\Carbon::now();
        $msgInput['time_msg'] = $mytime->toDateTimeString();


        $chat = Chat::create($msgInput);
        if ($chat)
            return $this->sendResponse($chat, 'Message envoyé avec succès');
    }

    public function getChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required',
            'receiver_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Bad Request', $validator->errors());
        }
        $getChat = Chat::where('sender_id', $request['sender_id'])->where('receiver_id', $request['receiver_id'])->get();
        if ($getChat) {
            return $this->sendResponse($getChat, 'Succès!');
        }
    }

}