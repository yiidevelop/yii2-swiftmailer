Swift Mailer library for Yii2
================
This is the yii2-swiftmailer extension.

Requirements
------------

required "swiftmailer/swiftmailer": "5.1.*@dev"

Installation 
------------
The preferred way to install this extension is [composer](http://getcomposer.org/download/).
Either run
```
php composer.phar require yiidoc/yii2-swiftmailer "*"
```
or add to the require section of your composer.json.
```
    "require": {
        "php": ">=5.3.0",
        "yiidoc/yii2-swiftmailer": "dev-master"
    },
```
Usage
------------

This example use with transport type is **smtp** with live server email.

```php
    
        $mailer = new yii\mailer\Mailer(array(
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.live.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'yourname@hotmail.com',
                'password' => 'your password',
            ),
            'from' => array('yourname@hotmail.com' => 'Your Name'),
            'viewPath'=>'@app/views/emailtemplate'
        ));

        $mailer->setTo(array('recipient@domain.ltd' => 'Recipient Name'));
        $mailer->message->addCc(array('yiidevelop@hotmail.com'=>'Nghia Nguyen'));
        $mailer->setSubject('Subject of this message');
        $mailer->attachment(Yii::getAlias('@webroot/images/noavatar.png'));
        $logoSrc = $mailer->embedImage(Yii::getAlias('@webroot/images/logo.png'), 'brand.png');
        $body = $mailer->renderTemplate('testemail', array('logo' => $logoSrc));
        $mailer->setBody($body);
        if(!$mailer->send()){
            print_r($mailer->failedRecipients);
        }else{
            echo "Message sent successful.";
        }
```
Create view file testemail.php on web/views/emailtemplate folder.

```
    <div id="#brand"><img src="<?php echo $logo; ?>" /></div>
    <h1>Test email usage yii2-swiftmailer extension</h1>
    <h2>Congratulations!</h2>
    <p>You have successfully sent email.</p>
    <p>If you have any questions please contact us via email yiidevelop@hotmail.com</p>

    <p>Thank you for choosing yii2-swiftmailer component</p>
```

This extension is in the process of construction. 
You can test it and contributed to the building with us.

