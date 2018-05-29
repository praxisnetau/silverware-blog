<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Blog\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */

namespace SilverWare\Blog\Components;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use SilverWare\Blog\Pages\Blog;
use SilverWare\Blog\Pages\BlogPost;
use SilverWare\Components\BaseComponent;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a blog archive component.
 *
 * @package SilverWare\Blog\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class BlogArchiveComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Blog Archive Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Blog Archive Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows an archive of blog posts by year and month';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/blog: admin/client/dist/images/icons/BlogArchiveComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_BlogArchiveComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'CanCollapse' => 'Boolean',
        'ShowTotals' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Blog' => Blog::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'CanCollapse' => 1,
        'ShowTotals' => 1
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
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'BlogID',
                    $this->fieldLabel('BlogID'),
                    $this->getBlogOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'BlogArchiveOptions',
                    $this->fieldLabel('BlogArchiveOptions'),
                    [
                        CheckboxField::create(
                            'CanCollapse',
                            $this->fieldLabel('CanCollapse')
                        ),
                        CheckboxField::create(
                            'ShowTotals',
                            $this->fieldLabel('ShowTotals')
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
        
        $labels['BlogID'] = _t(__CLASS__ . '.BLOG', 'Blog');
        $labels['ShowTotals'] = _t(__CLASS__ . '.SHOWTOTALS', 'Show totals');
        $labels['CanCollapse'] = _t(__CLASS__ . '.CANCOLLAPSE', 'Can collapse');
        $labels['BlogArchiveOptions'] = _t(__CLASS__ . '.BLOGARCHIVE', 'Blog Archive');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Blog'] = _t(__CLASS__ . '.has_one_Blog', 'Blog');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of class names for the HTML template.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = parent::getClassNames();
        
        if ($this->CanCollapse) {
            $classes[] = 'can-collapse';
        }
        
        return $classes;
    }
    
    /**
     * Answers a list of archive years with a series of months within.
     *
     * @return ArrayList
     */
    public function getArchive()
    {
        // Define Archive List:
        
        $archive = ArrayList::create();
        
        // Check Blog Existence:
        
        if ($this->Blog()->exists()) {
            
            // Obtain Blog:
            
            $blog = $this->Blog();
            
            // Obtain Blog Category IDs:
            
            $categoryIds = $blog->AllChildren()->column('ID');
            
            // Answer Early (if no categories):
            
            if (!$categoryIds) {
                return $archive;
            }
            
            // Obtain Data Object Schema:
            
            $schema = DataObject::getSchema();
            
            // Obtain Table Suffix:
            
            $suffix = (Versioned::get_stage() === Versioned::LIVE) ? '_Live' : '';
            
            // Obtain Formatted Date Clause:
            
            $date = DB::get_conn()->formattedDatetimeClause('"Date"', '%Y-%m');
            
            // Obtain Table Names:
            
            $baseTable = $schema->baseDataTable(BlogPost::class) . $suffix;
            $dataTable = $schema->tableName(BlogPost::class) . $suffix;
            
            // Build SQL Select Query:
            
            $query = SQLSelect::create();
            
            // Add From and Joins:
            
            $query->addFrom($baseTable)->addLeftJoin(
                $dataTable,
                sprintf(
                    '"%s"."ID" = "%s"."ID"',
                    $baseTable,
                    $dataTable
                )
            );
            
            // Set Select Fields:
            
            $query->setSelect([
                'Date' => $date,
                'Total' => 'COUNT("Date")'
            ]);
            
            // Add Where Clause:
            
            $query->addWhere([
                sprintf(
                    '"ParentID" IN (%s)',
                    DB::placeholders($categoryIds)
                ) => $categoryIds
            ]);
            
            // Add Group and Order By:
            
            $query->addGroupBy($date)->addOrderBy('"Date" DESC');
            
            // Obtain Data:
            
            $data = $query->execute();
            
            // Build Year Array:
            
            $years = [];
            
            foreach ($data as $record) {
                
                // Obtain Year and Month:
                
                list($year, $month) = explode('-', $record['Date']);
                
                // Create Year Array:
                
                if (!isset($years[$year])) {
                    $years[$year] = [];
                }
                
                // Define Month Total:
                
                $years[$year][$month] = $record['Total'];
                
            }
            
            // Build Archive:
            
            foreach ($years as $yn => $mr) {
                
                $yt = 0;
                
                $months = ArrayList::create();
                
                foreach ($mr as $mn => $mt) {
                    
                    $yt += $mt;
                    
                    $dt = DBDate::create()->setValue(strtotime("{$yn}-{$mn}"));
                    
                    $months->push(
                        ArrayData::create([
                            'Link' => $blog->getArchiveLink($yn, $mn),
                            'Month' => $dt->Month(),
                            'Total' => $mt
                        ])
                    );
                    
                }
                
                $archive->push(
                    ArrayData::create([
                        'Link' => ($this->CanCollapse ? '#' : $blog->getArchiveLink($yn)),
                        'Year' => $yn,
                        'Total' => $yt,
                        'Months' => $months
                    ])
                );
                
            }
            
        }
        
        // Answer Archive List:
        
        return $archive;
    }
    
    /**
     * Answers a map of options for the blog field.
     *
     * @return Map
     */
    public function getBlogOptions()
    {
        return Blog::get()->map('ID', 'NestedTitle');
    }
    
    /**
     * Answers a message string to be shown when no data is available.
     *
     * @return string
     */
    public function getNoDataMessage()
    {
        return _t(__CLASS__ . '.NODATAAVAILABLE', 'No data available.');
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->getArchive()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
