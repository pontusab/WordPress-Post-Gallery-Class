
## Installation

Follow these steps:

* [Download the latest release](https://github.com/pontusab/WordPress-Post-Gallery-Class/archive/master.zip).
* Clone the repo: `git clone git://github.com/pontusab/WordPress-Post-Gallery-Class.git`.
* Install with in `/wp-content/plugins/`.
* Add `$page = new WPgallery( 'page' );` to your functions.php.
* Change 'page' to any post type.
* To get the attached imaged use: `<?php echo WPgallery::images( $size ); ?>` where size is the same as your own defined sizes or WordPress 'thumbnail' and so on.

## Screenshots

#### Gallery in admin
![Alt text](/screenshot-1.png "")
![Alt text](/screenshot-2.png "")
![Alt text](/screenshot-3.png "")
![Alt text](/screenshot-4.png "")

## Authors

**Pontus Abrahamsson**

+ [http://twitter.com/pontusab](http://twitter.com/pontusab)
+ [http://github.com/pontusab](http://github.com/pontusab)

## Copyright and license

Copyright 2013 Pontus Abrahamsson.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this work except in compliance with the License.
You may obtain a copy of the License in the LICENSE file, or at:

  [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.



