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

use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use SilverWare\Blog\Model\BlogTag;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ToggleGroup;
use SilverWare\Lists\ListAlert;
use SilverWare\Lists\ListFilter;
use SilverWare\Lists\ListSource;
use SilverWare\Tags\Tag;
use SilverWare\Tags\TagSource;
use Page;

/**
 * An extension of the page class for a blog.
 *
 * @package SilverWare\Blog\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class Blog extends Page implements ListSource, TagSource
{
    use ListAlert;
    use ListFilter;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Blog';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Blogs';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Holds a series of blog posts organised into categories';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/blog: admin/client/dist/images/icons/Blog.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Blog';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = BlogCategory::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ShowCategoryInList' => 'Boolean',
        'ShowAuthorsInList' => 'Boolean',
        'ShowTagsInList' => 'Boolean',
        'HideAuthors' => 'Boolean',
        'FeedTitle' => 'Varchar(255)',
        'FeedDescription' => 'Varchar(255)',
        'FeedNumberOfPosts' => 'Int',
        'FeedEnabled' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowCategoryInList' => 1,
        'ShowAuthorsInList' => 0,
        'ShowTagsInList' => 0,
        'HideAuthors' => 0,
        'FeedEnabled' => 1,
        'FeedNumberOfPosts' => 10
    ];
    
    /**
     * Defines the default values for the list view component.
     *
     * @var array
     * @config
     */
    private static $list_view_defaults = [
        'PaginateItems' => 1,
        'ItemsPerPage' => 10
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        BlogCategory::class
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
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'PostOptions',
                    $this->fieldLabel('PostOptions'),
                    [
                        CheckboxField::create(
                            'ShowCategoryInList',
                            $this->fieldLabel('ShowCategoryInList')
                        ),
                        CheckboxField::create(
                            'ShowAuthorsInList',
                            $this->fieldLabel('ShowAuthorsInList')
                        ),
                        CheckboxField::create(
                            'ShowTagsInList',
                            $this->fieldLabel('ShowTagsInList')
                        ),
                        CheckboxField::create(
                            'HideAuthors',
                            $this->fieldLabel('HideAuthors')
                        )
                    ]
                ),
                FieldSection::create(
                    'FeedOptions',
                    $this->fieldLabel('FeedOptions'),
                    [
                        ToggleGroup::create(
                            'FeedEnabled',
                            $this->fieldLabel('FeedEnabled'),
                            [
                                TextField::create(
                                    'FeedTitle',
                                    $this->fieldLabel('FeedTitle')
                                ),
                                TextField::create(
                                    'FeedDescription',
                                    $this->fieldLabel('FeedDescription')
                                ),
                                NumericField::create(
                                    'FeedNumberOfPosts',
                                    $this->fieldLabel('FeedNumberOfPosts')
                                )
                            ]
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['FeedTitle'] = _t(__CLASS__ . '.FEEDTITLE', 'Title');
        $labels['FeedOptions'] = _t(__CLASS__ . '.FEED', 'Feed');
        $labels['PostOptions'] = _t(__CLASS__ . '.POSTS', 'Posts');
        $labels['FeedEnabled'] = _t(__CLASS__ . '.FEEDENABLED', 'Feed enabled');
        $labels['FeedDescription'] = _t(__CLASS__ . '.DESCRIPTION', 'Description');
        $labels['FeedNumberOfPosts'] = _t(__CLASS__ . '.NUMBEROFPOSTS', 'Number of posts');
        $labels['ShowCategoryInList'] = _t(__CLASS__ . '.SHOWCATEGORYINLIST', 'Show category in list');
        $labels['ShowAuthorsInList'] = _t(__CLASS__ . '.SHOWAUTHORSINLIST', 'Show authors in list');
        $labels['ShowTagsInList'] = _t(__CLASS__ . '.SHOWTAGSINLIST', 'Show tags in list');
        $labels['HideAuthors'] = _t(__CLASS__ . '.HIDEAUTHORS', 'Hide authors');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->FeedTitle = _t(
            __CLASS__ . '.DEFAULTFEEDTITLE',
            'Latest Blog Posts'
        );
        
        $this->FeedDescription = _t(
            __CLASS__ . '.DEFAULTFEEDDESCRIPTION',
            'The latest posts from our blog.'
        );
    }
    
    /**
     * Answers a list of posts within the blog.
     *
     * @return DataList
     */
    public function getPosts()
    {
        return BlogPost::get()->filter('ParentID', $this->AllChildren()->column('ID') ?: null);
    }
    
    /**
     * Answers a list of posts within the blog for the RSS feed.
     *
     * @return DataList
     */
    public function getFeedPosts()
    {
        return $this->getPosts()->limit($this->FeedNumberOfPosts);
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
        return $this->Link(Controller::join_links('author', $member->URLSegment, $action));
    }
}
