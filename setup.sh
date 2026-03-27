#!/bin/bash
# OntoViz - Script d'installation
# Exécuter depuis la racine du projet : bash setup.sh

set -e
echo "=== OntoViz Installation ==="

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo "ERREUR: PHP non trouvé. Installez PHP 8+"
    exit 1
fi
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION;')
if [ "$PHP_VERSION" -lt 8 ]; then
    echo "ERREUR: PHP 8+ requis (version actuelle: $(php --version | head -1))"
    exit 1
fi
echo "✓ PHP $(php --version | head -1 | cut -d' ' -f2)"

# Vérifier Composer
if ! command -v composer &> /dev/null; then
    echo "Composer non trouvé. Téléchargement..."
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
fi
echo "✓ Composer $(composer --version 2>/dev/null | head -1)"

# Installer dépendances
echo "Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader
echo "✓ EasyRdf installé"

# Créer dossier data
mkdir -p data
chmod 777 data
echo "✓ Dossier data/ créé"

# Vérifier mod_rewrite
if command -v apache2ctl &> /dev/null; then
    if apache2ctl -M 2>/dev/null | grep -q rewrite; then
        echo "✓ mod_rewrite activé"
    else
        echo "⚠ mod_rewrite non actif. Activez-le : sudo a2enmod rewrite"
    fi
fi

echo ""
echo "=== Installation terminée ==="
echo "Accédez à l'application : http://votre-serveur/"
echo "Fichier exemple : public/assets/humans.rdf"
