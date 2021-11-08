<?php
namespace classes;

class GetMegaZips{

	public function runIt(){
		$zipfiles = scandir(self::MEGA_URL);
		logDebug('mega files: '.var_export($zipfiles, true));
	}

	const MEGA_URL = 'https://mega.nz/#F!cVECnDaT!G5dxK1VOQ2crMnaRDjWCTw';
}
