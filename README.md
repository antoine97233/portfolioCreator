# PortfolioCreator

PortfolioCreator est une application web simple construite avec Symfony pour vous aider à créer et gérer votre portfolio facilement.

## Installation

Suivez ces étapes pour configurer PortfolioCreator sur votre machine locale :

1. **Cloner le Dépôt :**
   git clone https://github.com/antoine97233/portfolioCreator.git

2. **Accéder au Répertoire du Projet :**
   cd portfolio

3. **Installer les Dépendances :**
   composer install

4. **Configurer la Base de Données :**

   - Mettez à jour le fichier .env en fonction de votre configuration locale

   DATABASE_URL=mysql://votre_utilisateur:votre_mot_de_passe@127.0.0.1:3306/nom_de_votre_base

   - Créer la base de données

   php bin/console doctrine:database:create

   - Exécuter les migrations

   php bin/console doctrine:migrations:migrate

   - Charger les fixtures

   php bin/console doctrine:fixtures:load

5. **Lancer le Serveur Symfony :**
   symfony server:start

6. **Lancer le Serveur Symfony :**
   Ouvrez votre navigateur et rendez-vous sur http://localhost:8000 pour accéder à l'application

7. **Configurer le Serveur SMTP (Pour la Validation du Compte) :**

   - PortfolioCreator utilise la fonctionnalité de validation du compte par e-mail. Pour tester cette fonctionnalité en local, vous devez configurer un serveur SMTP.

   - Si vous n'avez pas de serveur SMTP local, vous pouvez utiliser des services tiers tels que [Mailtrap](https://mailtrap.io/) pour créer un compte et obtenir les informations de configuration SMTP.

   - Mettez à jour le fichier `.env` avec les informations du serveur SMTP :

     ```env
     MAILER_DSN=smtp://votre_utilisateur:votre_mot_de_passe@smtp.example.com:587
     ```

   - Assurez-vous de remplacer `votre_utilisateur`, `votre_mot_de_passe`, et `smtp.example.com` par les informations correctes de votre serveur SMTP.
