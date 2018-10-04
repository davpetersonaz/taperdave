<?php
//square artist logo
//date / venue / city
//setlist
//source
//notes??
//mega link
//audio sample

logDebug('showinfo queryArray: '.var_export($queryArray, true));
//$filename = WWW_DIR.'files/'.$queryArray[1];
$mp3link = false;
//if(file_exists($filename)){
//	$file = fopen($filename, "r");
//	$loop = 0;
//	while(!feof($file)){
//		$line = fgets($file);
//		if($loop === 0){
//			$artist = $line;
//		}elseif($loop === 1){
//			$date_portion = substr($line, 0, 8);
//			logDebug('date-portion: '.$date_portion);
//			$showdate = date_create_from_format('m-d-y', $date_portion);
//			logDebug('showdate: '.var_export($showdate, true));
			
$artist = urldecode($queryArray[1]);
$showdate = $queryArray[2];
$source = $queryArray[3];
$show = $db->getShowRecord($artist, $showdate, $source);
$artistLogo = Func::getLogoFile($artist, '/images/artists/square/');
$artistLogo = ($artistLogo ? '<img src="'.$artistLogo.'" class="img img-responsive">' : '<h2>'.$artist.'</h2>');
$venueLogo = Func::getLogoFile($show['venue'], '/images/venues/');
$venueLogo = ($venueLogo ? '<img src="'.$venueLogo.'" class="img img-responsive">' : '<h2>'.$show['venue'].'</h2>');
$megalink = ($show['megalink'] ? "<p><a href='{$show['megalink']}' class='megalink' target='_blank'>Link to MP3 Download</a></p>" : '');
$sampleFilename = Func::getSampleFilename($artist, $showdate, $source);
$sampleFilename = ($sampleFilename ? "<p>sample<br /><audio controls><source src='/{$sampleFilename}' type='audio/mpeg'>Your browser does not support the audio element.</audio></p>" : '');
?>
			<div class='showinfo'>
				<div class='row'>
					<div class='col-xs-12 col-sm-6 left-side text-center'>
						<div>
							<?=$artistLogo?><br />
							at<br />
							<?=$venueLogo?>
							<?=$megalink?>
							<?=$sampleFilename?>
						</div>
					</div>
					<div class='col-xs-12 col-sm-6 right-side'>
						<h2><?=$artist?></h2>
						<h3><?=$showdate?></h3>
						<h4><?=$show['venue']?></h4>
						<h4><?=$show['city_state']?></h4>
						<?=nl2br($show['setlist'])?>
					</div>
				</div>
			</div>
