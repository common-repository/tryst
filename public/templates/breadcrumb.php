<ul class="breadcrumb mt-3 no-print">
    <li>
        <a href="<?php echo site_url(); ?>"><?= __('Home', 'tryst') ?></a>
    </li>
    <li>
        <a href="<?php the_permalink(); ?>" class="c-title"><?php the_title(); ?></a>
    </li>
    <?php if(isset($agenda_id )):  ?>
    <?php 
    $agenda = new Tryst\Agenda($agenda_id); 
    $agenda->load_category(); 
    ?>
    <li>
        <a href="<?php get_page_link(); ?>?agenda_id=<?php echo $agenda->cat_ID; ?>" class="c-title"><?php echo $agenda->category->name; ?></a>
    </li>                
    <?php endif; ?>    
    
    <?php if(isset($meeting)):  ?>
    <li>
        <a href="<?php echo get_permalink(); ?>?meeting_id=<?php echo $meeting->meeting->ID; ?>" class="c-title"><?php echo $meeting->meeting->post_title; ?></a>
    </li>
    <?php endif; ?>   
    
    
</ul>