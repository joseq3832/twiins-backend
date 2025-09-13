#!/usr/bin/env bash

echo "🚀 Iniciando configuración del proyecto Laravel..."
echo ""

alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}📋 Configurando host local...${NC}"
if ! grep -q "backend.twiins.local" /etc/hosts; then
    echo "127.0.0.1 backend.twiins.local" | sudo tee -a /etc/hosts
    echo -e "${GREEN}✅ Host backend.twiins.local agregado a /etc/hosts${NC}"
else
    echo -e "${YELLOW}⚠️  Host backend.twiins.local ya existe en /etc/hosts${NC}"
fi
echo ""

echo -e "${BLUE}📦 Copiando archivo de configuración...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ Archivo .env creado desde .env.example${NC}"
else
    echo -e "${YELLOW}⚠️  Archivo .env ya existe${NC}"
fi
echo ""

echo -e "${BLUE}🐳 Levantando servicios de Docker...${NC}"
./vendor/bin/sail up -d
echo ""

echo -e "${BLUE}🔑 Generando clave de aplicación...${NC}"
./vendor/bin/sail artisan key:generate
echo ""

echo -e "${BLUE}🗄️  Ejecutando migraciones...${NC}"
./vendor/bin/sail artisan migrate:fresh
echo ""

echo -e "${BLUE}🌱 Ejecutando seeders...${NC}"
./vendor/bin/sail artisan db:seed
echo ""

echo -e "${BLUE}📚 Generando documentación de API...${NC}"
./vendor/bin/sail artisan l5-swagger:generate
echo ""

echo -e "${BLUE}🧹 Limpiando caché...${NC}"
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
echo ""

echo -e "${GREEN}🎉 ¡Configuración completada!${NC}"
echo ""
echo -e "${BLUE}📍 URLs disponibles:${NC}"
echo -e "   • Aplicación: ${GREEN}http://backend.twiins.local${NC}"
echo -e "   • Documentación API: ${GREEN}http://backend.twiins.local/api/documentation${NC}"
echo -e "   • Documentación Para Postman/Insomnia/etc: ${GREEN}http://backend.twiins.local/docs?api-docs.json${NC}"
echo ""
echo -e "${BLUE}👤 Usuario de prueba:${NC}"
echo -e "   • Email: ${YELLOW}admin@admin.com${NC}"
echo -e "   • Password: ${YELLOW}admin${NC}"
echo ""
echo -e "${BLUE}🛠️  Comandos útiles:${NC}"
echo -e "   • Detener servicios: ${YELLOW}./vendor/bin/sail down${NC}"
echo -e "   • Ver logs: ${YELLOW}./vendor/bin/sail logs${NC}"
echo -e "   • Ejecutar tests: ${YELLOW}./vendor/bin/sail test${NC}"
echo -e "   • Acceder al contenedor: ${YELLOW}./vendor/bin/sail shell${NC}"
echo ""
echo -e "${BLUE}⚙️  Configuración de puerto:${NC}"
echo -e "   • Puerto actual: ${YELLOW}80${NC} (sin necesidad de especificar en URL)"
echo -e "   • Para cambiar puerto: edita ${YELLOW}APP_PORT${NC} en .env y ejecuta:"
echo -e "     ${YELLOW}./vendor/bin/sail down && ./vendor/bin/sail up -d${NC}"
echo ""
echo -e "${GREEN}✨ ¡El proyecto está listo para usar!${NC}"