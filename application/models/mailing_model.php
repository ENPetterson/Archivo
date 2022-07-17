<?php
class Mailing_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public $newsletter_id;
    public $listas;
                
    
    public function enviarMail($html, $senderNombre, $senderMail, $subject, $to, $tags, $images, $attachments){
        
        $this->load->config('sendgrid');
        $this->load->library('sendgrid');
        
        $sendgrid_username = $this->config->item('sendgrid_username');
        $sendgrid_password = $this->config->item('sendgrid_password');
        $sendgrid = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));
        
        $email    = new SendGrid\Email();
        
        $email->setSmtpapiTos($to)->
               setFrom($senderMail)->
               setFromName($senderNombre)->
               setSubject($subject)->
               setHtml($html)->
               addHeader('X-Sent-Using', 'SendGrid-API')->
               addHeader('X-Transport', 'web');
        
        
        $imagePath =  $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
        foreach ($images as $image){
            file_put_contents($imagePath . $image['name'] . '.png', base64_decode($image['content']));
            $email->addAttachment($imagePath . $image['name'] . '.png', $image['name'] . '.png', $image['name']);
        }
        
        foreach($attachments as $attach){
            $email->addAttachment($attach);
        }
        
        $response = $sendgrid->send($email);

        return array('response'=>$response->code);

    }

}