#!/usr/bin/env bash
php scripts/sami.phar update sami.php
git checkout -qf FETCH_HEAD
php scripts/create-redirector.php build/docs/api/index.html
php scripts/create-redirector.php build/docs/index.html
