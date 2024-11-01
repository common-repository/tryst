<?php

/* 
Meetings must be bound to agenda somehow. 
* post
* member & user
* email
*/

namespace Tryst;

use Tryst\Contracts\Mail;

class Meeting implements Mail {
    
    protected $post, $timestamp, $agenda, $meta, $media_files, $member, $domain, $email_key;
    
    public function __construct($meeting_id = null, $meta = null, $agenda = null, $member = null, $media_files = []){
        global $tryst_plugin;
        $this->post = $meeting_id != null ? get_post($meeting_id) : null;
        
        //new register
        if($meta != null){
            $this->meta = self::getFilteredMeta($meta);
            $this->setTimestamp($this->getMeta('date').' '.$this->getMeta('time'));
        } 
        
        //register exist, query from db
        if($meta == null && $this->post != null){
            $this->meta = $this->load_meta();        
            $this->setTimestamp($this->getMeta('date').' '.$this->getMeta('time'));
        }
        
        $this->agenda = $agenda != null ? $agenda : $this->load_agenda();
        $this->member = $member;
        
        $this->media_files = $media_files;
        
        if($this->member == null && $this->post != null){
          	//member from Member extension
            if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
            $this->setMember(\Tryst\Member::find($this->getMeta('user_id')));
            }
        } 

    }
    
    
    public static function findByFormKey($key){
        global $wpdb;

        $meta = current($wpdb->get_results( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='meeting_key' AND meta_value = '$key'", OBJECT ));

        return self::getInstance($meta->post_id);
    }

    public static function getInstance($meeting_id = null, $meta = null, $agenda = null, $member = null, $media_files = []) {
        return new static($meeting_id, $meta, $agenda, $member, $media_files);
    }

    public function getFormKey(){
        global $wpdb;

        $post_id = $this->post->ID;

        $meta = current($wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = $post_id AND meta_key = 'meeting_key' ", OBJECT ));

        if(empty($meta))
        return null;

        return $meta->meta_value;
    }
    
    public function load_agenda(){
        global $wpdb;

        $agenda_main_name = __('Tryst Agenda', 'tryst');
        
        if($this->post != null && !empty(get_the_category( $this->post->ID )))
        $category = current(get_the_category( $this->post->ID ));
        else
        $category = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}terms WHERE name = '$agenda_main_name' ", OBJECT );

        $date = new \DateTime($this->getMeta('date'));
        return new Agenda($category->term_id, $date->format('m'), $date->format('Y'));
    }
    
    public function save(){   
        global $wpdb;
        if($this->getMember() !== null){
            $member_name = $this->getMember()->getUser()->display_name;
        }
        
        // Create post object
        $post_data = array(
            'post_title'    => isset($member_name) ? __('Meeting', 'tryst').' – '.$member_name  : __('Meeting', 'tryst'),
            'post_content'  => '',
            'post_type'		=> 'tryst',
            'post_status'   => 'publish',
            'post_author'   => 1
        );
        
        $id =  wp_insert_post( $post_data );


        
        if($id){
            
            //save member id with the meeting
            if($this->getMember() !== null){
                $this->setMeta('user_id', $this->getMember()->getUser()->ID);
            }
            
            $this->setMeta('meeting_key', wp_hash($this->getAgenda()->getId().strtotime('now')));

                $table = $wpdb->prefix.'term_relationships';
                $data = array('object_id' => $id, 'term_taxonomy_id' => $this->getAgenda()->getId(), 'term_order' => 0);
                $format = array('%d','%d', '%d');
                $wpdb->insert($table,$data,$format);

            
            foreach($this->getMeta() as $k => $v){
                delete_post_meta($id, $k);
                add_post_meta($id, $k, $v);
            }
            
            $this->post = get_post($id);
        }
        
        return $this;
        
    }
    
    public function getId(){
        return $this->post->ID;
    }
    
    public function setTimestamp($timestamp){
        $this->timestamp = new \DateTime($timestamp);
    }
    
    public function getTimestamp($load_meta = false){
        return $this->timestamp;        
    }
    
    public function setMedia($media_items){
        array_push($this->media, $media_items);
        return $this->media;
    }
    
    public function getMedia(){
        return $this->media;
    }
    
    public function thumbnail(){
        $image = plugin_dir_url( __FILE__ ) . '../../public/img/meeting-1.png';
        $thumbnail = get_the_post_thumbnail_url($this->post);
        return $thumbnail == false ? $image : $thumbnail;
    }
    
    public function card(){
        $markup = '<div class="col-md-3"><a href="'.get_page_link().'?meeting_id='.$this->post->ID.'"><figure class="row" data-id="'.$this->post->ID.'">
        <div class="col-4"><img src="'.$this->thumbnail().'" class="img-fluid"></div>
        <div class="col-8">'.$this->post->post_title.'</div>
        </figure>
        </a></div>';
        
        return $markup;
    }
    
    public function load_media_files(){
        $this->setMedia(get_field('file_1'));
        $this->setMedia(get_field('file_2'));
        $this->setMedia(get_field('file_3'));
        $this->setMedia(get_field('file_4'));
        $this->setMedia(get_field('file_5'));
        
        return $this->getMedia();
    }
    
    public function load_meta(){
        return get_post_meta($this->post->ID);
    }
    
    /* 
    * data to be shown on single-meeting
    */
    public function get_content(){
        return $this->post->post_content;
    }
    
    /**
    * Get the value of post
    */ 
    public function getPost()
    {
        return $this->post;
    }
    
    
    
    /**
    * Set the value of meta from form post values
    *
    * @return  self
    */ 
    public function getFilteredMeta($meta)
    {
        //todo: read this from get_options
        $allowed = ['user_id' => true, 'date' => true, 'time' => true, 'observation' => true];
        
        $date = \DateTime::createFromFormat('d/m/Y', $meta['date']);
        $meta['date'] = $date->format('Y-m-d');
        
        $filtered = array_intersect_key($meta, $allowed);
        
        return $filtered;
    }
    
    
    /**
    * Get the value of member
    */ 
    public function getMember()
    {
        return $this->member;
    }
    
    
    /**
    * Set the value of member
    *
    * @return  self
    */ 
    public function setMember($member)
    {
        $this->member = $member;
        
        return $this;
    }
    
    
    /**
    * Get the value of agenda
    */ 
    public function getAgenda()
    {
        return $this->agenda;
    }
    
    public function setMeta($key, $value){
        $this->meta[$key] = $value;
        return $this;
    }
    
    public function getMeta($key = null)
    {
        if($key != null){            
            if(isset($this->meta[$key])){
                if(is_array($this->meta[$key])){
                    return current($this->meta[$key]);
                } else {
                    return $this->meta[$key];
                }
            } else {
                return null;
            }
        }
        
        return $this->meta;
    }


        
    /*
    * return UL HTML element with all information from object for intro
    */
    public function getAgendaLink(){
        global $post;
        $options = get_option('tryst');
 
        $link = 'https://www.google.com/calendar/render?action=TEMPLATE&text='.$this->getEmailTitle().'&dates='.$this->getTimestamp()->format('Ymd\THi\0\0\Z').'/'.$this->getTimestamp()->format('Ymd\THi\0\0\Z').'&details='.get_permalink().'?tryst_meeting_hash='.$this->getFormKey();

        if(!empty($this->getMeta('location')))
        $link .= '&location='.$this->getMeta('location');

        $link .= '&sf=true&output=xml';

        return $link;
    }
    
    
    /*
    * return UL HTML element with all information from object for intro
    */
    public function getEmailIntro(){
        $member_name = !empty($this->getMember()) ? $this->getMember()->getName() : __('Member', 'tryst');
        $markup = '<p>'. sprintf(__('Hello %s, this is the meeting confirmation to help on memorize the tryst.', 'tryst'), $member_name).'</p>';
        return $markup;
    }
    
    
    /*
    * return UL HTML element with all information from object
    */
    public function getEmailForm(){
        global $post;
        $options = unserialize(get_option('tryst_option'));
        $markup = '<ul>';
        $markup .= '<li><strong>'.__('Date', 'tryst').'</strong>: '.$this->getTimestamp()->format('d/m/Y').'</li>';
        $markup .= '<li><strong>'.__('Hour', 'tryst').'</strong>: '.$this->getTimestamp()->format('H:i').'</li>';
        $markup .= '<li><strong>'.__('Address', 'tryst').'</strong>: '.$this->getMeta('location').'</li>';

        if(!empty($this->getMember())){
            $markup .= '<li><strong>'.__('E-mail', 'tryst').'</strong>: '.$this->getMember()->getEmail().'</li>';
            $markup .= '<li><strong>'.__('Phone', 'tryst').'</strong>: '.$this->getMember()->getPhone().'</li>';
        }

        if(!empty($options['tryst_mail']))
        $markup .= '<li><strong>'.__('Recepcionist E-mail', 'tryst').'</strong>: '.$options['tryst_mail'].'</li>';
        
        $markup .= '<li><strong>'.__('Full information', 'tryst').'</strong>: <a href="'.get_permalink().'?tryst_meeting_hash='.$this->getFormKey().'">'.__('Click to open view on the site', 'tryst').'</a></li>';
        $markup .= '</ul>';
        return $markup;
    }
    
    /*
    * return UL HTML element with all information from object for footer
    */
    public function getEmailFooter(){
        $options = unserialize(get_option('tryst_option'));
        $markup = '<p>'.__('We will be waiting for you.', 'tryst').'</p>';
        $markup .= '<p><a href="'.$this->getAgendaLink().'">+ Add Google Agenda</a></p>';
        $markup .= '<p><a href="'.site_url().'">'.__('Visit our website', 'tryst').'</a></p>';
        if(!empty($options['email_footer']))
        $markup .= $options['email_footer'];
        return $markup;
    }
    
    public function getEmailFilePath(){
        //send mail
        return realpath(dirname(__FILE__)).'/../../public/templates/E-mail/meeting.html';
    }
    
    public function getEmailTitle(){

        global $tryst_plugin;
        
        switch($this->getEmailKey()){
            case "confirm":
            return sprintf(__('Our meeting at %s is now confirmed', 'tryst'), get_bloginfo('name'));
            break;
            case "confirm-repeat":
            return sprintf(__('Repeat requested – Our meeting at %s is now confirmed', 'tryst'), get_bloginfo('name'));
            break;
            case "update":
            return sprintf(__('The meeting at %s is now updated', 'tryst'),  get_bloginfo('name'));
            break;
            case "cancel":
            return sprintf(__('The meeting at %s is now canceled', 'tryst'),  get_bloginfo('name'));
            break;
        }
        
        return __('Meeting', 'tryst');
    }
    
    public function setEmailKey($key){
        $this->email_key = $key;
    }
    
    public function getEmailKey(){
        return $this->email_key;
    }
}