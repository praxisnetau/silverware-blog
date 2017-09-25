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
use SilverStripe\ORM\DataObjectInterface;
use SilverWare\Blog\Model\BlogTag;
use SilverWare\Forms\TagField;
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
    private static $icon = 'silverware-blog/admin/client/dist/images/icons/BlogPost.png';
    
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
        'Tags' => BlogTag::class
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
     * Defines the list item details to show for this object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'category' => [
            'icon' => 'folder-o',
            'text' => '$CategoryLink'
        ]
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
                    _t(__CLASS__ . '.TAGS', 'Tags'),
                    BlogTag::get()
                )
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
     * Answers the parent blog of the receiver.
     *
     * @return Blog
     */
    public function getBlog()
    {
        return $this->getCategory()->getParent();
    }
}
