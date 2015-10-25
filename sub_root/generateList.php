<?php
if ($handle = opendir('./images/past_events/2012/2012_pictures')) {
    echo "Directory handle: $handle\n";
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
		if ($entry!='..' && $entry !='.')
		{
			$file	=  $entry ;
			$var[]	= '<a class="highslide" href="{IMAGE_PATH}past_events/2012/2012_pictures/'. $file .'" title="" onclick="return hs.expand(this, miniGalleryOptions1)"><img src="{IMAGE_PATH}past_events/2012/2012_pictures_thumbnails/'. $file .'" alt="" title=""/></a>';
		}
    }
	sort($var, SORT_STRING);
	foreach ( $var as $file)
	{
		echo $file ."\n";
	}
    closedir($handle);
}
?>
