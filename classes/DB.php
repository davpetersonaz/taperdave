<?php
class DB extends DBcore{

	function __construct(){
		parent::__construct(self::HOST, self::USER, self::PASS, self::DB_TABLES);
	}

	public function getShowRecord($artist, $date, $source=false){
		$params = array('artist'=>$artist, 'showdate'=>$date);
		$query = 'SELECT * FROM shows WHERE artist=:artist AND showdate=:showdate';
		if($source){
			$query .= ' AND source=:source';
			$params['source'] = $source; 
		}
		$query .= ' LIMIT 1';
		$shows = $this->select($query, $params);
		return $shows[0];
	}
	
	public function addShowRecord($params){
		$show_id = $this->insert('shows', $params);
		return $show_id;
	}
	
	public function updateShowRecord($params){
		$rows_updated = $this->update('
				UPDATE shows 
				SET artist=:artist, artist_sort=:artist_sort, showdate=:showdate, 
					venue=:venue, city=:city, city_state=:city_state, 
					source=:source, setlist=:setlist, pcloudlink=:pcloudlink, 
					archivelink=:archivelink, samplefile=:samplefile 
				WHERE show_id=:show_id
			', $params);
		return $rows_updated;
	}
	
	public function getShowsByArtist($ascending=true){
		$shows = $this->select('SELECT * FROM shows ORDER BY artist_sort '.($ascending?'ASC':'DESC').', showdate DESC');
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

	public function getMostPopularArtists($numberArtists=40){
		$query = 'SELECT COUNT(artist) AS theCount, artist FROM shows GROUP BY artist HAVING theCount>2 ORDER BY theCount DESC LIMIT '.intval($numberArtists);
		logDebug('query: '.$query);
		$shows = $this->select($query);
		return ($shows ? $shows : array());
	}
	
	public function getMostPopularArtistsRandomized($numberArtists=40){
		$query = 'SELECT COUNT(artist) AS theCount, artist FROM shows GROUP BY artist HAVING theCount>2';
		logDebug('query: '.$query);
		$shows = $this->select($query);
		if($shows){ 
			shuffle($shows); 
			$shows = array_slice($shows, 0, $numberArtists);
			return $shows;
		}else{
			return array();
		}
	}
	
	public function removeAllShows(){
		return $this->delete('shows');
	}

	const DB_TABLES = array('shows', 'sources');
	const HOST = 'mysql:host=localhost;dbname=dbmtgd4tjtlkdm';
	const USER = 'ufuatclejhy1j';
	const PASS = '~y^i[%k#4Gfs';
	
}