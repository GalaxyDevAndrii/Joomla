<?php
$content = $displayData['content'];
if (!$content) return;

$data = $displayData['data'];

// Overlay
$overlay = '';
if (!empty($data['overlay_type']) && $data['overlay_type'] != 'none') {
	switch ($data['overlay_type']) {
		case 'image': 
			$overlay = "<div class=\"bg-overlay bg-overlay-image\">&nbsp;</div>";

			break;
		case 'video':
			if(!empty($data['video_type']) && !empty($data['video_id'])){
				$overlay = "<div class=\"bg-overlay\"><div class=\"bg-overlay-vid\">";

				if ($data['video_type'] == 'vimeo') {
					$overlay .= '<iframe frameborder="0" width="100%" height="100%" allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen="" src="//player.vimeo.com/video/' . $data['video_id'] . '?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;loop=1&muted=0" allow="autoplay; fullscreen" ></iframe>';
				}
				if ($data['video_type'] == 'youtube') {
					$overlay .= '<iframe frameborder="0" width="100%" height="100%" allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen="" src="//www.youtube.com/embed/' . $data['video_id'] . '?playlist=' . $data['video_id'] . '&amp;rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;loop=1&amp;html5=1&autohide=1&mute=0" allow="autoplay; fullscreen" ></iframe>';
				}

			    $overlay .= '</div></div>';
			}
			break;
		case 'file':
			break;
	}
}


// no section for empty content
$container = empty($data['container']) ? 'container' : $data['container'];
$role = "";
if ($container == '1') $container = 'container';
$secId = !empty($data['id']) ? [$data['id']] : [];
// render section html
$id = !empty($data['id']) ? " id=\"{$data['id']}\"" : '';
$seccls = ['t4-section'];
if (!empty($data['name'])) 
	$seccls[] = ' t4-' . preg_replace('/\s/', '-', strtolower($data['name']));
if(!empty($data['t4-role']))
	$role = ' role="' . strtolower($data['t4-role']).'"';
else
	$role_Arr = !empty($data['name']) ? explode("-", str_replace(" ", "-", $data['name'])) : array('section');
	if(in_array(strtolower($role_Arr[0]), array('header,body,main,banner,nav,mainnav,footer'))){
		$role = ' role="' . strtolower($role_Arr[0]).'"';
	}
if (!empty($data['extra_class'])) {
  $seccls[] = ' ' . $data['extra_class'];
}
if (!empty($data['color_pattern'])) {
  $seccls[] = ' t4-palette-' . $data['color_pattern'];
}
if (!empty($data['sticky'])) {
  $seccls[] = ' t4-sticky';
}
$sectionId = " id='".implode(' ', $secId)."'";
$sectionClasses = ' class="' . implode(' ', $seccls) . '"';

$containerClasses = " class=\"t4-section-inner $container\"";

$open = $close = '';
if ($container == 'none') {
	if (!empty($id) || count($seccls) > 1 || !empty($overlay)) {
		$open = "<div$id$sectionClasses>$overlay";
		$close = "</div>";
	}
} else {
	$open = "<div$id$sectionClasses$role>$overlay\n<div$containerClasses>";
	$close = "</div>\n</div>";
}
?>

<?php echo $open ?>
<?php echo $content ?>
<?php echo $close ?>
