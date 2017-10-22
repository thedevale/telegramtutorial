<?php
  include 'token.php';

  $update = file_get_contents('php://input');
  $update = json_decode($update, TRUE);

  $chatId = $update['message']['from']['id'];
  $nome = $update['message']['from']['first_name'];
  $text = $update['message']['text'];


  $query = $update['callback_query'];

  $queryid = $query['id'];
  $queryUserId = $query['from']['id'];
  $queryusername = $query['from']['username'];
  $querydata = $query['data'];
  $querymsgid = $query['message']['message_id'];


  $inlinequery = $update['inline_query'];

  $inlineid = $inlinequery['id'];
  $inlineUserId = $inlinequery['from']['id'];
  $inlinequerydata = $inlinequery['query'];

  if(isset($update['inline_query']))
  {
    gestisciInlineQuery($inlineid,$inlineUserId,$inlinequerydata,$inlinequery['from']['username'],$inlinequery['from']['first_name'],$inlinequery['from']['last_name']);
    exit();
  }


  $agg = json_encode($update,JSON_PRETTY_PRINT);

  if($querydata == "StampaMessaggio")
  {
    answerQuery($queryid,"Ciao $queryusername! Come stai?!");
    exit();
  }
  if($querydata == "ModificaMessaggio")
  {
    editMessageText($queryUserId,$querymsgid,"HEYLA!");
    exit();
  }


   if(strpos($text,"+")!==false)
   {
          sendMessage($chatId,eval('return '.$text.';'));
         exit();
   }

   $esempiotastierainline = '[{"text":"Testo","url":"http://yt.alexgaming.me"},{"text":"Inline","switch_inline_query":"Ciao!"}],[{"text":"Testo","callback_data":"StampaMessaggio"},{"text":"Modifica Messaggio","callback_data":"ModificaMessaggio"}]';

  switch($text)
  {
    case "/start":
        sendMessage($chatId,"Weyla!");
        break;
    case "/tastiera":
        sendMessage($chatId,"Test tastiera Inline!",$esempiotastierainline,"inline");
        break;
    case "Bene":
        sendMessage($chatId,"Ottimo!");
        break;
    case "Tu?":
        sendMessage($chatId,"Eh... Sono ancora in via di sviluppo!");
        break;
    default:
      $tastierabenvenuto = '["Bene"],["Tu?"],["'.$nome.'"]';
      sendMessage($chatId,"Ciao <b>$nome</b>! Come stai?",$tastierabenvenuto,"fisica");
      break;
  }





  function sendMessage($chatId,$text,$tastiera,$tipo)
  {
    if(isset($tastiera))
    {
      if($tipo == "fisica")
      {
        $tastierino = '&reply_markup={"keyboard":['.$tastiera.'],"resize_keyboard":true}';
      }
      else {
        $tastierino = '&reply_markup={"inline_keyboard":['.$tastiera.'],"resize_keyboard":true}';
      }
    }
    $url = $GLOBALS[website]."/sendMessage?chat_id=$chatId&parse_mode=HTML&text=".urlencode($text).$tastierino;
    file_get_contents($url);
  }

  function answerQuery($callback_query_id,$text)
  {
    $url = $GLOBALS[website]."/answerCallbackQuery?callback_query_id=$callback_query_id&text=".urlencode($text);
    file_get_contents($url);
  }

  function editMessageText($chatId,$message_id,$newText)
  {
    $url = $GLOBALS[website]."/editMessageText?chat_id=$chatId&message_id=$message_id&parse_mode=HTML&text=".urlencode($newText);
    file_get_contents($url);
  }

  function gestisciInlineQuery($queryId,$chatId,$querydata,$username,$name,$cognome)
  {
       if(strpos($querydata,"+") !== false)
        {
              $numeric = eval('return '.$querydata.';');
              $risultati=[[
          "type" => "article",
          "id" => "0",
          "title" => "Risultato".urlencode($querydata),
          "input_message_content" => array("message_text" => "<b>".urlencode($querydata)."</b>\n\nRisultato: $numeric", "parse_mode" => "HTML"),
          "description" => "Descrizione del result",

          ]
      ];
      $risultati = json_encode($risultati,true);
      $url = $GLOBALS[website]."/answerInlineQuery?inline_query_id=$queryId&results=$risultati&cache_time=0&switch_pm_text=Vai al Bot&switch_pm_parameter=123";
      file_get_contents($url);
      exit();
         }
      $infoUtente = "<b>Ciao! Io sono $username</b>\n\nIl mio chatId è <code>$chatId</code>\nIl mio nome è $name\nIl mio cognome è $cognome";

      $risultati=[[
          "type" => "article",
          "id" => "0",
          "title" => "Titolo del Result",
          "input_message_content" => array("message_text" => "Testo del Result", "parse_mode" => "HTML"),
          "reply_markup" => array("inline_keyboard" => [[array("text" => "CLICCA QUI","url" => "yt.alexgaming.me")],[array("text" => "CLICCA QUI","callback_data" => "StampaMessaggio")]]),
          "description" => "Descrizione del result",

          ],
          [
              "type" => "article",
              "id" => "1",
              "title" => "Invia le tue informazioni",
              "input_message_content" => array("message_text" => "$infoUtente", "parse_mode" => "HTML"),
              "reply_markup" => array("inline_keyboard" => [[array("text" => "CLICCA QUI","url" => "yt.alexgaming.me")],[array("text" => "CLICCA QUI","callback_data" => "StampaMessaggio")]]),
              "description" => "Descrizione del result",

              ],
      ];
      $risultati = json_encode($risultati,true);
      $url = $GLOBALS[website]."/answerInlineQuery?inline_query_id=$queryId&results=$risultati&cache_time=0&switch_pm_text=Vai al Bot&switch_pm_parameter=123";
      file_get_contents($url);
      exit();
  }
?>
