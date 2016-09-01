#!/bin/bash
set -ev
$NVM_PATH install 5.8.0
$NVM_PATH use 5.8.0
$NVM_PATH install -g gulp-cli
$NVM_PATH install -g pnpm
$NVM_PATH set progress=false
cd $TRAVIS_BUILD_DIR
travis_retry pnpm i --no-bin-links
gulp build
mkdir s3_upload
mkdir s3_upload/css
mkdir s3_upload/js
cp app/webroot/css/goalous.min.css s3_upload/css/
cp app/webroot/js/goalous.min.js s3_upload/js/
cp app/webroot/js/goalous.prerender.min.js s3_upload/js/
cp app/webroot/js/ng_app.min.js s3_upload/js/
cp app/webroot/js/ng_vendors.min.js s3_upload/js/
cp app/webroot/js/vendors.min.js s3_upload/js/
mkdir s3_upload_dist
tar czvf s3_upload_dist/s3_upload.tar.gz s3_upload
