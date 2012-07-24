<?php
/********** DEFAULT CONFIG **********/

$categories = '';
$post_subject = '';
$post_tags = ''; 
$possible_icons = '';
$post_security = '';
$entry_cut = true; 
$cut_text = ''; 


/********** START FUNCTIONS *********/

function rec($item,$dc) {
    $i = 0;
    $post = '<dt><a href="';
    $post .= $item->link;
    $post .= '">';
    $post .= $item->title;
    $post .= "</a></dt>\n";
    $description = $item->description;
    if ($description != "") {
        $post .= '<dd>';
        $post .= $description;
        $post .= "</dd>\n";
    }
    $poster = $dc->creator;
    $subject = $dc->subject;
    $tags = preg_split('/ /', $subject);
    $i = 0;
    if ($subject != "") {
        $post .= '<dd style="font-size: 80%; text-align: right;">(';
        foreach ($tags as $tagname) {
            if ($i > 0) { $post .= ', '; }
            if (preg_match("/\//", $tagname) > 0) {
                $pattern="/\//";
                $replacement='%252f';
                $tagurl = preg_replace($pattern, $replacement, $tagname);
            } else {
                $tagurl = $tagname;
            }
            $post .= '<a href="http://pinboard.in/u:'.$poster.'/t:'.$tagurl.'">'.$tagname.'</a>';
            $i++;
        }
        $post .= ")</dd>\n";
    }

    return $post;
}

function roman_numerals($input_arabic_numeral) {
//swiped from http://www.php.net/manual/en/function.base-convert.php#71589
//yes, I'm aware of the PEAR solution; I can't assume everyone has it installed

    if ($input_arabic_numeral == '') { $input_arabic_numeral = date("Y"); } // DEFAULT OUTPUT: THIS YEAR
    $arabic_numeral            = intval($input_arabic_numeral);
    $arabic_numeral_text    = "$arabic_numeral";
    $arabic_numeral_length    = strlen($arabic_numeral_text);

    if (!preg_match('/[0-9]+/', $arabic_numeral_text)) {
return false; }

    if ($arabic_numeral > 4999) {
return false; }

    if ($arabic_numeral < 1) {
return false; }

    if ($arabic_numeral_length > 4) {
return false; }

    $roman_numeral_units    = $roman_numeral_tens        = $roman_numeral_hundreds        = $roman_numeral_thousands        = array();
    $roman_numeral_units[0]    = $roman_numeral_tens[0]    = $roman_numeral_hundreds[0]    = $roman_numeral_thousands[0]    = ''; // NO ZEROS IN ROMAN NUMERALS

    $roman_numeral_units[1]='I';
    $roman_numeral_units[2]='II';
    $roman_numeral_units[3]='III';
    $roman_numeral_units[4]='IV';
    $roman_numeral_units[5]='V';
    $roman_numeral_units[6]='VI';
    $roman_numeral_units[7]='VII';
    $roman_numeral_units[8]='VIII';
    $roman_numeral_units[9]='IX';

    $roman_numeral_tens[1]='X';
    $roman_numeral_tens[2]='XX';
    $roman_numeral_tens[3]='XXX';
    $roman_numeral_tens[4]='XL';
    $roman_numeral_tens[5]='L';
    $roman_numeral_tens[6]='LX';
    $roman_numeral_tens[7]='LXX';
    $roman_numeral_tens[8]='LXXX';
    $roman_numeral_tens[9]='XC';

    $roman_numeral_hundreds[1]='C';
    $roman_numeral_hundreds[2]='CC';
    $roman_numeral_hundreds[3]='CCC';
    $roman_numeral_hundreds[4]='CD';
    $roman_numeral_hundreds[5]='D';
    $roman_numeral_hundreds[6]='DC';
    $roman_numeral_hundreds[7]='DCC';
    $roman_numeral_hundreds[8]='DCCC';
    $roman_numeral_hundreds[9]='CM';

    $roman_numeral_thousands[1]='M';
    $roman_numeral_thousands[2]='MM';
    $roman_numeral_thousands[3]='MMM';
    $roman_numeral_thousands[4]='MMMM';

    if ($arabic_numeral_length == 3) { $arabic_numeral_text = "0" . $arabic_numeral_text; }
    if ($arabic_numeral_length == 2) { $arabic_numeral_text = "00" . $arabic_numeral_text; }
    if ($arabic_numeral_length == 1) { $arabic_numeral_text = "000" . $arabic_numeral_text; }

    $anu = substr($arabic_numeral_text, 3, 1);
    $anx = substr($arabic_numeral_text, 2, 1);
    $anc = substr($arabic_numeral_text, 1, 1);
    $anm = substr($arabic_numeral_text, 0, 1);

    $roman_numeral_text = $roman_numeral_thousands[$anm] . $roman_numeral_hundreds[$anc] . $roman_numeral_tens[$anx] . $roman_numeral_units[$anu];
return ($roman_numeral_text);
}

