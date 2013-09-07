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
 * The component mailer for yii2 framework to send email message.
 * 
 * @property \Swift_Mailer $mailer Swift Mailer class.
 * @property \Swift_Message $message Message (RFC 2822) object.
 * @property \Swift_Transport $transport The Transport used to send messages.
 */
class Mailer extends \yii\base\Object
{

    /**
     * Set the priority of this message.
     * @var int The value is an integer where 1 is the highest priority and 5 is the lowest. 
     */
    public $priority = 3;

    /**
     * @var string The character set of this message.
     */
    public $charset = 'utf-8';

    /**
     * @var string The Content-type of this message.
     */
    public $contentType = 'text/html';

    /**
     * @var string The Transport used to send messages. cases smtp|sendmail default is php.
     */
    public $transportType = 'php';
    public $transportOptions = array();

    /**
     * @var string|array The sender of this message.
     */
    public $from = array('webmaster@localhost', 'Webmaster');

    /**
     * @var array An array of failures by-reference
     */
    public $failedRecipients = array();
    public $viewPath = '@app/views/mailer';

    /**
     * The Transport used to send messages 
     */
    protected $_transport;

    /**
     * The Message (RFC 2822) object.
     */
    protected $_message;

    /**
     * Swift Mailer class.
     */
    protected $_mailer;

    /**
     * Swift Mailer class.
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        if (empty($this->_mailer)) {
            $this->_mailer = new \Swift_Mailer($this->getTransport());
        }
        return $this->_mailer;
    }

    /**
     * A Message (RFC 2822) object.
     * @return \Swift_Message
     */
    public function getMessage()
    {
        if (empty($this->_message)) {
            $this->_message = \Swift_Message::newInstance();
            $this->_message->setPriority($this->priority);
            $this->_message->setCharset($this->charset);
            $this->_message->setContentType($this->contentType);
            $this->_message->setFrom($this->from);
        }
        return $this->_message;
    }

    /**
     * The Transport used to send messages.
     * @return \Swift_Transport
     */
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

    /**
     * Attach a [[Swift_Mime_MimeEntity]] such as an Attachment or MimePart.
     * @param string $path Path a file to attach. (ex: Yii::getAlias('@webroot/upload/docs.pdf'); ).
     * @param string $filename The visual file name for attachment file. (ex: document.pdf).
     * @param string $contentType The header content type of file. (ex: application/pdf).
     * @return type
     */
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
     * ```php
     *      $mailer->setTo('name@domain'=>'Your Name');
     * ```
     * @param string|array $addresses List addresses to send message.
     * @return \Swift_Message
     */
    public function setTo($addresses)
    {
        if (is_array($addresses)) {
            foreach ($addresses as $address) {
                $this->message->addTo($address);
            }
        } else {
            $this->message->setTo($addresses);
        }
        return $this->message;
    }

    /**
     * Set the subject of this message.
     * @param string $subject
     * @return \Swift_Message $message
     */
    public function setSubject($subject)
    {
        return $this->message->setSubject($subject);
    }

    /**
     * Set the body of this message.
     * @param string $body
     * @return \Swift_Message $message
     */
    public function setBody($body)
    {
        return $this->message->setBody($body);
    }

    /**
     * Renders a view file.
     * @param string $view the view file.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result
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