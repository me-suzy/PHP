</div>

<?php
if (!empty($_SESSION[messageTitle]) || !empty($_SESSION[messageBody]))
{
	echo "<div class=\"content\">\n";
        echo "<h1>Notification</h1>\n";
        echo "<h2>$_SESSION[messageTitle]<h2>\n";
        echo "<p>$_SESSION[messageBody]</p>";
        echo "</div>";

        $_SESSION[messageTitle] = NULL;
        $_SESSION[messageBody] = NULL;
}

?>

<div class="content">
Tunez (c) Quarkness
</div>

<div id="leftnav">
	<h2>Your Wish?</h2>
	<p>
		<a href="index.php">:: Home</a><br>
<?php if ($_SESSION[perms][p_upload]) { ?>
        <a href="upload.php">:: Upload a Song</a><br>
<?php } ?>
        <a href="browse.php">:: Browse by Title</a><br>
        <a href="browse_artist.php">:: Browse by Artist</a><br>
        <a href="browse_album.php">:: Browse by Album</a><br>
        <a href="browse_genre.php">:: Browse by Genre</a><br>
        <a href="history.php">:: Play History</a><br>
        <a href="charts.php">:: Charts</a><br>
        <a href="recent.php">:: Recently added</a><br>
        <a href="blocked.php">:: Blocked Songs</a><br>
        <br>
    <a href="preferences.php">:: Preferences</a><br>
<?php
if($_SESSION[user_id]) {
    echo "<a href=\"login.php?action=logout\">:: Log Out</a>";
}
?>
    </p>
    <h2>Search</h2>
        <form action="search.php" method="get">
        <p>
            <input type="hidden" name="action" value="doSearch">
		    <input class="field" type="text" name="searchFor" size="13" value="<?php echo stripslashes($_SESSION[searchFor]); ?>">
            <select class="dropdown" name="search_type">
                <option <?php selected("songtitle", $_SESSION[search_type]); ?> value="songtitle">Song
                <option <?php selected("artist_name", $_SESSION[search_type]); ?> value="artist_name">Artist
                <option <?php selected("album_name", $_SESSION[search_type]); ?> value="album_name">Album
                <option <?php selected("uploader_id", $_SESSION[search_type]); ?> value="uploader_id">Uploader
                <option <?php selected("all", $_SESSION[search_type]); ?> value="all">All
            </select>
            <input type="submit" value="Search">
        </p>
        </form>
    <br><br>
<?php
if ($_SESSION[perms][p_daemon] OR 
        $_SESSION[perms][p_volume] OR 
        $_SESSION[perms][p_skip] OR 
        $_SESSION[perms][p_change_perms] OR 
        $_SESSION[perms][p_updateDb] OR 
        $_SESSION[perms][p_sync]) {
    
	print "<h2>Your Command!</h2>\n<p>";
    if ($_SESSION[perms][p_daemon]) {
        print "<a href=\"admin_daemon.php?action=stop\">:: Stop daemon</a><br>";
        print "<a href=\"admin_daemon.php?action=start\">:: Start daemon</a><br>";
    }
    if ($_SESSION[perms][p_volume]) {
        print "<a href=\"admin_volume.php\">:: Volume</a><br>";
    }
    if ($_SESSION[perms][p_skip]) {
        echo "  <a href=\"admin_skip.php\">:: Skip Song</a><br>";
    }
    if ($_SESSION[perms][p_change_perms]) {
        echo "  <a href=\"admin_users.php\">:: User Admin</a><br>";
    }
    if ($_SESSION[perms][p_change_perms]) {
        echo "  <a href=\"admin_groups.php\">:: Group Admin</a><br>";
    }
    if ($_SESSION[perms][p_updateDb]) {
        echo "  <a href=admin_updateDb.php>:: Update Database</a><br>";
    }
    if ($_SESSION[perms][p_sync]) {
        echo "  <a href=admin_sync.php>:: Sync entries back to mp3's</a></p>";
    }
}
?>
</div>

<div id="rightnav">
	<h2>Now Playing</h2>
<p>
<?php nowPlaying(); ?>
</p>
	<h2>PlayList</h2>
<?php displayMenuQueue();
?>
<h2>About You</h2>
<?php
if (empty($_SESSION[user_id])) {
?>
	<p>You are not logged in.</p>
	<div class="formdiv">
	<form action="login.php" method="post">
	<h4>Username</h4>
	<p><input type="hidden" name="action" value="login">
	<input class="field" type="text" name="user" size="16" value=""></p>
	<h4>Password</h4>
	<p><input class="field" type="password" name="pw" size="16" value=""></p>
	<p><input type="checkbox" name="remember">remember me</p>
	<p><input class="button" type=submit value="Log In"></p>
	</form>
	</div>
<p style="text-align: center">
<a href="signup.php">Sign Up Here!</a>
</p>
<?php

}
else
{
    echo "<p>You are known as $_SESSION[user]</p>";
    echo "<p>Click <a href='login.php?action=logout'>here</a> to logout</p>";
    echo "<p>Click <a href=\"admin_users_clear.php?clear_user_id=$_SESSION[user_id]\">here</a> to clear your votes</p>";
    echo "<h4>Random song for your voting pleasure</h4>";

    // This is a better way to select for a random song and much faster
    // when dealing with large numbers of song ids
    include_once("song.class.php");
    $randoms = random_song_ids(1);
    foreach ($randoms as $song_id) {
        $mysong = new Song($song_id, NULL);
        $mysong->read_data_from_db(NULL);
        $mysong->print_info(TRUE);
    }
}
?>

</div>
<script LANGUAGE="JavaScript" src="js/clock.js"></script>

</body>
</html>

