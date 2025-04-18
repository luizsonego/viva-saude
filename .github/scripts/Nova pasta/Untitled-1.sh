#!/bin/bash

target_branch="production"
working_tree="/home2/imoveisingacom/app.imoveisinga.com.br"

while read oldrev newrev refname
do
    branch=$(git rev-parse --symbolic --abbrev-ref $refname)
    if [ -n "$branch" ] && [ "$target_branch" == "$branch" ]; then
    
       GIT_WORK_TREE=$working_tree git checkout $target_branch -f
       NOW=$(date +"%Y%m%d-%H%M")
       git tag release_$NOW $target_branch
    
       echo "   /==============================="
       echo "   | DEPLOYMENT COMPLETED"
       echo "   | Target branch: $target_branch"
       echo "   | Target folder: $working_tree"
       echo "   | Tag name     : release_$NOW"
       echo "   \=============================="
    fi
done