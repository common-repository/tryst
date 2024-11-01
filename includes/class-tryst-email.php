<?php
/**
* Fired during plugin activation
*
* @link       https://matteus.dev
* @since      1.0.0
*
* @package    Tryst
* @subpackage Tryst/includes
*/
/**
* Fired during plugin activation.
*
* This class defines all code necessary to run during the plugin's activation.
*
* @since      1.0.0
* @package    Tryst
* @subpackage Tryst/includes
* @author     Matteus Barbosa <contato@desenvolvedormatteus.com.br>
*/
class Tryst_Email {
    protected $object, $key, $form, $title, $body, $attachments, $recipient;
    /* 
    * $key: unique id that helps finding the right layout, title and placeholders
    */
    public function __construct($object, $key){
        $this->object = $object;
        $this->key = $key;
        $this->getObject()->setEmailKey($key);
        /* 
        * Every object that sends e-mail must be aware of a common interface
        */
        $this->file_path = $this->getObject()->getEmailFilePath();
        $this->title = $this->getObject()->getEmailTitle();
        $this->file_dom = $this->getFileDom();
        $this->email_dom = $this->getEmailDom();
    }
    public function addRecipient($email){
        $this->recipient[] = $email;
    }
    public function getFileDom(){
        $body = file_get_contents($this->file_path);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        $doc->loadHTML("<?xml encoding='utf-8' ?>".$body);
        // We need to validate our document before refering to the id
        return $doc;
        //echo "The element whose id is 'php-basics' is: " . $doc->getElementById('part-2')->tagName . "\n";
    }
    protected function appendHTML(DOMNode $parent, $source) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML("<?xml encoding='utf-8' ?>".$source);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }
    public function getEmailDom(){
        $this->appendHTML($this->file_dom->getElementById('intro'), $this->getObject()->getEmailIntro());
        $this->appendHTML($this->file_dom->getElementById('form'), $this->getObject()->getEmailForm());
        if(!empty($this->getObject()->getEmailFooter()))
        $this->appendHTML($this->file_dom->getElementById('footer'), $this->getObject()->getEmailFooter());
        return $this->file_dom->saveHTML();
    }
    /* polymorphic: meeting, member, event... etc */
    public function setObject($obj){
        $this->object = $obj;
    }
    /* polymorphic: meeting, member, event... etc */
    public function getObject(){
        return $this->object;
    }
    public function send(){
        $to = $this->recipient;
        $subject = $this->title;
        $body = $this->email_dom;
        $headers = 'Content-Type: text/html; charset=UTF-8';
        $sent = wp_mail( $to, $subject, $body, $headers );
        return $sent;
    }
}