<?php

 // This is a minimum example of using the class
 include("FeedWriter.php");
 include("simple_html_dom.php");

 // Create new instance of a feed
 $TestFeed = create_new_feed("ATOM");

 $html = file_get_html('http://wetteren.be/Nieuws/505/default.aspx');

 //print_r($html);
 
 // Loop through html pulling feed items out
 foreach($html->find('#overzicht .nieuws li') as $article)
 {
    // Get a parsed item
    $item = get_item_from_article($article);

    // Get the item formatted for feed
    $formatted_item = create_feed_item($TestFeed, $item);

    //Now add the feed item
    $TestFeed->addItem($formatted_item);
 }

 //OK. Everything is done. Now generate the feed.
 $TestFeed->genarateFeed();


// HELPER FUNCTIONS

/**
 * Create new feed - encapsulated in method here to allow
 * for change in feed class etc
 */
function create_new_feed()
{
     //Creating an instance of FeedWriter class.
     $TestFeed = new FeedWriter(RSS2);

     //Use wrapper functions for common channel elements
     $TestFeed->setTitle('Wetteren.be - Nieuws');
     $TestFeed->setLink('http://www.wetteren.be');
     $TestFeed->setDescription('RSS feed voor het nieuws uit Wetteren');

     //Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
     //$TestFeed->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/images/logo.gif');

     return $TestFeed;
}


/**
 * Take in html article segment, and convert to usable $item
 */
function get_item_from_article($article)
{
    $item['title'] = $article->find('a.title', 0)->plaintext;
    $item['title'] = html_entity_decode($item['title'], ENT_NOQUOTES, 'UTF-8');

    $item['description'] = $article->find('..detailKort', 0)->plaintext;
    $item['link'] = "http://www.wetteren.be/" . $article->find('a.title', 0)->href;
    $item['pubDate'] = $article->find('span.date', 0)->plaintext;

    return $item;
}


/**
 * Given an $item with feed data, create a
 * feed item
 */
function create_feed_item($TestFeed, $item)
{
    //Create an empty FeedItem
    $newItem = $TestFeed->createNewItem();

    //Add elements to the feed item
    $newItem->setTitle($item['title']);
    $newItem->setLink($item['link']);
    $newItem->setDate($item['pubDate']);
    $newItem->setDescription($item['description']);

    return $newItem;
}
?>