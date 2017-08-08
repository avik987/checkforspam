<div class="page_container">
	<div id="main_logo"><a href="/">CheckForSPAM.com</a></div>
	<?php
		if ($display_logout) {
			?>
            <ul id="top_menu">
                <li><a href="/login.php?action=logout&session_id=<?php echo $session_id; ?>">Logout</a></li>
            </ul>
            <?php
		} else {
			?>
            <ul id="top_menu">
                <li><a href="/login.php">Login</a></li>
                <li><a href="/register.php">Register</a></li>
            </ul>
            <?php
		}
	?>

	<div style="clear:both;"></div>
</div>