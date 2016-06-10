<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use DateTime;
use Session;
use Hash;
use App\User;
use App\UserLottery;
use App\Lottery;
use App\Util;
use App\JSON;
use Validator;
use Illuminate\Http\Request;
use Psy\Exception\ErrorException;

class LotteryController extends Controller {

    public function get(Request $request, $id = null) {

        $offset = $request->input('offset');
        $limit  = $request->input('limit');
        $search = $request->input('search');

        $count = Lottery::count('id');

        if(strlen(trim($search)) > 0) {
            $items = Lottery::where('title', 'LIKE', '%' . $search . '%')->where('description', 'LIKE', '%' . $search . '%', 'OR');
        } else {
            $items = new Lottery();
        }

        $items = $limit > 0 ? $items->take($limit) : $items;
        $items = $offset > 0 ? $items->skip($offset) : $items;

        $tmpItems = $items->with('users')->where('draw_at', '>=', (new DateTime())->sub(new \DateInterval('PT1H')))->orderBy('draw_at')->orderBy('expires_at')->orderBy('title')->get();
        $retItems = [];

        $user = null;
        if(Auth::check()) $user = Auth::user();

        foreach($tmpItems as $item) {
            $item['ticket_won']   = null;
            $item['participates'] = false;
            $item['joined_at']    = null;
            $item['ticket_num']   = null;

            foreach($item->users as $iusr) {
                if($iusr->id == $item->winner_id && $iusr->pivot->ticket_num) {
                    $item['ticket_won'] = str_pad($iusr->pivot->ticket_num . "", 3, '0', STR_PAD_LEFT);
                    break;
                }
            }

            if($user && Util::searchArray(Util::objectToArray($item->users), 'id', $user->id) > 0) {
                foreach($item->users as $iusr) {
                    if($iusr->id == $user->id) {
                        $item['joined_at']  = $iusr->pivot->created_at . "";
                        $item['ticket_num'] = str_pad($iusr->pivot->ticket_num . "", 3, '0', STR_PAD_LEFT);
                        break;
                    }
                }
                $item['participates'] = true;

            }

            unset($item['users']);
            array_push($retItems, $item);
        }

        if($id) {
            return response()->json(['result' => $items->find($id)]);
        } else {
            return response()->json(['result' => ['items' => $retItems, 'count' => $count, 'server_dt' => (new DateTime())->format('Y-m-d H:i:s')],]);
        }

    }

    public function join(Request $request) {
        $input = JSON::decode($request->getContent());

        $lottery_id = Util::valueOrNull($input->data, 'id');

        if(Auth::check()) {
            $user = Auth::user();

            $ul  = UserLottery::where('user_id', '=', $user->id)->where('lottery_id', '=', $lottery_id)->first();
            $lot = Lottery::find($lottery_id);

            if($ul) return response()->json(['error' => ['message' => 'You have already joined this lottery.']]);
            if($lot && (new DateTime($lot->draw_at)) < (new DateTime("now"))) return response()->json(['error' => ['message' => 'This lottery has ended, you cannot join it.']]);

            $maxTicket = UserLottery::where('lottery_id', '=', $lottery_id)->max('ticket_num');

            $nUl             = new UserLottery();
            $nUl->user_id    = $user->id;
            $nUl->lottery_id = $lottery_id;
            $nUl->is_active  = true;
            $nUl->ticket_num = $maxTicket + 1;
            $nUl->save();

            return response()->json(['result' => ['user_id' => $user->id, 'lottery_id' => $lottery_id, 'joined_at' => $nUl->created_at . "", 'ticket_won' => null, 'ticket_num' => str_pad($nUl->ticket_num . "", 3, '0', STR_PAD_LEFT)]]);

        }

        return response()->json(['error' => ['message' => 'You are not logged in.', 'redir' => 'login']]);

    }

    public function notifyResults(Request $request) {
        $input      = JSON::decode($request->getContent());
        $user_id    = Util::valueOrNull($input->data, 'user_id');
        $lottery_id = Util::valueOrNull($input->data, 'lottery_id');
        $ticket_num = Util::valueOrNull($input->data, 'ticket_num');

        $user = User::find($user_id);
        $lot  = Lottery::find($lottery_id);

        //return response()->json(['result' => ['user_id' => $user->id, 'lottery_id' => $lottery_id, 'joined_at' => $nUl->created_at . "", 'ticket_won' => null, 'ticket_num' => str_pad($nUl->ticket_num . "", 3, '0', STR_PAD_LEFT)]]);

        if(!$user) return response()->json(['error' => ['message' => 'no such user']]);
        if(!$lot) return response()->json(['error' => ['message' => 'no such lottery']]);

        $url    = env('APP_URL');
        $ticket = str_pad($ticket_num . "", 3, '0', STR_PAD_LEFT);

        if($user->email) {
            $subject = "Congratulations you won a lottery!";
            $body    = "Hi! You won the lottery '" . $lot->title . "' with the ticket number: " . $ticket . ". Visit " . $url . " to claim your prize!";

            Mail::raw($body, function ($m) use ($user, $subject, $body) {
                $m->to($user->email)->subject($subject);
            });
        }

        if($user->mobile) {
            $username = env('SMSAPI_USER');
            $password = env('SMSAPI_PASS');
            $to       = env('SMSAPI_MOBILEPREFIX') . $user->mobile;
            $from     = env('SMSAPI_FROM');
            $message  = "Hi! You won the lottery '" . $lot->title . "' with the ticket number: " . $ticket . ". Visit " . $url . " to claim your prize!";
            $url      = 'https://api.smsapi.com/sms.do';
            $c        = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, 'username=' . $username . '&password=' . $password . '&from=' . $from . '&to=' . $to . '&message=' . $message);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($c);
            curl_close($c);
        }

        return response()->json(['result' => ['message' => 'winner notified']]);
    }

    public function caas() {
        Lottery::clearAndAddSamples();

        return response('ok', 200);
    }

}