#!/usr/bin/env bash

# E.g. teststandards.sh https://floss.social/users/matdave
TEST_ACTOR=$1
RESULT_FILE=${TEST_ACTOR//[:.\/]/_}
activitypub-testing test actor "$TEST_ACTOR" | jq | tee results.$RESULT_FILE.ndjson
