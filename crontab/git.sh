#!/bin/bash
cd ../
unset GIT_DIR

git fetch origin 2>&1 | tee  ./crontab/git.log
git reset --hard origin/master 2>&1 | tee  ./crontab/git.log
git pull origin master  2>&1 | tee  ./crontab/git.log


