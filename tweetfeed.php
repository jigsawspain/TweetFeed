<?php
/* TweetFeeder */
/* Build 001 - 16/10/2011 */
/* By Jigsaw Spain */

/* Usage */
/*

   1) Upload script to server
   2) Include in webpage
      e.g. include('path/to/tweettest.php');
   3) Add function using the desires twitter username
      e.g. tweetfeed('jigsawspain');
	  
*/

/* CSS Customisation */
/*

   This script creates a DIV with and id of 'twfeed' containing an unnumbered list
   for customisation, use the following CSS hooks in your CSS file:
   
   #twfeed {}         // The wrapper around the whole output
   #twfeed ul {}      // The wrapper around the Tweets
   #twfeed ul li {}   // Individual Tweets
   #twfeed ul li a {} // The link to teh Tweet
   
 */


function tweetfeed($twitter_username)
// $twitter_username > Username to feed from Twitter
{

	/* Construct Feed Array */

	// Fetch Twitter Feed
	$twitter_URL = 'http://api.twitter.com/1/statuses/user_timeline/'.$twitter_username.'.xml';
	$feed_content = file_get_contents( $twitter_URL );
	if ( empty( $feed_content ) )
	{
		die( "ERROR 0: Unable to Retrieve Twitter Feed or Feed Empty!" );
	}

	// Read Twitter feed
	$xml = new DOMDocument();
	$xml->loadXML( $feed_content );	
	$xpath = new DOMXpath( $xml );
	$tweets = $xpath->query( "//status" );
	$i = 0;

	// Build Array

	foreach ( $tweets as $tweet )
	{
		// Tags
		$tags = $tweet->childNodes;
		foreach ( $tags as $tag )
		{
			if ( !empty( $tag->tagName ) )
			// General Tags
			{
				$tweet_array[$i][$tag->tagName] = $tag->nodeValue;
			}
		}
		$i++;
	}

	if (count($tweet_array)!=0)
	// Tweets Found
	{
		echo '
	<div id="twfeed">
		<!-- TweetFeed by JigsawSpain.com -->
		<ul>';
		foreach ($tweet_array as $tweet)
		{
			// Get Time Difference
			$time_diff = time()-strtotime($tweet['created_at']);
			if ($time_diff>=2678400)
			{
				$time = 'over a month ago';
			}
			elseif ($time_diff>=604800)
			{
			if ((int)($time_diff/604800)==1)
				{
					$time = 'last week';
				}
				else
				{
					$time = (int)($time_diff/604800).' weeks ago';
				}
			}
			elseif ($time_diff>=86400)
			{
				if ((int)($time_diff/86400)==1)
				{
					$time = 'yesterday';
				}
				else
				{
					$time = (int)($time_diff/86400).' days ago';
				}
			}
			elseif ($time_diff>=3600)
			{
				$time = 'about '.(int)($time_dif/3600).' hour(s) ago';
			}
			elseif ($time_diff>=60)
			{
				$time = 'about '.(int)($time_diff/60).' minute(s) ago';
			}
			else
			{
				if ($time_diff <= 10)
				{
					$time = 'just now';
				}
				else
				{
					$time = $time_diff.' seconds ago';
				}
			}
			
			//Output the Tweet
			echo '<li>'.$tweet['text'].'<br/>Posted '.$time.' <a href="http://twitter.com/#!/'.$twitter_username.'/status/'.$tweet['id'].'" target="_blank">@'.$twitter_username.'</a></li>';
		}
		echo '
		</ul>
		<!-- END: TweetFeed -->
	</div>';
	}
	else
	// No Tweets Found
	{
		die( "ERROR 1: Twitter Feed Empty!" );
	}
}
?>