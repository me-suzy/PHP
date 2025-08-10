<?php

// XMLRPC for PHP 1.02 from http://phpxmlrpc.sourceforge.net/
// It's very basic, but it gets the job done
// require_once("xmlrpc.inc");

function pdbg( $var ) {
	echo "<pre>";
	print_r( $var );
	echo "</pre>";
}

// breaks a formatted time/date string into pieces and returns them as an array
// "Y-m-d H:i:s"
// I'm sure that there's a way to do this more efficiently with preg_split
function lj_tmbr( $pdt ) {
	$z = explode( " ", $pdt );
	$z_d = explode( "-", $z[0] );
	$z_t = explode( ":", $z[1] );
	$zdt = array( "y" => $z_d[0], "m" => $z_d[1], "d" => $z_d[2], "h" => $z_t[0], "i" => $z_t[1] );
	return( $zdt );
}

function get_ljuserdata($userid) {
	global $tableljusers,$querycount,$cache_ljuserdata,$use_cache;
	if ((empty($cache_ljuserdata[$userid])) OR (!$use_cache)) {
		$sql = "SELECT * FROM $tableljusers WHERE ID = '$userid'";
		$result = mysql_query($sql) or die("Your SQL query: <br />$sql<br /><br />MySQL said:<br />".mysql_error());
		$myrow = mysql_fetch_array($result);
		$querycount++;
		$cache_ljuserdata[$userid] = $myrow;
	} else {
		$myrow = $cache_ljuserdata[$userid];
	}
	return($myrow);
}

function get_ljuserdata2($userid) { // for team-listing
	global $tableljusers,$row;
	$ljuser_data['ID'] = $userid;
	$ljuser_data['lj_userid'] = $row->user_login;
	$ljuser_data['lj_passwd'] = $row->user_pass;
	return($ljuser_data);
}

function get_ljpostdata( $post_ID ){
	global $tableljposts,$querycount,$cache_ljposts,$use_cache;
	if ((empty($cache_ljposts[$post_ID])) OR (!$use_cache)) {
		$sql = "SELECT * FROM $tableljposts WHERE ID = '$post_ID'";
		$result = mysql_query($sql) or die("Your SQL query: <br />$sql<br /><br />MySQL said:<br />".mysql_error());
		$myrow = mysql_fetch_array($result);
		$querycount++;
		$cache_ljposts[$post_ID] = $myrow;
	} else {
		$myrow = $cache_ljposts[$post_ID];
	}
	return($myrow);
}

// not pleased with the assumption of index.php but i don't see any way to guess at the display name
// this is based off of permalink_single - significant changes in that function should be reflected here
function lj_permalink_single( $post_ID, $file='') {
	global $siteurl;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file=='')
		$file = 'index.php';
	$ljpm = $siteurl.'/'.$file.$querystring_start.'p'.$querystring_equal.$post_ID.$querystring_separator.'more'.$querystring_equal.'1'.$querystring_separator.'c'.$querystring_equal.'1';
	return( $ljpm );
}

function lj_update($post_ID, $user_ID, $pdt, $title, $content) {
	global $tableljposts, $blogname;
	global $lju_disablecomments, $lju_sourcefooter;
	// prep the LJClient for posting
	$ljuserdata = get_ljuserdata( $user_ID );
	// echo "ljc->U:".$ljuserdata['user_login']." ljc->P:".$ljuserdata['user_pass']."<br />";
	$ljc = new LJClient( $ljuserdata['user_login'], $ljuserdata['user_pass'] );
	// echo "ljc->U:".$ljc->lj_userid." ljc->P:".$ljc->lj_passwd."<br />";
	// and away we go... do some data prep
	$evt_time = lj_tmbr( $pdt );
	$content = stripslashes( stripslashes( $content ) );  // OUCH HACK!
	if ($lju_sourcefooter) {
		$content = $content.'<br /><br /><p>[cloned from <a href="'.lj_permalink_single( $post_ID ).'">'.$blogname.'</a>]</p>';
	}
	$response = $ljc->postevent( $title, $content,
									$evt_time['y'],	$evt_time['m'],	$evt_time['d'],
									$evt_time['h'],	$evt_time['i'],
									array( "opt_preformatted" => 1, "opt_nocomments" => $lju_disablecomments ) );
	$fc = $response->faultCode();
	if ( $fc == 0 ) {
		$ret_val = true;
		$rsp = xmlrpc_decode( $response->value() );

		// pdbg( $rsp );

		$query = "INSERT INTO $tableljposts (ID, LJID, post_author) VALUES ( $post_ID, ".$rsp['itemid'].", $user_ID )";
		$result = mysql_query($query);
	
		if (!$result)
		die ("Error updating B2-to-LJ Event Map... ".mysql_error()."<br />$query<br />contact the <a href=\"mailto:$admin_email\">webmaster</a>");
	} else {
		$ret_val = false;
	}
	return( $ret_val );
}

