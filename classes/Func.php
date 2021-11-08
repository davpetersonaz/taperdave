<?php
namespace classes;

class Func{
	
	public static function getArrayOfRandomFonts(){
		$fonts = array(
			'Circular, Sailec, Graphik, Recta, Calibre, Arial',
			'"GT Walsheim", Filson, "FF Mark", Campton, Verdana',
			'"Brandon Grotesque", Neutraface, "FF Super Grotesk", "MTT Milano", "Halis Rounded", Georgia',
			'Caslon, Plantin, Ehrhardt, Sabon, "Hoefler Text", Bookman',
			'Avenir, Neuzeit, Lasiver, Equip, Fakt, "Comic Sans MS"',
			'Brown, Campton, Sofia, "Value Sans", "Galano Grotesque", Impact, Arial',
			'Gotham, Armitage, Neuzeit, Graphik, "Trebuchet MS", Garamond, Arial',
			'"Proxima Nova", "Akzidenz Grotesk", "Museo Sans", Gibson, "Niveau Grotesk", Palatino, Arial',
			'Aperçu, "ITC Johnston", "Questa Sans", Maple, "Brezel Grotesk", "Courier New"',
			'Futura, "Harmonia Sans", "Twentieth Century", Ano, "ITC Avant Garde Gothic", "Trebuchet MS", Arial'
		);
		shuffle($fonts);
		return $fonts;
	}
	
	public static function updateLastElement($array, $value){
		end($array);
		$lastElement = key($array);
		if(is_numeric($lastElement)){
			if(is_array($value)){ logDebug('ERROR: value is array: '.var_export($value, true)); }
			$array[$lastElement] .= $value;
		}else{
			$array[] = $value;
		}
	}

