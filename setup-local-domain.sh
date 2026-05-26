#!/bin/bash

# Script helper to configure local domain resolution for gymapp.katrix.com.ar

echo "========================================================="
echo "Configurando resolución local para gymapp.katrix.com.ar"
echo "========================================================="

# 1. Add hosts entry if not exists
if ! grep -q "gymapp.katrix.com.ar" /etc/hosts; then
    echo "-> Agregando entrada a /etc/hosts (se solicitará contraseña sudo)..."
    echo "127.0.0.1 gymapp.katrix.com.ar" | sudo tee -a /etc/hosts
    echo "✓ Entrada agregada a /etc/hosts."
else
    echo "✓ El dominio gymapp.katrix.com.ar ya existe en /etc/hosts."
fi

# 2. Add Apache VirtualHost entry if XAMPP is used and it's not already added
VHOSTS_FILE="/opt/lampp/etc/extra/httpd-vhosts.conf"
if [ -f "$VHOSTS_FILE" ]; then
    if ! grep -q "gymapp.katrix.com.ar" "$VHOSTS_FILE"; then
        echo "-> Configurando VirtualHost en XAMPP Apache..."
        sudo tee -a "$VHOSTS_FILE" <<'EOF'

<VirtualHost *:80>
    ServerName gymapp.katrix.com.ar
    ServerAlias *.gymapp.katrix.com.ar
    DocumentRoot "/home/nachin/Documentos/katrix/gym-app/public"
    <Directory "/home/nachin/Documentos/katrix/gym-app/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
        echo "✓ VirtualHost agregado a $VHOSTS_FILE."
        echo "-> Reiniciando XAMPP Apache..."
        sudo /opt/lampp/lampp restart
        echo "✓ XAMPP reiniciado."
    else
        echo "✓ El VirtualHost ya estaba configurado en XAMPP."
    fi
fi

echo "========================================================="
echo "¡Configuración completada!"
echo "Ahora puedes acceder desde tu navegador usando:"
echo "-> Si usas XAMPP (Puerto 80): http://gymapp.katrix.com.ar"
echo "-> Si usas artisan serve (Puerto 8001): http://gymapp.katrix.com.ar:8001"
echo "========================================================="
