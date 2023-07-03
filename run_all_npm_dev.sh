
#get all git react within current project
echo "";
echo "";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
echo "All webpack.config.js this project:";
find . -type f -name 'webpack.config.js';
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
echo "React within this project:";
find . -type f -name 'webpack.config.js' -not -path "*/node_modules/*" -not -path "*/shared_code/react_app/*";
sleep 20;

#get current dir so we can cd back to it
current_dir=$(pwd);

# init incrementor
inc="0"

#take pictures
while [ $inc -lt 999999 ]
do

#get all 'webpack files'
webpack_files=$(find . -type f -name 'webpack.config.js' -not -path "*/node_modules/*" -not -path "*/shared_code/react_app/*");

	#loop through 'ps' files and exec
	for react_project in $webpack_files
	do
	#string replace webpack.config.js
		react_project="${react_project/webpack.config.js/''}"
	#change dir
		cd "$react_project";
	#notify
		echo "";
		echo "Running dev for: $react_project";
		sleep 5;
	#run npm
		npm run dev;
	#cd back to parent dir
		cd "$current_dir";
	#output success
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
		echo "Done running npm dev for: $react_project";
	#sleep 
		sleep 20;
	done

#done while
done