	public static function identifyLine($line, $previousLineId, $log=false){
		$result = false;
		$line = rtrim(strtoupper($line));
		//very specific first:

		if (strpos($line, '---------------------------------------------------------------') === 0){ 
			$result = self::DASHED_LINE; 
		}elseif(strpos($line, 'NOT TRADEABLE') !== false){ 
			$result = self::NOT_TRADEABLE_LINE; 
		}elseif(strpos($line, '[SHOW]') === 0){
			$result = self::SHOW_LINE;
		}elseif(strpos($line, '[STUDIO]') === 0){
			$result = self::SHOW_LINE;
		}elseif(strpos($line, 'PC: ') === 0){
			$result = self::PH_COMP_LINE;
		}elseif(strpos($line, 'PHISH NOTES:') === 0){
			$result = self::PH_COMP_LINE;
		}elseif(strpos($line, 'HTTP://') === 0 || strpos($line, 'HTTPS://') === 0){
			$result = self::HTTP_LINE;
		}elseif(preg_match(self::REGEX_THE_BAND_LINE, $line) === 1){
			$result = self::THE_BAND_LINE;
		}elseif(strpos($line, '//') === 0){
			$result = self::MY_INFO_LINE;
		}elseif(strpos($line, '/**') === 0){
			$result = self::COMMENT_REST_LINE;
		}elseif(empty($line)){
			$result = self::BLANK_LINE;
		}elseif(preg_match(self::REGEX_FOOTNOTE_LINE, $line) === 1){
			$result = self::FOOTNOTE_LINE;
		}elseif(preg_match(self::REGEX_TOTAL_DISCS_LINE, $line) === 1){
			$result = self::TOTAL_DISCS_LINE;
		}elseif(preg_match(self::REGEX_DATE_LINE, $line) === 1){
			$result = self::DATE_LINE;
		}elseif(preg_match(self::REGEX_AUG_DATE, $line) === 1 && $previousLineId === self::VENUE_LINE){
			$result = self::AUGMENTED_DATE_LINE;
		}elseif(preg_match(self::REGEX_NOTE_LINE, $line) === 1){
			$result = self::NOTE_LINE;
		}elseif( //SOURCE_LINE
			preg_match('/^BROADCAST.*:\ .*$/', $line) === 1 ||//can be two words
			strpos($line, 'CATALOG: ') === 0 || 
			strpos($line, 'CDR>SHN: ') === 0 ||
			strpos($line, 'COMPILATION: ') === 0 ||
			strpos($line, 'CONFIG: ') === 0 ||
			strpos($line, 'CONFIGURATION: ') === 0 ||
			strpos($line, 'CONVERSION: ') === 0 ||
			strpos($line, 'DAT>SHN: ') === 0 ||
			strpos($line, 'DITHER: ') === 0 ||
			preg_match('/^EDIT\w*:\ .*$/', $line) === 1 ||
			strpos($line, 'ENCODED BY: ') === 0 ||
			strpos($line, 'ENCODING: ') === 0 ||
			strpos($line, 'EQUIPMENT: ') === 0 ||
			strpos($line, 'EXTRACTED BY: ') === 0 ||
			strpos($line, 'FIXES:') === 0 ||
			strpos($line, 'FLAC: ') === 0 ||
			strpos($line, 'FORMAT: ') === 0 ||
			strpos($line, 'GENERATION: ') === 0 ||
			strpos($line, 'LINEAGE: ') === 0 ||
			strpos($line, 'LOCATION: ') === 0 ||
			strpos($line, 'MANUFACTURED BY: ') === 0 ||
			strpos($line, 'MASTERED BY: ') === 0 ||
			strpos($line, 'MASTERING: ') === 0 ||
			strpos($line, 'MASTERING BY: ') === 0 ||
			preg_match('/^MIC[S]?:\ .*/', $line) === 1 ||
			strpos($line, 'MIXED BY: ') === 0 ||
			strpos($line, 'MIXING: ') === 0 ||
			strpos($line, 'ORIGINAL LABEL: ') === 0 ||
			strpos($line, 'PATCH: ') === 0 ||
			strpos($line, 'PATCHER: ') === 0 ||
			strpos($line, 'PATCHED BY: ') === 0 ||
			strpos($line, 'PROCESSING: ') === 0 ||
			preg_match('/^PRODUCED\ (.*)?BY:\ .*$/', $line) === 1 ||
			preg_match('/^RECORDED\ (.*)?BY:\ .*$/', $line) === 1 ||
			strpos($line, 'RECORDING: ') === 0 ||
			strpos($line, 'RECORDING AND TRANSFER BY: ') === 0 ||
			strpos($line, 'REFERENCE: ') === 0 ||
			strpos($line, 'REMASTERED BY: ') === 0 ||
			strpos($line, 'RETRACKED BY: ') === 0 ||
			strpos($line, 'SEED: ') === 0 ||
			strpos($line, 'SEEDED BY: ') === 0 ||
			strpos($line, 'SOUND QUALITY: ') === 0 ||
			preg_match('/^SOURCE(S)?.*:\ .*$/', $line) === 1 ||
			strpos($line, 'TAPED BY: ') === 0 ||
			strpos($line, 'TAPER: ') === 0 ||
			preg_match('/^TAPE.*MASTERED.*(\ BY)?:\ .*$/', $line) === 1 ||
			preg_match('/^TAPE.*TRANSFER.*(\ BY)?:\ .*$/', $line) === 1 ||
			preg_match('/^TRANSFER.*:\ .*$/', $line) === 1 ||
			strpos($line, 'TAPER NOTES: ') === 0 ||
			strpos($line, 'TAPERS NOTES: ') === 0 ||
			strpos($line, 'TRACKED BY: ') === 0 ||
			strpos($line, 'TRACKING: ') === 0 ||
			strpos($line, 'UPLOADED BY: ') === 0 ||
			strpos($line, 'VERSION: ') === 0 ||
			strpos($line, 'XREF: ') === 0
		){
			$result = self::SOURCE_LINE;
		}elseif($line === 'LIVEPHISH'){
			$result = self::PRIMUS_LIVE_PHISH;
		}elseif($line === 'PRIMUSLIVE'){
			$result = self::PRIMUS_LIVE_PHISH;
		}elseif($line === 'UMLIVE'){
			$result = self::PRIMUS_LIVE_PHISH;
		}elseif(preg_match(self::REGEX_RATING_LINE, $line) === 1){
			$result = self::RATING_LINE;
		}elseif(preg_match(self::REGEX_MISSING_TRACK, $line) === 1){
			$result = self::SETLIST_LINE;//missing-song line, i.e. [Missing Song]
		}elseif(strpos($line, ':') > 0 &&
			(
				preg_match(self::REGEX_SONG_LINE, $line) === 1 ||
				strpos($line, '[DISC ') === 0 ||
				preg_match(self::REGEX_SET_LINE, $line) === 1 ||
				preg_match(self::REGEX_TWEENER_LINE, $line) === 1 ||
				preg_match(self::REGEX_FILLER_LINE, $line) === 1 ||
				preg_match(self::REGEX_SOUNDCHECK_LINE, $line) === 1 ||
				preg_match(self::REGEX_ENCORE_LINE, $line) === 1
			)
		){
			$result = self::SETLIST_LINE;
		}elseif(preg_match(self::REGEX_DAUD_SBD_LINE, $line, $matches) === 1){
			$result = self::DAUD_SBD_LINE;
		}else{
			if($previousLineId === self::DATE_LINE || $previousLineId === self::TITLE_LINE || $previousLineId === self::VENUE_LINE){
				$result = self::VENUE_LINE;
			}elseif($previousLineId === self::ARTIST_LINE){
				$result = self::TITLE_LINE;
			}else{	//just plop it down
				$result = self::NORMAL_LINE;
			}
		}
		if($log){ logDebug('identify line as ['.$result.']['.$previousLineId.']['.self::getIdentityString($result).']: '.$line); }
		return $result;
	}