/*********** END FUNCTIONS **********/

/*********** START SCRIPT ***********/

// load the info
require('config.php');

$updated = simplexml_load_file('updated.xml');
$lastposted = $updated->lastposted;
$postnumber = $updated->postnumber;

$pintag = '';

if ($followedtag != null) {
    foreach ($followedtag as $tag) {
        if (preg_match("/\//", $tag) > 0) {
            $pattern="/\//";
            $replacement='%252f';
            $tag = preg_replace($pattern, $replacement, $tag);
        }
        $tag = "/t:".$tag;
        $pintag .= $tag;
    }
}
if ($pinboard != null) {
    $pinboard = '/u:'.$pinboard;
}
$feedpath = 'http://feeds.pinboard.in/rss'.$pinboard.$pintag;
$file = file_get_contents($feedpath);
$xml = new SimpleXmlElement($file);

//build the post content
$list = '';

foreach ($xml->item as $item) {
    $namespaces = $item->getNameSpaces(true);
    $dc = $item->children($namespaces['dc']);
    $pubdate = (string) $dc->date;
    $pubdate = strtotime($pubdate);
    $subject = $dc->subject;
    if ($pubdate > $lastposted) {
        if ($categories != null) {
            foreach($categories as $tag => $category) {
                $tag_pattern = '/'.$tag.'/';
                if (preg_match($tag_pattern,$subject)) {
                    $category_md5 = md5($category);
                    $listname = 'list_'.$category_md5;
                    ${$listname} .= rec($item,$dc);
                }
            }
        } else {
            $list .= rec($item,$dc);
        }
    }
}

if ($categories != null) {
    $parts = array_flip($categories); //il faudra escaper tout ça
    foreach($parts as $part_titles => $tag) {
        $category_md5 = md5($part_titles);
        $part_titles = htmlentities($part_titles);
        $listname = 'list_'.$category_md5;
        if (${$listname} != null) {
            if ($entry_cut == true) {
                $list .= "<cut text='".$part_titles."'>\n<h2>".$part_titles."</h2>\n<dl>\n".${$listname}."</dl>\n</cut><br />\n";
            } else {
                $list .= "<h2>".$part_titles."</h2>\n<dl>\n".${$listname}."</dl>\n<br />\n";
            }
        }
    }
} else {
    if ($entry_cut == true) {
        $list = "<cut text='".$cut_text."'>\n<dl>\n".$list."</dl>\n</cut>";
    } else {
        $list = "<dl>\n".$list."</dl>";
    }
}

$post = "<raw-code>".$list."</raw-code>";

//post title
if ($title_number == true) {
    $postnumber = $postnumber + 1;
    if ($roman_numbered == true) {
        $romanposted = roman_numerals($postnumber);
    } else { $romanposted = $postnumber; }
    $post_subject = $post_subject.' '.$romanposted;
}

//post tags
$post_tags = "post-tags: ".$post_tags."\n";

//post icons
if ($possible_icons != '') {
    $maxarray = count($possible_icons) - 1;
    $post_icons = "post-icon: ".$possible_icons [ rand (0,$maxarray) ]."\n";
} else { $post_icons = ''; }

//post security
if ($post_security != '') {
    $post_security = "post-security: ".$post_security."\n";
}

//build email options
if ($list != "") {
    $to = $dreamwidth.'+'.$pin.'@post.dreamwidth.org';
    $additional_headers = 'From: '.$email;
    $message = $post_tags.$post_icons.$post_security."\n".$post;

//send email
    mail ($to,
    $post_subject,
    $message,
    $additional_headers);


//update updated.xml
    $newfile = "<?xml version='1.0'?>\n<document>\n\t<lastposted>";
    $newfile .= time();
    $newfile .= "</lastposted>\n\t<postnumber>";
    $newfile .= $postnumber;
    $newfile .= "</postnumber>\n</document>";

    $update = fopen("updated.xml", "w");
    fwrite($update,$newfile);
    fclose($update);
}

/************ END SCRIPT ************/
?>
