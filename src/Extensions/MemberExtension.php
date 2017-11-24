<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Blog\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */

namespace SilverWare\Blog\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;
use SilverWare\Blog\Pages\Blog;
use SilverWare\Blog\Pages\BlogPost;
use SilverWare\Blog\Pages\BlogController;

/**
 * A data extension which adds blog functionality to member objects.
 *
 * @package SilverWare\Blog\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class MemberExtension extends DataExtension
{
    /**
     * Defines the reciprocal many-many associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'BlogPosts' => BlogPost::class
    ];
    
    /**
     * Answers a link for the author.
     *
     * @param string $action
     *
     * @return string
     */
    public function getAuthorLink($action = null)
    {
        $controller = Controller::curr();
        
        if ($controller->hasMethod('getAuthorLink')) {
            return $controller->getAuthorLink($this->owner, $action);
        }
        
        if ($blog = Blog::get()->first()) {
            return $blog->getAuthorLink($this->owner, $action);
        }
    }
}
