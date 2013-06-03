#!/bin/bash
 
host="chess-demo@web1.infi.nl"
source=$(readlink -e "$(dirname "$0")/..")
target="$host:/www/chess.demo.infi.nl/htdocs/api"
 
options="crv --delete-after"
exclude="--exclude-from $source/dev/exclude.txt"
 
rsync -n$options --exclude "/revision.txt" $exclude $source/ $target/
 
echo
echo rsync -$options $exclude $source/ $target/
echo
 
read -p "Uitvoeren? [y/N] " input
 
if [ "$input" == "y" ]; then
        svn info $source | grep 'Revision:' > $source/revision.txt
        date >> $source/revision.txt
        echo $(whoami)@$(hostname) >> $source/revision.txt
 
        rsync -$options $exclude $source/ $target/
 
        rm $source/revision.txt
fi