	public static function getSecondaryIdentities($line, $line_identity, $log=false){
		$line = rtrim(strtoupper($line));
		$result = array();
		if($line_identity === self::SOURCE_LINE){
			if(strpos($line, ' BY: ') > 0){
				if(strpos($line, 'EDIT') !== false){ $result[] = self::SOURCE_MASTERBY; }//master/process/edit
				if(strpos($line, 'MASTER') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'MIX') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'PATCH') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'PROCESS') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'PRODUCE') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'RECORD') !== false){ $result[] = self::SOURCE_TAPEDBY; }
				if(strpos($line, 'TAPE') !== false){ $result[] = self::SOURCE_TAPEDBY; }
				if(strpos($line, 'TRACK') !== false){ $result[] = self::SOURCE_MASTERBY; }
				if(strpos($line, 'TRANSFER') !== false){ $result[] = self::SOURCE_TRANSFERBY; }
			}
			elseif(strpos($line, 'TAPER:') === 0){ $result[] = self::SOURCE_TAPEDBY; }
			else{
				if(strpos($line, 'CONFIG') !== false){ $result[] = self::SOURCE_MICS; }
				if(strpos($line, 'CONVERSION') !== false){ $result[] = self::SOURCE_TRANSFER; }
				if(strpos($line, 'DITHER') !== false){ $result[] = self::SOURCE_TRANSFER; }
				if(strpos($line, 'EDIT') !== false){ $result[] = self::SOURCE_PROCESSING; }
				if(strpos($line, 'EQUIPMENT') === 0){ $result[] = self::SOURCE_MICS; }
				if(strpos($line, 'LINEAGE') !== false){ $result[] = self::SOURCE_TRANSFER; }
				if(strpos($line, 'LOCATION') !== false){ $result[] = self::SOURCE_LOCATION; }
				if(strpos($line, 'MASTER') !== false){ $result[] = self::SOURCE_PROCESSING; }//master/process/edit
				if(strpos($line, 'MICS') !== false){ $result[] = self::SOURCE_MICS; }
				if(strpos($line, 'MIX') !== false){ $result[] = self::SOURCE_PROCESSING; }
				if(strpos($line, 'NOTES') !== false){ $result[] = self::SOURCE_NOTE; }
				if(strpos($line, 'PATCH') !== false){ $result[] = self::SOURCE_PROCESSING; }
				if(strpos($line, 'PROCESS') !== false){ $result[] = self::SOURCE_PROCESSING; }
				if(strpos($line, 'PRODUCE') !== false){ $result[] = self::SOURCE_PROCESSING; }
				if(strpos($line, 'RECORD') === 0){ $result[] = self::SOURCE_MICS; }
				if(strpos($line, 'SHN') !== false){ $result[] = self::SOURCE_TRANSFER; }
				if(strpos($line, 'SOURCE') === 0){ $result[] = self::SOURCE_MICS; }
				if(strpos($line, 'TRACKING') !== false){ $result[] = self::SOURCE_PROCESSING; }
			}
		}
		elseif($line_identity === self::DAUD_SBD_LINE){
			if(strpos($line, 'SBD') !== false){ $result[] = self::SBD_LINE; }
			elseif(strpos($line, 'MATRIX') !== false){ $result[] = self::SBD_MATRIX_LINE; }
			elseif(strpos($line, 'SHN-') !== false){ $result[] = self::SHN_LINE; }
		}
		elseif($line_identity === self::SETLIST_LINE){
			if(preg_match(self::REGEX_SONG_LINE, $line) === 1){ $result[] = self::SETLIST_SONG_LINE; }
			if(strpos($line, '[DISC ') === 0){ $result[] = self::SETLIST_DISC_LINE; }
			if(preg_match(self::REGEX_SET_LINE, $line) === 1){ $result[] = self::SETLIST_SET_LINE; }
			if(preg_match(self::REGEX_TWEENER_LINE, $line) === 1){ $result[] = self::SETLIST_TWEENER_LINE; }
			if(preg_match(self::REGEX_SOUNDCHECK_LINE, $line) === 1){ $result[] = self::SETLIST_SOUNDCHECK_LINE; }
			if(preg_match(self::REGEX_ENCORE_LINE, $line) === 1){ $result[] = self::SETLIST_ENCORE_LINE; }
			if(preg_match(self::REGEX_FILLER_LINE, $line) === 1){ $result[] = self::SETLIST_FILLER_LINE; }
			if(preg_match(self::REGEX_MISSING_TRACK, $line) === 1){ $result[] = self::SETLIST_MISSING_SONG; }
		}
		if($log && !empty($result)){ logDebug('secondary identities: '.implode('|', $result)); }
		return $result;
	}

	public static function getIdentityString($theConstants, $log=false){
		if(is_array($theConstants)){
			$result = '';
			foreach($theConstants as $aConstant){
				if(!empty($result)){ $result .= '|'; }
				$result .= self::getLineIdentityArray()[$aConstant];
			}
		}else{
			$result = self::getLineIdentityArray()[$theConstants];
		}
		if($log){ logDebug('identitystring ('.$theConstants.') is: '.$result); }
		return $result;
	}

	public static function getLineIdentityArray(){
		return self::LINE_IDENTITY;
	}

	public static function getStrippedName($name=''){
		$strippedName = preg_replace("/[^A-Za-z0-9]/", '', $name);
		if(strpos($strippedName, 'The') === 0){ 
			$strippedName = substr($strippedName, 3).substr($strippedName, 0, 3); 
		}
		return $strippedName;
	}
	
	public static function getLogoFile($artist, $dir){
		$strippedName = Func::getStrippedName($artist);
		$wildcard = $dir.$strippedName.'Logo.*';
		$logoFiles = glob(WWW_DIR.$wildcard);
		$logoFilename = (count($logoFiles) > 0 ? str_replace(WWW_DIR, '', $logoFiles[0]) : false);
		if(!$logoFilename){ 
			logDebug('ERROR: no logo for: '.$wildcard);
		}else{
//			logDebug('logoFilename: '.$logoFilename);
		}
		return $logoFilename;
	}

	public static function getSampleFilename($artist, $showdate, $source){
		$strippedArtist = preg_replace("/[^A-Za-z0-9]/", '', $artist);
//		logDebug('stripped-artist: '.$strippedArtist);
		$possibleSampleFilename = 'music/'.strtolower($strippedArtist).$showdate.'.mp3';
		$possibleSampleFilepath = WWW_DIR.$possibleSampleFilename;
//		logDebug('possible-filepath: '.$possibleSampleFilepath);
		$sampleFilename = false;
		if(file_exists($possibleSampleFilepath)){
			$sampleFilename = $possibleSampleFilename;
		}else{
			logDebug('no sample file: '.$possibleSampleFilepath);
		}
		return $sampleFilename;
	}
	
	//show header
	const DASHED_LINE = 1;
	const BLANK_LINE = 2;
	const ARTIST_LINE = 3;
	const TITLE_LINE = 4;//for pink-floyd style bootlegs
	const DATE_LINE = 5;
	const VENUE_LINE = 6;//includes city/state
	const AUGMENTED_DATE_LINE = 7;//for pink-floyd style bootlegs
	const TOTAL_DISCS_LINE = 8;
	const DAUD_SBD_LINE = 9;
	const SBD_LINE = 10;//subset of DAUD_SBD_LINE
	const SBD_MATRIX_LINE = 11;//subset of DAUD_SBD_LINE
	const SHN_LINE = 12;//on the same line as DAUD_SBD_LINE
	const PRIMUS_LIVE_PHISH = 13;
	const RATING_LINE = 14;
	const NOT_TRADEABLE_LINE = 15;
	const MY_INFO_LINE = 16;//comments

	//setlisting
	const SHOW_LINE = 20;//can also be [studio]
	const SETLIST_DISC_LINE = 21;
	const SETLIST_LINE = 22;
	const SETLIST_SET_LINE = 23;//sub-category of SETLIST_LINE
	const SETLIST_TWEENER_LINE = 24;//sub-category of SETLIST_LINE
	const SETLIST_FILLER_LINE = 25;//sub-category of SETLIST_LINE
	const SETLIST_ENCORE_LINE = 26;//sub-category of SETLIST_LINE
	const SETLIST_SOUNDCHECK_LINE = 27;//sub-category of SETLIST_LINE
	const SETLIST_SONG_LINE = 28;
	const SETLIST_MISSING_SONG = 29;

	//the band and ending notes
	const THE_BAND_LINE = 30;
	const FOOTNOTE_LINE = 31;
	const PH_COMP_LINE = 32;
	const PH_NOTES_LINE = 33;
	const SOURCE_LINE = 34;//broad definition (includes source/transfer/etc), create something to seperate all the combined source lines into specific slots
	const SOURCE_MICS = 35;
	const SOURCE_LOCATION = 36;
	const SOURCE_PROCESSING = 37;
	const SOURCE_TRANSFER = 38;
	const SOURCE_NOTE = 39;
	const SOURCE_MASTERBY = 40;
	const SOURCE_TAPEDBY = 41;
	const SOURCE_TRANSFERBY = 42;
	const HTTP_LINE = 43;
	const NOTE_LINE = 44;
	const NORMAL_LINE = 45;
	const COMMENT_REST_LINE = 46;//comments that don't appear in the html, found at the bottom of a show, such as the raw setlist, or names of the band's songs

	const LINE_IDENTITY = array(
		'',//element[0] is not a constant from above
		//show header
		'dashed',
		'blank',
		'artist',
		'title',
		'date',
		'venue',
		'augmented-date',
		'total-discs',
		'daud-sbd-line',
		'sbd',
		'sbd-matrix',
		'shn-location',
		'primus-live-phish',
		'rating',
		'not-tradeable',
		'my-info',
		'',//unused
		'',//unused
		'',//unused
		//setlisting (20-on)
		'show',
		'disc-number',
		'setlist-line',
		'set',
		'tweener',
		'filler',
		'encore',
		'soundcheck',
		'song-title',
		'missing-song',
		//the band and ending notes (30-on)
		'the-band',
		'footnote',
		'phish-companion',
		'phish-notes',
		'source-line',
		'source-mics',
		'source-location',
		'source-processing',
		'source-transfer',
		'source-note',
		'source-masterby',
		'source-tapedby',
		'source-transferby',
		'http',
		'note',
		'normal-line',
		'comment-out-the-rest'
	);

	//regex
	const REGEX_AUG_DATE = '/^([A-Za-z]+)?\ ?(\d{1,2})?[+-]?(\d{1,2})?,?\ ?(\d{4})$/';//i.e. - January 12, 1994
	const REGEX_DATE_LINE = '/^\d{2}-\d{2}-\d{2}(\w*)?$/';
	const REGEX_DAUD_SBD_LINE = '/^(DAUD|AUD|DSBD|SBD|FM|MATRIX|RADIO|STUDIO|VCD)?(?:\/(\S+))?(?:,?\ ?SHN\-(\d\d?)\-([\d,\-]+))?$/';
	const REGEX_ENCORE_LINE = '/^ENCORE(S)?(\ \d)?:$/';
	const REGEX_FILLER_LINE = '/^(.*\ )?(PH)?(F)?ILLER\ ?(.+)?:$/';//i.e. - QUADROPHENIA PHILLER FROM 5/5/93:
	const FOOTNOTE_REGEX = '[~`!@#$%\^&*\-=+?]+';
	const REGEX_FOOTNOTE_LINE = '/^('.self::FOOTNOTE_REGEX.')\ (.*)$/';
	const REGEX_MISSING_TRACK = '/^\[(.*)\]$/';
	const REGEX_NOTE_LINE = '/^NOTE[S]?:\ ?.*$/';
	const REGEX_RATING_LINE = '/^[A|B|C|D|E|F][\+\-\?]{0,2}$/';
	const REGEX_SET_LINE = '/^(.+)?\ ?SET\ ?(\d\d?)?\ ?(.+)?(\ CON\'T)?:$/';//i.e. - set 1 con't:
	//for some reason, i cannot get this regex to parse out the footnote symbols or the continuation sign
	//([A-Za-z0-9\- ]+) ?([~`!@#$%\^&*\-=+?]?)( >)?
	const REGEX_SONG_LINE = '/^(\d\d?)\. ([A-Za-z0-9\- ]+?) ?('.self::FOOTNOTE_REGEX.')? ?(> )?\((\d\d?:\d\d)\)$/';//i.e. - 12. song name * (1:23)
	const REGEX_SONG_FOOTNOTE_CONT = '/^$/';
	const REGEX_SOUNDCHECK_LINE = '/^SOUNDCHECK(\ .*)?:$/';
	const REGEX_THE_BAND_LINE = '/^THE\ .*BAND:$/';
	const REGEX_TOTAL_DISCS_LINE = '/^\d\ DISC[S]?$/';
	const REGEX_TWEENER_LINE = '/^(.+)?\ ?TWEENER\ ?(\d)?\ ?(.+)?:$/';//i.e. - DJ Al tweener 1:
}