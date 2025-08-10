<?php

	// phpODP.inc - v 1.0UF2 (unfinished release 2) - PHP Class phpODP for use with ODP (dmoz.org) data retrieval
	// Copyright (c) 2000, Ryan Heggem.
	//	URL:	http://www.gren.addr.com/
	//	EMAIL:	gren@addr.com
	//
        // A readme for this class can be found at:
        // http://www.gren.addr.com/scripts/PHP/phpODP/README.html
        //
        // A demo of what this class can do can be found at:
        // http://www.gren.addr.com/scripts/PHP/phpODP/demo/
	//
	// Released under the GNU GPL.
	// http://www.gnu.org/copyleft/gpl.html
	// 
	// This program is free software; you can redistribute it and/or
	// modify it under the terms of the GNU General Public License
	// as published by the Free Software Foundation; either version 2
	// of the License, or (at your option) any later version.
	// 
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
	// 
	// You should have received a copy of the GNU General Public License
	// along with this program; if not, write to the Free Software
	// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

	class phpODP {

		function _init() {

			global $SERVER_NAME, $SCRIPT_NAME, $PATH_INFO, $HTTP_SERVER_VARS, $DMOZPATH;


			// misc stuff...
			$this->dirs = explode('/', preg_replace('|^/(.*)/$|', '\\1', $PATH_INFO));

			// spit out each section of a dmoz.org catagory into an array
			// TODO #1: 
			$this->contents = explode('<hr>', implode('', file('http://dmoz.org' . $DMOZPATH) ) );

			// get page title
			eregi('<title>(.*)</title>', $this->contents[0], $tmp_page_title);
			$this->page_title = $tmp_page_title[1];

			// check for description and faq files, then set up links to them
			$this->desc_file = (preg_match('|desc.html|i', $this->contents[0])) ? ' <a href="http://dmoz.org' . $PATH_INFO . 'desc.html">Description</a> ' : '';
			$this->faq_file = (preg_match('|faq.html|i', $this->contents[0])) ? ' <a href="http://dmoz.org' . $PATH_INFO . 'faq.html">FAQ</a> ' : '';

			// check for an A-Z catagory listing, then set up links to each one
			$a2zA = explode(',', strtoupper('a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z'));
			if(preg_match('|/[A-Z]/|i', $this->contents[0])){
				for($z=0;$z<=25;$z++){
					$sfx = ($z != 25) ? ' / ' : '';
					$this->a2z .= '<a href="' . $HTTP_SERVER_VARS["SCRIPT_URI"] . "?DMOZPATH=" . $a2zA[$z] . '/">' . $a2zA[$z] . '</a>' . $sfx;
				}
			} else{ $this->a2z = ''; }

			// this chunk of code...
			//  goes through each array element of $contents to see what's what
			//   replaces all the dmoz.org relative links with a local version
			//    sets a <font> tag in <td> tags to make the text in them look nice
			//     sets <img> tags to point to dmoz.org
			if(preg_match('|href="/|i', $this->contents[1])){
				$this->catagories = preg_replace('|href="/(.*)"|im', 'href="' . $SCRIPT_NAME . "?DMOZPATH=" . '/\\1"', preg_replace('|(<td valign=top>)|im', '\\1', preg_replace('|(</td>)|im', '\\1', $this->contents[1])));
				if(preg_match('|href="/|i', $this->contents[2])){
					$this->other_catagories = preg_replace('|href="/(.*)"|im', 'href="' . $SCRIPT_NAME . '/\\1"', preg_replace('|(<td valign=top>)|im', '\\1', preg_replace('|(</td>)|im', '\\1', $this->contents[2])));
					if(preg_match('|<li><a href="http://|i', $this->contents[3])){
						$this->links = preg_replace('|src="/(.*)"|im', 'src="http://dmoz.org/\\1"', $this->contents[3]);
					} else{ $this->links = ''; }
				} elseif(preg_match('|<li><a href="http://|i', $this->contents[2])){
					$this->links = preg_replace('|src="/(.*)"|im', 'src="http://dmoz.org/\\1"', $this->contents[2]);
				} else{ $this->other_catagories = ''; $this->links = ''; }
			} elseif(preg_match('|<li><a href="http://|i', $this->contents[1])){
				$this->links = preg_replace('|src="/(.*)"|im', 'src="http://dmoz.org/\\1"', $this->contents[1]);
			} else{ $this->catagories = ''; $this->other_catagories = ''; $this->links = ''; }

		}

		function odp2rss() {
			global $SERVER_NAME, $SCRIPT_NAME, $PATH_INFO;
			$links_arr = explode('<li>',$this->links);
			$this->rss = "<?xml version=\"1.0\" encoding=\"US-ASCII\"?>\n";
			$this->rss .= "<!DOCTYPE rss PUBLIC \"-//Netscape Communications//DTD RSS 0.91//EN\"\n";
			$this->rss .= "\t\"http://my.netscape.com/publish/formats/rss-0.91.dtd\">\n\n";
			$this->rss .= "<rss version=\"0.91\">\n\n";
			$this->rss .= "<channel>\n";
			$this->rss .= "\t<title>$this->page_title</title>\n";
			$this->rss .= "\t<link>$SERVER_NAME$SCRIPT_NAME$PATH_INFO</link>\n";
			$this->rss .= "\t<description>$this->page_title</description>\n";
			$this->rss .= "\t<language>en-us</language>\n\n";
			for($i=1;$i<=(count($links_arr)-1);$i++){
				if(!preg_match('/<img/i',$links_arr[$i])){
					preg_replace('/(<ul>|<\/ul>|<p>|<img.*">|\n|\r|&nbsp;)/i','',$links_arr[$i]);
					preg_match('/^.*href="(.*)">(.*)<\/a>\s+\-\s+(.*)$/im',$links_arr[$i],$items);
					$this->rss .= "\t<item>\n";
					$this->rss .= "\t\t<title>$items[2]</title>\n";
					$this->rss .= "\t\t<link>$items[1]</link>\n";
					$this->rss .= "\t\t<description>$items[3]</description>\n";
					$this->rss .= "\t</item>\n\n";
				}
			}
			$this->rss .= "</channel>\n\n";
			$this->rss .= "</rss>\n";
		}

	}
?>
