<?php
class ReadShowFiles{

	public function __construct($db){
		$this->db = $db;
	}

	public function runIt(){
		$this->show_file_names = scandir(self::TXT_FILES_DIR);
		logDebug('running ReadShowFiles');
		$this->db->removeAllShows();
		foreach($this->show_file_names as $file){
			if($file === '.' || $file === '..'){ continue; }
			if(file_exists(self::TXT_FILES_DIR.$file)){
				logDebug('processing file: '.self::TXT_FILES_DIR.$file);

				//just get the whole string for taper and source values
				$contents = file_get_contents(self::TXT_FILES_DIR.$file);

				//verify i'm the taper
				$taperpos = strrpos($contents, 'taper: ');
				if($taperpos === false){ 
					$taperpos = strrpos($contents, 'taped by: '); 
				}
				if($taperpos >= 0){
					$aftertaper = trim(substr($contents, $taperpos));
					if(($mepos = strpos($aftertaper, 'davpeterson')) === false){
						$afterme = substr($aftertaper, $mepos);
						logDebug('ERROR: taper is not me: '.trim(substr($afterme, 0, 25)).': '.$file);
						continue;
					}
				}else{
					logDebug('ERROR: no taper field: '.$file);
					continue;
				}

				//figure out the source
				$showInfo = array();
				$sourcepos = strpos($contents, 'source: ');
				if($sourcepos > 0){
					$line = strtolower(trim(substr($contents, $sourcepos+8, 30)));
					if(strpos($line, 'sbd + at853') !== false){
						$showInfo['source'] = self::MATRIX_WITH_AT853;
					}elseif(strpos($line, 'sbd + zoomh4') === 0){
						$showInfo['source'] = self::MATRIX_WITH_H4;
					}elseif(strpos($line, 'sbd + zoomh5') === 0){
						$showInfo['source'] = self::MATRIX_WITH_H5;
					}elseif(strpos($line, 'sbd + zoomh6') === 0){
						$showInfo['source'] = self::MATRIX_WITH_H6;
					}elseif(strpos($line, 'sbd + golden age') === 0){
						$showInfo['source'] = self::MATRIX_WITH_GAP;
					}elseif(strpos($line, 'sbd') === 0){
						$showInfo['source'] = self::SBD;
					}elseif(strpos($line, 'zoomh4') === 0){
						$showInfo['source'] = self::ZOOMH4;
					}elseif(strpos($line, 'zoomh5') === 0){
						$showInfo['source'] = self::ZOOMH5;
					}elseif(strpos($line, 'zoomh6') === 0){
						$showInfo['source'] = self::ZOOMH6;
					}elseif(strpos($line, 'at853') !== false){
						$showInfo['source'] = self::AT853;
					}elseif(strpos($line, 'golden age') !== false){
						$showInfo['source'] = self::GAP;
					}elseif(strpos($line, 'mbho') === 0){
						$showInfo['source'] = self::MBHO;
					}else{
						$showInfo['source'] = self::OTHER;
					}
					logDebug('source field: '.var_export($showInfo['source'], true));
				}else{
					logDebug('ERROR: no source field: '.$file);
					continue;
				}

				//now get the showdate/venue/city by reading the file line-by-line
				$handle = fopen(self::TXT_FILES_DIR.$file, 'r');
				$showInfo['artist'] = trim(fgets($handle));//artist
				logDebug('artist: '.$showInfo['artist']);
				$dateline = trim(fgets($handle), "'");//date
				logDebug('dateline: '.trim($dateline));
				if(preg_match('/^.*(\d\d)\-(\d\d)\-(\d\d)(.*)$/', $dateline, $matches) === 1){
					$showInfo['showdate'] = date_create_from_format('ymd', $matches[3].$matches[1].$matches[2]);
					$showInfo['showdateplus'] = (isset($matches[4]) ? $matches[4] : false);//only used to find the mp3 sample, at this point, not saved to db
					$showInfo['showdate'] = $showInfo['showdate']->format('Y-m-d');
					logDebug('showdate: '.$showInfo['showdate']);
				}else{
					logDebug('ERROR: unknown date: '.dateline.': '.$file);
					continue;
				}

				//retrieve the other pieces
				$showInfo['venue'] = trim(fgets($handle));//venue
				if(($pos = strpos($showInfo['venue'], ', ')) !== false){
					$showInfo['venue'] = substr($showInfo['venue'], 0, $pos);
				}
				logDebug('venue: '.$showInfo['venue']);
				$showInfo['city'] = $showInfo['city_state'] = $city = trim(fgets($handle));//city
				if(($pos = strpos($showInfo['city_state'], ', ')) !== false){
					$showInfo['city'] = substr($showInfo['city_state'], 0, $pos);
				}
				logDebug('city: '.$showInfo['city']);
				
				//pcloud/archive links, or blank line
				$possibleLink = trim(fgets($handle));
				while(!empty($possibleLink)){
					logDebug('possibleLink: '.$possibleLink);
					if(strpos($possibleLink, 'my.pcloud.com') !== false || strpos($possibleLink, 'u.pcloud.link') !== false || strpos($possibleLink, 'mega.nz') !== false){
						$showInfo['pcloudlink'] = $possibleLink;
					}else{
						logDebug('MISSING pcloud link: '.$possibleLink);
					}
					if(strpos($possibleLink, 'archive.org') !== false){
						$showInfo['archivelink'] = $possibleLink;
					}else{
						logDebug('MISSING archive link: '.$possibleLink);
					}
					$possibleLink = trim(fgets($handle));
				}
				
				//get setlist and sourceinfo
				$showInfo['setlist'] = '';
				while(($nextLine = fgets($handle)) !== false){
					$showInfo['setlist'] .= trim($nextLine).EOL;
				}

				//get the sample
				$possibleSampleFileUrl = 'music/'.strtolower(Func::getStrippedName($showInfo['artist'])).$showInfo['showdate'].($showInfo['showdateplus']?$showInfo['showdateplus']:'').'.mp3';
				$possibleSampleFilepath = WWW_DIR.$possibleSampleFileUrl;
				logDebug('possible-filepath: '.$possibleSampleFilepath);
				if(file_exists($possibleSampleFilepath)){
					$showInfo['samplefile'] = $possibleSampleFileUrl;
				}else{
					logDebug('MISSING sample file: '.$possibleSampleFilepath);
					$showInfo['samplefile'] = '';
				}
				
				//add or update the show
				//VERIFY: ONCE I AUTOMATE THE SHOW-FILE-GATHERING, THE SQL-UPDATE SHOULD ONLY UPDATE ROWS THAT HAVE CHANGED, VERIFY!
				$show = $this->db->getShowRecord($showInfo['artist'], $showInfo['showdate'], $showInfo['source']);
				unset($showInfo['showdateplus']);
				if($show){
					$showInfo['show_id'] = $show['show_id'];
					$rows_updated = $this->db->updateShowRecord($showInfo);
					if($rows_updated){
						logDebug('show updated: '.$show['show_id']);
					}
				}else{
					$show_id = $this->db->addShowRecord($showInfo);
					logDebug('added show id: '.var_export($show_id, true));
				}
			}
		}
		
		//TODO: EVERYTHING IS READ-IN, SO NOW I SHOULD GO THRU ALL THE ARTISTS
		//	AND MAKE SURE THEY HAVE A /images/artists/wide/ FILE
		//	AND IF THEY HAVE MORE THAN 1 SHOW, THEY SHOULD ALSO HAVE A /images/artists/square/ FILE
		//	IF THEY DONT THEN WRITE A TXT FILE IN ROOT
		//
		//COULD EVEN DO IT WHILE PROCESSING THE INITIAL PASS THRU THE FILES, EVERY TIME GET NEW ARTIST, MAKE SURE LOGOS ARE THERE
		//
		
	}
	
	const TXT_FILES_DIR = WWW_DIR.'files/';
	
	const GAP = 4;//Golden Age Project FC4s
	const AT853 = 5;//AudioTechnica 853s
	const MATRIX_WITH_AT853 = 9;//SBD + AT853
	const MATRIX_WITH_H5 = 10;//SBD + ZoomH5
	const MATRIX_WITH_H6 = 11;//SBD + ZoomH6
	const MATRIX_WITH_H4 = 12;//SBD + ZoomH4n
	const MATRIX_WITH_GAP = 13;//SBD + Golden Age Projects
	const SBD = 20;//Soundboard
	const MBHO = 25;//MBHO (patched into grout's rig)
	const OTHER = 29;//other (anything else will probably be better than the Zoom mics)
	const ZOOMH5 = 34;//ZoomH5
	const ZOOMH6 = 35;//ZoomH6
	const ZOOMH4 = 36;//ZoomH4n

	private $sources = array();
	private $db;
}