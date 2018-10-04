<?php  
$sorting = (isset($_GET['s']) ? $_GET['s'] : false);
logDebug('sorting: '.$sorting);
?>
			<div class='showlist'>
				<h1>Shows I Have Taped</h1>

<?php
function displayShows($shows, $sortingField, $showListingValues, $sources){
	logDebug('displayShows ['.$sortingField.']');
	if($shows){
		$current = false;
		$firstShowBlock = true;
		$arrayIterator = new ArrayIterator(Func::getArrayOfRandomFonts());
		foreach($shows as $show):
			logDebug('processing show: '.var_export($show, true));
			$showBlockValue = ($sortingField === 'showdate' ? substr($show['showdate'], 0, 4) : 
				($sortingField === 'source' ? $show['source'] : $show[$sortingField]));
			logDebug('showBlockValue: '.$showBlockValue);
			
			//start a new show block?
			if($showBlockValue !== $current){
				$current = $showBlockValue;
				if($sortingField === 'artist'){
					$logoFilename = Func::getLogoFile($show[$sortingField], '/images/artists/wide/');
				}elseif($sortingField === 'venue'){
					$logoFilename = Func::getLogoFile($show[$sortingField], '/images/venues/');
				}else{
					$logoFilename = false;
				}
?>
<?php
				if($firstShowBlock){
					$firstShowBlock = false;
?>
				<span id='<?=(str_replace(array('\'', ' ', '-'), '', $showBlockValue))?>' class="internalAnchorSpan">
					<div class='show-block'><!-- show-block start1a -->
<?php
				}else{ //finish the last unnumbered-list and show-block and start a new one
?>
						</ul>
					</div><!-- show-block -->
				</span>
				<span id='<?=(str_replace(array('\'', ' ', '-'), '', $showBlockValue))?>' class="internalAnchorSpan">
					<div class='show-block'><!-- show-block start2a -->
<?php
				}
				if($logoFilename){
					$imagesize = getimagesize(WWW_DIR.$logoFilename);
					$imageSizeHtml = (isset($imagesize[1]) ? ' height='.$imagesize[1].' width='.$imagesize[0] : '');
?>
						<img src='<?=$logoFilename?>' alt='<?=$show[$sortingField]?>'<?=$imageSizeHtml?>>
<?php
				}else{
					if(!($nextFont = next($arrayIterator))){
						//i can't get the InfiniteIterator to work
						$arrayIterator = new ArrayIterator(Func::getArrayOfRandomFonts());
						$nextFont = next($arrayIterator);
					}
?>
						<h3 style='font-family:<?=$nextFont?>;'><?=($sortingField === 'source' ? $sources[$showBlockValue] : $showBlockValue)?></h3>
<?php
				}
?>
						<ul>
<?php
			}//new-sorting-block
			
			$listingValue0 = getFieldValue($showListingValues[0], $show[$showListingValues[0]], $sources);
			$listingValue1 = getFieldValue($showListingValues[1], $show[$showListingValues[1]], $sources);
			$listingValue2 = getFieldValue($showListingValues[2], $show[$showListingValues[2]], $sources);
//			$listingValue1 = ($sortingField === 'showdate' ? date("n/j/y", strtotime($show[$showListingValues[1]])) : $show[$showListingValues[1]]);
//			$listingValue2 = ($sortingField === 'source' ? $sources[$show[$showListingValues[2]]] : $show[$showListingValues[2]]);
			logDebug('listing_values: '.$listingValue0.' '.$listingValue1.' '.$listingValue2);
			$downloadIcon = (!empty($show['megalink']) ? " <a href='".$show['megalink']."' target='_blank'><span class='fas fa-file-download' title='link to mp3 files'></span></a>" : '');
			$sampleIcon = (!empty($show['samplefile']) ? " <a href='".$show['samplefile']."' target='_blank'><span class='fas fa-play' title='sample audio file'></span></a>" : '');
			$artist = urlencode($show['artist']);
			logDebug('encoded artist: '.$artist);
?>
							<li class=''>
								<p><a href='/showinfo/<?=$artist?>/<?=$show['showdate']?>/<?=$show['source']?>' target='_blank'><?=$listingValue0?> - <?=$listingValue1?> - <?=$listingValue2?> <?=$downloadIcon?> <?=$sampleIcon?></a></p>
							</li>
<?php
		endforeach;
?>
						</ul>
					</div><!-- show-block end2a -->
<?php
	}
}

function getFieldValue($field, $value, $sources){
	if($field === 'source'){
		return $sources[$value];
	}
	if($field === 'showdate'){
		return date('n/j/y', strtotime($value));
	}
	return $value;
}

if($sorting === 'a'){
	$shows = $db->getShowsByArtist();
	displayShows($shows, 'artist', array('showdate', 'venue', 'source'), $sources);

}elseif($sorting === 'v'){
	$shows = $db->getShowsByVenue();
	displayShows($shows, 'venue', array('artist', 'showdate', 'source'), $sources);

}elseif($sorting === 'c'){
	$shows = $db->getShowsByCity();
	displayShows($shows, 'city_state', array('artist', 'showdate', 'source'), $sources);

}elseif($sorting === 's'){
	$shows = $db->getShowsBySource();
	displayShows($shows, 'source', array('artist', 'showdate', 'venue'), $sources);

}else{ //$sorting === 'showdate' or anything else
	$shows = $db->getShowsByDate();
	displayShows($shows, 'showdate', array('artist', 'showdate', 'source'), $sources);
} ?>

				<div class='up-button-div'>
					<button id='up-button' class='btn bg-dark' onclick='toTheTop();'>Up To Top</button>
				</div>
				
				<!-- TODO MOVE THIS TO THE FOOTER!!!!!!!!!!!!!! -->
				
				<div class='regenerate-shows'>
					<button class='btn bg-dark'>Regenerate Shows</button>
				</div>
			</div><!-- END-showlist -->
