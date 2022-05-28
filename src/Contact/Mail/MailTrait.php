<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Contact\Mail;

/*
|---------------------------------------------------
| Mail trait
|---------------------------------------------------
*/
trait MailTrait {

    /*
    * Prepare
    * Prepare (mail)
    * Prepare to send a Email
    */
    protected function prepare_mail()
    {
        // Subject
        $subject_text = $this->subject;
        $subject = '=?UTF-8?B?' . base64_encode($subject_text) . '?=';

        // Message
        $message = ($this->message);

        // To
        $to = '';

        foreach($this->to as $item){
            if(empty($item[1])){
                $to .= $item[0];
            }else {
                $to .= '=?UTF-8?B?' . base64_encode($item[1]) . '?= <'.$item[0].'>';
            }
        }

        // From
        $from = '';

            if(empty($this->from[1])){
                $from .= $this->from[0];
            }else {
                $from .= '=?UTF-8?B?' . base64_encode($this->from[1]) . '?= <'.$this->from[0].'>';
            }

        // Reply
        $reply = '';

        foreach($this->reply as $item){
            if(empty($item[1])){
                $reply .= $item[0];
            }else {
                $reply .= '=?UTF-8?B?' . base64_encode($item[1]) . '?= <'.$item[0].'>';
            }
        }

        if(!empty($reply)){
            $header_reply = 'Reply-To: ' . $reply . "\r\n"; // Reply-To
        }else{
            $header_reply='';
        }

        // CC
        $cc = '';

        foreach($this->cc as $item){
            if(empty($item[1])){
                $cc .= $item[0];
            }else {
                $cc .= '=?UTF-8?B?' . base64_encode($item[1]) . '?= <'.$item[0].'>';
            }
        }

        if(!empty($cc)){
            $header_cc = 'Cc: ' . $cc . "\r\n"; // Cc
        }else{
            $header_cc='';
        }

        // BCC
        $bcc = '';

        foreach($this->bcc as $item){
            if(empty($item[1])){
                $bcc .= $item[0];
            }else {
                $bcc .= '=?UTF-8?B?' . base64_encode($item[1]) . '?= <'.$item[0].'>';
            }
        }

        if(!empty($cc)){
            $header_bcc = 'Bcc: ' . $bcc . "\r\n"; // Bcc
        }else{
            $header_bcc = '';
        }

        // Type
        if($this->isHtml){
            $type = "text/html";
        }else{
            $type = "text/plain";
        }

        // Headers
        $headers = '';
        $headers .= 'From: ' . $from . "\r\n"; // From
        $headers .= 'MIME-Version: 1.0' . "\r\n"; // MIME
        $headers .= $header_reply; // Reply-To
        $headers .= $header_cc; // CC
        $headers .= $header_bcc; // BCC
        $headers .= 'X-Mailer: PHP/' . phpversion(); // Mailer

        // Attachments    
        $files = $this->attachment;   

        // Boundary  
        $semi_rand = md5(time());  
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
    
        // Headers for attachment  
        $headers .= "\nContent-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  
    
        // Multipart boundary  
        $message = "--{$mime_boundary}\n" . "Content-Type: $type; charset=\"UTF-8\"\n" . 
        "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";  
    
        // Preparing attachment 
        if(!empty($files)){ 
            for($i=0;$i<count($files);$i++){ 
                if(is_file($files[$i])){ 
                    $file_name = basename($files[$i]); 
                    $file_size = filesize($files[$i]); 
                    
                    $message .= "--{$mime_boundary}\n"; 
                    $fp =    @fopen($files[$i], "rb"); 
                    $data =  @fread($fp, $file_size); 
                    @fclose($fp); 
                    $data = chunk_split(base64_encode($data)); 
                    $message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .  
                    "Content-Description: ".$file_name."\n" . 
                    "Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
                    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
                } 
            } 
        } 
        
        $message .= "--{$mime_boundary}--"; 

        return compact("subject", "message", "to", "headers");
    }

    /*
    * Send
    * Send a Email
    */
    public function send_mail($prepare)
    {
        return @mail($prepare->to, $prepare->subject, $prepare->message, $prepare->headers);
    }

}