<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Blog\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */

namespace SilverWare\Blog\Pages;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\Security\Member;
use SilverWare\Blog\Model\BlogTag;
use PageController;

/**
 * An extension of the page controller class for a blog controller.
 *
 * @package SilverWare\Blog\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class BlogController extends PageController
{
    /**
     * Defines the URLs handled by this controller.
     *
     * @var array
     * @config
     */
    private static $url_handlers = [
        'archive/$Year/$Month' => 'archive',
        'author/$Author!' => 'author',
        'tag/$Tag!' => 'tag',
        '$Post' => 'index'
    ];
    
    /**
     * Defines the allowed actions for this controller.
     *
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'rss',
        'tag',
        'author',
        'archive'
    ];
    
    /**
     * Default action for this controller, either render the blog or redirect to an existing post.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse|DBHTMLText|array
     */
    public function index(HTTPRequest $request)
    {
        if ($segment = $request->param('Post')) {
            
            if ($post = BlogPost::get()->find('URLSegment', $segment)) {
                return $this->redirect($post->Link());
            }
            
        }
        
        return $this->render();
    }
    
    /**
     * Renders a list of the latest blog posts as an RSS feed.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse|DBHTMLText|array
     */
    public function rss(HTTPRequest $request)
    {
        // Answer 404 (if disabled):
        
        if (!$this->FeedEnabled) {
            return $this->httpError(404);
        }
        
        // Create Feed Object:
        
        $rss = RSSFeed::create(
            $this->getFeedPosts(),
            $this->Link(),
            $this->FeedTitle,
            $this->FeedDescription
        );
        
        // Output Feed Data:
        
        return $rss->outputToBrowser();
    }
    
    /**
     * Renders a list of the blog posts for the requested year and optional month.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse|DBHTMLText|array
     */
    public function archive(HTTPRequest $request)
    {
        // Obtain Year Param:
        
        if ($year = $request->param('Year')) {
            
            // Convert Year to Integer:
            
            $year = (integer) $year;
            
            // Check Year Value:
            
            if ($year && in_array($year, $this->data()->getValidYears())) {
                
                // Obtain Month Param (optional):
                
                $month = (integer) $request->param('Month');
                
                // Check Month Value:
                
                if ($month && !in_array($month, range(1, 12))) {
                    return $this->httpError(404);
                }
                
                // Define Date Value:
                
                $dateMessage = $year;
                
                if ($month) {
                    $date = DBDate::create()->setValue(strtotime(sprintf('%d-%02d', $year, $month)));
                    $dateMessage = $date->format('LLLL y');
                }
                
                // Define Filter Message:
                
                $message = sprintf(
                    _t(__CLASS__ . '.SHOWINGPOSTSFORDATE', 'Showing posts for %s'),
                    $dateMessage
                );
                
                // Filter Posts by Year:
                
                $this->data()->addListWhere(['YEAR("Date")' => $year]);
                
                // Filter Posts by Month:
                
                if ($month) {
                    $this->data()->addListWhere(['MONTH("Date")' => $month]);
                }
                
                // Add Filter Alert to List:
                
                $this->data()->addListAlert($message);
                
                // Answer Template Data:
                
                return [];
                
            }
            
        }
        
        // Answer 404 Not Found:
        
        return $this->httpError(404);
    }
    
    /**
     * Renders a list of the blog posts matching the requested author.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse|DBHTMLText|array
     */
    public function author(HTTPRequest $request)
    {
        // Obtain Author Segment:
        
        if ($segment = $request->param('Author')) {
            
            // Obtain Tag Object:
            
            if ($author = Member::get()->find('URLSegment', $segment)) {
                
                // Filter Posts by Author Post IDs:
                
                $this->data()->addListFilter(['ID' => ($author->BlogPosts()->column('ID') ?: null)]);
                
                // Add Filter Alert to List:
                
                $this->data()->addListAlert(
                    sprintf(_t(__CLASS__ . '.SHOWINGPOSTSBYAUTHOR', 'Showing posts by author "%s"'), $author->Name)
                );
                
                return [];
                
            }
            
        }
        
        // Answer 404 Not Found:
        
        return $this->httpError(404);
    }
    
    /**
     * Renders a list of the blog posts matching the requested tag.
     *
     * @param HTTPRequest $request
     *
     * @return HTTPResponse|DBHTMLText|array
     */
    public function tag(HTTPRequest $request)
    {
        // Obtain Tag Segment:
        
        if ($segment = $request->param('Tag')) {
            
            // Obtain Tag Object:
            
            if ($tag = BlogTag::get()->find('URLSegment', $segment)) {
                
                // Filter Posts by Tagged Post IDs:
                
                $this->data()->addListFilter(['ID' => $tag->Posts()->column('ID')]);
                
                // Add Filter Alert to List:
                
                $this->data()->addListAlert(
                    sprintf(_t(__CLASS__ . '.SHOWINGPOSTSTAGGEDWITH', 'Showing posts tagged with "%s"'), $tag->Title)
                );
                
                return [];
                
            }
            
        }
        
        // Answer 404 Not Found:
        
        return $this->httpError(404);
    }
    
    /**
     * Answers the associated blog record.
     *
     * @return Blog
     */
    public function getBlog()
    {
        return $this->data();
    }
    
    /**
     * Performs initialisation before any action is called on the receiver.
     *
     * @return void
     */
    protected function init()
    {
        // Initialise Parent:
        
        parent::init();
        
        // Create Feed Link (if enabled):
        
        if ($this->FeedEnabled) {
            RSSFeed::linkToFeed($this->Link('rss'), $this->FeedTitle);
        }
    }
}
