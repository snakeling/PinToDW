<?php
# PINBOARD TO DREAMWIDTH
# Code by snakeling (snakeling.dreamwidth.org)
# https://github.com/snakeling/PinToDW
# CC-BY-NC

# Last modified 2012-07-24

/*************************************
             CONFIGURATION
*************************************/

/************* PINBOARD *************/

// your Pinboard username
$pinboard = 'memyselfi';

// if you want to select up to 3 tags, wrap them in quotes and separate them with commas
// this will pull all bookmarks tagged with both tag1 and tag2
// if you want to select every tag, replace array() with empty quotes
$followedtag = array('tag1', 'tag2');

// this does nothing yet, but maybe I'll extend support to Delicious/other bookmarking sites in the future
$bookmarking = 'pinboard';


/************ DREAMWIDTH ************/

// your Dreamwidth username
$dreamwidth = 'memyselfi';

// password you choose when you configured post by mail
$pin = 'postingbymail';

// if you want to separate your bookmarks in different categories, like "Fic" and "Art"
// basically, you're associating a tag with a category
// you can associate several tags with the same category, but not one tag to several categories
// tags go on the left of the arrow, category names on the right
// if you don't want to use categories, simply delete or comment out (by using # at the beginning of the lines)
$categories = array('fic' => 'Fic',
    'series' => 'Fic',
    'art' => 'Art'
    );

// choose true if you want the post titles to be numbered
$title_number = true;

// choose true if you want the title number to be Roman style (VIII) rather than Arabic (8)
$roman_numbered = true;

// an email address that's been authorised for posting by mail
$email = 'memyselfi@isp.com';

// the subject of the Dreamwidth post
$post_subject = 'My bookmarks';

// the tags on the Dreamwidth post; separate them with a comma
$post_tags = 'tag1, tag2, tag3'; 

// the icons you want to associate with your bookmark posts
// the icons will be pulled randomly
// copy-paste the keywords exactly
// if you want to use all your icons, delete or comment out the lines
$possible_icons = array("icons: keyword1",
        "icons: keyword2",
        "icons: keyword3");

// entry security: 'public', 'private', 'access', or the name of the filter
// delete or comment out to use your journal's default security
$post_security = 'public';

// set to false if you don't want your bookmarks to be cut
// remember to spare your friends' rlists!
$entry_cut = true; 

// text you want for the cut on Dreamwidth; leave empty for the default
$cut_text = 'More under the cut';

// this does nothing yet, but I plan on extending support to LJ and LJ clones in the future
$journaling = 'dreamwidth';

?>
