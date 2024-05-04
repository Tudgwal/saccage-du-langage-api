
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
> Attention: si vous utilisez docker, pensez à bien mettre les bonnes variables pour la base de donnée. par défaut, elle devrait ressembler à ça:
> ```
> DATABASE_URL="mysql://symfony:symfony@database:3306/symfony"
> ```

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

### en local

```bash
symfony server:start
```

Vous pourrez ensuite vous connecter à [votre API](http://127.0.0.1:8000)

### avec docker

Initialiser la base de donnée

```
make init
```

Pour faire les migrations

```
make migrate
```
Pensez à faire les migrations régulièrement pour gardez votre base de donnée à jour.

Pour démarrer le projet:
```
make up
```

Pour l'éteindre:
```
make down
```

Pour vous connecter en bash dans docker (utile pour faire les commandes php):
```
make bash
```

Pour vous connecter depuis les images docker:
- [votre site](http://localhost:8080)
- [PhpMyAdmin](http://localhost:8899)