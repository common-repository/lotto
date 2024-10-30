<?php
/*
* Plugin Name: Lotto
* Description: This plugin consists in a widget which displays random lotto numbers when clicking on a button.
* Version: 1.1
* Author: Marc Oliveras
* Author URI: http://oligalma.com
* License: GPLv3
*/

if(!defined('ABSPATH'))
	exit;

class Lotto_Widget extends WP_Widget
{ 
	// actual widget processes
	public function __construct()
	{
		$widget_ops = array(
		'classname' => 'lotto',
		'description' => 'Displays random lotto numbers when clicking on a button.');

		parent::__construct(
		'lotto',  // Base ID
		'Lotto',   // Name
		$widget_ops
		);
	}
 
	// outputs the content of the widget
	public function widget($args, $instance)
	{
		$widget_id = rand(1,999);
	?>
		<p>
			<input class="get-numbers-button" id="get-numbers-button<?= $widget_id ?>" type="button" value="<?= $instance['button_text'] ?>" />
		</p>
		<p>
			<b>Combinations:</b>&nbsp;<input id="combinations<?= $widget_id ?>" type="text" value="5" size="2" />
		</p>
		<div class="numbers-display" id="numbers-display<?= $widget_id ?>"></div>
		<script>
			jQuery(document).ready(function($){
				$("#get-numbers-button<?= $widget_id ?>").click(function(){
					var maxNumber = <?= $instance['max_number'] ?>;
					var ballsNumber = <?= $instance['balls_number'] ?>;
					var maxPowerballNumber = <?= $instance['max_powerball_number'] ?>;
					var enableAnimation = <?= ($instance['enable_animation'] == 'on' ? 'true' : 'false') ?>;
					var delay = <?= $instance['delay'] ?>;
					var enableSound = <?= ($instance['enable_sound'] == 'on' ? 'true' : 'false') ?>;
					
					$("#numbers-display<?= $widget_id ?>").html("");
					
					if(enableSound)
						playSound();
							
					for(z = 1; z <= $("#combinations<?= $widget_id ?>").val(); z++)
					{
						var numbers = [];
						for (i = 0; i < ballsNumber; i++) {
							var number = Math.floor(Math.random() * maxNumber) + 1;
							var found = false;
							for(j = 0; j < numbers.length; j++)
							{
								if(numbers[j] == number)
									found = true;
							}
							if(!found)
								numbers.push(number);
							else
								i--;
						}

						numbers.sort(function sortNumber(a,b){
							return a - b;
						});
						
						var powerBall = Math.floor(Math.random() * maxPowerballNumber) + 1;
						
						if(enableAnimation)
						{
							$("#numbers-display<?= $widget_id ?>").append('<b class="hidden-combination" id="combination' + z + '<?= $widget_id ?>' + '">' + z + '</b>');
							
							$.each(numbers, function (index, value) {
								$("#numbers-display<?= $widget_id ?>").append('<div class="lotto-ball hidden-ball hidden-ball<?= $widget_id ?>">' + value + '</div>');
							});

                            if(maxPowerballNumber)
                                $("#numbers-display<?= $widget_id ?>").append('<div class="lotto-ball power-ball hidden-ball hidden-ball<?= $widget_id ?>">' + powerBall + '</div>');
                        
							$(".hidden-ball<?= $widget_id ?>").each(function (index, value) {
								$(this).delay(delay * index).fadeIn();
							});
							
							$("#combination" + z + '<?= $widget_id ?>').delay(delay * ballsNumber * z).fadeIn();
						}
						else
						{	
							$("#numbers-display<?= $widget_id ?>").append('<b>' + z + '</b>');
							
							$.each(numbers, function (index, value) {
								$("#numbers-display<?= $widget_id ?>").append('<div class="lotto-ball">' + value + '</div>');
							});
							
                            if(maxPowerballNumber)
                                $("#numbers-display<?= $widget_id ?>").append('<div class="lotto-ball power-ball">' + powerBall + '</div>');
						}
						
						$("#numbers-display<?= $widget_id ?>").append('<div class="clear"></div>');
					}
				});
				
				function playSound()
				{
					var blop_audio = document.createElement("audio");
					blop_audio.setAttribute("src", "<?= plugins_url('/magic.mp3', __FILE__) ?>");
					blop_audio.play();		
				}
			});      
		</script>
	<?php
	}
	
