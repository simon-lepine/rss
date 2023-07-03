#! /bin/bash

#get current dir
current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )";

git -C $current_dir pull;
echo "GIT pulled";

#sleep 10 seconds before importing SQL
#sleep 10;

#mysql import optional
#mysql -u root -pRT..lakebed.io < ~/public_html/lakebed/sql_struct/data.sql;
#echo "SQL updated";


#output success
echo "Done pulling Git for ${current_dir}!";
exit 0;
