<?php
global $post;
$post_type = get_queried_object()->post_type;
$i = 'sidebar_related_posts';
if(isset($sidebarExclude)){
	array_push($sidebarExclude, $post->ID);
}else{
	$sidebarExclude = array($post->ID);
}

// Set up the arguments
$sidebar_taxonomy_query = array();

$sidebar_args = array(
	'post_status' => 'publish', 
	"post_type" => $post_type,
	"posts_per_page" => 3,
	"post__not_in" => $sidebarExclude
);

// If the override is selected 
if(get_field('sidebar_posts_override_bool'))  {
	$sidebarSelectMethod = get_field('sidebar_select_method', $post->ID);

	switch ($sidebarSelectMethod) {
		// If choosing categories/taxonomy terms
	    case "category":		
			$sidebar_args['post_type'] = get_field('post_type', $post->ID);
			$tax = array(
				'relation' => 'AND',
			);
			$sidebar_taxonomy_query = array($tax);
			foreach(get_field('sidebar_taxonomy_filters', $post->ID) as $term) {
				if(!empty($term)) {
					$term_array = array('taxonomy' => $term->taxonomy, 'field' => 'slug', 'terms' => $term->slug);
					array_push($tax, $term_array);
				}
			}

			$sidebar_args['tax_query'] = $tax;


			break;
		// Else if choosing individual posts
	    case "individual":   
			$select_posts  = get_field('sidebar_post_object_content', $post->ID);
			$posts_in = array();
			$post_types = array();
			foreach($select_posts as $s) {
				$type = $s['filter_by_post_type'];
				array_push($posts_in, $s['post_object_'.$type]->ID);
				array_push($post_types, $type);
			}
			$sidebar_args['post_type']  = $post_types;
			$sidebar_args['post__in'] = $posts_in;
	        break; 
	} // END SWITCH 
}
// Build the query
$sidebar_posts = new WP_Query( $sidebar_args );

// If the query has less than three posts, fill them in with with more posts from the current post's type
if(count($sidebar_posts->posts) < 3) {
	$fill_args = array(
		'post_type' => $post_type,
		'numberposts' => 3 - count($sidebar_posts->posts),
		'post__not_in' => $sidebarExclude,
	);
	$fill_posts = get_posts($fill_args);
	foreach($fill_posts as $fill_post) {
		array_push($sidebar_posts->posts, $fill_post);
	}
}
?>

<div class="sidebar-cont">
	<div class="post-sidebar">
		<a href="<?php echo get_post_type_archive_link($post_type); ?>"><h4>Related Articles </h4></a>
		<?php foreach($sidebar_posts->posts as $p) { ?>
			<div class="sidebar-post">
				<?php 
					if($p->post_type == 'videos'){
						$dataAttributes = 'data-fancybox ';
						
						if ( get_field('video_source',$p) == 'vimeo' ):
							$permalink = 'https://player.vimeo.com/video/'.get_field('vimeo_embed_code',$p).'?title=0&byline=0&portrait=0';
						endif;
						if ( get_field('video_source',$p) == 'vimeo_channel' ):
							$dataAttributes = 'target="_blank" ';
							$permalink = get_field('vimeo_channel_url',$p);
						endif;
						if ( get_field('video_source',$p) == 'youtube' ):
							$permalink = 'https://www.youtube.com/embed/'.get_field('youtube_embed_code',$p).'?rel=0&showinfo=0';
						endif;
						if ( get_field('video_source',$p) == 'html5' ):
							$permalink = get_field('html_video',$p);
						endif;	
					} else {
						$permalink = get_permalink($p->ID);
						$dataAttributes = '';
					}
				?>
				<a <?php echo $dataAttributes; ?> href="<?php echo $permalink; ?>">
					<p class="post-title"><?php echo $p->post_title; ?> </p>
					
					<?php if(get_field('blog_image', $p )){ 
						echo wp_get_attachment_image(get_field('blog_image', $p), 'dra-200px-wide','', array('class' => 'sidebar-thumbnail')); 				
					}elseif( has_post_thumbnail($p) ){ 
						echo get_the_post_thumbnail($p, 'dra-200px-wide', array('class' => 'sidebar-thumbnail')); 
					} ?>

					<?php
					$author_name = get_the_author_meta('display_name', $p->post_author); ?>
					<div class="post-author">
						<?php echo get_avatar($p->post_author); ?>
						<p><?php echo $author_name; ?></p>
					</div>
				</a>
			</div>
		<?php } 
		
		wp_reset_query(); ?>

	</div>
</div>