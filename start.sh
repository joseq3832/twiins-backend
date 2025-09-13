#!/usr/bin/env bash

# Configurar alias para Sail (solo para este script)
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

echo "El alias 'sail' se ha configurado temporalmente para este script."
echo "Para que el alias 'sail' esté disponible permanentemente en tu terminal, añade la siguiente línea a tu ~/.zshrc o ~/.bashrc y reinicia tu terminal:"
echo "alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'"
echo ""

# Levantar los servicios de Docker con Sail
./vendor/bin/sail up -d