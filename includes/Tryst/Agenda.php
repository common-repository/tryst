<?php
/* 
* Tryst main object to support a meetings catalog
* there can be multiple agendas
*/
namespace Tryst;
class Agenda{
    private $id, 
    $month,
    $year,
    $schedule,
    $options,
    $domain;
    public function __construct($agenda_id = null, 
    $month = null,
    $year = null,
    $schedule = null){

        $this->id = $agenda_id;
        if($month != null && $year != null ){
            $this->month = $month;
            $this->year = $year;
            $this->timestamp_start = $this->getTimestampStart();
            $this->week_start = $this->getWeekFirstDay();
            $this->week_end = $this->getWeekLastDay();
        }
        $this->category = $this->load_category();
        $this->schedule = $schedule != null ? $schedule : $this->load_schedule();
        $this->options = get_option('tryst_option');
    }
    public function setMonth($month){
        $this->month = $month;
    }
    public function setYear($year){
        $this->year = $year;
    }
    public function getPreviousMonthLink(){
        global $post;
        $dt = $this->getTimestampStart();
        $dt_prev = $dt->sub(new \DateInterval('P1M'));
        $url = add_query_arg( array(
            'tryst_yr' => $dt_prev->format('Y'),
            'tryst_mth' => $dt_prev->format('m'),
        ),  get_permalink() );
        return $url;
    }
    public function getNextMonthLink(){
        global $post;
        $dt = $this->getTimestampStart();
        $dt_next = $dt->add(new \DateInterval('P1M'));
        $url = add_query_arg( array(
            'tryst_yr' => $dt_next->format('Y'),
            'tryst_mth' => $dt_next->format('m'),
        ),  get_permalink() );
        return $url;
    }
    public function getOptions(){
        return $this->options;
    }
    public function getId(){
        return $this->category->term_id;
    }
    public function save(){
        $custom_fields = get_term_meta($this->id);
        $tryst_option = $this->getOptions();
        if(isset($custom_fields['tryst_available_ops']) && count($custom_fields['tryst_available_ops']) > 0){
            delete_term_meta($this->id, 'tryst_available_ops');
            add_term_meta($this->id, 'tryst_available_ops', $custom_fields['tryst_available_ops'][0]);
        }
    }
    public function getMonth(){
        return $this->month;
    }
    public function getYear(){
        return $this->year;
    }
        /* 
    * return número 1 a 7 para dia da semana
    */
    public function getDateTime(){
        $d = new \DateTime($this->getYear().'-'.$this->getMonth().'-01');
        return $d;
    }
    /* 
    * return número 1 a 7 para dia da semana
    */
    public function getWeekFirstDay(){
        $d = new \DateTime($this->getYear().'-'.$this->getMonth().'-01');
        return $d->format('w')+1;
    }
    /* 
    * return número 1 a 7 para dia da semana
    */
    public function getWeekLastDay(){
        $days = $this->getTimestampStart()->format('t');
        $d = new \DateTime($this->getYear().'-'.$this->getMonth().'-'.$days);
        return $d->format('w')+1;
    }
    public function getTimestampStart(){
        $d = new \DateTime($this->getYear().'-'.$this->getMonth().'-01');
        return $d;
    }
    public function load_category(){
        global $wpdb;
        if(!empty($this->id)){
            $this->category =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}terms WHERE term_id = ".$this->id, OBJECT );
            return $this->category;
        }
        $agenda_main_name = __('Tryst Agenda', 'tryst');
        $category = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}terms WHERE name = '$agenda_main_name' ", OBJECT );
        $this->category = $category;
        return $category;
    }
    public function thumbnail(){
        $image = plugin_dir_url( __FILE__ ) . '../../public/img/folder-1.png';
        return $image;
    }
    public function load_posts(){
        $args = array(
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => 'tryst',
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'tax_query' => array(
                array(
                    'taxonomy' => 'agenda',
                    'field'    => 'term_id',
                    'terms'    => [$this->category->term_id]
                    )
                ));
                $posts_array = get_posts( $args );
                return $posts_array;
            }
            public function getMeetings($date = null){
                $posts = $this->load_posts();
                $meetings = [];
                foreach($posts as $k => $p){    
                    $meeting = new Meeting($p->ID, null, $this);    
                    $meeting_date = $meeting->getMeta('date');
                    $meeting_time = $meeting->getMeta('time');
                    $meetings[$meeting_date][$meeting_time] = $meeting;
                }
                if($date != null && isset($meetings[$date]))
                return $meetings[$date];
                else
                return $meetings;
            }
            public function load_schedule(){
                global $tryst_plugin;
                $schedule = [];
                $meetings = $this->getMeetings();
                for($c = 1; $c <= $this->getTimestampStart()->format('t'); $c++){
                    $d = new \DateTime($this->getYear().'-'.$this->getMonth().'-'.$c);
                    $index = $d->format('Y-m-d');
                    $day_class =  !empty($tryst_plugin->getNamespace()) ? $domain_class = "\Tryst\\Domain\\Day" :"\Tryst\\Day";	
                    if(isset($meetings[$index]))
                    $schedule[$c] = new  $day_class($d, $this, $meetings[$index]);
                    else
                    $schedule[$c] = new  $day_class($d, $this);
                }
                return $schedule;
            }
            public function getSchedule(){
                return $this->schedule;
            }
        }