<?php

$content = file_get_contents("php://input");
if($content){
    $token = '5371659947:AAETj3h8gA5QVGrXFBoI1VF0iJmj1wwojq0';
    $usernamebot= '@dayryybot';
    
    $apiLink = "https://api.telegram.org/bot$token";
    
    $update = file_get_contents('php://input');
    $val = json_decode($update, TRUE);
  
    $chat_id   = $val['message']['chat']['id'];
    $text      = $val['message']['text'];
    $update_id = $val['update_id'];
    $sender    = $val['message']['from'];
    $uid       = $val['message']['from']['username'];
    $uid_stat  = $val['message']['chat']['type'];

    $pecah2 = explode(' ', $text, 10);
            $katake1 = strtolower($pecah2[0]); //untuk command
            $katake2 = strtolower($pecah2[1]); // kata pertama setelah command
            $katake3 = strtolower($pecah2[2]); // kata kedua setelah command
            $katake4 = strtolower($pecah2[3]); // kata kedua setelah command
            $katake5 = strtolower($pecah2[4]); // kata kedua setelah command
            $katake6 = strtolower($pecah2[5]); // kata kedua setelah command
            $katake7 = strtolower($pecah2[6]); // kata kedua setelah command

            
    $pecah = explode(' ', $text, 2);
    $katapertama = strtolower($pecah[0]); //untuk command
   
    $dataurl= file_get_contents("https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json");
    $json= json_decode($dataurl, TRUE);
    $tanggal=$json['Infogempa']['gempa']['Tanggal'];   
    $jam=$json['Infogempa']['gempa']['Jam'];  
    $kedalaman=$json['Infogempa']['gempa']['Kedalaman'];  
    $wilayah=$json['Infogempa']['gempa']['Wilayah']; 
    $potensi=$json['Infogempa']['gempa']['Potensi']; 



    $databaseHost     = 'localhost';
    $databaseName     = 'tpendaftaran';
    $databaseUsername = 'root';
    $databasePassword = '';

    $mysqli         = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName); 
    
    $getpendaftaran = mysqli_query($mysqli,"SELECT * FROM usertele WHERE chat='$chat_id'");
    $getdata        = mysqli_fetch_array($getpendaftaran);

    $penduduk = mysqli_query($mysqli,"SELECT * FROM penduduk WHERE nik = '$katake2'");
    $getwarga       = mysqli_fetch_array($penduduk);

    if (($getdata['id'] =='') and ($text=='/daftar'))
       {
		    mysqli_query($mysqli,"insert into usertele values ('','$uid','$uid_stat','','$chat_id')");
            $reply.="Assalaumalaikum Wr.wb !!. Selamat datang. ";
            $reply.=$uid;
            $reply.=", Anda sekarang sudah menjadi anggota ";
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply);
	   }
     else if (($text=='/hapus') and ($getdata['chat'] ==$chat_id))  
        {      
            //DELETE FROM tb_category WHERE category_id =
            mysqli_query($mysqli,"DELETE FROM usertele WHERE chat = '$chat_id'");
            //DELETE FROM `usertele` WHERE `usertele`.`id` = 1
            $reply.=", Anda sekarang bukan anggota lagi ";
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply); 
        }
        else if (($text=='/cek') and ($getdata['chat'] ==$chat_id))  
        {      
            //DELETE FROM tb_category WHERE category_id =
            mysqli_query($mysqli,"SELECT * FROM usertele WHERE chat = '$chat_id'");
            $reply.=", Anda ditemukan dengan username : ";
            $reply.=$getdata['username'];
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply); 
        }
          else if (($katake1=='nik') and ($getdata['chat'] ==$chat_id) and ($getwarga['nik'] ==$katake2)) 
        {      
                      
            $reply.="hasil pencarian : ".$text." a.n ";
            $reply.=$getwarga['nama'];
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply); 

        }
              else if (($katake1=='tambah') and ($getdata['chat'] ==$chat_id)) 
        {      
            //cek nik 
            $penduduk = mysqli_query($mysqli,"SELECT * FROM penduduk WHERE nik = '$katake2'");
            $getwarga       = mysqli_fetch_array($penduduk);

            mysqli_query($mysqli,"insert into penduduk values ('','$katake2','$katake3','$katake4','$katake5','$katake6','$katake7','','','')");
            
            $reply.="data berhasil ditambahkan: ".$getwarga['nik']." a.n ";
            $reply.=$getwarga['nama'];
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply); 
         
        }
          else if (($katake1=='infogempa') and ($getdata['chat'] ==$chat_id) OR ($katake1=='infogempa'.$usernamebot) ) 
        {     
            global $usernamebot;
                        
            $reply.="Infogempa terbaru ";
             $reply.="Tanggal : ".$tanggal;
             $reply.=" Pukul : ".$jam;
             $reply.=" Wilayah : ".$wilayah;
             $reply.=" Kedalaman : ".$kedalaman;   
             $reply.=" Potensi : ".$potensi;          
           
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply); 
         
        }
      else {
            
            
            $reply.="kata kunci pencarian tidak ditemukan : ";
            $reply.=$text;
            $key =['remove_keyboard'=>true,];
            sendTyping($apiLink, $chat_id);
            sendMessage($key,$apiLink, $chat_id, $reply);
           }
    }


function sendMessage($key,$website, $chatId, $message){
    global $usernamebot;
    $encodedMarkup = json_encode($key);
    $message = urlencode($message);
    $ch = curl_init($website."/sendmessage?chat_id=$chatId&parse_mode=HTML&text=$message&reply_markup=$encodedMarkup");// jika parse_mode ganti jadi markdown
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
}

function sendTyping($website, $chatId){
    global $usernamebot;
    $ch = curl_init($website."/sendChatAction?chat_id=$chatId&action=typing");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $result = curl_exec($ch);
    curl_close($ch);
}

?>
