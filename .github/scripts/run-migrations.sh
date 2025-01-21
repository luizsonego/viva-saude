#!/bin/sh

# Pre-receive hook for running Yii2 migrations on a Git push

# Get the root directory of the repository
repo_root=$(git rev-parse --show-toplevel)

# Go to the root directory of the repository
cd $repo_root

# Run the migrations
php yii migrate --interactive=0