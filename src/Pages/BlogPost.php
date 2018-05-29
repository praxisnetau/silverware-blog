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

use SilverStripe\Forms\DatetimeField;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverWare\Blog\Model\BlogTag;
use SilverWare\Extensions\Model\DetailFieldsExtension;
use SilverWare\Forms\TagField;
use SilverWare\Select2\Forms\Select2Field;
use Page;

/**
 * An extension of the page class for a blog post.
 *
 * @package SilverWare\Blog\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class BlogPost extends Page
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Blog Post';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Blog Posts';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An individual post within a blog category';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/blog: admin/client/dist/images/icons/BlogPost.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_BlogPost';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = '"Date" DESC';
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Date' => 'Datetime'
    ];
    
    /**
     * Defines the many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $many_many = [
        'Tags' => BlogTag::class,
        'Authors' => Member::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowInMenus' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'TagsHTML' => 'HTMLFragment',
        'AuthorsHTML' => 'HTMLFragment',
        'CategoryLink' => 'HTMLFragment'
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        DetailFieldsExtension::class
    ];
    
    /**
     * Defines the format for the meta date field.
     *
     * @var string
     * @config
     */
    private static $meta_date_format = 'd MMMM Y h:mma';
    
    /**
     * Defines the asset folder for uploaded meta images.
     *
     * @var string
     * @config
     */
    private static $meta_image_folder = 'Blog';
    
    /**
     * Defines the list item details to show for this object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'category' => [
            'icon' => 'folder-o',
            'text' => '$CategoryLink',
            'show' => 'ShowCategoryInList'
        ],
        'tags' => [
            'icon' => 'tag',
            'text' => '$TagsHTML',
            'show' => 'ShowTagsInList'
        ],
        'authors' => [
            'icon' => 'user-circle',
            'text' => '$AuthorsHTML',
            'show' => 'ShowAuthorsInList'
        ]
    ];
    
    /**
     * Defines the detail fields to show for the object.
     *
     * @var array
     * @config
     */
    private static $detail_fields = [
        'date' => [
            'name' => 'Date',
            'icon' => 'calendar',
            'text' => '$MetaDateFormatted'
        ],
        'category' => [
            'name' => 'Category',
            'icon' => 'folder-o',
            'text' => '$CategoryLink'
        ],
        'authors' => [
            'name' => 'Authors',
            'icon' => 'user-circle',
            'text' => '$AuthorsHTML',
            'show' => 'ShowAuthors'
        ]
    ];
    
    /**
     * Defines the setting for showing the detail fields inline.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_inline = true;
    
    /**
     * Defines the setting for hiding the detail fields header.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_header = true;
    
    /**
     * Defines the setting for hiding the detail field names.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_names = true;
    
    /**
     * Code of the security group to use for authors.
     *
     * @var string
     * @config
     */
    private static $author_group;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DatetimeField::create(
                    'Date',
                    $this->fieldLabel('Date')
                ),
                TagField::create(
                    'Tags',
                    $this->fieldLabel('Tags'),
                    BlogTag::get()
                ),
                Select2Field::create(
                    'Authors',
                    $this->fieldLabel('Authors'),
                    $this->getAuthorOptions()
                )->setMultiple(true)
            ],
            'Content'
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
        
        $labels['Date'] = _t(__CLASS__ . '.DATE', 'Date');
        $labels['Tags'] = _t(__CLASS__ . '.TAGS', 'Tags');
        $labels['Title'] = _t(__CLASS__ . '.POSTTITLE', 'Post title');
        
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
        
        $this->Date = date('Y-m-d H:i:s');
        
        // Add Current Member as Author:
        
        if ($member = Security::getCurrentUser()) {
            $this->Authors()->add($member);
        }
    }
    
    /**
     * Answers the meta date for the receiver.
     *
     * @return DBDatetime
     */
    public function getMetaDate()
    {
        return $this->dbObject('Date');
    }
    
    /**
     * Answers the parent category of the receiver.
     *
     * @return BlogCategory
     */
    public function getCategory()
    {
        return $this->getParent();
    }
    
    /**
     * Answers a string of HTML containing a link to the parent category.
     *
     * @return string
     */
    public function getCategoryLink()
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $this->getCategory()->Link(),
            $this->getCategory()->Title
        );
    }
    
    /**
     * Answers a string of HTML containing the tags for the blog post.
     *
     * @return string
     */
    public function getTagsHTML()
    {
        $output = [];
        
        foreach ($this->Tags() as $tag) {
            $output[] = sprintf('<a class="tag" href="%s">%s</a>', $tag->Link, $tag->Title);
        }
        
        return implode(', ', $output);
    }
    
    /**
     * Answers a string of HTML containing the authors for the blog post.
     *
     * @return string
     */
    public function getAuthorsHTML()
    {
        $output = [];
        
        foreach ($this->Authors() as $author) {
            $output[] = sprintf('<a class="author" href="%s">%s</a>', $author->AuthorLink, $author->Name);
        }
        
        return implode(', ', $output);
    }
    
    /**
     * Answers true if the category is to be shown in the list.
     *
     * @return boolean
     */
    public function getShowCategoryInList()
    {
        return (boolean) $this->getBlog()->ShowCategoryInList;
    }
    
    /**
     * Answers true if the tags are to be shown in the list.
     *
     * @return boolean
     */
    public function getShowTagsInList()
    {
        return (boolean) $this->getBlog()->ShowTagsInList;
    }
    
    /**
     * Answers true if the authors are to be shown in the list.
     *
     * @return boolean
     */
    public function getShowAuthorsInList()
    {
        return ($this->getBlog()->ShowAuthorsInList && !$this->getBlog()->HideAuthors);
    }
    
    /**
     * Answers true if the authors are to be shown in the post.
     *
     * @return boolean
     */
    public function getShowAuthors()
    {
        return !$this->getBlog()->HideAuthors;
    }
    
    /**
     * Answers the parent blog of the receiver.
     *
     * @return Blog
     */
    public function getBlog()
    {
        return $this->getCategory()->getParent();
    }
    
    /**
     * Answers the security group used for blog authors.
     *
     * @return Group
     */
    public function getAuthorGroup()
    {
        if ($group = $this->config()->author_group) {
            return Group::get()->find('Code', $group);
        }
    }
    
    /**
     * Answers a map of options for the authors field.
     *
     * @return Map
     */
    public function getAuthorOptions()
    {
        return ($group = $this->getAuthorGroup()) ? $group->Members()->map() : Member::get()->map();
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
    
    /**
     * Answers the format for the date of the post.
     *
     * @return string
     */
    public function getMetaDateFormat()
    {
        if ($format = $this->getCategory()->DateFormat) {
            return $format;
        }
        
        return parent::getMetaDateFormat();
    }
}
