<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Nghia Nguyen <yiidevelop@hotmail.com>
 * @since 2.0
 */

namespace yii\mailer;

use Yii;

/**
 * 
 * @property \Swift_Mailer $mailer Description
 * @property \Swift_Message $message Description
 * @property \Swift_Transport $transport Description
 */
class Mailer extends \yii\base\Object
{

    public $priperty = 3;
    public $charset = 'utf-8';
    public $contentType = 'text/html';
    public $transportType = 'php';
    public $transportOptions = array();

    /**
     * @var string|array
     */
    public $from = array('webmaster@localhost', 'Webmaster');

    /**
     * @var array An array of failures by-reference
     */
    public $failedRecipients = array();
    public $viewPath = '@app/views/mailer';
    protected $_transport;
    protected $_message;
    protected $_mailer;

    public function getMailer()
    {
        if (empty($this->_mailer)) {
            $this->_mailer = new \Swift_Mailer($this->getTransport());
        }
        return $this->_mailer;
    }

    public function getMessage()
    {
        if (empty($this->_message)) {
            $this->_message = \Swift_Message::newInstance();
            $this->_message->setCharset($this->charset);
            $this->_message->setPriority($this->priority);
            $this->_message->setFrom($this->from);
        }
        return $this->_message;
    }

    public function getTransport()
    {
        if (!empty($this->_transport)) {
            return $this->_transport;
        }
        switch ($this->transportType) {
            case 'smtp':
                $this->_transport = \Swift_SmtpTransport::newInstance();
                if (count($this->transportOptions)) {
                    foreach ($this->transportOptions as $method => $value) {
                        $this->_transport->{'set' . ucfirst($method)}($value);
                    }
                }
                break;
            case 'sendmail':
                $this->_transport = \Swift_SendmailTransport::newInstance();
                if (count($this->transportOptions)) {
                    foreach ($this->transportOptions as $method => $value) {
                        $this->_transport->{'set' . ucfirst($method)}($value);
                    }
                }
                break;
            default:
                $this->_transport = \Swift_MailTransport::newInstance();
                $this->_transport->setExtraParams($this->transportOptions);
                break;
        }
        return $this->_transport;
    }

    public function attachment($path, $filename = null, $contentType = null)
    {
        $attachment = \Swift_Attachment::fromPath($path, $contentType);
        if (!empty($filename)) {
            $attachment->setFilename($filename);
        }
        return $this->message->attach($attachment);
    }

    public function embedImage($path, $filename = null, $contentType = null)
    {
        $image = \Swift_Image::fromPath($path);
        if (!empty($filename)) {
            $image->setFilename($filename);
        }
        if (!empty($contentType)) {
            $image->setContentType($contentType);
        }
        return $this->message->embed($image);
    }

    /**
     * Add a To: address to this message.
     * If $name is passed this name will be associated with the address.
     * @param string|array $address
     * @return \Swift_Message
     */
    public function setTo($addresses)
    {
        return $this->message->setTo($addresses);
    }

    public function setSubject($subject)
    {
        return $this->message->setSubject($subject);
    }

    public function setBody($body)
    {
        return $this->message->setBody($body);
    }

    /**
     * 
     * @param string $view
     * @param type $params
     * @return string
     */
    public function renderTemplate($view, $params = array())
    {
        $view = Yii::getAlias($this->viewPath) . DIRECTORY_SEPARATOR . $view . '.php';
        return Yii::$app->getView()->renderFile($view, $params);
    }

    /**
     * Send the given Message like it would be sent in a mail client.
     * All recipients (with the exception of Bcc) will be able to see the other recipients this message was sent to.
     * Recipient/sender data will be retrieved from the Message object.
     * The return value is the number of recipients who were accepted for delivery.
     * @return integer
     */
    public function send()
    {
        return $this->mailer->send($this->message, $this->failedRecipients);
    }

}