# ISET Clubs 🎓
Plateforme de gestion des clubs ISET Zaghouan.

## Installation
1. git clone https://github.com/khalil1404/iset-clubs.git
2. cd iset-clubs
3. composer install
4. cp .env .env.local
5. Configurer DATABASE_URL dans .env.local
6. php bin/console doctrine:database:create
7. php bin/console doctrine:migrations:migrate
8. php bin/console doctrine:fixtures:load
9. symfony server:start

## Comptes de test
- Admin     : admin@iset.tn / admin123
- Président : president@iset.tn / president123
- Étudiant  : etudiant1@iset.tn / etudiant123

## Stack
PHP 8.2 / Symfony 7.4 / Twig / Bootstrap 5 / MySQL
