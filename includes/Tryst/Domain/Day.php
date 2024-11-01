<?php

/* 
* children for Agenda
* days are made up in runtime
*/

namespace Tryst\Domain;

use Tryst\Day as D;

class Day extends D{

    private $timestamp, $agenda, $meetings, $isdayoff;

    public function __construct( $timestamp, $agenda, $meetings = null){
       
        parent::__construct($timestamp, $agenda, $meetings);

        $this->isDayOff();
    }

    public function isDayOff(){
        $options = get_option('tryst_option');

        if( $this->getTimestamp()->format('N') > 5){
            $this->setDayOff();
            return true;
        }

            if(!empty( $options['date_lock'])){
                $dates_lock = explode(',', $options['date_lock']);
                $current_date = $this->getTimestamp()->format('d/m/Y'); 

            if(in_array($current_date, $dates_lock)){
                $this->setDayOff();
                return true;
            }
        }

        return false;
        
    }
  
}