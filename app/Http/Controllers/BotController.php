<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram;
use App\Traits\RequestTrait;
use App\Traits\MakeComponents;

class BotController extends Controller
{
    use RequestTrait, MakeComponents;

    public function webhook() {
        return $this->apiRequest('setWebhook', [
            'url'   => str_replace('http', 'https', url(route('webhook')))
        ]) ? ['success'] : ['something went wrong'];
    }

    public function index() {
        $a = rand(0, 10);
        $b = rand(0, 10);
        $c = rand(0, 10);
        $resNum = $this->calculate($a, $b, $c);
        $result = json_decode(file_get_contents('php://input'));
        if (isset($result->message)) {
            $msg = $result->message;
            switch($msg->text) {
                case '/start':
                    $this->apiRequest('sendMessage', [
                        'chat_id'       => $msg->chat->id,
                        'text'          => 'Chào bạn, vui lòng giải pt bậc 2 sau:'. PHP_EOL. "{$a}x^2 + {$b}x + {$c} = 0",
                        'reply_markup'  => $this->keyboardBtn([
                            [$resNum, $this->calculate()],
                            [$this->calculate(), $this->calculate()]
                        ])
                    ]);
                    break;
                case $resNum:
                    $this->apiRequest('sendMessage', [
                        'chat_id'       => $msg->chat->id,
                        'text'          => "Đáp án là: {$resNum}". PHP_EOL. 'Bạn muốn tiếp tục chứ ?',
                        'reply_markup'  => $this->keyboardBtn([
                            ['Yes', 'No']
                        ])
                    ]);
                    break;
                case 'Yes':
                    $this->apiRequest('sendMessage', [
                        'chat_id'       => $msg->chat->id,
                        'text'          => 'Giải pt bậc 2 sau:'. PHP_EOL. "{$a}x^2 + {$b}x + {$c} = 0",
                        'reply_markup'  => $this->keyboardBtn([
                            [$resNum, $this->calculate()],
                            [$this->calculate(), $this->calculate()]
                        ])
                    ]);
                    break;
                case 'No':
                    $this->apiRequest('sendMessage', [
                        'chat_id'   => $msg->chat->id,
                        'text'      => 'Hẹn gặp lại lần sau',
                    ]);
                    break;
                default:
                    $this->apiRequest('sendMessage', [
                        'chat_id'       => $msg->chat->id,
                        'text'          => 'Bạn chọn sai rồi'. PHP_EOL. 'Bạn muốn tiếp tục chứ ?',
                        'reply_markup'  => $this->keyboardBtn([
                            ['Yes', 'No']
                        ])
                    ]);
                    break;
            }
        }
    }

    private function calculate($numA = null, $numB = null, $numC = null) {
        $numA = (!empty($numA)) ? $numA : rand(0, 10);
        $numB = (!empty($numB)) ? $numB : rand(0, 10);
        $numC = (!empty($numC)) ? $numC : rand(0, 10);

        if ($numA == 0) {
            if ($numB == 0) {
                return 'Vô nghiệm';
            } else {
                $x = (- $numC/$numB);
                return 'x = '. round($x, 2);
            }
        }

        $delta = $numB * $numB - 4 * $numA * $numC;
        $x1 = '';
        $x2 = '';

        if ($delta > 0) {
            $x1 = (- $numB + sqrt($delta)) / (2 * $numA);
            $x2 = (- $numB - sqrt($delta)) / (2 * $numA);
            return 'x1 = '. round($x1, 2). ', x2 = '. round($x2, 2);
        } else if ($delta == 0) {
            $x1 = - $numB / (2 * $numA);
            return 'x1 = x2 = '. round($x1, 2);
        } else {
            return 'Vô nghiệm';
        }
    }
}
