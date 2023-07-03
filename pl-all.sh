
#get all git repos within current project
echo "";
echo "";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
echo "Git repositories within this project:";
find . -type f -name '.git';


#get all 'pl files'
git_pull_files=$(find . -name "pl");

#loop through 'pl' files and exec
for pl_file in $git_pull_files
do
	$pl_file;
done
