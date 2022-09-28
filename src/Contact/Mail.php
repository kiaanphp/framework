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
namespace Kiaan\Contact;

/*
|---------------------------------------------------
| Mail
|---------------------------------------------------
*/
class Mail {

    /**
    * Traits
    *
    */
    use Mail\GatesTrait;
    use Mail\MailTrait;
    use Mail\SmtpTrait;

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;

    /*
    * Variables
    */
    const CRLF = "\r\n";
    protected $_config;

    protected $from = array();
    protected $to = array();
    protected $subject;
    protected $isHtml = false;
    protected $message;
    protected $reply = array();
    protected $cc = array();
    protected $bcc = array();
    protected $attachment = array();
    protected $mailer = 'mail';
    protected $isTLS = false;
    protected $isSSL = false;
    protected $server;
    protected $hostname;
    protected $port;
    protected $username;
    protected $password;

    /**
     * Construct
     * 
    */
    public function __construct() {}

    /**
     * Server
     * 
    */
    public function server($server, $port)
    {
        $this->server = $server;
        $this->port = $port;
        $this->hostname = gethostname();

        if($this->port == "smtp"){
            $this->setHeader('X-Mailer', 'PHP/' . phpversion());
            $this->setHeader('MIME-Version', '1.0');
        }

        return clone($this);
    }

    /**
     * Set SMTP Login authentication
     *
     * @param string $username
     * @param string $password
     * @return Email
     */
    public function login($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        return clone($this);
    }

    /**
     * reset
     *
     * Resets all properties to initial state.
     *
     * @return self
     */
    protected function _reset()
    {
        //$this->from = array();
        $this->to = array();
        $this->subject = '';
        $this->isHtml = false;
        $this->message = '';
        $this->reply = array();
        $this->cc = array();
        $this->bcc = array();
        $this->attachment = array();
        $this->mailer = 'mail';
        //$this->isTLS = false;
        //$this->isSSL = false;
        //$this->hostname = '';
        //$this->server = '';
        //$this->port = '';
        //$this->username = '';
        //$this->password = '';

        // SMTP
        $this->socket = '';
        //$this->protocol = null;
        $this->logs = array();
        $this->headers = array();
    }

    /*
    * From
    *
    */
    public function from($email, $name='')
    {
        $this->from = array($email, $name);
        return clone($this);
    }

    /*
    * To
    *
    */
    public function to($email, $name='')
    {
        $this->to[] = array($email, $name);
        return clone($this);
    }

    /*
    * Reply
    *
    */
    public function reply($email, $name='')
    {
        $this->reply[] = array($email, $name);
        return clone($this);
    }

    /*
    * CC
    *
    */
    public function cc($email, $name='')
    {
        $this->cc[] = array($email, $name);
        return clone($this);
    }

    /*
    * BCC
    *
    */
    public function bcc($email, $name='')
    {
        $this->bcc[] = array($email, $name);
        return clone($this);
    }


    /*
    * Subject
    *
    */
    public function subject($value)
    {
        $this->subject = $value;
        return clone($this);
    }

    /*
    * HTML
    *
    * Enable Html in message.
    */
    public function html()
    {
        $this->isHtml = true;
        return clone($this);
    }

    /*
    * Message
    *
    */
    public function message($value)
    {
        $this->message = $value;
        return clone($this);
    }

    /*
    * Attach
    *
    */
    public function attach($file)
    {
        if (file_exists($file)) {
            $this->attachment[]  = $this->filesystemRoot() . $file;
        }
        return clone($this);
    }
  
    /*
    * Mailer
    *
    * Set mailer
    */
    public function mailer($mailer='mail')
    {
        switch ($mailer) {
            case "mail":
                $this->mailer='mail';
            break;
            case "smtp":
                $this->mailer='smtp';
            break;            
            default:
            $this->mailer='mail';
        }
        return clone($this);
    }

    /*
    * Prepare
    *
    * Prepare to send a Email
    */
    protected function prepare()
    {
        switch ($this->mailer) {
            case "mail":
                $prepare = (object) $this->prepare_mail();
                $send = $this->send_mail($prepare);
            break;
            case "smtp":
                $send = $this->send_smtp();
            break;
        }
        
        return $send;
    }

    /*
    * Send
    *
    * Send a Email
    */
    public function send()
    {
        $prepare = $this->prepare();
        
        // Reset
        $this->_reset();

        return $prepare;
    }

}