# SilverWare Blog Module

[![Latest Stable Version](https://poser.pugx.org/silverware/blog/v/stable)](https://packagist.org/packages/silverware/blog)
[![Latest Unstable Version](https://poser.pugx.org/silverware/blog/v/unstable)](https://packagist.org/packages/silverware/blog)
[![License](https://poser.pugx.org/silverware/blog/license)](https://packagist.org/packages/silverware/blog)

Provides a blog for [SilverWare][silverware] apps, divided into a series of categories and posts.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverWare][silverware]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/blog
```

## Usage

The module provides three pages ready for use within the CMS:

- `Blog`
- `BlogCategory`
- `BlogPost`

Create a `Blog` page as the top-level of your blog. Under the `Blog` you
may add `BlogCategory` pages as children to divide the blog into a series
of categories. Then, as children of `BlogCategory`, add your `BlogPost` pages.

A `BlogPost` consists of a title, content, date, and a series of tags. The tags
used by your `BlogPost` pages can be used in conjuction with the
`TagCloudComponent` provided by SilverWare to render a tag cloud of your
blog tags.

`Blog` and `BlogCategory` pages are also implementors of `ListSource`, and can be used with
components to show a series of blog posts as list items.

### RSS Feed

A `Blog` also supports the automatic generation of an RSS feed based on the
latest blog posts. You can enable or disable this functionality using the "Feed enabled"
checkbox on the `Blog` options tab.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](https://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](https://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[composer]: https://getcomposer.org
[issues]: https://github.com/praxisnetau/silverware-blog/issues
