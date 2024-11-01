<?php

/* 
* Media must be sent as attachment to post
* not in use. left for future needs
*/

namespace Tryst;

class Media{



    public function __construct($attachment, $post, $id = null, $sorted_position = null){
        $this->attachment = $attachment;
        $this->post = $post;
        $this->id = $this->attachment->id;
        $this->sorted_position = $sorted_position;
    }

    public function card(){
        $markup = '<div class="col-md-3"><a href="'.get_permalink().'"><figure class="row" data-id="'.$this->id.'">
        <div class="col-4"><img src="'.$this->thumbnail().'" class="img-fluid"></div>
        <div class="col-8">Mídia anexada</div>
        </figure>
        </a></div>';

        return $markup;
    }

    
    public function thumbnail(){
        $image = plugin_dir_url( __FILE__ ) . '../../public/img/picture-1.png';
        $thumbnail = get_the_post_thumbnail_url($this->post);
        return $thumbnail == false ? $image : $thumbnail;
     }

}