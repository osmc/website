#!/bin/bash -e

# Automated deployment of OSMC website using Jenkins

pushd /root/osmc-blog
service osmcblog stop
git pull --rebase
service osmcblog start
# Give time for Ghost to start, then purge cache
sleep 60
varnishadm "ban req.http.host == osmc.tv"
