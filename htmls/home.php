<?php
logDebug('home');
$into_offset = 'offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6';
?>

<div class='home_page'>

	<div class='feature_image'>
		<img src='/images/home/at_mics_budfest2018.jpg'>
	</div>

	<div class='intro'>
		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p>
					<b>A quick note about taping live bands...</b>
					The bands I record are <i>'taper-friendly'</i>, they follow the tradition set forth by the Grateful Dead
					that <em>live audience recordings</em> are beneficial to the promotion and success of a touring band, 
					especially those who throw a lot of improvisation into their sets 
					(and therefore appreciate having their accomplishments preserved so they can relisten to what they brought forth).
				</p>
			</div>
		</div>

		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p>
					Having said that, <b>if you are with a band and I am inappropriately sharing your musical creations</b>,
					please <a href='mailto:me@taperdave.com'>email me right away</a>.  
					And I apologize and will not share those recordings in the future. <em>Sincerely.</em>
				</p>
			</div>
		</div>

		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p>
					Likewise, <b>if you are one of the creators and would like the original WAV files</b>, 
					please <a href='mailto:me@taperdave.com'>email me</a>
					and I will put the files on my website for you to download via a link I will email you.
				</p>
			</div>
		</div>

		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p>
					<b>I do not make money off these recordings</b>, there are no advertisements on this website, 
					and I do not sell the recordings or their presence on this site in any way.
					<em>I will never sell any live recordings without direct positive agreement with and financial compensation to the artists.</em>
				</p>
				<p>
					I will eventually put everything on 
					<a href='https://archive.org/details/@taper-dave'>The Internet Archive</a>,
					however I'll probably be somewhat selective on that so it will happen slowly (as I catch up with backlog).
				</p>
			</div>
		</div>

		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p>
					Keep in mind some recordings are better than others for a variety of reasons 
					(the microphones, the placement, the room, the soundguy, the amps, etc).
					Generally, the older the recording, the higher the likelihood it might not be 'that great', 
					I have provided samples of each show so you can take a quick listen before downloading.
					For your reference, the progression of equipment I have used started with the ZoomH4n, then the ZoomH6 for a brief period, then the ZoomH5,
					and lately my Audio Technica 853 microphones (which provide better bass definition and overall clarity than the Zoom's mics).
				</p>
			</div>
		</div>

		<div class='row'>
			<div class='<?=$into_offset?>'>
				<p style='font-size:larger;'>
					<em>Download, listen, share with your friends!</em> and, most importantly, 
					<b>GO SEE THESE BANDS WHEN THEY TOUR NEAR YOU!!</b>
					And, <em>buy their merchandise!</em> 
					And buy their studio albums -- alot of these bands exhibit different sides of themselves in the studio vs on the road!
					They are great people and love to get down and have a good time, 
					and a recording can only capture <em>one</em> aspect of a band's performance, 
					so go experience the <em>whole thing</em> for yourself!!
				</p>
			</div>
		</div>
	</div>
	
	<div class='artist_icons'>
		<div class='row'>
			<div class="col-xs-12">
				<a href='https://taperdave.com/showlist.php?s=y' style='text-decoration:none;'><h3>And On to The Music!</h3></a>
			</div>
			<p class='text-center'>here are some of the bands I've taped the most</p>
		</div>
		
		<div class='row'>

<?php 
$popularArtists = $db->getMostPopularArtists(18);
//logDebug('popularArtists: '.var_export($popularArtists, true));
$mostPopular = ($popularArtists ? array_column($popularArtists, 'artist') : array());
$numberDisplayed = 0;
foreach($mostPopular as $name):
	$logoFilename = Func::getLogoFile($name, '/images/artists/square/');
	$numberDisplayed++;
	?>
			<div class='col-xs-12 col-sm-4 col-md-3 col-lg-2'>
				<div class='thumbnail'>
					<a href='/showlist?s=a#<?=str_replace(array('\'', ' ', '-'), '', $name)?>'>
						<img src='<?=$logoFilename?>' class='img-responsive rounded'>
					</a>
				</div>
			</div>
<?php 
	if($numberDisplayed >= 24){
		break;
	}
endforeach;
?>
		</div>
	</div><!-- artist_icons -->

</div><!-- home_page -->
<?php
logDebug('home complete');