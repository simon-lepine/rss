
#get all git repos within current project
echo "";
echo "";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
echo "Git repositories within this project:";
find . -type f -name '.git';

#get current task
read -r commit_mes < current_task.txt;

#get commit message
echo "";
echo "";
echo "Git message/test:";
echo $commit_mes;
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
echo "Add additional commit message:";

#get user commit message
user_mes=$1;
if [ -z "$1" ] 
then
	read user_mes;
fi

#get all 'ps files'
git_push_files=$(find . -name "ps");

#loop through 'ps' files and exec
for ps_file in $git_push_files
do
	$ps_file "$user_mes";
done
