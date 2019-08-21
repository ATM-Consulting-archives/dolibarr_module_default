#!/bin/bash

DEFAULT_FOLDER=$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd ) # On garde le chemin vers le répertoire contenant le module default
cd ..											# On remonte d'un cran pour être dans /custom

read -p "Quel est le nom de la class principal du module ? " MODULE_CLASS_NAME
read -p "En quoi voulez-vous la renommer ? " NEW_MODULE_CLASS_NAME

MODULE_NAME_MIN=$(echo ${MODULE_CLASS_NAME} | tr [:upper:] [:lower:])
MODULE_NAME_UCFIRST="$(echo ${MODULE_NAME_MIN:0:1} | tr [:lower:] [:upper:])${MODULE_NAME_MIN:1}"
MODULE_NAME_UC=$(echo ${MODULE_CLASS_NAME} | tr [:lower:] [:upper:])

NEW_MODULE_NAME_MIN=$(echo ${NEW_MODULE_CLASS_NAME} | tr [:upper:] [:lower:])
NEW_MODULE_NAME_UCFIRST="$(echo ${NEW_MODULE_NAME_MIN:0:1} | tr [:lower:] [:upper:])${NEW_MODULE_NAME_MIN:1}"
NEW_MODULE_NAME_UC=$(echo ${NEW_MODULE_CLASS_NAME} | tr [:lower:] [:upper:])


echo "Nom du module   : ${MODULE_NAME_MIN}"
echo "Nom de la class : ${MODULE_CLASS_NAME}"
echo "Renomage module : ${NEW_MODULE_NAME_MIN}"
echo "Renomage class  : ${NEW_MODULE_CLASS_NAME}"
echo "-------------------------"
read -p "Confirmer (Y/n): " CONFIRM


if [[ -z ${CONFIRM} ]] || [[ ${CONFIRM} =~ ^y|Y(es|ES)?$ ]]
then
    cp -R ${MODULE_NAME_MIN} ${NEW_MODULE_NAME_MIN}
    cd ${NEW_MODULE_NAME_MIN}

    # Renommage des fichiers (insensible à la casse)
    for FILENAME in $(find . -iname "*${MODULE_CLASS_NAME}*")
    do
        NEW_FILENAME=$(echo ${FILENAME} | sed "s/${MODULE_CLASS_NAME}/${NEW_MODULE_CLASS_NAME}/g" | sed "s/${MODULE_NAME_MIN}/${NEW_MODULE_NAME_MIN}/g")
        if [[ ${FILENAME} = ${NEW_FILENAME} ]]; then
            echo "WARNING nom du fichier : ${FILENAME}"
        else
            mv ${FILENAME} ${NEW_FILENAME}
        fi
    done

    for FILENAME in $(find . -type f -not -path "*.git/*")
    do
        sed -i "s/${MODULE_CLASS_NAME}/${NEW_MODULE_CLASS_NAME}/g" ${FILENAME}
        sed -i "s/${MODULE_NAME_UCFIRST}/${NEW_MODULE_NAME_UCFIRST}/g" ${FILENAME}
        sed -i "s/${MODULE_NAME_MIN}/${NEW_MODULE_NAME_MIN}/g" ${FILENAME}
        sed -i "s/${MODULE_NAME_UC}/${NEW_MODULE_NAME_UC}/g" ${FILENAME}
    done
else
    echo "Action annulée"
fi

exit 0
