#!/bin/bash

if [[ $# < 2 ]]
then
    echo "Ce script nécessite 2 paramètres : [NAME_CAMEL_CASE] [NUMBER]"
    exit 1
fi


DEFAULT_FOLDER=$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd ) # On garde le chemin vers le répertoire contenant le module default
cd ..											# On remonte d'un cran

MODULE_CLASS_NAME=$1
MODULE_NUMBER=$2

MODULE_NAME_MIN=$(echo ${MODULE_CLASS_NAME} | tr [:upper:] [:lower:])
MODULE_NAME_UCFIRST="$(echo ${MODULE_NAME_MIN:0:1} | tr [:lower:] [:upper:])${MODULE_NAME_MIN:1}"
MODULE_NAME_UC=$(echo ${MODULE_CLASS_NAME} | tr [:lower:] [:upper:])

echo "Nom du module   : ${MODULE_NAME_MIN}"
echo "Nom de la class : ${MODULE_CLASS_NAME}"
echo "Numéro          : ${MODULE_NUMBER}"
echo "-------------------------"
read -p "Confirmer (Y/n): " CONFIRM

if [[ -z ${CONFIRM} ]] || [[ ${CONFIRM} =~ ^y|Y(es|ES)?$ ]]
then
    cp -R ${DEFAULT_FOLDER} ${MODULE_NAME_MIN}			# On copie le répertoire default dans le répertoire cible
    cd ${MODULE_NAME_MIN}								# On se place dans le répertoire cible

    rm -rf .git
    rm new_module.sh
    rm rename_module.sh
    [[ -d .settings ]] && rm -rf .settings
    [[ -d nbproject ]] && rm -rf nbproject
    [[ -f .project ]] && rm .project

    # Renommage des fichiers (insensible à la casse)
    for FILENAME in $(find . -iname "*MyModule*")
    do
        NEW_FILENAME=$(echo ${FILENAME} | sed "s/MyModule/${MODULE_CLASS_NAME}/g" | sed "s/mymodule/${MODULE_NAME_MIN}/g")
        mv ${FILENAME} ${NEW_FILENAME}
    done

    CURRENT_YEAR=$(date +%Y)
    # Renommage des variables dans les fichiers
    for FILENAME in $(find . -type f)
    do
        sed -i "s/MyModule/${MODULE_CLASS_NAME}/g" ${FILENAME}
        sed -i "s/Mymodule/${MODULE_NAME_UCFIRST}/g" ${FILENAME}
        sed -i "s/mymodule/${MODULE_NAME_MIN}/g" ${FILENAME}
        sed -i "s/MYMODULE/${MODULE_NAME_UC}/g" ${FILENAME}
        sed -i "s/100000/${MODULE_NUMBER}/g" ${FILENAME}
        sed -i "s/Copyright (C) 2019/Copyright (C) ${CURRENT_YEAR}/g" ${FILENAME}
    done

    git init

    echo "Nouveau module ${MODULE_CLASS_NAME} préparé et n'oublie jamais : ** Ce que je sais, c’est que je ne sais rien ** Socrate"
fi

exit 0