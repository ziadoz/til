#!/usr/bin/env bash

# Install GH CLI client
brew install gh

# Configure organisation and username variables
ORG="orgname"
USER="username"

# Retrieve pull request JSON
# @see: https://github.com/cli/cli/issues/1268
gh api --method GET /search/issues --raw-field q='is:pr org:$ORG author:$USER' --paginate | jq 'reduce inputs as $i (.; . += $i)' > pull_requests.json
gh api --method GET /search/issues --raw-field q='type:pr author:$USER' --paginate | jq > pull_requests.json
gh api --method GET /search/issues --raw-field q='type:pr author:$USER' --jq '.items.[].title' --paginate > pull_requests.json 
gh api --method GET /search/issues --raw-field q='type:pr author:$USER' --jq '.items.[] | {title,body}' --paginate > pull_requests.json