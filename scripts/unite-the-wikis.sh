#! /bin/sh
#
# Unite us. Unite the wikis.


# must be root or sudoer
if [ "$(whoami)" != "root" ]; then
	echo "Try running this script with sudo: \"sudo bash import-wiki.sh\""
	exit 1
fi


# If /usr/local/bin is not in PATH then add it
# Ref enterprisemediawiki/meza#68 "Run install.sh with non-root user"
if [[ $PATH != *"/usr/local/bin"* ]]; then
	PATH="/usr/local/bin:$PATH"
fi


#
# For now this script is not called within the same shell as install.sh
# and thus it needs to know how to get to the config.sh script on it's own
#
source "/opt/meza/config/core/config.sh"


# prompt for wikis to merge
while [ -z "$wikis" ]
do
echo -e "Enter comma-separated list of wikis to merge and hit [ENTER]: "
read wikis
done

# for new wiki id
while [ -z "$wiki_id" ]
do
echo -e "Enter the ID of the new wiki you're creating and hit [ENTER]: "
read wiki_id
done

# # new wiki name
# while [ -z "$wiki_name" ]
# do
# echo -e "Enter the name of the new wiki and hit [ENTER]: "
# read wiki_name
# done

# # prompt user for MySQL root password
# while [ -z "$mysql_root_pass" ]
# do
# echo -e "\nEnter MySQL root password and press [ENTER]: "
# read -s mysql_root_pass
# done


# # Create a wiki to merge into
# source "$m_meza/scripts/create-wiki.sh"

echo -e "\nSetting up merge"
WIKI="$wiki_id" php "$m_meza/scripts/uniteTheWikis.php" "--mergedwiki=$wiki_id" "--sourcewikis=$wikis"


# Each pass of this loop checks to see how many imports are remaining
# This is broken out this way because PHP CLI has a memory leak (I think), and
# leting bash control repeated calls to the script gets around this.
while [[ `WIKI="$wiki_id" php "$m_meza/scripts/uniteTheWikis.php" --imports-remaining` != "0" ]]; do
	echo -e "\n\n*********************\nANOTHER ROUND\n******************\n"
	WIKI="$wiki_id" php "$m_meza/scripts/uniteTheWikis.php"
done;

echo -e "\nCleaning up..."
WIKI="$wiki_id" php "$m_meza/scripts/uniteTheWikis.php" --cleanup

echo -e "\nCOMPLETE!"
