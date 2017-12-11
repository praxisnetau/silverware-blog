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

use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SilverWare\Blog\Model\BlogTag;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Lists\ListSource;
use SilverWare\Tags\Tag;
use SilverWare\Tags\TagSource;
use Page;

/**
 * An extension of the page class for a blog category.
 *
 * @package SilverWare\Blog\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class BlogCategory extends Page implements ListSource, TagSource
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Blog Category';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Blog Categories';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A category within a blog which holds a series of posts';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/blog: admin/client/dist/images/icons/BlogCategory.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_BlogCategory';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = BlogPost::class;
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ListInherit' => 1,
        'HideFromMainMenu' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        BlogPost::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class,
        ImageDefaultsExtension::class
    ];
    
    /**
     * Answers the parent blog of the receiver.
     *
     * @return Blog
     */
    public function getBlog()
    {
        return $this->getParent();
    }
    
    /**
     * Answers a list of posts within the blog category.
     *
     * @return DataList
     */
    public function getPosts()
    {
        return BlogPost::get()->filter('ParentID', $this->ID);
    }
    
    /**
     * Answers a list of posts within the receiver.
     *
     * @return DataList
     */
    public function getListItems()
    {
        return $this->getPosts();
    }
    
    /**
     * Answers a list of tags for the posts within the receiver.
     *
     * @return ArrayList
     */
    public function getTags()
    {
        return BlogTag::forSource($this, $this->getPosts());
    }
    
    /**
     * Answers a link for the given author.
     *
     * @param Member $member
     * @param string $author
     *
     * @return string
     */
    public function getAuthorLink(Member $member, $action = null)
    {
        return $this->getBlog()->getAuthorLink($member, $action);
    }
}
