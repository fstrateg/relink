<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Divoitbot extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("main");
        $this->load->model("admin");
    }

    public function index() {
        //define('BOT_TOKEN', '2135432708:AAGbvBKQHfr3XYCCxTGDB14EwAW5SZU6Bik');
        $buttons = array();
        $content = file_get_contents('php://input');

        $stop = 0;

        //$ddt['data'] = $content;
        //$this->db->insert('test2',$ddt);
        //exit();

        $message = $this->main->get_message($content);
        $chat_id = $message['chat_id'];
        $user_id = $message['user_id'];
        $text = $message['text'];
        $file_id = $message['file_id'];
        $type = $message['type'];
        $justKeyboard = '';

        //$ddt['data'] = $text;
        //$this->db->insert('test2',$ddt);

        $this->db->where('chat_id', $chat_id);
        $query = $this->db->get('bot');
        $bots = $query->result_array();
        $last_command = $bots[0]['last'];

        $data2['data'] = time();
        $data2['description'] = $text;
        $data2['chat_id'] = $chat_id;
        $this->db->insert('divoitbot_log',$data2);

        //exit();

        $a = explode('_',$text);
        if (@$a[0] == 'conv') {
            $to = $a[1];
            $this->db->where('chat_id', $chat_id);
            $query = $this->db->get('bot');
            $bot = $query->result_array();
            $text = $bot[0]['last'];
        } else $to = ''; // в какую страну принудительно конвертировать

        // зарегистрирован ли человек
        $this->db->where('chat_id', $chat_id);
        $query = $this->db->get('bot');
        $bot = $query->result_array();
        if (count($bot) == 0) {
            $data['chat_id'] = $chat_id;
            $data['country'] = 'KG';
            $data['last'] = $text;
            $this->db->insert('bot',$data);
        } else {
            if ($bot[0]['last'] == 'ideas') { // если хотят предложить идею
                $this->main->send_message(452369376,$text,$justKeyboard);
                $this->main->send_message(154139631,$text,$justKeyboard);
                $this->main->send_message($chat_id,'Спасибо! Ваша идея будет рассмотрена!',$justKeyboard);
                $data = array();
                $data['last'] = 'stop';
                $this->db->where('chat_id', $chat_id);
                $this->db->update('bot',$data);
            } else { // если не хотят предложить идею
                $data['last'] = $text;
                $this->db->where('chat_id', $chat_id);
                $this->db->update('bot',$data);
            }
        }
        // зарегистрирован ли человек

        // массив доступных стран без страны по умолчанию
        $countries = array('KG','KZ','RU','UZ','TJ');
        $this->db->where('chat_id', $chat_id);
        $query = $this->db->get('bot');
        $bot = $query->result_array(); // вытаскиваем мои настройки
        $mycountry = $bot[0]['country']; // моя страна из настроек
        $arr = array();
        for ($i=0; $i<count($countries); $i++) {
            if ($countries[$i] != $mycountry) {
                $arr[] = $countries[$i];
            }
        }
        $symb['KG'] = '🇰🇬'.' KG';
        $symb['KZ'] = '🇰🇿'.' KZ';
        $symb['RU'] = '🇷🇺'.' RU';
        $symb['UZ'] = '🇺🇿'.' UZ';
        $symb['TJ'] = '🇹🇯'.' TJ';
        // массив доступных стран без страны по умолчанию

        $len = strlen($text);
        $num = '';
        for ($i=0; $i<$len; $i++) {
            if (is_numeric(substr($text,$i,1))) $num .= substr($text,$i,1);
        }

        $first_symbol = substr($num,0,1);

        if ($to != '') $mycountry = $to; // если принудительная страна не равна пустоте

        if ($mycountry == 'KG') {
            if ($first_symbol == '0') $wanum = '996'.substr($num,1,100);
            elseif (substr($num,0,3) != '996') $wanum = '996'.$num;
            else $wanum = $num;
        }

        if ($mycountry == 'KZ') {
            if ($first_symbol == '8') $wanum = '7'.substr($num,1,100);
            elseif (substr($num,0,3) != '7') $wanum = '7'.$num;
            else $wanum = $num;
        }

        if ($mycountry == 'RU') {
            if ($first_symbol == '8') $wanum = '7'.substr($num,1,100);
            elseif (substr($num,0,3) != '7') $wanum = '7'.$num;
            else $wanum = $num;
        }

        if ($mycountry == 'TJ') {
            if ($first_symbol == '8') $wanum = '992'.substr($num,1,100);
            elseif (substr($num,0,3) != '992') $wanum = '992'.$num;
            else $wanum = $num;
        }

        if ($mycountry == 'UZ') {
            if ($first_symbol == '8') $wanum = '998'.substr($num,1,100);
            elseif (substr($num,0,3) != '998') $wanum = '998'.$num;
            else $wanum = $num;
        }

        /*if ($text=='commands') { // все команды
            $mes = 'Нажмите одну из кнопок:';
            $buttons[0][0] = $this->buildInlineKeyBoardButton('🔍 Распознать QR-код', '/recqr','');
            $buttons[1][0] = $this->buildInlineKeyBoardButton('📇 Генерировать QR-код', '/createqr','');
            $buttons[2][0] = $this->buildInlineKeyBoardButton('💬 Ссылка на WhatsApp', 'whatsapp','');
            $justKeyboard = $this->buildInlineKeyBoard($buttons);
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }*/

        if (substr($text,0,3) =='geo') { // сохранить локацию
            $arr2 = explode(',',str_replace('geo:','',$text));

            $ddt = array();
            $ddt['geo'] = $arr2[0].','.$arr2[1];
            $ddt['chat_id'] = $chat_id;
            $this->db->insert('geo',$ddt);

            $mes = 'Укажите название места:';
            $justKeyboard = '';
            $this->main->send_message($chat_id,$mes,$justKeyboard);

            $type = 'geo';

            $data = array();
            $data['last'] = 'set_place_name';
            $this->db->where('chat_id', $chat_id);
            $this->db->update('bot',$data);
        }

        if ($last_command=='set_place_name') {
            $ddt = array();
            $ddt['title'] = $text;
            $ddt['data'] = time();
            $this->db->where('title',null);
            $this->db->where('chat_id',$chat_id);
            $this->db->update('geo',$ddt);

            $mes = "Место сохранено: ".$text."\r\nДата: ".date('d.m.Y H:i:s');
            $justKeyboard = '';
            $this->main->send_message($chat_id,$mes,$justKeyboard);

            $type = 'geo';

            $data = array();
            $data['last'] = '';
            $this->db->where('chat_id', $chat_id);
            $this->db->update('bot',$data);
        }

        if ($text=='/myloc') { // мои локации
            $this->db->where("chat_id", $chat_id);
            $query = $this->db->get("geo");
            $geo = $query->result_array();
            for ($i=0; $i<count($geo); $i++) {
                $buttons[$i][0] = $this->buildInlineKeyBoardButton('📍 '.$geo[$i]['title'], 'place_'.$geo[$i]['geo_id'],'');
                $justKeyboard = $this->buildInlineKeyBoard($buttons);
            }
            if (count($geo) > 0) {
                $mes = 'Ваши локации:';

            } else {
                $mes = 'У Вас пока нет сохраненных мест';
            }

            $this->main->send_message($chat_id,$mes,$justKeyboard);
            $stop = 1;
        }

        if (substr($last_command,0,5) == 'note_') {
            $this->db->where("geo_id", str_replace('note_','',$last_command));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $data = array();

            $data['comment'] = $text;
            $this->db->where('geo_id',$geo[0]['geo_id']);
            $this->db->update('geo',$data);

            $mes = 'Заметка к месту '.$geo[0]['title'].' сохранена!';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,5) == 'edit_') {
            $this->db->where("geo_id", str_replace('edit_','',$text));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $mes = 'Название места сейчас: '.$geo[0]['title'].'. Напишите новое название места.';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($last_command,0,5) == 'edit_') {
            $this->db->where("geo_id", str_replace('edit_','',$last_command));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $data = array();

            $data['title'] = $text;
            $this->db->where('geo_id',$geo[0]['geo_id']);
            $this->db->update('geo',$data);

            $mes = 'Место сохранено как: '.$text;
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,9) == 'photoadd_') {
            $this->db->where("geo_id", str_replace('photoadd_','',$text));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $this->db->where('geo_id', str_replace('photoadd_','',$text));
            $this->db->from('geo_pics');
            $count = $this->db->count_all_results();

            if ($count < 3) {
                $mes = 'Скиньте боту фотографии места. Максимальное количество - 3 фотографии.';
            } else {
                $mes = 'Превышен лимит (3 шт.) на количество фотографий.';

                $data = array();
                $data['last'] = '1';
                $this->db->where("chat_id", $chat_id);
                $this->db->update("bot", $data);

                $stop = 1;

            }
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($last_command,0,9) == 'photoadd_' and $stop != 1 and $type == 'img') {
            $this->db->where("geo_id", str_replace('photoadd_','',$last_command));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $this->db->where('geo_id', str_replace('photoadd_','',$last_command));
            $this->db->from('geo_pics');
            $count = $this->db->count_all_results();

            if ($count < 4) {

                $aa = explode('/',$text);
                $fname = $aa[count($aa)-1];
                $bb = explode('.',$fname);
                $ext = $bb[1];

                $rand = md5(time().rand(1,1000000000)).'.'.$ext;

                rename('./uploads/telegram/'.$fname, './uploads/telegram/'.$rand);

                $data3['url'] = 'https://letty.kz/uploads/telegram/'.$rand;
                $data3['geo_id'] = $geo[0]['geo_id'];
                $data3['data'] = time();
                $this->db->insert('geo_pics',$data3);

                if ($count == 2) {
                    $data = array();
                    $data['last'] = '1';
                    $this->db->where("chat_id", $chat_id);
                    $this->db->update("bot", $data);
                } else {
                    $data = array();
                    $data['last'] = 'photoadd_'.$geo[0]['geo_id'];
                    $this->db->where("chat_id", $chat_id);
                    $this->db->update("bot", $data);
                }



                $mes = 'Фотография к месту '.$geo[0]['title'].' добавлена!';
            } else {
                $mes = 'Превышен лимит (3 шт.) на количество фотографий.';
            }



            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,5) == 'note_') {
            $mes = 'Напишите описание локации текстом.';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,9) == 'delplace_') {
            $this->db->where("geo_id", str_replace('delplace_','',$text));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $this->db->where("geo_id", str_replace('delplace_','',$text));
            $this->db->delete("geo");

            $this->db->where("geo_id", str_replace('delplace_','',$text));
            $this->db->delete("geo_pics");

            $mes = 'Локация '.$geo[0]['title'].' удалена.';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,9) == 'delphoto_') {
            $this->db->where("id", str_replace('delphoto_','',$text));
            $query = $this->db->get("geo_pics");
            $geo_pics = $query->result_array();

            $aa = explode('/',$geo_pics[0]['url']);

            unlink('./uploads/telegram/'.$aa[count($aa)-1]);

            $this->db->where("id", str_replace('delphoto_','',$text));
            $this->db->delete("geo_pics");

            $mes = 'Фотография удалена';

            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (substr($text,0,6) == 'photo_') {
            $this->db->where("geo_id", str_replace('photo_','',$text));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $this->db->where("geo_id", str_replace('photo_','',$text));
            $query = $this->db->get("geo_pics");
            $geo_pics = $query->result_array();

            if (count($geo_pics) == 0) {
                $mes = 'Фотографий пока нет';
                $this->main->send_message($chat_id,$mes,$justKeyboard);
            } else {
                for ($i=0; $i<count($geo_pics); $i++) {
                    $buttons[0][0] = $this->buildInlineKeyBoardButton('❌ Удалить фотографию', 'delphoto_'.$geo_pics[$i]['id'],'');
                    $justKeyboard = $this->buildInlineKeyBoard($buttons);
                    $aa = explode('/',$geo_pics[$i]['url']);
                    $this->main->send_photo($chat_id, $aa[count($aa)-1], $justKeyboard);
                }
            }
        }

        if (substr($text,0,6) == 'place_') {
            $this->db->where('geo_id', str_replace('place_','',$text));
            $this->db->from('geo_pics');
            $count = $this->db->count_all_results();

            $this->db->where("geo_id", str_replace('place_','',$text));
            $query = $this->db->get("geo");
            $geo = $query->result_array();

            $mes = $geo[0]['title']."\r\n";
            $mes .= "Дата: ".date('d.m.Y H:i:s',$geo[0]['data'])."\r\n";
            if (strlen($geo[0]['comment']) > 0) $mes .= "Заметка: ".$geo[0]['comment'];
            $this->main->send_message($chat_id,$mes,$justKeyboard);
            $this->main->send_location($chat_id,$geo[0]['geo']);

            $buttons[0][0] = $this->buildInlineKeyBoardButton('❌ Удалить место', 'delplace_'.$geo[0]['geo_id'],'');
            $buttons[0][1] = $this->buildInlineKeyBoardButton('📜 Заметка к месту', 'note_'.$geo[0]['geo_id'],'');
            $buttons[1][0] = $this->buildInlineKeyBoardButton('✏️ Изменить название места', 'edit_'.$geo[0]['geo_id'],'');
            $buttons[1][1] = $this->buildInlineKeyBoardButton('📷️ Фотографии места ('.$count.')', 'photo_'.$geo[0]['geo_id'],'');
            $buttons[2][0] = $this->buildInlineKeyBoardButton('📸️ Добавить фото', 'photoadd_'.$geo[0]['geo_id'],'');
            $justKeyboard = $this->buildInlineKeyBoard($buttons);

            $mes = 'Изменить локацию:';

            $this->main->send_message($chat_id,$mes,$justKeyboard);

        }

        if ($text=='/saveloc') { // сохранить локацию
            $mes = "👉 Отправьте боту локацию из меню Telegram. Также Вы можете отправить ссылку на Google-карты, Яндекс-карты или 2Gis. Приятного пользования 😉";
            $justKeyboard = '';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($text=='/walink') { // whatsapp
            $mes = "👉 Отправьте боту номер телефона, например: 0(500)101-202.\r\nБот ответит Вам ссылкой на WhatsApp, для общения с этим пользователем. Приятного пользования 😉";
            $justKeyboard = '';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($last_command=='/recqr') {
            $mes = $this->main->qr_decode($text);
            //$buttons[0][0] = $this->buildInlineKeyBoardButton('🔍 Распознать QR-код', '/recqr','');
            //$buttons[1][0] = $this->buildInlineKeyBoardButton('📇 Генерировать QR-код', '/createqr','');
            //$buttons[2][0] = $this->buildInlineKeyBoardButton('💬 Ссылка на WhatsApp', 'whatsapp','');
            //$justKeyboard = $this->buildInlineKeyBoard($buttons);
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($text=='/recqr') {
            $data['last'] = '/recqr';
            $this->db->where('chat_id', $chat_id);
            $this->db->update('bot',$data);
            $justKeyboard = '';
            $mes = 'Отправьте боту фотографию QR-кода, который необходимо расшифровать.';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($text=='/createqr') {
            $data['last'] = '/createqr';
            $this->db->where('chat_id', $chat_id);
            $this->db->update('bot',$data);
            $justKeyboard = '';
            $mes = 'Отправьте боту текст, который Вы хотите зашифровать. Это может быть ссылка, номер телефона или же обычный текст.';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($last_command=='/createqr') {
            if ($text != '/createqr' and $text != '/recqr' and $text != 'whatsapp' and $text != 'commands' and $text != 'settings') {
                $justKeyboard = '';
                $mes = "QR-код формируется, подождите около 5 секунд";
                $this->main->send_message($chat_id,$mes,$justKeyboard);

                $this->main->send_qr_photo($chat_id,$text);
                $this->main->send_qr_document($chat_id,$text);
                $this->main->send_qr_png_document($chat_id,$text);
                //$mes = 'Выше сгенерирован QR-код в виде фотографии, векторного исходника и исходника в PNG.';
                $mes = 'Выше сгенерирован QR-код  из ссылки: '.$text.' в виде фотографии, векторного исходника и исходника в PNG.';
                //$buttons[0][0] = $this->buildInlineKeyBoardButton('🔍 Распознать QR-код', '/recqr','');
                //$buttons[1][0] = $this->buildInlineKeyBoardButton('📇 Генерировать QR-код', '/createqr','');
                //$buttons[2][0] = $this->buildInlineKeyBoardButton('💬 Ссылка на WhatsApp', 'whatsapp','');
                //$justKeyboard = $this->buildInlineKeyBoard($buttons);
                $this->main->send_message($chat_id,$mes,$justKeyboard);
            }
        }

        if ($text=='ideas') { // подать идею
            $mes = 'Напишите текст Вашей идеи ниже:';
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if ($text=='/start' or $text=='settings') { // стартовые настройки
            $mes = 'Выберите страну по умолчанию для WA-конвертера:';
            for ($i=0; $i<count($countries); $i++) {
                $buttons[0][$i] = $this->buildInlineKeyBoardButton($symb[$countries[$i]], 'sett_'.$countries[$i],'');
            }
            //$buttons[1][0] = $this->buildInlineKeyBoardButton('💡 Предложить идею!', 'ideas','');
            $justKeyboard = $this->buildInlineKeyBoard($buttons);
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (@$a[0] == 'sett') { // установка страны по умолчанию
            $data = array();
            $data['country'] = $a[1];
            $this->db->where('chat_id', $chat_id);
            $this->db->update('bot',$data);
            $mes = 'Установлена страна по умолчанию: '.$symb[$a[1]];
            $mes .= "\r\n\r\n👉 Чтобы проверить работу бота, отправьте ему номер телефона, например: 0(500)101-202.\r\n\r\nБот ответит Вам ссылкой на WhatsApp, для общения с этим пользователем. Приятного пользования 😉";
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        if (strlen($wanum) >=10 and $last_command!='/recqr' and $last_command!='/createqr' and $type !='geo' and $type != 'img') {
            for ($i=0; $i<count($arr); $i++) {
                $buttons[0][$i] = $this->buildInlineKeyBoardButton($symb[$arr[$i]], 'conv_'.$arr[$i],'');
            }
            $buttons[0][count($arr)] = $this->buildInlineKeyBoardButton('⚙', 'settings','');
            $buttons[1][0] = $this->buildInlineKeyBoardButton('✉ Написать → '.$wanum, '','https://wa.me/'.$wanum);
            //$buttons[2][0] = $this->buildInlineKeyBoardButton('🚀 Все команды', 'commands');
            $justKeyboard = $this->buildInlineKeyBoard($buttons);

            $link = 'Номер сконвертирован для страны '.$symb[$mycountry].': https://wa.me/'.$wanum;

            $mes = $link;
            $this->main->send_message($chat_id,$mes,$justKeyboard);
        }

        //$this->main->send_message($chat_id,$mes,$justKeyboard);
    }

    public function getKeyBoard($data)
    {
        $keyboard = array(
            "keyboard" => $data,
            "one_time_keyboard" => true,
            "resize_keyboard" => true
        );
        return json_encode($keyboard);
    }

    public function buildInlineKeyBoard(array $options)
    {
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        $encodedMarkup = json_encode($replyMarkup, true);
        return $encodedMarkup;
    }

    public function buildInlineKeyboardButton($text, $callback_data = '', $url = '')
    {
        $replyMarkup = [
            'text' => $text,
        ];
        if ($url != '') {
            $replyMarkup['url'] = $url;
        } elseif ($callback_data != '') {
            $replyMarkup['callback_data'] = $callback_data;
        }
        return $replyMarkup;
    }


}