function lj_edit($post_ID, $user_ID, $pdt, $title, $content) {
	global $blogname;
	global $lju_disablecomments, $lju_sourcefooter;
	$ljpostdata = get_ljpostdata( $post_ID );
	// $lj_eventid = lj_mappost( $post_ID );
	if ( !empty( $ljpostdata['LJID'] ) ) {
		// prep the LJClient for posting
		$ljuserdata = get_ljuserdata( $user_ID );
		// echo "ljc->U:".$ljuserdata['user_login']." ljc->P:".$ljuserdata['user_pass']."<br />";
		$ljc = new LJClient( $ljuserdata['user_login'], $ljuserdata['user_pass'] );
		// echo "ljc->U:".$ljc->lj_userid." ljc->P:".$ljc->lj_passwd."<br />";
		// and away we go... do some data prep
		$evt_time = lj_tmbr( $pdt );
		$content = stripslashes( stripslashes( $content ) );  // OUCH HACK!
		if ($lju_sourcefooter) {
			$content = $content.'<br /><br /><p>[cloned from <a href="'.lj_permalink_single( $post_ID ).'">'.$blogname.'</a>]</p>';
		}
		$response = $ljc->editevent( $ljpostdata['LJID'], $title, $content,
										$evt_time['y'],	$evt_time['m'],	$evt_time['d'],
										$evt_time['h'],	$evt_time['i'],
										array( "opt_preformatted" => 1, "opt_nocomments" => $lju_disablecomments ) );
		$fc = $response->faultCode();
		if ( $fc == 0 ) {
			$ret_val = true;
		} else {
			$ret_val = false;
			// die("XMLRPC Error:".$response->faultString());
		}
	}
	return( $ret_val );
}

function lj_delete($post_ID, $user_ID) {
	global $tableljposts;
	$ljpostdata = get_ljpostdata( $post_ID );
	// $lj_eventid = lj_mappost( $post_ID );
	if ( !empty( $ljpostdata['LJID'] ) ) {
		// prep the LJClient for posting
		$ljuserdata = get_ljuserdata( $user_ID );
		// echo "ljc->U:".$ljuserdata['user_login']." ljc->P:".$ljuserdata['user_pass']."<br />";
		$ljc = new LJClient( $ljuserdata['user_login'], $ljuserdata['user_pass'] );
		// echo "ljc->U:".$ljc->lj_userid." ljc->P:".$ljc->lj_passwd."<br />";
		$response = $ljc->editevent( $ljpostdata['LJID'], "", "", "", "", "", "", "", "" );
		$fc = $response->faultCode();
		if ( $fc == 0 ) {
			$ret_val = true;
			$rsp = xmlrpc_decode( $response->value() );

			// pdbg( $rsp );

			$query = "DELETE FROM $tableljposts WHERE ID = $post_ID";
			$result = mysql_query($query);
		
			if (!$result)
			die ("Error updating B2-to-LJ Event Map... ".mysql_error()."<br />$query<br />contact the <a href=\"mailto:$admin_email\">webmaster</a>");
		} else {
			$ret_val = false;
			// die("XMLRPC Error:".$response->faultString());
		}
	}
	return( $ret_val );
}

class JournalEvent
{
	// this might have real functionality in the future
	// right now it exists to remind me to work on it :)

	// ------------------------------------------------------------
	var $event_id;
	var $title;
	var $content;
	var $evt_year;
	var $evt_month;
	var $evt_day;
	var $evt_hour;
	var $evt_minute;
	var $meta_props;
	// ------------------------------------------------------------

}

