<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Blog\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */

namespace SilverWare\Blog\Model;

use SilverWare\Blog\Pages\BlogPost;
use SilverWare\Tags\Tag;

/**
 * An extension of the tag class for a blog tag.
 *
 * @package SilverWare\Blog\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-blog
 */
class BlogTag extends Tag
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Blog Tag';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Blog Tags';
    
    /**
     * Defines the reciprocal many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'Posts' => BlogPost::class
    ];
    
    /**
     * Defines the name of the default source to obtain from the current controller.
     *
     * @var string
     * @config
     */
    private static $default_source = 'Blog';
}
