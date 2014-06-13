Plaid-Backend
=============
This project contains the following main elements:
- PHP backend and API that interfaces with the Plaid API to aggregate banking info from several major banks
- Fully responsive AngularJS frontend for viewing and managing aggregated banking info

Pull requests are welcome and encouraged!


Compatable Frontend Projects
============================
- Android App (https://github.com/mbarany/Plaid-Android)


Requirements
============
- Linux OS (tested on CentOS 6)
- PHP 5.4+
- Relational SQL database (MySQL, SQL Server, etc) that Doctrine supports (http://www.doctrine-project.org/2010/02/11/database-support-doctrine2.html)
- Composer (https://getcomposer.org/)
- NodeJS & NPM (https://github.com/joyent/node/wiki/Installing-Node.js-via-package-manager)
- Bower `npm install -g bower`


Configuration
=============
- Setup your HTTP Web Server webroot to point at the `web` directory
- Run `composer install` for PHP dependencies. The configuration script will run at the end to enter various configs and credentials (DB, Plaid, Google)
- Run `npm install` for build dependencies
- Run `bower install` for javascript dependencies
- Run `gulp bower-files` to build the project javascript files


Privacy Policy
==============
This code uses the Paid API. See Plaid's Privacy Policy at https://plaid.com/privacy


License
=======

    Copyright 2014 Michael Barany

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
