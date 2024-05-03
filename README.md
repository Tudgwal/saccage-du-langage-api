
# Saccage du langage API

Saccage du langage est un projet de Clément Viktorovitch de recenser les mensonges des politiciens sur son [Discord](https://discord.gg/invite/clemovitch-922206054308266014)

Cette API a pour but de proposer le recensement sur un site web.



## Requirements

| Nom | Version |
| ------ | ------- |
| PHP | >=8.2 |
| Composer | >=2.* |
| Symfony | >= 7.* |

## Installation

Installez le projet avec Symfony et Composer

### Installer les dépendences

```bash
composer install 
```

### Créer les variables d'environnement

```bash
cp .env .env.local
```

Modifiez la ligne DATABASE de .env.local pour mettre les valeurs de votre base de donnée

Génerez votre APP_SECRET
```bash
php bin/console regenerate-app-secret
```

### Initialisation de la base de donnée

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
## Utilisation

```bash
symfony server:start
```

Vous pourrez ensuite vous connecter à [votre API](127.0.0.1:8000)