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
| Gates trait
|---------------------------------------------------
*/
trait GatesTrait {

    /*
    * Mail
    * Set mailer as mail
    */
    public function mail()
    {
        $this->mailer('mail');

        return clone($this);
    }

    /*
    * SMTP
    * Set mailer as SMTP
    */
    public function smtp()
    {
        $this->mailer('smtp');

        return clone($this);
    }

}