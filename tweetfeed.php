<?php
/* TweetFeeder */
/* Build 003 - 16/10/2011 */
/* By Jigsaw Spain */

/* Usage */
/*

   1) Upload script to server
   2) Include in webpage
      e.g. include('path/to/tweettest.php');
   3) Add function using the desired twitter username and attributes
      e.g. tweetfeed('jigsawspain', 5, true);

   ATTRIBUTES
   ==========
   There is one required attribute and two optionl attributes available.
 
   $twitter_username (REQUIRED) - the username to produce the feed from
   $tweet_count (OPTIONAL) - how many tweets to show. (Max 20)
     0 (Default) - Show ALL available tweets
     1, 2, 3 etc - Show 1, 2 or 3 tweets only
   $show_exact_time (OPTIONAL) - Whether or not to show exact time/date of all tweets
     false (Default) - show "1 minute ago"
     true - show '01/01/2011 at 12:00
     
	  
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

function tweetfeed($twitter_username, $tweet_count = 0, $show_exact_time = false)
/* 
 * $twitter_username > (REQUIRED) Username to feed from Twitter
 * $tweet_count > (OPTIONAL) How many tweets to show. 0 = all (Max 20)
 * $show_exact_time > (OPTIONAL) Show exact time/date of tweet, false = show time since post
 */
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
		if ($i == $tweet_count) {
			break;
		}
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
			if ($show_exact_time)
			// Get Exact Time
			{
				$time = 'on '.date('d M Y', strtotime($tweet['created_at'])).' at '.date('H:i', strtotime($tweet['created_at']));
			} else
			// Get Time Difference
			{
				$time_diff = time()-strtotime($tweet['created_at']);
				if ($time_diff>=2678400)
				{
					$time = 'on '.date('d M Y', strtotime($tweet['created_at']));
				}
				elseif ($time_diff>=604800)
				{
					$weeks = (int)($time_diff/604800);
					if ($weeks==1)
					{
						$time = 'last week';
					}
					else
					{
						$time = $weeks.' weeks ago';
					}
				}
				elseif ($time_diff>=86400)
				{
					$days = (int)($time_diff/86400);
					if ($days == 1)
					{
						$time = 'yesterday';
					}
					else
					{
						$time = $days.' days ago';
					}
				}
				elseif ($time_diff>=3600)
				{
					$hours = (int)($time_diff/3600);
					if ($hours == 1)
					{
						$time = 'about '. $hours .' hour ago';
					}
					else
					{
						$time = 'about '. $hours .' hours ago';
					}
				}
				elseif ($time_diff>=60)
				{
					$minutes = (int)($time_diff/60);
					if ($minutes == 1)
					{
						$time = 'about '. $minutes .' minute ago';
					}
					else
					{
						$time = 'about '. $minutes .' minutes ago';
					}
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