class LJClient
{

	// see http://www.livejournal.com/doc/server/ljp.csp.xml-rpc.protocol.html
	// for documentation on the livejournal XML-RPC interface

	// ------------------------------------------------------------
	var $clientid;
	var $protocol_version;
	var $lineendings;
	var $rpc_timeout;

	var $lj_srvr;
	var $lj_port;
	var $lj_xmlrpcuri;

	var $lj_userid;
	var $lj_passwd;

	var $lj_loggedin;
	// ------------------------------------------------------------

	function LJClient( $lj_userid = "", $lj_passwd = "" ){
		$this->clientid = "PHP-LJRPC/0.0.4";
		$this->protocol_version = 0;
		$this->lineendings = "unix";
		$this->rpc_timeout = 60;

		$this->lj_srvr = "www.livejournal.com";
		$this->lj_port = "80";
		$this->lj_xmlrpcuri = "/interface/xmlrpc";

		$this->lj_logged = false;
		$this->lj_userid = $lj_userid;
		$this->lj_passwd = md5( $lj_passwd );
		$this->client = new xmlrpc_client( $this->lj_xmlrpcuri, $this->lj_srvr, $this->lj_port );
	}

	// ------------------------- API ------------------------------

	function login(){
		$lj_method = "LJ.XMLRPC.login";
		$params = array( xmlrpc_encode( array( "username" => $this->lj_userid,
												"hpassword" => $this->lj_passwd,
												"ver" => $this->protocol_version,
												"clientversion" => $this->clientid ) ) );
		// print_r( xmlrpc_decode( $params[0] ) );
		$response = $this->do_the_thing( $lj_method, $params );
		$fc = $response->faultCode();
		if ( $fc == 0 ) {
			$this->loggedin = true;
			// if we want to do anything with the values from the server
			// this is where we'd do it
			// $rsp = xmlrpc_decode( $response->value() );
			return( true );
		}
		return( false );
	}

	function postevent( $subject, $event, $evt_year, $evt_month, $evt_day, $evt_hour, $evt_minute, $meta_props ){

		$this->chk_login();

		$lj_method = "LJ.XMLRPC.postevent";
		$params = array( xmlrpc_encode( array( "username" => $this->lj_userid,
												"hpassword" => $this->lj_passwd,
												"ver" => $this->protocol_version,
												"lineendings" => $this->lineendings,
												"subject" => $subject,
												"event" => $event,
												"year" => $evt_year,
												"mon" => $evt_month,
												"day" => $evt_day,
												"hour" => $evt_hour,
												"min" => $evt_minute,
												"props" => $meta_props ) ) );
		// pdbg( xmlrpc_decode( $params[0] ) );
		$response = $this->do_the_thing( $lj_method, $params );
		return( $response );
	}

	function editevent( $id, $subject, $event, $evt_year, $evt_month, $evt_day, $evt_hour, $evt_minute, $meta_props ){

		$this->chk_login();

		$lj_method = "LJ.XMLRPC.editevent";
		$params = array( xmlrpc_encode( array( "username" => $this->lj_userid,
												"hpassword" => $this->lj_passwd,
												"ver" => $this->protocol_version,
												"lineendings" => $this->lineendings,
												"itemid" => $id,
												"subject" => $subject,
												"event" => $event,
												"year" => $evt_year,
												"mon" => $evt_month,
												"day" => $evt_day,
												"hour" => $evt_hour,
												"min" => $evt_minute,
												"props" => $meta_props ) ) );
		// pdbg( xmlrpc_decode( $params[0] ) );
		$response = $this->do_the_thing( $lj_method, $params );
		return( $response );
	}

	// ------------------------------------------------------------

	// ------------------- Internal Functions ---------------------

	function do_the_thing( $method, $params ){
		$xmlrpc_msg = new xmlrpcmsg( $method, $params );
		$xmlrpc_rsp = $this->client->send( $xmlrpc_msg, $this->rpc_timeout );
		return( $xmlrpc_rsp );
	}

	function chk_login(){
		if ($this->loggedin == false){
			$this->login();
		}
	}

	// ------------------------------------------------------------

}
?>