<?php echo $this->fd->html('alert.extended', 'This is just some title here', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Maiores, quibusdam repellendus consequatur, optio iure quos pariatur porro. Dolorem molestias, quae placeat praesentium, consequuntur rem doloribus voluptatibus nemo soluta. Optio, modi?', 'danger', [
	'icon' => 'fdi fas fa-check-circle',
	'button' => $this->fd->html('button.link', 'https://stackideas.com', 'Click Me', 'danger', 'default', [
		'ghost' => true
	]),
	'dismissible' => true
]); ?>
