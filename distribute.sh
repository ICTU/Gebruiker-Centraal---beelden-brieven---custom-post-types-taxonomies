# shortcode: 'gcbeeld'
# shortcode: 'gcbrief'
# sh '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/development/wp-content/plugins/ictuwp-plugin-beeldbank/distribute.sh' &>/dev/null

echo '----------------------------------------------------------------';
echo 'Distribute GC post type plugin';

# clear the log file
> '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/development/wp-content/debug.log'

# copy to temp dir
rsync -r -a --delete '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/development/wp-content/plugins/ictuwp-plugin-beeldbank/' '/Users/paul/shared-paul-files/Webs/temp/'

# clean up temp dir
rm -rf '/Users/paul/shared-paul-files/Webs/temp/.git/'
rm '/Users/paul/shared-paul-files/Webs/temp/.gitignore'
rm '/Users/paul/shared-paul-files/Webs/temp/config.codekit3'
rm '/Users/paul/shared-paul-files/Webs/temp/.config.codekit3'

rm '/Users/paul/shared-paul-files/Webs/temp/distribute.sh'
rm '/Users/paul/shared-paul-files/Webs/temp/README.md'
rm '/Users/paul/shared-paul-files/Webs/temp/LICENSE'
rm '/Users/paul/shared-paul-files/Webs/temp/.DS_Store'



# --------------------------------------------------------------------------------------------------------------------------------
# Vertalingen --------------------------------------------------------------------------------------------------------------------
# --------------------------------------------------------------------------------------------------------------------------------
# remove the .pot
rm '/Users/paul/shared-paul-files/Webs/temp/languages/ictuwp-plugin-beeldbank.pot'

# copy to sep. folder for translations
rsync -r -a --delete '/Users/paul/shared-paul-files/Webs/temp/languages/' '/Users/paul/shared-paul-files/Webs/temp-lang/'

# remove lang dir
rm -rf '/Users/paul/shared-paul-files/Webs/temp/languages/'


mv '/Users/paul/shared-paul-files/Webs/temp-lang/nl_NL.po' '/Users/paul/shared-paul-files/Webs/temp-lang/ictuwp-plugin-beeldbank-nl_NL.po'
mv '/Users/paul/shared-paul-files/Webs/temp-lang/nl_NL.mo' '/Users/paul/shared-paul-files/Webs/temp-lang/ictuwp-plugin-beeldbank-nl_NL.mo'

mv '/Users/paul/shared-paul-files/Webs/temp-lang/en_US.po' '/Users/paul/shared-paul-files/Webs/temp-lang/ictuwp-plugin-beeldbank-en_US.po'
mv '/Users/paul/shared-paul-files/Webs/temp-lang/en_US.mo' '/Users/paul/shared-paul-files/Webs/temp-lang/ictuwp-plugin-beeldbank-en_US.mo'


# copy files to /wp-content/languages/themes
rsync -ah '/Users/paul/shared-paul-files/Webs/temp-lang/' '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/development/wp-content/languages/plugins/'

# languages Sentia accept
rsync -ah '/Users/paul/shared-paul-files/Webs/temp-lang/' '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/sentia/accept/www/wp-content/languages/plugins/'

# languages Sentia live
rsync -ah '/Users/paul/shared-paul-files/Webs/temp-lang/' '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/sentia/live/www/wp-content/languages/plugins/'


# remove temp dir
rm -rf '/Users/paul/shared-paul-files/Webs/temp-lang/'



cd '/Users/paul/shared-paul-files/Webs/temp/'
find . -name ‘.DS_Store’ -type f -delete
find . -name ‘*.DS_Store’ -type f -delete


# een kopietje naar Sentia accept
rsync -r -a --delete '/Users/paul/shared-paul-files/Webs/temp/' '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/sentia/accept/www/wp-content/plugins/ictuwp-plugin-beeldbank/'

# en een kopietje naar Sentia live
rsync -r -a --delete '/Users/paul/shared-paul-files/Webs/temp/' '/Users/paul/shared-paul-files/Webs/ICTU/Gebruiker Centraal/sentia/live/www/wp-content/plugins/ictuwp-plugin-beeldbank/'

# remove temp dir
rm -rf '/Users/paul/shared-paul-files/Webs/temp/'


echo 'Ready';
echo '----------------------------------------------------------------';
