#!/bin/bash
function package() {
    echo "Building"

    mkdir $T_PACKAGE_FOLDER
    cp -r * $T_PACKAGE_FOLDER
    cd $T_PACKAGE_FOLDER
    rm -rf .git
    cd -
    cd /tmp
    tar -czf $T_PACKAGE_NAME $T_PACKAGE_FOLDER
    cd -
}

function submit() {
    echo "Submitting"

    S_PORT=$1
    S_USER=$2
    S_HOST=$3

    $T_SUBMIT_COMMAND -P $S_PORT $T_PACKAGE_NAME $S_USER@$S_HOST:$T_PACKAGE_NAME
    $T_RUN_COMMAND -p$S_PORT $S_USER@$S_HOST $T_RUN_SCRIPT $TRAVIS_BRANCH
}

function deploy_dev() {
    echo "Deploying Dev Branch to Staging"

    package
    submit ${DEPLOY_PORT} ${DEPLOY_USER} ${DEPLOY_HOST}
}

function deploy_prod() {
    echo "Deploying Master Branch to Production"

    package
    submit ${LIVE_DEPLOY_PORT} ${LIVE_DEPLOY_USER} ${LIVE_DEPLOY_HOST}
}

if [ "$TRAVIS_BRANCH" == "$DEV_BRANCH" ]; then
  deploy_dev
elif [ "$TRAVIS_BRANCH" == "$LIVE_BRANCH" ]; then
  deploy_prod
else
  echo "Not a deployment branch"
fi
