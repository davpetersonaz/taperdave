<?php
class DB extends DBcore{

	function __construct(){
		parent::__construct(self::HOST, self::USER, self::PASS, self::DB_TABLES);
	}

	public function getShowRecord($artist, $date, $source){
		$params = array('artist'=>$artist, 'showdate'=>$date, 'source'=>$source);
		$shows = $this->select('SELECT * FROM shows WHERE artist=:artist AND showdate=:showdate AND source=:source LIMIT 1', $params);
		return $shows[0];
	}
	
	public function addShowRecord($params){
		$show_id = $this->insert('shows', $params);
		return $show_id;
	}
	
	public function updateShowRecord($params){
		$rows_updated = $this->update('UPDATE shows SET artist=:artist, showdate=:showdate, venue=:venue, city=:city, city_state=:city_state, source=:source, setlist=:setlist, pcloudlink=:pcloudlink, archivelink=:archivelink, samplefile=:samplefile WHERE show_id=:show_id', $params);
		return $rows_updated;
	}
	
	public function getShowsByArtist($ascending=true){
		$shows = $this->select('SELECT * FROM shows ORDER BY artist '.($ascending?'ASC':'DESC').', showdate DESC');
		return ($shows ? $shows : array());
	}
	
	public function getShowsByVenue($ascending=true){
		$shows = $this->select('SELECT * FROM shows ORDER BY venue '.($ascending?'ASC':'DESC').', showdate DESC');
		return ($shows ? $shows : array());
	}
	
	public function getShowsByCity($ascending=true){
		$shows = $this->select('SELECT * FROM shows ORDER BY city_state '.($ascending?'ASC':'DESC').', showdate DESC');
		return ($shows ? $shows : array());
	}
	
	public function getShowsBySource($ascending=true){
		$shows = $this->select('SELECT * FROM shows ORDER BY source '.($ascending?'ASC':'DESC').', showdate DESC');
		return ($shows ? $shows : array());
	}
	
	public function getShowsByDate($ascending=false){
		$shows = $this->select('SELECT * FROM shows ORDER BY showdate '.($ascending?'ASC':'DESC').', artist ASC');
		return ($shows ? $shows : array());
	}

	public function getSourceIdentities(){
		$sources = $this->select('SELECT id, sourcetext FROM sources');
		return ($sources ? $sources : array());
	}

	public function getMostPopularArtists($numberArtists){
		$query = 'SELECT COUNT(artist) AS theCount, artist FROM shows GROUP BY artist HAVING theCount>1 ORDER BY theCount DESC';
		logDebug('query: '.$query);
		$shows = $this->select($query);
		if($shows){ 
			shuffle($shows); 
			$shows = array_slice($shows, 0, 18);
			return $shows;
		}else{
			return array();
		}
	}
	
	public function removeAllShows(){
		return $this->delete('shows');
	}

	const DB_TABLES = array('shows');
	const HOST = 'mysql:host=localhost;dbname=davpeter_taper';
	const USER = 'davpeter_taper';
	const PASS = 'mus1cr0cks';
	
}