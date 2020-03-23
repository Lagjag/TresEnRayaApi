#!/bin/bash

#
#   Sheel script que limpia y vuelve a crear la BD
#

echo
echo -n "¿Quieres resetear la BD? por defecto: No (s/n)"
read -r answer

if [ "$answer" != "${answer#[Ss]}" ] ; then
    echo
    echo "Limpiando la BD"
    COMANDO="php bin/console"
else
    echo
    echo "Fin del script"
    exit
fi

echo
echo "Borrando la BD"
$COMANDO doctrine:schema:drop --force
$COMANDO doctrine:schema:create

echo
echo "Finalizado con éxito"
exit