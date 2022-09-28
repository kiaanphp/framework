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
| Smtp trait
|---------------------------------------------------
*/
trait SmtpTrait {

    /** 
     * @var resource $socket 
     * 
    */
    protected $socket;

    /** 
     * @var int $connectionTimeout
     * 
     */
    protected $connectionTimeout = 30;

    /**
     *  @var int $responseTimeout
     * 
    */
    protected $responseTimeout = 8;

    /**
     *  @var string|null $protocol
     * 
    */
    protected $protocol = null;

    /** 
     * @var array $logs 
     * 
    */
    protected $logs = array();

    /** 
     * @var string $charset
     * 
    */
    protected $charset = 'utf-8';

    /** 
     * @var array $headers
     * 
     */
    protected $headers = array();

    // CRLF
    protected $CRLF = "\r\n";

    /**
     * @param string $key
     * @param mixed|null $value
     * @return Email
     */
    public function setHeader($key, $value = null)
    {
        $this->headers[$key] = $value;

        return clone($this);
    }

    /**
     * Get message character set
     *
     * @param string $charset
     * @return Email
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return clone($this);
    }

    /**
     * Set SMTP Server protocol
     * -- default value is null (no secure protocol)
     *
     * @param string $protocol
     * @return Email
     */
    public function protocol($protocol = null)
    {
        switch ($protocol) {
            case "tls":
                $this->isTLS = true;
                $this->isSSL = false;
                $this->protocol = "tcp";
              break;
            case "ssl":
                $this->isTLS = false;
                $this->isSSL = true;
                $this->protocol = "ssl";
              break;
            default:
            $this->protocol = $protocol;
        }

        return clone($this);
    }

    /**
     * Get log array
     * -- contains commands and responses from SMTP server
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Send email to recipient via mail server
     *
     * @return bool
     */
    public function send_smtp()
    {
        $message = null;
        $this->socket = fsockopen(
            $this->getServer(),
            $this->port,
            $errorNumber,
            $errorMessage,
            $this->connectionTimeout
        );

        if (empty($this->socket)) {
            return false;
        }

        $this->logs['CONNECTION'] = $this->getResponse();
        $this->logs['HELLO'][1] = $this->sendCommand('EHLO ' . $this->hostname);

        if ($this->isTLS) {
            $this->logs['STARTTLS'] = $this->sendCommand('STARTTLS');
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->logs['HELLO'][2] = $this->sendCommand('EHLO ' . $this->hostname);
        }

        $this->logs['AUTH'] = $this->sendCommand('AUTH LOGIN');
        $this->logs['USERNAME'] = $this->sendCommand(base64_encode($this->username));
        $this->logs['PASSWORD'] = $this->sendCommand(base64_encode($this->password));
        $this->logs['MAIL_FROM'] = $this->sendCommand('MAIL FROM: <' . $this->from[0] . '>');

        $recipients = array_merge($this->to, $this->cc, $this->bcc);
        foreach ($recipients as $address) {
            $this->logs['RECIPIENTS'][] = $this->sendCommand('RCPT TO: <' . $address[0] . '>');
        }

        $this->setHeader('Date', date('r'));
        $this->setHeader('Subject', $this->subject);
        $this->setHeader('From', $this->formatAddress($this->from));
        $this->setHeader('Return-Path', $this->formatAddress($this->from));
        $this->setHeader('To', $this->formatAddressList($this->to));

        if (!empty($this->replyTo)) {
            $this->setHeader('Reply-To', $this->formatAddressList($this->replyTo));
        }

        if (!empty($this->cc)) {
            $this->setHeader('Cc', $this->formatAddressList($this->cc));
        }

        if (!empty($this->bcc)) {
            $this->setHeader('Bcc', $this->formatAddressList($this->bcc));
        }

        $boundary = md5(uniqid(microtime(true), true));
        $this->setHeader('Content-Type', 'multipart/mixed; boundary="mixed-' . $boundary . '"');

        if (!empty($this->attachment)) {
            $this->headers['Content-Type'] = 'multipart/mixed; boundary="mixed-' . $boundary . '"';
            $message .= '--mixed-' . $boundary . $this->CRLF;
            $message .= 'Content-Type: multipart/alternative; boundary="alt-' . $boundary . '"' . $this->CRLF . $this->CRLF;
        } else {
            $this->headers['Content-Type'] = 'multipart/alternative; boundary="alt-' . $boundary . '"';
        }


        // Type
        if($this->isHtml){
            $type = "text/html";
        }else{
            $type = "text/plain";
        }

        // Message
        
        $message .= '--alt-' . $boundary . $this->CRLF;
        $message .= "Content-Type: $type; charset=" . $this->charset . $this->CRLF;
        $message .= 'Content-Transfer-Encoding: base64' . $this->CRLF . $this->CRLF;
        $message .= chunk_split(base64_encode($this->message)) . $this->CRLF;


        $message .= '--alt-' . $boundary . '--' . $this->CRLF . $this->CRLF;
        

        if (!empty($this->attachment)) {
            foreach ($this->attachment as $attachment) {
                $filename = pathinfo($attachment, PATHINFO_BASENAME);
                $contents = file_get_contents($attachment);
                $type = mime_content_type($attachment);
                if (!$type) {
                    $type = 'application/octet-stream';
                }

                $message .= '--mixed-' . $boundary . $this->CRLF;
                $message .= 'Content-Type: ' . $type . '; name="' . $filename . '"' . $this->CRLF;
                $message .= 'Content-Disposition: attachment; filename="' . $filename . '"' . $this->CRLF;
                $message .= 'Content-Transfer-Encoding: base64' . $this->CRLF . $this->CRLF;
                $message .= chunk_split(base64_encode($contents)) . $this->CRLF;
            }

            $message .= '--mixed-' . $boundary . '--';
        }

        $headers = '';
        foreach ($this->headers as $k => $v) {
            $headers .= $k . ': ' . $v . $this->CRLF;
        }

        $this->logs['MESSAGE'] = $message;
        $this->logs['HEADERS'] = $headers;
        $this->logs['DATA'][1] = $this->sendCommand('DATA');
        $this->logs['DATA'][2] = $this->sendCommand($headers . $this->CRLF . $message . $this->CRLF . '.');
        $this->logs['QUIT'] = $this->sendCommand('QUIT');
        fclose($this->socket);

        return substr($this->logs['DATA'][2], 0, 3) == 250;
    }

    /**
     * Get server url
     * -- if set SMTP protocol then prepend it to server
     *
     * @return string
     */
    protected function getServer()
    {
        return ($this->protocol) ? $this->protocol . '://' . $this->server : $this->server;
    }

    /**
     * Get Mail Server response
     * @return string
     */
    protected function getResponse()
    {
        $response = '';
        stream_set_timeout($this->socket, $this->responseTimeout);
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= trim($line) . "\n";
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }

        return trim($response);
    }

    /**
     * Send command to mail server
     *
     * @param string $command
     * @return string
     */
    protected function sendCommand($command)
    {
        fputs($this->socket, $command . $this->CRLF);

        return $this->getResponse();
    }

    /**
     * Format email address (with name)
     *
     * @param array $address
     * @return string
     */
    protected function formatAddress($address)
    {
        return (empty($address[1])) ? $address[0] : '"' . addslashes($address[1]) . '" <' . $address[0] . '>';
    }

    /**
     * Format email address to list
     *
     * @param array $addresses
     * @return string
     */
    protected function formatAddressList(array $addresses)
    {
        $data = array();
        foreach ($addresses as $address) {
            $data[] = $this->formatAddress($address);
        }

        return implode(', ', $data);
    }

}