<?php

/* 
* children for Agenda
* days are made up in runtime
*/

namespace Tryst;

class Day{
    
    private $timestamp, $agenda, $meetings, $isdayoff;
    
    public function __construct( $timestamp, $agenda, $meetings = null){
        
        $this->timestamp = $timestamp;
        
        $this->isdayoff = $this->isDayOff();
        
        $this->agenda = $agenda;
        
        $this->available = $this->getAvailable();
        
        if($meetings != null)
        $this->meetings = $meetings != null ? $meetings : $this->agenda->getMeetings($timestamp->format('Y-m-d'));
    }
    
    public function setDayOff(){
        $this->isdayoff = true;
    }
    
    public function isDayOff(){
        $options = get_option('tryst_option');
        
        if($this->timestamp->format('N') > 5){
            $this->setDayOff();
            return true;
        }
        
        if(!empty( $options['date_lock'])){
            $dates_lock = explode(',', $options['date_lock']);
            $current_date = $this->getTimestamp()->format('Y-m-d'); 
            
            if(in_array($current_date, $dates_lock)){
                $this->setDayOff();
                return true;
            }
        }
        
        return false;
        
    }
    
    public function getTimestamp(){
        return $this->timestamp;
    }
    
    public function getMeetings(){
        return $this->meetings;
    }
    
    public function getAgenda(){
        return $this->agenda;
    }
    
    public function getAvailable(){
        if(isset($this->available))
        return $this->available;
        else
        return $this->load_available();
    }
    
    public function load_available(){
        
        $timesheet = [];
        $custom_fields = get_term_meta($this->getAgenda()->getId());
        $today = new \DateTime(); 

        $data = json_decode($custom_fields['tryst_available_ops'][0]);
        
        foreach($data as $k => $time){
            $split = explode(':', $time); $h = $split[0]; $i = $split[1]; 
            $this->getTimestamp()->setTime($h, $i); 
            $diff = $today->diff($this->getTimestamp()); 
            
            if($diff->h >= 0 && $diff->invert == 0){
                $timesheet[$k] = $time;
            }
        }
        
        return $timesheet;
    }
}