	// outputs the options form in the admin
	public function form($instance)
	{
		/* Set up some default widget settings. */		
		if(empty($instance['max_number']))
			$instance['max_number'] = 49;
		if(empty($instance['balls_number']))
			$instance['balls_number'] = 6;
		if(is_null($instance['max_powerball_number']))
			$instance['max_powerball_number'] = 0;
		if(is_null($instance['delay']))
			$instance['delay'] = 100;
		if(empty($instance['button_text']))
			$instance['button_text'] = 'Get numbers';
	?>
		<p>
		<label for="<?php echo $this->get_field_id('max_number'); ?>">Maximum number:</label>
		<input type="number" name="<?php echo $this->get_field_name('max_number') ?>" id="<?php echo $this->get_field_id('max_number') ?> " value="<?php echo $instance['max_number'] ?>" size="20">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('balls_number'); ?>">Balls number:</label>
		<input type="number" name="<?php echo $this->get_field_name('balls_number') ?>" id="<?php echo $this->get_field_id('balls_number') ?> " value="<?php echo $instance['balls_number'] ?>" size="20">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('max_powerball_number'); ?>">Max powerball number:</label>
		<input type="number" name="<?php echo $this->get_field_name('max_powerball_number') ?>" id="<?php echo $this->get_field_id('max_powerball_number') ?>" value="<?php echo $instance['max_powerball_number'] ?>" size="20">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('enable_animation'); ?>">Enable animation</label>
		<input type="checkbox" id="<?php echo $this->get_field_id('enable_animation'); ?>" name="<?php echo $this->get_field_name('enable_animation'); ?>" <?php if ($instance['enable_animation']) echo 'checked="checked"' ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('delay'); ?>">Delay:</label>
		<input type="number" name="<?php echo $this->get_field_name('delay') ?>" id="<?php echo $this->get_field_id('delay') ?> " value="<?php echo $instance['delay'] ?>" size="20">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('enable_sound'); ?>">Enable sound</label>
		<input type="checkbox" id="<?php echo $this->get_field_id('enable_sound'); ?>" name="<?php echo $this->get_field_name('enable_sound'); ?>" <?php if ($instance['enable_sound']) echo 'checked="checked"' ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('button_text'); ?>">Button text</label>
		<input type="text" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" value="<?php echo $instance['button_text'] ?>" size="20" />
		</p>
	<?php
	}
	
	// processes widget options to be saved
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
	
		if(!empty($new_instance['max_number']) && $new_instance['max_number'] > 0)
			$instance['max_number'] = $new_instance['max_number'];
		if(!empty($new_instance['balls_number']) && $new_instance['balls_number'] > 0)
			$instance['balls_number'] = $new_instance['balls_number'];
		if(!is_null($new_instance['max_powerball_number']) && $new_instance['max_powerball_number'] >= 0)
			$instance['max_powerball_number'] = $new_instance['max_powerball_number'];
		if(!is_null($new_instance['delay']) && $new_instance['delay'] >= 0)
			$instance['delay'] = $new_instance['delay'];
		if(!empty($new_instance['button_text']))
			$instance['button_text'] = $new_instance['button_text'];
			
		$instance['enable_animation'] = $new_instance['enable_animation'];
		$instance['enable_sound'] = $new_instance['enable_sound'];
	
		return $instance;
	}
}

!is_admin() && add_action('init', 'lotto_register_styles');

function lotto_register_styles()
{
    wp_enqueue_style('lotto_style', plugins_url('/style.css', __FILE__));
}

add_action('widgets_init', function() { register_widget( 'Lotto_Widget' ); }); 

