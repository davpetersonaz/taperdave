<?php include_once('../router.php'); ?>

<?php include_once(HTMLS_PATH.'templates/header.php'); ?>

		<div class="container-fluid">
			<!--  CONTENT BELOW  -->

<?php include_once(HTMLS_PATH.$page.'.php'); ?>

			<!--  CONTENT COMPLETE  -->
		</div><!-- END-container-fluid -->

<?php include_once(HTMLS_PATH.'templates/footer.php'); ?>

		<script>
		$(document).ready(function(){

<?php if($page === 'showlist'){ ?>
			// When the user scrolls down 20px from the top of the document, show the button (??)
			window.onscroll = function() {scrollFunction()};
<?php } ?>
	
			$('.regenerate-shows button').on('click', function(){
				$.post('/ajax/backgroundTask.php');
			});

			//tried to make the navbar menu-drop button clickable, but for some reason this doesnt get hit??
			$('#dropdownMenuButton').on('click', function(){
				console.warn('dropdownMenuButton click');
				window.location.href = "/showlist.php?s=s";
			});
		});
		function scrollFunction() {
			if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
				document.getElementById("up-button").style.display = "block";
			} else {
				document.getElementById("up-button").style.display = "none";
			}
		}
		function toTheTop(){
			document.body.scrollTop = 0; // For Safari
			document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
		}
		</script>
		
	</body>
</html>
