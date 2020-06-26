#!/bin/bash
#
# Coller ce fichier à la racine du dépôt dans lequel importer les sources d'un autre dépôt
# 
# Commande pour utiliser ce script BASH :
# sh import-external-repo.sh git@github.com:O-clock-XXXXX/nom-de-depot.git nom-de-la-branche-a-creer [nom-de-la-branche-source]
#
# l'argument "git@github.com:O-clock-XXXXX/nom-de-depot.git" correspond au lien permettant de cloner le dépôt
# l'argument "nom-de-la-branche-a-creer" correspond au nom de la branche dans laquelle les sources seront récupérées (on n'écrase pas les sources actuelles)
# l'argument OPTIONNEL "nom-de-la-branche-source" correspond au nom de la branche que l'on souhaite copier sur le dépôt source (master si non précisé)

if [ $# -eq 0 ] || [ $# -eq 1 ]
then
    echo " "
    echo "Usage: sh import-external-repo.sh git@github.com:O-clock-XXXXX/repository-name.git branch-name-to-create [branch-name-source]"
    echo " "
    exit 0
fi

# Check if gitignore exists
if [ -f .gitignore ]
then
    if ! grep -Fxq import-external-repo.sh .gitignore
    then
        git add .
        git stash
        echo " "
        echo " --- Updating .gitignore file --- "
        echo "" >> .gitignore
        echo "import-external-repo.sh" >> .gitignore
        echo " --- OK ---"
        echo " "
        git add .gitignore
        git commit -m "import-external-repo.sh added to .gitignore"
        git stash apply
        git stash drop
    fi
else
        git add .
        git stash
        echo " "
        echo " --- Creating .gitignore file --- "
        echo "import-external-repo.sh" > .gitignore
        echo " --- OK ---"
        echo " "
        git add .gitignore
        git commit -m "import-external-repo.sh added to .gitignore"
        git stash apply
        git stash drop
fi

stashCommand=""

if [[ `git status --porcelain` ]]
then
    # Changes
    echo " "
    echo "Il y a des modifiations non commitées"
    read -p "Voulez-vous les écraser ou les garder (E = écraser / G = garder / A = abandon) ? " choice
    
    if [ "$choice" = "E" ] || [ "$choice" = "e" ]
    then
        git add .
        git stash
        stashCommand="drop"
    elif [ "$choice" = "G" ] || [ "$choice" = "g" ]
    then
        git add .
        git stash
        stashCommand="apply"
    else
        echo "--- Abandon ---"
        echo "Vous devriez exécuter la commande \"git status\" afin de vérifier les modifications non commitées"
        echo "et ensuite annuler les modifications ou bien les commiter"
        exit 0
    fi
fi

# Get argument
cloneURL=$1
branchName=$2

# Create branch
git checkout -b $branchName

# Clone in subdirectory
echo " "
if [ $# -eq 3 ]
then
    # Get argument
    sourceBranchName=$3
    echo " --- Cloning from $sourceBranchName --- "
    git clone -b $sourceBranchName $cloneURL $branchName
else
    echo " --- Cloning from master --- "
    git clone $cloneURL $branchName
fi

# Copy all files
echo " "
echo " --- Copying --- "
cp -Rf $branchName/* .

# Delete clone
rm -Rf $branchName

# Stage files
echo " "
echo " --- Creating commit --- "
git add .

# Commit
git commit -m "import ok"

# Push branch to origin
echo " "
echo " --- Pushing new branch --- "
git push --set-upstream origin $branchName
bases de symfony
# Restore or delete Stash
if [ "$stashCommand" = "apply" ]
then
    echo " "
    echo " --- Retrieving old source --- "
    git stash apply
    git stash drop
elif [ "$stashCommand" = "drop" ]
then
    git stash drop
fi

echo " "
echo " --- Import successfull --- "
echo " "